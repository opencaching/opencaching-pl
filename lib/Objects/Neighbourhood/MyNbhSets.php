<?php
namespace lib\Objects\Neighbourhood;

use lib\Objects\BaseObject;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\GeoCacheLog;

class MyNbhSets extends BaseObject
{

    /** @var Coordinates */
    private $coords;

    /** @var int */
    private $radius;

    public function __construct(Coordinates $coords, $radius)
    {
        parent::__construct();
        $this->coords = $coords;
        $this->radius = $radius;
        $this->createLocalCachesTable();
    }

    public function __destruct()
    {
        $this->dropLocalCachesTable();
    }

    /**
     * Returns array with latest caches in the nbh - as GeoCache objects
     *
     * @param number $limit
     * @param number $offset
     * @param boolean $onlyFTF
     *            - list only FTF caches if true
     * @return GeoCache[]
     */
    public function getLatestCaches($limit = 10, $offset = 0, $onlyFTF = false)
    {
        $params = [];
        $params['excludedtypes']['value'] = GeoCache::TYPE_EVENT;
        $params['excludedtypes']['data_type'] = 'integer';
        $params['status']['value'] = GeoCache::STATUS_READY;
        $params['status']['data_type'] = 'integer';
        $params['offset']['value'] = $offset;
        $params['offset']['data_type'] = 'integer';
        $params['limit']['value'] = $limit;
        $params['limit']['data_type'] = 'integer';
        $query = '
                SELECT `cache_id`
                  FROM `local_caches`
                  WHERE `type` NOT IN (:excludedtypes)
                    AND `status` = :status
                    AND `date_hidden` <= NOW()
                    AND `date_created` <= NOW()';
        if ($onlyFTF) {
            $query .= ' AND `founds` = 0 ';
        }
        $query .= '
                  ORDER BY
                    IF( (`date_hidden` > `date_created`), `date_hidden`, `date_created`) DESC,
                    `cache_id` DESC
                  LIMIT :offset, :limit';
        $stmt = $this->db->paramQuery($query, $params);
        return $this->db->dbFetchAllAsObjects($stmt, function ($row) {
            return GeoCache::fromCacheIdFactory($row['cache_id']);
        });
    }

    /**
     * Returns count of all rows can be returned by getLatestCaches() method
     *
     * @param boolean $onlyFTF
     *            - count only FTF caches if true
     * @return int
     */
    public function getLatestCachesCount($onlyFTF = false)
    {
        $query = '
                SELECT COUNT(*)
                  FROM `local_caches`
                  WHERE `type` NOT IN (:1)
                    AND `status` = :2
                    AND `date_hidden` <= NOW()
                    AND `date_created` <= NOW()';
        if ($onlyFTF) {
            $query .= ' AND `founds` = 0 ';
        }
        $query .= ' LIMIT 1';
        return $this->db->multiVariableQueryValue($query, 0, GeoCache::TYPE_EVENT, GeoCache::STATUS_READY);
    }

    /**
     * Returns array with top rated caches in the nbh - as GeoCache objects
     *
     * @param number $limit
     * @param number $offset
     * @return GeoCache[]
     */
    public function getTopRatedCaches($limit = 10, $offset = 0)
    {
        $params = [];
        $params['excludedtypes']['value'] = GeoCache::TYPE_EVENT;
        $params['excludedtypes']['data_type'] = 'integer';
        $params['status']['value'] = GeoCache::STATUS_READY;
        $params['status']['data_type'] = 'integer';
        $params['offset']['value'] = $offset;
        $params['offset']['data_type'] = 'integer';
        $params['limit']['value'] = $limit;
        $params['limit']['data_type'] = 'integer';
        $query = '
                SELECT `cache_id`
                  FROM `local_caches`
                  WHERE `type` NOT IN (:excludedtypes)
                    AND `status` = :status
                    AND `topratings` > 0
                  ORDER BY `topratings` DESC,
                    `cache_id` DESC
                  LIMIT :offset, :limit';
        $stmt = $this->db->paramQuery($query, $params);
        return $this->db->dbFetchAllAsObjects($stmt, function ($row) {
            return GeoCache::fromCacheIdFactory($row['cache_id']);
        });
    }

