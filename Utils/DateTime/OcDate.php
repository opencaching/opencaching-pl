<?php
namespace Utils\DateTime;

use lib\Objects\OcConfig\OcConfig;

class OcDate {


    public static function getFormattedDate($date){
        if( $date instanceof \DateTime){
            $dateObj = $date;
        }else{
            $dateObj = new \DateTime($date);;
        }

        return $dateObj->format(OcConfig::instance()->getDateFormat());
    }

}
