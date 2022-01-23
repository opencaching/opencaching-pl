<?php

namespace src\Models\Notify;

use src\Models\BaseObject;
use src\Models\GeoCache\GeoCache;
use src\Models\User\User;
use src\Utils\Debug\Debug;

class Notify extends BaseObject
{
    private int $id;

    private int $cacheId;

    private ?GeoCache $cache = null;

    private int $userId;

    private ?User $user = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCacheId(): int
    {
        return $this->cacheId;
    }

    public function getCache(): ?GeoCache
    {
        if ($this->cache == null && $this->isDataLoaded()) {
            $this->cache = GeoCache::fromCacheIdFactory($this->cacheId);
        }

        return $this->cache;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUser(): ?User
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
                    Debug::errorLog("Unknown column: {$key}");
            }
        }
    }

    private static function fromDbRowFactory(array $dbRow): Notify
    {
        $n = new self();
        $n->loadFromDbRow($dbRow);

        return $n;
    }

    /**
     * Returns array of Notify objects for given user_id
     *
     * @return Notify[]
     */
    public static function getAllNotifiesForUserId(int $itemUserId): array
    {
        $query = 'SELECT *
            FROM `notify_waiting`
            WHERE `user_id` = :1
            ORDER BY `id` ASC';
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
    public static function getUniqueUserIdNotifiesList(): array
    {
        $query = '
            SELECT DISTINCT `user_id`
                FROM `notify_waiting`';
        $stmt = self::db()->multiVariableQuery($query);

        return self::db()->dbResultFetchAll($stmt);
    }

    /**
     * Deletes all notifies from DB for given userId
     */
    public static function deleteNotifiesForUserId(int $userId)
    {
        $query = '
            DELETE
                FROM `notify_waiting`
                WHERE `user_id` = :1';
        self::db()->multiVariableQuery($query, $userId);
    }

    /**
     * Inserts into notify_waiting table info about new cache notifications
     * for users - for given by $cache.
     */
    public static function generateNotifiesForCache(GeoCache $cache)
    {
        // Check user's home coords
        self::db()->multiVariableQuery('
            INSERT INTO `notify_waiting` (`cache_id`, `user_id`)
                SELECT :1, `user`.`user_id`
                FROM `user`
                WHERE `user`.`notify_caches` = TRUE
                AND `user`.`is_active_flag` = 1
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
                    AND `user`.`is_active_flag` = 1
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
