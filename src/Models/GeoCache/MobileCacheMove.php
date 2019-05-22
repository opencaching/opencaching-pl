<?php

namespace src\Models\GeoCache;

use src\Models\BaseObject;
use src\Models\Coordinates\Coordinates;
use okapi\Facade;
use src\Utils\Gis\Gis;

class MobileCacheMove extends BaseObject
{
    private $id;
    private $userId;
    private $cacheId;
    private $logId;
    /** @var \DateTime */
    private $date;
    /** @var Coordinates */
    private $coordinates;
    /** @var float */
    private $km;

    public function __construct()
    {
        parent::__construct();
    }

    private function loadFromDbRow(array $row)
    {
        foreach ($row as $col => $val) {
            switch ($col) {
                case 'id':
                    $this->id = $val;
                    break;
                case 'user_id':
                    $this->userId = $val;
                    break;
                case 'cache_id':
                    $this->cacheId = $val;
                    break;
                case 'log_id':
                    $this->logId = $val;
                    break;
                case 'date':
                    $this->date = new \DateTime($val);
                    break;
                case 'km':
                    $this->km = floatval($val);
                    break;
            }
        }

        if (isset($row['latitude']) && isset($row['longitude'])) {
            $this->coordinates = Coordinates::FromCoordsFactory($row['latitude'], $row['longitude']);
        }
    }

    private function loadById($id)
    {
        $stmt = $this->db->multiVariableQuery(
            "SELECT * FROM `cache_moved` WHERE `id` = :1 LIMIT 1", $id);

        $dbRow = $this->db->dbResultFetch($stmt);

        if (is_array($dbRow)) {
            $this->loadFromDbRow($dbRow);
        } else {
            throw new \Exception("Cache Moved Id not found");
        }
    }

    private function updateDistance($newDistance)
    {
        $this->km = $newDistance;

        $this->db->multiVariableQuery(
            "UPDATE `cache_moved` SET `km` = :1 WHERE `log_id` = :2",
            $this->km,
            $this->logId);

        Facade::schedule_user_entries_check($this->cacheId, $this->userId);
    }


    public static function updateDateOnLogEdit(GeoCacheLog $log, $newDate)
    {

        self::db()->multiVariableQuery(
            "UPDATE cache_moved SET date=:1 WHERE log_id=:2", $newDate, $log->getId());

        self::recalculateMobileMoves($log->getGeoCache());
    }

    public static function updateMovesOnLogRemove(GeoCacheLog $log)
    {
        self::db()->multiVariableQuery(
            "DELETE FROM `cache_moved` WHERE `log_id` = :1 LIMIT 1", $log->getId());

        self::recalculateMobileMoves($log->getGeoCache());
    }

    /**
     * @deprecated
     */
    public static function recalculateMobileMovesByCacheId($cacheId)
    {
        $cache = GeoCache::fromCacheIdFactory($cacheId);
        self::recalculateMobileMoves($cache);
    }

    public static function recalculateMobileMoves(GeoCache $cache)
    {
        $db = self::db();

        $stmt = $db->multiVariableQuery(
            "SELECT `id`
            FROM `cache_moved`
            WHERE `cache_id` = :1
            ORDER BY `date` ASC",
            $cache->getCacheId()
        );

        /** @var MobileCacheMove $lastMove */
        $lastMove = null;

        while ($row = $db->dbResultFetch($stmt)) {
            $move = new self();
            $move->loadById($row['id']);

            if (!$lastMove) {
                // this is initial location of cache
                $distance = 0;
            } else {
                $distance = Gis::distanceBetween($move->coordinates, $lastMove->coordinates);
                $distance = round($distance, 2);
            }

            if ($move->km != $distance) {
                $move->updateDistance($distance);
            }

            $lastMove = $move;
        }

        if ($lastMove) {
            // update cache coords if last (if necessary)
            if (!$cache->getCoordinates()->areSameAs($lastMove->coordinates)) {
                $cache->updateCoordinates($lastMove->coordinates);
                $cache->updateAltitude(); // reset altitude of the position
            }
        }
    }

}
