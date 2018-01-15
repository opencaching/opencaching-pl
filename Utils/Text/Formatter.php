<?php
namespace Utils\Text;

use DateTime;
use lib\Objects\OcConfig\OcConfig;

/**
 * This class implements common formatters which format:
 * - date
 * - date-time (not implemented yet!)
 * - number
 *
 * according to
 *
 */
class Formatter
{
    /**
     * Format deciml fractions or bigger numbers (with thousand point)
     * according to values set in config
     *
     * @param $number
     * @param $decimals - how many decimals should be display
     * @return string - formatted number
     */
    public static function number($number, $decimals=0)
    {
        global $config; //TODO

        return number_format($number, $decimals,
            $config['numberFormatDecPoint'], $config['numberFormatThousandsSep']);
    }

    /**
     * Format date according to config setting
     *
     * @param $date - can be timestamp or DateTime obj
     * @return string - formatted date
     */
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

    /**
     * Format date-time value according to config setting
     *
     * @param $date - can be timestamp or DateTime obj
     * @return string - formatted date
     */
    public static function dateTime($date)
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

        return $dateObj->format(OcConfig::instance()->getDatetimeFormat());
    }


}