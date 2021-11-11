<?php

namespace src\Models\GeoCache;

use src\Utils\Database\XDb;
use src\Models\Coordinates\Coordinates;

/**
 * Geocache Waypoint (place of interest)
 * represented in db by waypoints table
 */
class Waypoint extends WaypointCommons
{
    const OPENCHECKER_ENABLED = 1;

    private $id;
    private $geocache;
    private $type;

    /**
     * @var Coordinates $coordinates
     */
    private $coordinates;
    private $description;
    private $status;
    private $stage;
    private $cacheId;
    private $openChecker;

    private static function FromDbRow($row)
    {
        $waypoint = new Waypoint();
        $waypoint->loadFromDbRow($row);
        return $waypoint;
    }

    private function loadFromDbRow($row)
    {
        $this->id           = $row['wp_id'];
        $this->cacheId      = $row['cache_id'];
        $this->type         = $row['type'];
        $this->coordinates  = Coordinates::FromCoordsFactory($row['latitude'], $row['longitude']);
        $this->description  = $row['desc'];
        $this->status       = $row['status'];
        $this->stage        = $row['stage'];
        $this->openChecker  = $row['opensprawdzacz'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @return Coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStage()
    {
        return $this->stage;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setStage($stage)
    {
        $this->stage = $stage;
        return $this;
    }

    public function getGeocache()
    {
        return $this->geocache;
    }

    public function setGeocache($geocache)
    {
        $this->geocache = $geocache;
        return $this;
    }

    public function getIconName()
    {
        return $this->getIcon($this->type);
    }


    public function isHidden(){
        return $this->status == self::STATUS_HIDDEN;
    }

    public function areCoordsHidden(){
        return $this->status == self::STATUS_VISIBLE_HIDDEN_COORDS;
    }

    public function getTypeTranslationKey()
    {
        return 'wayPointType'.$this->getType();
    }

    public function getDesc4Html()
    {
        return nl2br($this->description);
    }

    /**
     * Returns array of waypoints for given geocache
     */
    public static function GetWaypointsForCache(GeoCache $geoCache, $skipHiddenWps=true): array
    {
        if($geoCache->getCacheType() == GeoCache::TYPE_MOVING){
            // mobiles can't have waypoints...
            return [];
        }

        return self::GetWaypointsForCacheId($geoCache->getCacheId(), $skipHiddenWps);
    }

    /**
     * Returns array of waypoints for given geocache
     */
    public static function GetWaypointsForCacheId(int $cacheId, $skipHiddenWps=true): array
    {
        $s = XDb::xSql(
            "SELECT wp_id, type, longitude, latitude, `desc`, status, stage, opensprawdzacz, cache_id
            FROM waypoints
            WHERE cache_id = ? ORDER BY stage, wp_id",
            $cacheId);

        $results = [];
        while($row = XDb::xFetchArray($s)){
            $wp = Waypoint::FromDbRow($row);
            if( !$wp->isHidden() || !$skipHiddenWps ){
                $results[] = $wp;
            }
        }
        return $results;
    }
}
