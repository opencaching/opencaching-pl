<?php
namespace Utils\Text;

use DateTime;
use lib\Objects\OcConfig\OcConfig;
use lib\Controllers\Php7Handler;

/**
 * This class implements common formatters which format:
 * - date
 * - date-time
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
    public static function number($number, $decimals = 0)
    {
        global $config; //TODO

        return number_format($number, $decimals,
            $config['numberFormatDecPoint'], $config['numberFormatThousandsSep']);
    }

    /**
     * Format date according to config setting
     *
     * @param mixed $date
     *            can be timestamp or DateTime obj
     * @param boolean $useTime
     *            true if the result string should contain date and time
     * @return string formatted date or date and time
     */
    public static function date($date = null, $useTime = false)
    {
        if ($date instanceof DateTime) {
            $dateObj = $date;
        } else {
            if (is_numeric($date)) {
                // this is timestamp
                $date = "@$date"; // DateTime uses such format
            } elseif (in_array($date, ['0000-00-00', '0000-00-00 00:00:00'], true)) {
                return '-';
            }

            try {
                $dateObj = new DateTime($date);
            } catch (\Exception $e) {
                return '-';
            }
        }

        return $dateObj->format(
            Php7Handler::Boolval($useTime) ?
            OcConfig::instance()->getDatetimeFormat() :
            OcConfig::instance()->getDateFormat()
        );
    }

    /**
     * Formats date and time according to config setting
     *
     * @param mixed $datetime
     *            can be timestamp or DateTime obj
     * @return string formatted date and time
     */
    public static function dateTime($datetime = null)
    {
        return self::date($datetime, true);
    }
}