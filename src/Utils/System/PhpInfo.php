<?php
namespace src\Utils\System;

/**
 *
 * This class may be used to perform simple operation on image
 */
class PhpInfo
{
    public static function versionAtLeast($mainNum, $minNum=0, $relNum=0)
    {
        return  (version_compare(PHP_VERSION, "$mainNum.$minNum.$relNum") >= 0);
    }
}