    /**
     * Returns count of all rows can be returned by getTopRatedCaches() method
     *
     * @return int
     */
    public function getTopRatedCachesCount()
    {
        $query = '
                SELECT COUNT(*)
                  FROM `local_caches`
                  WHERE `type` NOT IN (:1)
                    AND `status` = :2
                    AND `topratings` > 0
                  LIMIT 1';
        return $this->db->multiVariableQueryValue($query, 0, GeoCache::TYPE_EVENT, GeoCache::STATUS_READY);
    }

    /**
     * Returns array with titled caches in the nbh - as GeoCache objects
     *
     * @param number $limit
     * @param number $offset
     * @return GeoCache[]
     */
    public function getLatestTitledCaches($limit = 10, $offset = 0)
    {
        $params = [];
        $params['offset']['value'] = $offset;
        $params['offset']['data_type'] = 'integer';
        $params['limit']['value'] = $limit;
        $params['limit']['data_type'] = 'integer';
        $query = '
                SELECT `local_caches`.`cache_id` AS cache_id
                  FROM `local_caches`
                  INNER JOIN `cache_titled` ON `local_caches`.`cache_id` = `cache_titled`.`cache_id`
                  ORDER BY `cache_titled`.`date_alg` DESC,
                    `local_caches`.`cache_id` DESC
                  LIMIT :offset, :limit';
        $stmt = $this->db->paramQuery($query, $params);
        return $this->db->dbFetchAllAsObjects($stmt, function ($row) {
            return GeoCache::fromCacheIdFactory($row['cache_id']);
        });
    }

    /**
     * Returns count of all rows can be returned by getLatestTitledCaches() method
     *
     * @return int
     */
    public function getLatestTitledCachesCount()
    {
        $query = '
                SELECT COUNT(*)
                  FROM `local_caches`
                  INNER JOIN `cache_titled` ON `local_caches`.`cache_id` = `cache_titled`.`cache_id`
                  LIMIT 1';
        return $this->db->simpleQueryValue($query, 0);
    }

    /**
     * Returns array with upcomming events in the nbh - as GeoCache objects
     *
     * @param number $limit
     * @param number $offset
     * @return GeoCache[]
     */
    public function getUpcomingEvents($limit = 10, $offset = 0)
    {
        $params = [];
        $params['type']['value'] = GeoCache::TYPE_EVENT;
        $params['type']['data_type'] = 'integer';
        $params['status']['value'] = GeoCache::STATUS_READY;
        $params['status']['data_type'] = 'integer';
        $params['offset']['value'] = $offset;
        $params['offset']['data_type'] = 'integer';
        $params['limit']['value'] = $limit;
        $params['limit']['data_type'] = 'integer';
        $query = 'SELECT `cache_id`
                    FROM `local_caches`
                    WHERE `type` = :type
                      AND `status` = :status
                      AND `date_hidden` >= DATE(NOW())
                      ORDER BY `date_hidden` ASC
                    LIMIT :offset, :limit';
        $stmt = $this->db->paramQuery($query, $params);
        return $this->db->dbFetchAllAsObjects($stmt, function ($row) {
            return GeoCache::fromCacheIdFactory($row['cache_id']);
        });
    }

    /**
     * Returns count of all rows can be returned by getUpcomingEvents() method
     *
     * @return int
     */
    public function getUpcomingEventsCount()
    {
        $query = '
                SELECT COUNT(*)
                  FROM `local_caches`
                    WHERE `type` = :1
                      AND `status` = :2
                      AND `date_hidden` >= DATE(NOW())
                  LIMIT 1';
        return $this->db->multiVariableQueryValue($query, 0, GeoCache::TYPE_EVENT, GeoCache::STATUS_READY);
    }

