<?php
/**
 * This script is used (can be loaded) by /search.php
 */

use Utils\Database\XDb;
global $content, $bUseZip, $hide_coords, $usr, $dbcSearch;

set_time_limit(1800);

$cacheTypeText[1] = 'Unknown Cache';
$cacheTypeText[2] = 'Traditional Cache';
$cacheTypeText[3] = 'Multi-Cache';
$cacheTypeText[4] = 'Virtual Cache';
$cacheTypeText[5] = 'Webcam Cache';
$cacheTypeText[6] = 'Event Cache';
$cacheTypeText[7] = 'Quiz';
$cacheTypeText[8] = 'Moving Cache';
$cacheTypeText[9] = 'PodCastCache';

if ($usr || ! $hide_coords) {
    // prepare the output
    $caches_per_page = 20;

    $query = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $query .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    } else {
        if ($usr === false) {
            $query .= '0 distance, ';
        } else {
            // get the users home coords
            $rs_coords = XDb::xSql(
                "SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? LIMIT 1", $usr['userid']);


            $record_coords = XDb::xFetchArray($rs_coords);

            if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {
                $query .= '0 distance, ';
            } else {
                // TODO: load from the users-profile
                $distance_unit = 'km';

                $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
                $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

                $query .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
            }
            XDb::xFreeResults($rs_coords);
        }
    }
    $query .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';

    if ($usr === false) {
        $query .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id
                    FROM `caches` ';
    } else {
        $query .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = ' . $usr['userid'];
    }

    $query .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

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

    // startat?
    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
    if (! is_numeric($startat))
        $startat = 0;

    if (isset($_REQUEST['count']))
        $count = $_REQUEST['count'];
    else
        $count = $caches_per_page;

    $maxlimit = 1000000000;

    if ($count == 'max')
        $count = $maxlimit;
    if (! is_numeric($count))
        $count = 0;
    if ($count < 1)
        $count = 1;
    if ($count > $maxlimit)
        $count = $maxlimit;

    $queryLimit = ' LIMIT ' . $startat . ', ' . $count;

    // temporĂ¤re tabelle erstellen
    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `ov2content` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery('SELECT COUNT(*) `count` FROM `ov2content`');
    $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);

    if ($rCount['count'] == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc` FROM `ov2content`, `caches`
            WHERE `ov2content`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetchOneRowOnly($s);

        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } else {
            $rsName = XDb::xSql('SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= ? LIMIT 1', $options['queryid']);
            $rName = XDb::xFetchArray($rsName);
            XDb::xFreeResults($rsName);
            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
                $sFilebasename = str_replace(" ", "_", $sFilebasename);
            } else {
                $sFilebasename = 'ocpl' . $options['queryid'];
            }
        }
    }

    $bUseZip = ($rCount['count'] > 50);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    $bUseZip = false;
    if ($bUseZip == true) {
        $content = '';
        require_once ($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('', 6);
    }

    if ($bUseZip == true) {
        header("content-type: application/zip");
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
    } else {
        header("Content-type: application/ov2");
        header("Content-Disposition: attachment; filename=" . $sFilebasename . ".ov2");
    }

    $query = 'SELECT `ov2content`.`cache_id` `cacheid`, `ov2content`.`longitude` `longitude`, `ov2content`.`latitude` `latitude`, `ov2content`.cache_mod_cords_id, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`, `cache_size`.`pl` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username`, `cache_type`.`id` `type_id` FROM `ov2content`, `caches`, `cache_type`, `cache_size`, `user` WHERE `ov2content`.`cache_id`=`caches`.`cache_id` AND `ov2content`.`type`=`cache_type`.`id` AND `ov2content`.`size`=`cache_size`.`id` AND `ov2content`.`user_id`=`user`.`user_id`';
    $s = $dbcSearch->simpleQuery($query);

    while ($r = $dbcSearch->dbResultFetch($s)) {
        $lat = sprintf('%07d', $r['latitude'] * 100000);
        $lon = sprintf('%07d', $r['longitude'] * 100000);
        // modified coords
        if ($r['cache_mod_cords_id'] > 0) { // check if we have user coords
            $r['mod_suffix'] = "[F]";
        } else {
            $r['mod_suffix'] = "";
        }

        $comb_name = $r['mod_suffix'] . $r['name'];
        $name = convert_string($comb_name);
        $username = convert_string($r['username']);
        $type = convert_string($cacheTypeText[$r['type_id']]);
        $size = convert_string($r['sizedesc']);
        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $cacheid = convert_string($r['wp_oc']);

        $line = "$cacheid - $name by $username, $type ($difficulty/$terrain)";
        $record = pack("CLllA*x", 2, 1 + 4 + 4 + 4 + strlen($line) + 1, (int) $lon, (int) $lat, $line);

        append_output($record);
        ob_flush();
    }

    // phpzip versenden
    if ($bUseZip == true) {
        $phpzip->add_data($sFilebasename . '.ov2', $content);
        echo $phpzip->save($sFilebasename . '.zip', 'b');
    }
}
exit();

function convert_string($str)
{
    $newstr = iconv("UTF-8", "utf-8", $str);
    if ($newstr == false)
        return "--- charset error ---";
    else
        return $newstr;
}

function append_output($str)
    {
        global $content, $bUseZip;

        if ($bUseZip == true)
            $content .= $str;
        else
            echo $str;
    }

