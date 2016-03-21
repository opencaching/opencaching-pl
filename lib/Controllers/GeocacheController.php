<?php

namespace lib\Controllers;

use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\Waypoint;
use lib\Objects\Coordinates\Coordinates;
use Utils\Database\OcDb;

/**
 * Description of GeocacheController
 *
 * @author Łza
 */
class GeocacheController
{
    public static function buildWaypointsForGeocache(GeoCache $geoCache)
    {
        $db = OcDb::instance();
        $db->multiVariableQuery("SELECT `wp_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `stage` FROM `waypoints` WHERE `cache_id`=:1 ORDER BY `stage`,`wp_id`", $geoCache->getCacheId());
        foreach ($db->dbResultFetchAll() as $wpRecord) {
            $waypoint = new Waypoint();
            $waypoint->setCoordinates(new Coordinates(array('dbRow' => $wpRecord)))
                    ->setDescription($wpRecord['desc'])
                    ->setId((int) $wpRecord['wp_id'])
                    ->setStage((int) $wpRecord['stage'])
                    ->setStatus((int) $wpRecord['status'])
                    ->setType((int) $wpRecord['type'])
                    ->setGeocache($geoCache)
            ;
            $geoCache->getWaypoints()->append($waypoint);
        }
    }
}
