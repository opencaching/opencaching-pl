<?php
/**
 * This script is used (can be loaded) by /search.php
 */

ob_start();

use Utils\Database\XDb;

global $absolute_server_URI, $bUseZip, $usr, $hide_coords, $lang, $dbcSearch, $queryFilter;
require_once ($rootpath . 'lib/format.kml.inc.php');
require_once ($rootpath . 'lib/calculation.inc.php');

set_time_limit(1800);

if ($usr || ! $hide_coords) {
    // prepare the output
    $caches_per_page = 20;

    echo $kmlHead;

    $query = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $query .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    } else {
        if ($usr === false) {
            $query .= '0 distance, ';
        } else {
            // get the users home coords
            $rs_coords = XDb::xSql(
                'SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? LIMIT 1', $usr['userid']);

            $record_coords = XDb::xFetchArray($rs_coords);

            if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {
                $query .= '0 distance, ';
            } else {
                // TODO: load from the users-profile
                $distance_unit = 'km';

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
        } else {// by name

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

    // cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
    $dbcSearch->simpleQuery('DROP TEMPORARY TABLE IF EXISTS `kmlcontent`');

    // temporäre tabelle erstellen
    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `kmlcontent` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery(
        'SELECT COUNT(*) `count` FROM `kmlcontent`');
    $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);

    if ($rCount['count'] == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc` FROM `kmlcontent`, `caches`
            WHERE `kmlcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetchOneRowOnly($s);

        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } else {
            $rsName = XDb::xSql(
                'SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= ? LIMIT 1',
                $options['queryid']);

            $rName = XDb::xFetchArray($rsName);
            XDb::xFreeResults($rsName);
            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
            } else {
                $sFilebasename = "search" . $options['queryid'];
            }
        }
    }
    $sFilebasename = str_replace(" ", "_", $sFilebasename);

    $bUseZip = ($rCount['count'] > 50);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    // $bUseZip = false;
    if ($bUseZip == true) {
        require_once ($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('', 6);
    }

    /*
     * wp
     * name
     * username
     * type
     * size
     * lon
     * lat
     * icon
     */

    $s = $dbcSearch->simpleQuery('SELECT `kmlcontent`.`cache_id` `cacheid`, `kmlcontent`.`status` `status`, `kmlcontent`.`longitude` `longitude`, `kmlcontent`.`latitude` `latitude`, `kmlcontent`.cache_mod_cords_id, `kmlcontent`.`type` `type`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `cache_wp`, `cache_type`.`' . $lang . '` `typedesc`, `cache_size`.`' . $lang . '` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` FROM `kmlcontent`, `caches`, `cache_type`, `cache_size`, `user` WHERE `kmlcontent`.`cache_id`=`caches`.`cache_id` AND `kmlcontent`.`type`=`cache_type`.`id` AND `kmlcontent`.`size`=`cache_size`.`id` AND `kmlcontent`.`user_id`=`user`.`user_id`');
    while ($r = $dbcSearch->dbResultFetch($s)) {
        $thisline = $kmlLine;
        $thiskmlTypeIMG = $kmlTypeIMG;

        if (isset($kmlType[$r['type']])) {
            $icon = $kmlType[$r['type']];
            $thiskmlTypeIMG = str_replace('{type}', $kmlType[$r['type']], $thiskmlTypeIMG);
            $thiskmlTypeIMG = str_replace('{type_text}', $kmlGeocacheTypeText[$r['type']], $thiskmlTypeIMG);
        } else {
            // unknown
            $icon = $kmlType[1];
            $thiskmlTypeIMG = str_replace('{type}', $kmlType[1], $thiskmlTypeIMG);
            $thiskmlTypeIMG = str_replace('{type_text}', $kmlGeocacheTypeText[1], $thiskmlTypeIMG);
        }

        $statusStyle = 'color: green';
        if ($kmlArchived[$r['status']] == 'True') {
            $icon .= '-archived';
            $statusStyle = 'color: #900; text-decoration: line-through';
        } else {
            if ($kmlAvailable[$r['status']] == 'False') {
                $icon .= '-disabled';
                $statusStyle = 'color: rgb(240,100,100);';
            }
        }

        $thisline = str_replace('{icon}', $icon, $thisline);
        $thisline = str_replace('{typeimgurl}', $thiskmlTypeIMG, $thisline);
        $thisline = str_replace('{status}', tr('cacheStatus_' . $r['status']), $thisline);
        $thisline = str_replace('{status-style}', $statusStyle, $thisline);

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = str_replace('{lat}', $lat, $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = str_replace('{lon}', $lon, $thisline);

        $thisline = str_replace('{name}', xmlentities(convert_string($r['name'])), $thisline);

        // modified coords?
        if ($r['cache_mod_cords_id'] > 0) { // check if we have user coords
            $thisline = str_replace('{mod_suffix}', '[F]', $thisline);
        } else {
            $thisline = str_replace('{mod_suffix}', '', $thisline);
        }

        $thisline = str_replace('{type}', xmlentities(convert_string($r['typedesc'])), $thisline);
        $thisline = str_replace('{size}', xmlentities(convert_string($r['sizedesc'])), $thisline);

        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $thisline = str_replace('{difficulty}', $difficulty, $thisline);

        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $thisline = str_replace('{terrain}', $terrain, $thisline);

        $thisline = str_replace('{username}', xmlentities(convert_string($r['username'])), $thisline);
        $thisline = str_replace('{cache_wp}', xmlentities($r['cache_wp']), $thisline);

        echo $thisline;
        // DO NOT USE HERE:
        // ob_flush();
    }

    echo $kmlFoot;

    // compress using phpzip
    if ($bUseZip == true) {
        $content = ob_get_clean();
        $phpzip->add_data($sFilebasename . '.kml', $content);
        $out = $phpzip->save($sFilebasename . '.kmz', 'r');
        header('Content-Type: application/vnd.google-earth.kmz; charset=utf8');
        header('Content-Disposition: attachment; filename="' . $sFilebasename . '.kmz"');
        header('Pragma: no-cache');
        header('Cache-Control: no-store,no-cache,must-revalidate,post-check=0,pre-check=0,private');
        // header('Content-Transfer-Encoding: binary');
        echo $out;
        ob_end_flush();
    } else {
        header('Content-Type: application/vnd.google-earth.kml; charset=utf8');
        header('Content-Disposition: attachment; filename="' . $sFilebasename . '.kml"');
        ob_end_flush();
    }
}
exit();
