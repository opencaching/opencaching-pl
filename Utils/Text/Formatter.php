<?php
namespace Utils\Text;

use DateTime;
use lib\Objects\OcConfig\OcConfig;

class Formatter
{
    public static function number($number, $decimals=0)
    {
        global $config; //TODO

        return number_format($number, $decimals,
            $config['numberFormatDecPoint'], $config['numberFormatThousandsSep']);
    }

    public static function date($date)
    {
        if( $date instanceof DateTime){
            $dateObj = $date;
        }else{
            if(is_numeric($date)){
                //this is timestamp
                $date = "@$date"; // DateTime uses such format
            }

            try{
                $dateObj = new DateTime($date);
            }catch (\Exception $e){
                return '-';
            }
        }

        return $dateObj->format(OcConfig::instance()->getDateFormat());
    }
}