<?php
/**
 * This script sets cache location based on "nuts" data tables
 */

use Utils\Database\XDb;
use Utils\Gis;

/*
 *
 * Cleanup the table sys_temptables from entries of dead threads
 *
 * sql_dropTrigger('cacheLocationBeforeInsert');
 * sql("CREATE TRIGGER `cacheLocationBeforeInsert` BEFORE INSERT ON `cache_location`
 * FOR EACH ROW
 * BEGIN
 * SET NEW.`last_modified`=NOW();
 * END;");
 *
 * sql_dropTrigger('cacheLocationBeforeUpdate');
 * sql("CREATE TRIGGER `cacheLocationBeforeUpdate` BEFORE UPDATE ON `cache_location`
 * FOR EACH ROW
 * BEGIN
 * SET NEW.`last_modified`=NOW();
 * END;");
 *
 * run it once a day
 *
 */
$rootpath = '../../';
require_once ($rootpath . 'lib/common.inc.php');

global $lang;

$rsCache = XDb::xSql(
    "SELECT `caches`.`cache_id`, `caches`.`latitude`, `caches`.`longitude`
    FROM `caches`
        LEFT JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id`
    WHERE ISNULL(`cache_location`.`cache_id`)
    UNION
    SELECT `caches`.`cache_id`, `caches`.`latitude`, `caches`.`longitude`
    FROM `caches`
        INNER JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id`
    WHERE `caches`.`country`!='PL'
        AND `caches`.`last_modified`>`cache_location`.`last_modified`");

while ($rCache = XDb::xFetchArray($rsCache)) {
    $sCode = '';

    $rsLayers = XDb::xSql(
        "SELECT `level`, `code`, AsText(`shape`) AS `geometry` FROM `nuts_layer`
        WHERE WITHIN(GeomFromText( ? ), `shape`)
        ORDER BY `level` DESC", 'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')');

    while ($rLayers = XDb::xFetchArray($rsLayers)) {

        if ( Gis::ptInLineRing(
                $rLayers['geometry'],
                'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')') ) {
            $sCode = $rLayers['code'];
            break;
        }
    }
    XDb::xFreeResults($rsLayers);

    if ($sCode != '') {
        $adm1 = null;
        $code1 = null;
        $adm2 = null;
        $code2 = null;
        $adm3 = null;
        $code3 = null;
        $adm4 = null;
        $code4 = null;

        if (mb_strlen($sCode) > 5)
            $sCode = mb_substr($sCode, 0, 5);

        if (mb_strlen($sCode) == 5) {
            $code4 = $sCode;
            $adm4 = XDb::xSimpleQueryValue(
                "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
            $sCode = mb_substr($sCode, 0, 4);
        }

        if (mb_strlen($sCode) == 4) {
            $code3 = $sCode;
            $adm3 = XDb::xSimpleQueryValue(
                "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
            $sCode = mb_substr($sCode, 0, 3);
        }

        if (mb_strlen($sCode) == 3) {
            $code2 = $sCode;
            $adm2 = XDb::xSimpleQueryValue(
                "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
            $sCode = mb_substr($sCode, 0, 2);
        }

        if (mb_strlen($sCode) == 2) {
            $code1 = $sCode;

            if (checkField('countries', 'list_default_' . $lang))
                $lang_db = $lang;
            else
                $lang_db = "en";

            // try to get localised name first
            $adm1 = XDb::xSimpleQueryValue(
                "SELECT `countries`.`$lang`
                FROM `countries`
                WHERE `countries`.`short`='$sCode'", 0);

            if ($adm1 == null)
                $adm1 = XDb::xSimpleQueryValue(
                    "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
        }

        XDb::xSql(
            "INSERT INTO `cache_location` (`cache_id`, `adm1`, `adm2`, `adm3`, `adm4`, `code1`, `code2`, `code3`, `code4`)
            VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `adm1`= ?, `adm2`= ?, `adm3`= ?, `adm4`= ?,
                                    `code1`= ?, `code2`= ?, `code3`= ?, `code4`= ?",
            $rCache['cache_id'], $adm1, $adm2, $adm3, $adm4, $code1, $code2, $code3, $code4,
            $adm1, $adm2, $adm3, $adm4, $code1, $code2, $code3, $code4);

    } else {
        if (checkField('countries', 'list_default_' . $lang))
            $lang_db = $lang;
        else
            $lang_db = "en";
        $sCountry = XDb::xSimpleQueryValue(
            "SELECT `countries`.`pl` FROM `caches`
            INNER JOIN `countries` ON `caches`.`country`=`countries`.`short`
            WHERE `caches`.`cache_id`='$rCache[cache_id]'", 0);

        $sCode1 = XDb::xMultiVariableQueryValue(
            "SELECT `caches`.`country` FROM `caches`
            WHERE `caches`.`cache_id`=:1", null, $rCache['cache_id']);

        XDb::xSql(
            "INSERT INTO `cache_location` (`cache_id`, `adm1`, `code1`)
            VALUES ( ?,  ?,  ?) ON DUPLICATE KEY UPDATE
            `adm1`= ?, `adm2`=NULL, `adm3`=NULL, `adm4`=NULL, `code1`= ?,
            `code2`=NULL, `code3`=NULL, `code4`=NULL",
            $rCache['cache_id'], $sCountry, $sCode1, $sCountry, $sCode1);

    }
}
XDb::xFreeResults($rsCache);

