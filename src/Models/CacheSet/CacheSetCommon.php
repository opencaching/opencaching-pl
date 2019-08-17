<?php

namespace src\Models\CacheSet;


use src\Models\BaseObject;
use src\Utils\Debug\Debug;
use src\Models\Coordinates\Altitude;
use src\Models\Coordinates\Coordinates;
use src\Models\GeoCache\GeoCache;

class CacheSetCommon extends BaseObject
{

    const TYPE_GEODRAW = 1;
    const TYPE_TOURING = 2;
    const TYPE_NATURE = 3;
    const TYPE_THEMATIC = 4;

    const STATUS_OPEN = 1;
    const STATUS_UNAVAILABLE = 2;
    const STATUS_CLOSED = 3;
    const STATUS_INSERVICE = 4;

    const CACHESET_URL_BASE = '/powerTrail.php?ptAction=showSerie&ptrail=';

    const ACTIONLOG_CREATE = 1;
    const ACTIONLOG_ATTACH_CACHE = 2;
    const ACTIONLOG_REMOVE_CACHE = 3;
    const ACTIONLOG_ADD_OWNER = 4;
    const ACTIONLOG_REMOVE_OWNER = 5;
    const ACTIONLOG_CHANGE_STATUS = 6;

    public function __construct(){
        parent::__construct();
    }

    public static function GetTypeTranslationKey($type)
    {
        switch($type){
            case self::TYPE_GEODRAW:    return 'cs_typeGeoDraw';
            case self::TYPE_TOURING:    return 'cs_typeTouring';
            case self::TYPE_NATURE:     return 'cs_typeNature';
            case self::TYPE_THEMATIC:   return 'cs_typeThematic';

            default:
                Debug::errorLog("Unknown type: $type");
                return '';
        }
    }

    public static function GetTypeIcon($type)
    {
        $iconPath = '/images/blue/';

        switch($type){
            case self::TYPE_GEODRAW:    return $iconPath.'footprintRed.png';
            case self::TYPE_TOURING:    return $iconPath.'footprintBlue.png';
            case self::TYPE_NATURE:     return $iconPath.'footprintGreen.png';
            case self::TYPE_THEMATIC:   return $iconPath.'footprintYellow.png';

            default:
                Debug::errorLog("Unknown type: $type");
                return '';
        }
    }

    public static function GetStatusTranslationKey($status)
    {
        switch($status){
            case self::STATUS_OPEN:             return 'cs_statusPublic';
            case self::STATUS_UNAVAILABLE:      return 'cs_statusNotYetAvailable';
            case self::STATUS_CLOSED:           return 'cs_statusClosed';
            case self::STATUS_INSERVICE:        return 'cs_statusInService';

            default:
                Debug::errorLog("Unknown status: $status");
                return '';
        }
    }

    public static function getCacheSetUrlById($id){
        return self::CACHESET_URL_BASE.$id;
    }

    /**
     * Returns true is type of given cache is allowed in geopaths
     *
     * @param GeoCache $cache
     * @return boolean
     */
    public static function isCacheTypeAllowedForGeoPath(GeoCache $cache)
    {
        // these cache types are forbiden in geopaths
        $forbiddenTypes = [
            GeoCache::TYPE_EVENT,
            GeoCache::TYPE_OWNCACHE,
            GeoCache::TYPE_WEBCAM,
        ];

        return !in_array($cache->getCacheType(), $forbiddenTypes);
    }

    /**
     * Returns TRUE if given cache is in status which allow adding it to geopath
     * @param GeoCache $cache
     * @return boolean
     */
    public static function isCacheStatusAllowedForGeoPathAdd(GeoCache $cache)
    {
        // these cache statuses should prevent add to geoPath
        $forbiddenStatuses = [
            GeoCache::STATUS_BLOCKED,
            GeoCache::STATUS_ARCHIVED,
            GeoCache::STATUS_WAITAPPROVERS,
        ];

        return !in_array($cache->getStatus(), $forbiddenStatuses);
    }


    public function getCachePoints(GeoCache $cache)
    {
        // cache type to points
        $typePointsArray = [
            1 => 2, #Other
            2 => 2, #Trad.
            3 => 3, #Multi
            4 => 1, #Virt.
            5 => 0.2, #ICam.
            6 => 0, #Event
            7 => 4, #Quiz
            8 => 2, #Moving
            9 => 1, #podcast
            10 => 1, #own
        ];
        $typePoints = $typePointsArray[$cache->getCacheType()];

        $sizePointsArray = [
            2 => 2.5,   # Micro
            3 => 2,     # Small
            4 => 1.5,   # Normal [from 1 to 3 litres]
            5 => 1,     # Large [from 3 to 10 litres]
            6 => 0.5,   # Very large [more than 10 litres]
            7 => 0,     # no container
            8 => 3,     # Nano
        ];
        $sizePoints = $sizePointsArray[$cache->getSizeId()];

        $altitude = round($cache->getAltitude());
        if ($altitude <= 400){
            $altPoints = 1;
        }else{
            $altPoints = 1 + ($altitude - 400) / 200;
        }

        $difficPoint = round($cache->getDifficulty() / 3, 2);
        $terrainPoints = round($cache->getTerrain() / 3, 2);

        return ($altPoints + $typePoints + $sizePoints + $difficPoint + $terrainPoints);
    }

}
