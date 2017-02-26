<?php
namespace Utils\DateTime;


class Year
{
    public static function GetSeasonName()
    {
        $dayOfYear = date("z");
        if ($dayOfYear >= 79 && $dayOfYear <= 171 ) return 'spring';
        if ($dayOfYear >= 172 && $dayOfYear <= 264 ) return 'summer';
        if ($dayOfYear >= 265 && $dayOfYear <= 330 ) return 'autumn';
        return 'winter';
    }
}