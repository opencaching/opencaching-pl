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


    public static function GetTypeTranslationKey($type)
    {
        switch($type){
            case self::TYPE_GEODRAW:    return 'gp_typeGeoDraw';
            case self::TYPE_TOURING:    return 'gp_typeTouring';
            case self::TYPE_NATURE:     return 'gp_typeNature';
            case self::TYPE_THEMATIC:   return 'gp_typeThematic';

            default:
                error_log(__METHOD__.": Unknown type: $type");
                return '';
        }
    }

    public static function GetTypeIcon($type)
    {
        $iconPath = 'tpl/stdstyle/images/blue/';

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
            case self::STATUS_OPEN:             return 'gp_statusPublic';
            case self::STATUS_UNAVAILABLE:      return 'gp_statusNotYetAvailable';
            case self::STATUS_CLOSED:           return 'gp_statusClosed';
            case self::STATUS_INSERVICE:        return 'gp_statusInService';

            default:
                error_log(__METHOD__.": Unknown status: $status");
                return '';
        }
    }


}

