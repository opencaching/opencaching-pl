#!/usr/bin/php -q
<?php
/* * *************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Import Layer Data from NUTS_RG_03M_2003
 *
 * ************************************************************************* */

//  $opt['rootpath'] = '../../../';
// chdir to proper directory (needed for cronjobs)
//  chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));
//  require($opt['rootpath'] . 'lib2/cli.inc.php');

require('../lib/web.inc.php');
sql('USE `ocpl`');

//  sql("DELETE FROM nuts_layer");

$rsLayer = sql("SELECT GID, f_NUTS_ID, f_STAT_LEVL_ FROM nuts_rg_03m_2006");
while ($rLayer = mysql_fetch_assoc($rsLayer)) {
    echo "Import " . $rLayer['f_NUTS_ID'] . "\n";

    $pt = array();
    $sLastPt = '';

    $rsData = sql("SELECT x1, y1, x2, y2 FROM nuts_rg_03m_2006_num WHERE GID='&1' ORDER BY gid, eseq, seq", $rLayer['GID']);
    while ($rData = mysql_fetch_assoc($rsData)) {
        $pt[] = $rData['x1'] . ' ' . $rData['y1'];
        $sLastPt = $rData['x2'] . ' ' . $rData['y2'];
    }
    mysql_free_result($rsData);
    $pt[] = $sLastPt;

    $sLinestring = 'LINESTRING(' . implode(',', $pt) . ')';

    $sql = sql("INSERT INTO nuts_layer (level, code, shape) VALUES ('&1', '&2', LineFromText('&3'))", $rLayer['f_STAT_LEVL_'], $rLayer['f_NUTS_ID'], $sLinestring);
    mysql_query($sql);
    $sLinestring = '';
}
mysql_free_result($rsLayer);
?>
