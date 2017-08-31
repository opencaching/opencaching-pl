<?php
namespace lib\Objects\Notify;

use lib\Objects\BaseObject;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\User\User;

class Notify extends BaseObject
{

    const TYPE_NEWCACHE = 1;

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

    /* @var integer */
    private $type;

    public function __construct(array $params = array())
    {
        parent::__construct();
        if (isset($params['notifyId'])) {
            $this->loadById($params['notifyId']);
        }
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
            $this->cache = new GeoCache(array(
                'cacheId' => $this->cacheId
            ));
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
            $this->user = new User(array(
                'userId' => $this->userId
            ));
        }
        return $this->user;
    }

    private function loadById($notifyId)
    {
        $query = 'SELECT * FROM `notify_waiting` WHERE id = :1 LIMIT 1';
        $stmt = self::db()->multiVariableQuery($query, $notifyId);
        $dbRow = self::db()->dbResultFetch($stmt);
        
        if (is_array($dbRow)) {
            $this->loadFromDbRow($dbRow);
        } else {
            $this->dataLoaded = false;
        }
    }

    private function loadFromDbRow(array $dbRow)
    {
        foreach ($dbRow as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = (int) $val;
                    $this->dataLoaded = true;
                    break;
                case 'cache_id':
                    $this->cacheId = $val;
                    break;
                case 'user_id':
                    $this->userId = $val;
                    break;
                case 'type':
                    $this->type = $val;
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
     * @param int $type
     * @return Notify[]
     */
    public static function getAllNotifiesForUserId(int $itemUserId, int $type = Notify::TYPE_NEWCACHE)
    {
        $query = "SELECT *
            FROM `notify_waiting`
            WHERE `user_id` = :1 AND `type` = :2
            ORDER BY `id` ASC";
        $stmt = self::db()->multiVariableQuery($query, $itemUserId, $type);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    /**
     * Returns array of unique user_id's in notify queue
     * 
     * @param int $type
     * @return int[]
     */
    public static function getUniqueUserIdNotifiesList(int $type = Notify::TYPE_NEWCACHE)
    {
        $query ="
            SELECT DISTINCT `user_id`
            FROM `notify_waiting`
            WHERE `type` = :1";
        $stmt = self::db()->multiVariableQuery($query, $type);
        return self::db()->dbResultFetchAll($stmt);
    }

    /**
     * Deletes all notifies from DB for given userId
     * 
     * @param int $userId
     */
    public static function deleteNotifiesForUserId(int $userId, int $type = Notify::TYPE_NEWCACHE)
    {
        $query ="
            DELETE
            FROM `notify_waiting`
            WHERE `user_id` = :1 AND `type` = :2";
        self::db()->multiVariableQuery($query, $userId, $type);
    }

}