<?php

namespace lib\Objects\GeoCache;

use Utils\Database\XDb;
use lib\Objects\Coordinates\Coordinates;

/**
 * Geocache Waypoint (place of interest)
 * represented in db by waypoints table
 *
 * @author Łza
 */
class Waypoint
{
    const TYPE_PHYSICAL = 1;
    const TYPE_VIRTUAL = 2;
    const TYPE_FINAL = 3;
    const TYPE_INTERESTING = 4;
    const TYPE_PARKING = 5;

    const STATUS_VISIBLE = 1;
    const STATUS_VISIBLE_HIDDEN_COORDS = 2;
    const STATUS_HIDDEN = 3;

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

    private $iconNames = array(
        self::TYPE_PHYSICAL => 'images/waypoints/wp_physical.png',
        self::TYPE_VIRTUAL => 'images/waypoints/wp_virtual.png',
        self::TYPE_FINAL => 'images/waypoints/wp_final.png',
        self::TYPE_INTERESTING => 'images/waypoints/wp_reference.png',
        self::TYPE_PARKING => 'images/waypoints/wp_parking.png'
    );



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
     * @return \lib\Objects\Coordinates\Coordinates
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
        return $this->iconNames[$this->type];
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

    public static function GetWaypointsForCacheId(GeoCache $geoCache, $skipHiddenWps=true){


        if($geoCache->getCacheType() == GeoCache::TYPE_MOVING){
            // mobiles can't have waypoints...
            return [];
        }

        $s = XDb::xSql(
            "SELECT wp_id, type, longitude, latitude, `desc`, status, stage, opensprawdzacz, cache_id,
                    waypoint_type.en wp_type, waypoint_type.icon wp_icon
            FROM waypoints INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id)
            WHERE cache_id = ? ORDER BY stage, wp_id",
            $geoCache->getCacheId());

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
