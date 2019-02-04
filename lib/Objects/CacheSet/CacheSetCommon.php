<?php

namespace lib\Objects\CacheSet;


use lib\Objects\BaseObject;

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


    public static function GetTypeTranslationKey($type)
    {
        switch($type){
            case self::TYPE_GEODRAW:    return 'cs_typeGeoDraw';
            case self::TYPE_TOURING:    return 'cs_typeTouring';
            case self::TYPE_NATURE:     return 'cs_typeNature';
            case self::TYPE_THEMATIC:   return 'cs_typeThematic';

            default:
                error_log(__METHOD__.": Unknown type: $type");
                return '';
        }
    }

    public static function GetTypeIcon($type)
    {
        $iconPath = '/tpl/stdstyle/images/blue/';

        switch($type){
            case self::TYPE_GEODRAW:    return $iconPath.'footprintRed.png';
            case self::TYPE_TOURING:    return $iconPath.'footprintBlue.png';
            case self::TYPE_NATURE:     return $iconPath.'footprintGreen.png';
            case self::TYPE_THEMATIC:   return $iconPath.'footprintYellow.png';

            default:
                error_log(__METHOD__.": Unknown type: $type");
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
                error_log(__METHOD__.": Unknown status: $status");
                return '';
        }
    }

    public static function getCacheSetUrlById($id){
        return self::CACHESET_URL_BASE.$id;
    }
}
