<?php
namespace src\Models\GeoCache;

use src\Models\BaseObject;


class WaypointCommons extends BaseObject {

    const TYPE_PHYSICAL = 1;
    const TYPE_VIRTUAL = 2;
    const TYPE_FINAL = 3;
    const TYPE_INTERESTING = 4;
    const TYPE_PARKING = 5;
    const TYPE_TRAILHEAD = 6;

    const STATUS_VISIBLE = 1;
    const STATUS_VISIBLE_HIDDEN_COORDS = 2;
    const STATUS_HIDDEN = 3;

    const ICONS = array(
        self::TYPE_PHYSICAL => 'images/waypoints/wp_physical.png',
        self::TYPE_VIRTUAL => 'images/waypoints/wp_virtual.png',
        self::TYPE_FINAL => 'images/waypoints/wp_final.png',
        self::TYPE_INTERESTING => 'images/waypoints/wp_reference.png',
        self::TYPE_PARKING => 'images/waypoints/wp_parking.png',
        self::TYPE_TRAILHEAD => 'images/waypoints/wp_trailhead.png'
    );

    public static function typeTranslationKey($type)
    {
        return 'wayPointType'.$type;
    }

    public static function getTypesArray($forCacheType=null)
    {
        $cacheTypesWithSimpleWps = [
            GeoCacheCommons::TYPE_TRADITIONAL, GeoCacheCommons::TYPE_VIRTUAL,
            GeoCacheCommons::TYPE_WEBCAM, GeoCacheCommons::TYPE_EVENT,
            GeoCacheCommons::TYPE_GEOPATHFINAL
        ];

        if ($forCacheType && in_array($forCacheType, $cacheTypesWithSimpleWps) ) {
            return [
                self::TYPE_INTERESTING,
                self::TYPE_PARKING,
                self::TYPE_TRAILHEAD
            ];
        } else {
            return [
                self::TYPE_PHYSICAL,
                self::TYPE_VIRTUAL,
                self::TYPE_FINAL,
                self::TYPE_INTERESTING,
                self::TYPE_PARKING,
                self::TYPE_TRAILHEAD
            ];
        }
    }

    public static function getIcon($type){
        self::ICONS[$type];
    }

}