    /**
     * Returns array with all logs in the nbh - as GeoCacheLogs objects
     *
     * @param number $limit
     * @param number $offset
     * @return GeoCacheLog[]
     */
    public function getLatestLogs($limit = 10, $offset = 0)
    {
        $params = [];
        $params['offset']['value'] = $offset;
        $params['offset']['data_type'] = 'integer';
        $params['limit']['value'] = $limit;
        $params['limit']['data_type'] = 'integer';
        $query = '
            SELECT `cache_logs`.`id`
              FROM `cache_logs`
              WHERE `cache_logs`.`deleted` = 0
                AND `cache_logs`.`cache_id` IN (SELECT `cache_id` FROM `local_caches`)
              ORDER BY `cache_logs`.`date_created` DESC
              LIMIT :offset, :limit'; // TODO: AND cache_logs.date_created >= DATE_SUB(NOW(), INTERVAL 31 DAY)
        $stmt = $this->db->paramQuery($query, $params);
        return $this->db->dbFetchAllAsObjects($stmt, function ($row) {
            return GeoCacheLog::fromLogIdFactory($row['id']);
        });
    }

    /**
     * Returns count of all rows can be returned by getLatestLogs() method
     *
     * @return int
     */
    public function getLatestLogsCount()
    {
        $query = '
            SELECT COUNT(`cache_logs`.`id`)
              FROM `cache_logs`
              WHERE `cache_logs`.`deleted` = 0
                AND `cache_logs`.`cache_id` IN (SELECT `cache_id` FROM `local_caches`)
              LIMIT 1';
        return $this->db->simpleQueryValue($query, 0);
    }

    /**
     * Creates temporary table with all caches in neighbourhood.
     * Table is used by many other methods in this object.
     * Table is dropped by destructor.
     */
    private function createLocalCachesTable()
    {
        $this->dropLocalCachesTable();
        
        $params = [];
        $params['userid']['value'] = $this->getCurrentUser()->getUserId();
        $params['userid']['data_type'] = 'integer';
        $params['lon']['value'] = $this->coords->getLongitude();
        $params['lon']['data_type'] = 'large';
        $params['lat']['value'] = $this->coords->getLatitude();
        $params['lat']['data_type'] = 'large';
        $params['radius']['value'] = $this->radius;
        $params['radius']['data_type'] = 'integer';
        $excludedstatus = GeoCache::STATUS_WAITAPPROVERS . ' , ' . GeoCache::STATUS_NOTYETAVAILABLE . ' , ' . GeoCache::STATUS_BLOCKED;
        self::db()->paramQuery("
            CREATE TEMPORARY TABLE local_caches ENGINE=MEMORY
                SELECT `cache_id`, `status`, `type`,
                    `founds`, `date_hidden`, `date_created`, `topratings`
                FROM `caches`
                WHERE `cache_id` NOT IN (SELECT `cache_ignore`.`cache_id` FROM `cache_ignore` WHERE `cache_ignore`.`user_id`= :userid)
                AND caches.status NOT IN ($excludedstatus)
                AND (acos(cos((90 - `latitude` ) * PI() / 180) * cos((90- :lat) * PI() / 180) +
                    sin((90-`latitude`) * PI() / 180) * sin((90-:lat) * PI() / 180) * cos(( `longitude` - :lon) *
                    PI() / 180)) * 6370) <= :radius
            ", $params);
        
        // TODO: Sprawdzić, czy indeksy faktycznie przyspieszają, czy są tu z przyzwyczajenia
        self::db()->simpleQuery('ALTER TABLE local_caches
                ADD PRIMARY KEY ( `cache_id` ),
                ADD INDEX(`cache_id`),
                ADD INDEX(`type`),
                ADD INDEX(`status`),
                ADD INDEX(`founds`),
                ADD INDEX(`topratings`),
                ADD INDEX(`date_hidden`),
                ADD INDEX(`date_created`)');
    }

    /**
     * Drops temporary table created by createLocalCachesTable()
     * Method called by class destructor.
     */
    private function dropLocalCachesTable()
    {
        self::db()->simpleQuery('
                DROP TEMPORARY TABLE IF EXISTS local_caches
            ');
    }
}