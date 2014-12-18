#!/usr/bin/php -q
<?php
/* * *************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Import
 *

 * ************************************************************************* */

require('../../lib/web.inc.php');
sql('USE `ocpl`');


$rsArea = sql("SELECT `OGR_FID`, `sitename` AS `sitename`, `sitetype` AS `sitetype`, `sitecode` AS `sitecode`, `SHAPE` AS `shape` FROM `gis`.`n2k100k_laea_wgs84` WHERE `sitecode` LIKE 'PL%'");
while ($rArea = mysql_fetch_assoc($rsArea)) {
    $sql = sql("INSERT INTO `npa_areas` (`sitename`, `sitecode`,`sitetype`,`shape`) VALUES ('&1', '&2', '&3','&4')", $rArea['sitename'], $rArea['sitecode'], $rArea['sitetype'], $rArea['shape']);
    mysql_query($sql);
}
mysql_free_result($rsArea);
?>
