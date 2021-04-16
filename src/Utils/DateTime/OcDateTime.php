<?php
namespace src\Utils\DateTime;

use DateTime;

class OcDateTime
{

    public static function now(): DateTime
    {
        return new \DateTime();
    }

    /**
     * Returns TRUE id $checkedDate is before $baseDate
     *
     * @param DateTime $checkedDate   date to check
     * @param DateTime $baseDate      base date to comepare (or now if not given)
     */
    public static function isBefore (DateTime $checkedDate, DateTime $baseDate=null)
    {
        if (!$baseDate) {
            $baseDate = self::now();
        }
        return $checkedDate->getTimestamp() < $baseDate->getTimestamp();
    }

    /**
     * Returns TRUE id $checkedDate is after $baseDate
     *
     * @param DateTime $checkedDate   date to check
     * @param DateTime $baseDate      base date to comepare (or now if not given)
     */
    public static function isAfter (DateTime $checkedDate, DateTime $baseDate=null)
    {
        if (!$baseDate) {
            $baseDate = self::now();
        }
        return $checkedDate->getTimestamp() > $baseDate->getTimestamp();
    }

    /**
     * Returns TRUE id $checkedDate is in the future
     *
     * @param DateTime $checkedDate   date to check
     */
    public static function isFuture (DateTime $checkedDate): bool
    {
        return self::isAfter($checkedDate);
    }

    /**
     * Returns TRUE id $checkedDate is in the past
     *
     * @param DateTime $checkedDate   date to check
     */
    public static function isPast (DateTime $checkedDate): bool
    {
        return self::isBefore($checkedDate);
    }

}