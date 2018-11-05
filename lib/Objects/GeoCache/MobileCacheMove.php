<?php
namespace lib\Objects\GeoCache;

use Utils\Gis\Gis;
use lib\Objects\BaseObject;
use lib\Objects\Coordinates\Coordinates;
use okapi\Facade;

class MobileCacheMove extends BaseObject
{
    private $id;
    private $userId;
    private $cacheId;
    private $logId;
    private $date;
    private $latitude;
    private $longitude;
    private $km;

    public function __construct()
    {
        parent::__construct();
    }

    private function loadFromDbRow(array $row)
    {
        foreach ($row as $col=>$val){
            switch($col){
                case 'id': $this->id = $val; break;
                case 'user_id': $this->userId = $val; break;
                case 'cache_id': $this->cacheId = $val; break;
                case 'log_id': $this->logId = $val; break;
                case 'latitude': $this->latitude = $val; break;
                case 'longitude': $this->longitude = $val; break;
                case 'km': $this->km = $val; break;
            }
        }
    }

    private function updateDistance($newDistance)
    {
        $this->db->multiVariableQuery(
            "UPDATE cache_moved SET km = :1 WHERE logId = :2",
            $newDistance, $this->logId);

        Facade::schedule_user_entries_check($this->cacheId, $this->userId);
        Facade::disable_error_handling();
    }


    public static function updateDateOnLogEdit(GeoCacheLog $log, $newDate){

        self::db()->multiVariableQuery(
            "UPDATE cache_moved SET date=:1 WHERE log_id=:2",$newDate, $log->getId());

        self::recalculateMobileMoves($log->getGeoCache());
    }

    public static function updateMovesOnLogRemove(GeoCacheLog $log)
    {
        self::db()->multiVariableQuery(
            "DELETE FROM cache_moved WHERE log_id=:1 LIMIT 1", $log->getId());

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
            "SELECT id, user_id, latitude, longitude, km
            FROM cache_moved
            WHERE cache_id= :1
            ORDER BY date ASC", $cache->getCacheId());

        /** @var MobileCacheMove $lastMove */
        $lastMove = null;
        while($row = $db->dbResultFetch($stmt)){
            $move = new self();
            $move->loadFromDbRow($row);

            if(!lastMove){
                // this is initial location of cache
                $distance = 0;
            } else {
                $distance = Gis::distance($move->latitude, $move->longitude, $lastMove->latitude, $lastMove->longitude);
                $distance = round($distance, 2);
            }

            if($move->km != $distance){
                $move->updateDistance($distance);
            }

            $lastMove = $move;
        }

        if($lastMove) {
            // update cache coords if last if necessary
            $lastMoveCoords = Coordinates::FromCoordsFactory( $lastMove->latitude, $lastMove->longitude );

            if(!$cache->getCoordinates()->areSameAs($lastMoveCoords)){
                $cache->updateCoordinates($lastMoveCoords);
            }
        }
    }

}
