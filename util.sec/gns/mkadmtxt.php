#!/usr/bin/php -q
<?php
/**
 * 19.04.2016, kojoty
 *
 * This script is obsolete and unmaintenance!!!
 *
 *
 */
/* * *************************************************************************

  Ggf. muss die Location des php-Binaries angepasst werden.

  Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der
  GNS-DB.

  This script won't work in current setup, since geodb_* tables are empty.

 * ************************************************************************* */

require_once(__DIR__.'/../../lib/calculation.inc.php');
require_once(__DIR__.'/../../lib/search.inc.php');
require_once(__DIR__.'/../../tpl/stdstyle/selectlocid.inc.php');

/* begin search index rebuild */

$rsLocations = sql("SELECT `uni`, `lat`, `lon`, `rc`, `cc1`, `adm1` FROM `gns_locations` WHERE `dsg` LIKE 'PPL%'");
while ($rLocations = sql_fetch_array($rsLocations)) {
    $minlat = getMinLat($rLocations['lon'], $rLocations['lat'], 10, 1);
    $maxlat = getMaxLat($rLocations['lon'], $rLocations['lat'], 10, 1);
    $minlon = getMinLon($rLocations['lon'], $rLocations['lat'], 10, 1);
    $maxlon = getMaxLon($rLocations['lon'], $rLocations['lat'], 10, 1);

    // determine the nearest location in geodb
    $sql = 'SELECT ' . getSqlDistanceFormula($rLocations['lon'], $rLocations['lat'], 10, 1, 'lon', 'lat', 'geodb_coordinates') . ' `distance`,
                            `geodb_coordinates`.`loc_id` `loc_id`
                      FROM `geodb_coordinates`
                      WHERE `lon` > ' . $minlon . ' AND
                            `lon` < ' . $maxlon . ' AND
                            `lat` > ' . $minlat . ' AND
                            `lat` < ' . $maxlat . '
                      HAVING `distance` < 10
                      ORDER BY `distance` ASC
                      LIMIT 1';
    $rs = sql($sql);

    if (mysql_num_rows($rs) == 1) {
        $r = sql_fetch_array($rs);
        mysql_free_result($rs);

        $locid = $r['loc_id'];

        $admtxt1 = landFromLocid($locid);
        if ($admtxt1 == '0')
            $admtxt1 = '';

        // determine state
        $rsAdm2 = sql("SELECT `full_name`, `short_form` FROM `gns_locations` WHERE `rc`='&1' AND `fc`='A' AND `dsg`='ADM1' AND `cc1`='&2' AND `adm1`='&3' AND `nt`='N' LIMIT 1", $rLocations['rc'], $rLocations['cc1'], $rLocations['adm1']);
        if (mysql_num_rows($rsAdm2) == 1) {
            $rAdm2 = sql_fetch_array($rsAdm2);
            $admtxt2 = $rAdm2['short_form'];

            if ($admtxt2 == '')
                $admtxt2 = $rAdm2['full_name'];
        } else
            $admtxt3 = '';

        $admtxt3 = regierungsbezirkFromLocid($locid);
        if ($admtxt3 == '0')
            $admtxt3 = '';

        $admtxt4 = landkreisFromLocid($locid);
        if ($admtxt4 == '0')
            $admtxt4 = '';

        sql("UPDATE `gns_locations` SET `admtxt1`='&1', `admtxt2`='&2', `admtxt3`='&3', `admtxt4`='&4' WHERE uni='&5'", $admtxt1, $admtxt2, $admtxt3, $admtxt4, $rLocations['uni']);
    }
    else {
        // what to do?
    }
}
mysql_free_result($rsLocations);

/* end search index rebuild */
