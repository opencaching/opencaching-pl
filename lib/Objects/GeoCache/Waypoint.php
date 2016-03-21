<?php

namespace lib\Objects\GeoCache;

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

    private $id;
    private $geocache;
    private $type;
    private $coordinates;
    private $description;
    private $status;
    private $stage;

    private $iconNames = array(
        self::TYPE_PHYSICAL => 'images/waypoints/wp_physical.png',
        self::TYPE_VIRTUAL => 'images/waypoints/wp_virtual.png',
        self::TYPE_FINAL => 'images/waypoints/wp_final.png',
        self::TYPE_INTERESTING => 'images/waypoints/wp_reference.png',
        self::TYPE_PARKING => 'images/waypoints/wp_parking.png'
    );

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

}
