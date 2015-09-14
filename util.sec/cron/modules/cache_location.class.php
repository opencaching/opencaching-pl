<?php

/* * *************************************************************************
 *
 *  Cleanup the table sys_temptables from entries of dead threads

  sql_dropTrigger('cacheLocationBeforeInsert');
  sql("CREATE TRIGGER `cacheLocationBeforeInsert` BEFORE INSERT ON `cache_location`
  FOR EACH ROW
  BEGIN
  SET NEW.`last_modified`=NOW();
  END;");

  sql_dropTrigger('cacheLocationBeforeUpdate');
  sql("CREATE TRIGGER `cacheLocationBeforeUpdate` BEFORE UPDATE ON `cache_location`
  FOR EACH ROW
  BEGIN
  SET NEW.`last_modified`=NOW();
  END;");

 *
 *                         run it once a day
 *
 * ************************************************************************* */
$rootpath = '../../../';
//  require_once($rootpath.'lib2/logic/gis.class.php');
require_once($rootpath . 'lib/gis/gis.class.php');
require_once($rootpath . 'lib/clicompatbase.inc.php');
require_once($rootpath . 'lib/common.inc.php');
// checkJob(new cache_location());
global $lang;

class cache_location
{

    var $name = 'cache_location';
    var $interval = 0;

    function run()
    {


        /* begin db connect */
        db_connect();
        if ($dblink === false) {
            echo 'Unable to connect to database';
            exit;
        }
        /* end db connect */

//      global $opt;

        $rsCache = sql("SELECT `caches`.`cache_id`, `caches`.`latitude`, `caches`.`longitude` FROM `caches` LEFT JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id` WHERE ISNULL(`cache_location`.`cache_id`) UNION SELECT `caches`.`cache_id`, `caches`.`latitude`, `caches`.`longitude` FROM `caches` INNER JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id` WHERE `caches`.`last_modified`>`cache_location`.`last_modified`");
        while ($rCache = mysql_fetch_assoc($rsCache)) {
            $sCode = '';

            $rsLayers = sql("SELECT `level`, `code`, AsText(`shape`) AS `geometry` FROM `nuts_layer` WHERE WITHIN(GeomFromText('&1'), `shape`) ORDER BY `level` DESC", 'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')');
            while ($rLayers = mysql_fetch_assoc($rsLayers)) {
                if (gis::ptInLineRing($rLayers['geometry'], 'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')')) {
                    $sCode = $rLayers['code'];
                    break;
                }
            }
            mysql_free_result($rsLayers);

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
                    $adm4 = sqlValue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
                    $sCode = mb_substr($sCode, 0, 4);
                }

                if (mb_strlen($sCode) == 4) {
                    $code3 = $sCode;
                    $adm3 = sqlvalue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
                    $sCode = mb_substr($sCode, 0, 3);
                }

                if (mb_strlen($sCode) == 3) {
                    $code2 = $sCode;
                    $adm2 = sqlvalue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
                    $sCode = mb_substr($sCode, 0, 2);
                }

                if (mb_strlen($sCode) == 2) {
                    $code1 = $sCode;

                    if (checkField('countries', 'list_default_' . $lang))
                        $lang_db = $lang;
                    else
                        $lang_db = "en";

                    // try to get localised name first
                    $adm1 = sqlvalue("SELECT `countries`.`pl`
                     FROM `countries`
                    WHERE `countries`.`short`='$sCode'", 0);

                    if ($adm1 == null)
                        $adm1 = sqlvalue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
                }

                $sql = sql("INSERT INTO `cache_location` (`cache_id`, `adm1`, `adm2`, `adm3`, `adm4`, `code1`, `code2`, `code3`, `code4`) VALUES ('&1', '&2', '&3', '&4', '&5', '&6', '&7', '&8', '&9') ON DUPLICATE KEY UPDATE `adm1`='&2', `adm2`='&3', `adm3`='&4', `adm4`='&5', `code1`='&6', `code2`='&7', `code3`='&8', `code4`='&9'", $rCache['cache_id'], $adm1, $adm2, $adm3, $adm4, $code1, $code2, $code3, $code4);
                mysql_query($sql);
            }
            else {
                if (checkField('countries', 'list_default_' . $lang))
                    $lang_db = $lang;
                else
                    $lang_db = "en";
                $sCountry = sqlvalue("SELECT `countries`.`pl`
                                         FROM `caches`
                                   INNER JOIN `countries` ON `caches`.`country`=`countries`.`short`
                                        WHERE `caches`.`cache_id`='$rCache[cache_id]'", 0
                );
                $sCode1 = sqlvalue("SELECT `caches`.`country` FROM `caches` WHERE `caches`.`cache_id`='&1'", null, $rCache['cache_id']);
                $sql = sql("INSERT INTO `cache_location` (`cache_id`, `adm1`, `code1`) VALUES ('&1', '&2', '&3') ON DUPLICATE KEY UPDATE `adm1`='&2', `adm2`=NULL, `adm3`=NULL, `adm4`=NULL, `code1`='&3', `code2`=NULL, `code3`=NULL, `code4`=NULL", $rCache['cache_id'], $sCountry, $sCode1);
                mysql_query($sql);
            }
        }
        mysql_free_result($rsCache);

        db_disconnect();
    }

}

$cache_loc = new cache_location();
$cache_loc->run();
?>
