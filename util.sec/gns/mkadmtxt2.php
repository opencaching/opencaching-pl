#!/usr/bin/php -q
<?php
/**
 * 19.04.2016, kojoty
 *
 * This script is obsolete and unmaintenance!!!
 *
 *
 */
header('Content-Type: text/plain');
set_time_limit(0);

require_once(__DIR__.'/../../lib/search.inc.php');

/* begin search index rebuild */

// I don't know how to go deeper to get not only first-level administrative areas
// try exploring http://download.geonames.org/export/dump/
$count = 0;
$force = isset($_GET['force']) && $_GET['force'] == '1';
$rsLocations = sql(
        "SELECT `UNI`, `CC1`, `ADM1`
               FROM `gns_locations`
              WHERE `DSG` LIKE 'PPL%'
                AND `ADM1` is not null" .
        ($force ? '' : ' AND ADMTXT1 is NULL')
);
while ($rLocations = sql_fetch_array($rsLocations)) {
    $sql = 'SELECT `FULL_NAME` FROM `gns_locations` WHERE dsg = \'ADM1\' AND cc1 = \'&1\' AND adm1 = \'&2\'
                      AND nt = \'N\'
                      ORDER BY name_rank
                      LIMIT 1';
    $rs = sql($sql, $rLocations['CC1'], $rLocations['ADM1']);

    if (mysql_num_rows($rs) == 1) {
        $r = sql_fetch_array($rs);
        mysql_free_result($rs);

        $locid = $rLocations['UNI'];
        $admtxt1 = $r['FULL_NAME'];

        sql("UPDATE `gns_locations` SET `ADMTXT1`='&1' WHERE uni='&2'", $admtxt1, $locid);
        $count++;
    }
}
mysql_free_result($rsLocations);
echo "Updated $count rows";
/* end search index rebuild */
