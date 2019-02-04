<?php
/**
 * This script is used (can be loaded) by /search.php
 */

use Utils\Database\OcDb;
use lib\Objects\OcConfig\OcConfig;

global $content, $bUseZip, $usr, $hide_coords, $dbcSearch;

require_once (__DIR__.'/../lib/common.inc.php');
require_once (__DIR__.'/../lib/calculation.inc.php');

set_time_limit(1800);

if (! $usr && $hide_coords)
    die();

$dbc = OcDb::instance();

$query = 'SELECT ';

if (isset($lat_rad) && isset($lon_rad)) {
    $query .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
} else {
    if ($usr === false) {
        $query .= '0 distance, ';
    } else {
        // get the users home coords
        $stmt = $dbc->multiVariableQuery("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= :1 LIMIT 1", $usr['userid']);
        $record_coords = $dbc->dbResultFetchOneRowOnly($stmt);

        if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {
            $query .= '0 distance, ';
        } else {
            // TODO: load from the users-profile
            $distance_unit = 'km';

            $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
            $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

            $query .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
        }
    }
}
$query .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';

if ($usr === false) {
    $query .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
} else {
    $query .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`, `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.latitude,0) as cache_mod_cords_id
        FROM `caches`
        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = ' . $usr['userid'];
}
$query .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';
/* ,AVG(`caches`.`longitude`) AS avglongitude, AVG(`caches`.`latitude`) AS avglatitude */

$sortby = $options['sort'];
if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance')) {
    $query .= ' ORDER BY distance ASC';
} else
    if ($sortby == 'bycreated') {
        $query .= ' ORDER BY date_created DESC';
    } else // by name
{
        $query .= ' ORDER BY name ASC';
    }

$rs = $dbcSearch->simpleQuery(
    'SELECT MAX(`caches`.`longitude`) AS maxlongitude, MAX(`caches`.`latitude`) AS maxlatitude,
            MIN(`caches`.`longitude`) AS minlongitude, MIN(`caches`.`latitude`) AS minlatitude
    FROM `caches` WHERE `caches`.`cache_id` IN (' . $queryFilter . ')');

$r = $dbcSearch->dbResultFetchOneRowOnly($rs);
$minlat = $r['minlatitude'];
$minlon = $r['minlongitude'];
$maxlat = $r['maxlatitude'];
$maxlon = $r['maxlongitude'];


$stmt = $dbcSearch->simpleQuery($query);
$cnt = 0;
$hash = uniqid();
$f = fopen(OcConfig::getDynFilesPath() . "searchdata/" . $hash, "w");
while ($r = $dbcSearch->dbResultFetch($stmt)) {

    ++ $cnt;
    fprintf($f, "%s\n", $r['cache_id']);
}
fclose($f);

tpl_redirect(
    "/MainMap/embeded?searchdata=$hash&bbox=$minlon|$minlat|$maxlon|$maxlat");
