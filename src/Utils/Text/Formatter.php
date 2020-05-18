<?php
namespace src\Utils\Text;

use DateTime;
use Exception;
use src\Models\OcConfig\OcConfig;

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
            } elseif (in_array($date, ['0000-00-00', '0000-00-00 00:00:00', null], true)) {
                return '-';
            }

            try {
                $dateObj = new DateTime($date);
            } catch (Exception $e) {
                return '-';
            }
        }

        return $dateObj->format(
            boolval($useTime) ?
            OcConfig::instance()->getDatetimeFormat() :
            OcConfig::instance()->getDateFormat()
        );
    }

    /**
     * Formats date and time according to config setting
     *
     * @param mixed $datetime - can be timestamp or DateTime obj - if not set current time will be used
     * @return string formatted date and time
     */
    public static function dateTime($datetime = null)
    {
        if(!$datetime){
            $datetime = new DateTime();
        }
        return self::date($datetime, true);
    }

    /**
     * Formats $dateTime to use in SQL queries
     *
     * @param DateTime $dateTime
     * @return string
     */
    public static function dateTimeForSql(DateTime $dateTime): string
    {
        return $dateTime->format(OcConfig::instance()->getDbDateTimeFormat());
    }

    /**
     * Truncates $text to be at least $length, ending it with
     * ellipsis '(...)' if description is longer than $length.
     *
     * @param string $text Text to truncate
     * @param integer $length Max length of text
     * @return string
     */
    public static function truncateText($text, $length)
    {
        if (mb_strlen($text) > $length) {
            $result = mb_substr($text, 0, $length - 5)
            . "(...)";
        } else {
            $result = mb_substr($text, 0, $length);
        }
        return $result;
    }

}
