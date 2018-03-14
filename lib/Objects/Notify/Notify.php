<?php
namespace lib\Objects\Notify;

use lib\Objects\BaseObject;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\User\User;

class Notify extends BaseObject
{

    // Frequency of sending notifications
    const SEND_NOTIFICATION_DAILY = 0;

    const SEND_NOTIFICATION_HOURLY = 1;

    const SEND_NOTIFICATION_WEEKLY = 2;

    /* @var integer */
    private $id;

    /* @var integer */
    private $cacheId;

    /* @var $cache GeoCache */
    private $cache = null;

    /* @var integer */
    private $userId;

    /* @var $user User */
    private $user = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCacheId()
    {
        return $this->cacheId;
    }

    public function getCache()
    {
        if ($this->cache == null && $this->isDataLoaded()) {
            $this->cache = GeoCache::fromCacheIdFactory($this->cacheId);
        }
        return $this->cache;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUser()
    {
        if ($this->user == null && $this->isDataLoaded()) {
            $this->user = User::fromUserIdFactory($this->userId);
        }
        return $this->user;
    }

    private function loadFromDbRow(array $dbRow)
    {
        foreach ($dbRow as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = $val;
                    $this->dataLoaded = true;
                    break;
                case 'cache_id':
                    $this->cacheId = $val;
                    break;
                case 'user_id':
                    $this->userId = $val;
                    break;
                default:
                    error_log(__METHOD__ . ": Unknown column: $key");
            }
        }
    }

    private static function fromDbRowFactory(array $dbRow)
    {
        $n = new self();
        $n->loadFromDbRow($dbRow);
        return $n;
    }

    /**
     * Returns array of Notify obiects for given user_id
     *
     * @param int $itemUserId
     * @return Notify[]
     */
    public static function getAllNotifiesForUserId($itemUserId)
    {
        $query = "SELECT *
            FROM `notify_waiting`
            WHERE `user_id` = :1
            ORDER BY `id` ASC";
        $stmt = self::db()->multiVariableQuery($query, $itemUserId);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    /**
     * Returns array of unique user_id's in notify queue
     *
     * @return int[]
     */
    public static function getUniqueUserIdNotifiesList()
    {
        $query = "
            SELECT DISTINCT `user_id`
                FROM `notify_waiting`";
        $stmt = self::db()->multiVariableQuery($query);
        return self::db()->dbResultFetchAll($stmt);
    }

    /**
     * Deletes all notifies from DB for given userId
     *
     * @param int $userId
     */
    public static function deleteNotifiesForUserId($userId)
    {
        $query = "
            DELETE
                FROM `notify_waiting`
                WHERE `user_id` = :1";
        self::db()->multiVariableQuery($query, $userId);
    }

    /**
     * Inserts into notify_waiting table info about new cache notifications
     * for users - for given by $cacheId cache.
     *
     * @param int $cacheId
     */
    public static function generateNotifiesForCache($cacheId)
    {
        if (is_null($cache = GeoCache::fromCacheIdFactory($cacheId))) { // Check for sure
            exit();
        }
        // Check user's home coords
        self::db()->multiVariableQuery('
            INSERT INTO `notify_waiting` (`cache_id`, `user_id`)
                SELECT :1, `user`.`user_id`
                FROM `user`
                WHERE `user`.`notify_caches` = TRUE
                AND `user`.`is_active_flag` = TRUE
                AND `user`.`notify_radius` > 0
                AND NOT ISNULL(`user`.`latitude`)
                AND NOT ISNULL(`user`.`longitude`)
                AND (acos(cos((90 - :2) * PI() / 180) * cos((90-`user`.`latitude`) * PI() / 180) +
                    sin((90 - :2) * PI() / 180) * sin((90 - `user`.`latitude`) * PI() / 180) * cos((:3 -`user`.`longitude`) *
                    PI() / 180)) * 6370) <= `user`.`notify_radius`
            ON DUPLICATE KEY UPDATE `notify_waiting`.`user_id` = `notify_waiting`.`user_id`
                ', $cache->getCacheId(), $cache->getCoordinates()
            ->getLatitude(), $cache->getCoordinates()
            ->getLongitude());

        // Check additional neighbourhoods
        self::db()->multiVariableQuery('
            INSERT INTO `notify_waiting` (`cache_id`, `user_id`)
                SELECT :1, `user`.`user_id`
                FROM `user`
                LEFT JOIN `user_neighbourhoods` ON `user`.`user_id` = `user_neighbourhoods`.`user_id`
                WHERE `user`.`notify_caches` = TRUE
                    AND `user`.`is_active_flag` = TRUE
                    AND `user_neighbourhoods`.`notify` = TRUE
                    AND (acos(cos((90 - :2) * PI() / 180) * cos((90-`user_neighbourhoods`.`latitude`) * PI() / 180) +
                        sin((90 - :2) * PI() / 180) * sin((90 - `user_neighbourhoods`.`latitude`) * PI() / 180) * cos((:3 -`user_neighbourhoods`.`longitude`) *
                        PI() / 180)) * 6370) <= `user_neighbourhoods`.`radius`
                ON DUPLICATE KEY UPDATE `notify_waiting`.`user_id` = `notify_waiting`.`user_id`
                ', $cache->getCacheId(), $cache->getCoordinates()
            ->getLatitude(), $cache->getCoordinates()
            ->getLongitude());
    }
}