<?php

ob_start();

use Utils\Database\XDb;
use Utils\Database\OcDb;
use lib\Objects\GeoCache\GeoCacheCommons;
global $content, $bUseZip, $usr, $hide_coords, $dbcSearch, $queryFilter;

require_once($rootpath . 'lib/format.gpx.inc.php');
require_once($rootpath . 'lib/calculation.inc.php');

set_time_limit(1800);

function getPictures($cacheid, $picturescount)
{
    global $thumb_max_width;
    global $thumb_max_height;

    $rs = XDb::xSql(
        'SELECT uuid, title, url, spoiler
        FROM pictures
        WHERE object_id= ? AND object_type=2 AND display=1
        ORDER BY date_created',
        $cacheid);

    if (! isset($retval))
        $retval = '';
    while ($r = XDb::xFetchArray($rs)) {
        $retval .= '&lt;img src="' . $r['url'] . '"&gt;&lt;br&gt;' . cleanup_text($r['title']) . '&lt;br&gt;';
    }

    XDb::xFreeResults($rs);
    return $retval;
}

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
                "SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? ", $usr['userid']);

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
    $query .= '`caches`.`cache_id` `cache_id`, `caches`.`wp_oc` `cache_wp`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id` ,`caches`.`votes` `votes`,`caches`.`score` `score`, `caches`.`topratings` `topratings`,';
    if ($usr === false) {
        $query .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
    } else {
        $query .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`, `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id
                FROM `caches`
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
                "SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? ", $usr['userid']);

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
    $query .= '`caches`.`cache_id` `cache_id`, `caches`.`wp_oc` `cache_wp`, `caches`.`status` `status`, `caches`.`type` `type`, IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`, `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id, `caches`.`user_id` `user_id` ,`caches`.`votes` `votes`,`caches`.`score` `score`, `caches`.`topratings` `topratings`

                                        FROM `caches`
                                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = ' . $usr['userid'] . '
                                        WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

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
    $dbcSearch->simpleQuery('DROP TEMPORARY TABLE IF EXISTS `gpxcontent`');

    // create temporary table
    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `gpxcontent` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery('SELECT COUNT(*) `count` FROM `gpxcontent`');
    $rCount = $dbcSearch->dbResultFetch($s);
    $countGPX = $rCount['count'];

    if ($countGPX == 1) {
        $s = $dbcSearch->simpleQuery('SELECT `caches`.`wp_oc` `wp_oc` FROM `gpxcontent`, `caches` WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetch($s);

        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } elseif ($options['searchtype'] == 'bypt') {
            $sFilebasename = $options['gpxPtFileName'];
        } else {
            $rsName = XDb::xSql(
                'SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= ? LIMIT 1', $options['queryid']);

            $rName = XDb::xFetchArray($rsName);
            XDb::xFreeResults($rsName);
            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
                $sFilebasename = str_replace(" ", "_", $sFilebasename);
            } else {
                $sFilebasename = 'search' . $options['queryid'];
            }
        }
    }

    $bUseZip = ($rCount['count'] > 50);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));

    // Implementation ready, but do not use ZIP for now.
    $bUseZip = false;
    if ($bUseZip == true) {
        $content = '';
        require_once ($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('', 6);
    }

    $children = '';
    $time = date($gpxTimeFormat, time());
    $gpxHead = str_replace('{time}', $time, $gpxHead);

    $s = $dbcSearch->simpleQuery('SELECT `gpxcontent`.`cache_id` `cacheid` FROM `gpxcontent`');
    while ($rs = $dbcSearch->dbResultFetch($s)) {
        $rwp = XDb::xSql(
            "SELECT  `status` FROM `waypoints`
            WHERE  `waypoints`.`cache_id`= ? AND `waypoints`.`status`='1'", $rs['cacheid']);

        if ( XDb::xFetchArray($rwp) ) {
            $children = "(HasChildren)";
        }
    }
    $gpxHead = str_replace('{wpchildren}', $children, $gpxHead);
    echo $gpxHead;

    $stmt = XDb::xSql(
        'SELECT `gpxcontent`.`cache_id` `cacheid`, `gpxcontent`.`longitude` `longitude`,
                `gpxcontent`.`latitude` `latitude`, `gpxcontent`.cache_mod_cords_id,
                `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`,
                `caches`.`picturescount` `picturescount`, `caches`.`name` `name`,
                `caches`.`country` `country`, `caches`.`terrain` `terrain`,
                `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`,
                `caches`.`size` `size`, `caches`.`type` `type`, `caches`.`status` `status`,
                `user`.`username` `username`, `gpxcontent`.`user_id` `owner_id`,
                `cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`,
                `cache_desc`.`rr_comment`, `caches`.`logpw`,`caches`.`votes` `votes`,`caches`.`score` `score`,
                `caches`.`topratings` `topratings`
        FROM `gpxcontent`, `caches`, `user`, `cache_desc`
        WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id`
            AND `caches`.`cache_id`=`cache_desc`.`cache_id`
            AND `caches`.`default_desclang`=`cache_desc`.`language`
            AND `gpxcontent`.`user_id`=`user`.`user_id`');

    while ( $r = XDb::xFetchArray($stmt) ) {

        if (@$enable_cache_access_logs) {

            $dbc = OcDb::instance();

            $cache_id = $r['cacheid'];
            $user_id = $usr !== false ? $usr['userid'] : null;
            $access_log = @$_SESSION['CACHE_ACCESS_LOG_GPX_' . $user_id];
            if ($access_log === null) {
                $_SESSION['CACHE_ACCESS_LOG_GPX_' . $user_id] = array();
                $access_log = $_SESSION['CACHE_ACCESS_LOG_GPX_' . $user_id];
            }
            if (@$access_log[$cache_id] !== true) {
                $dbc->multiVariableQuery('INSERT INTO CACHE_ACCESS_LOGS
                                    (event_date, cache_id, user_id, source, event, ip_addr, user_agent, forwarded_for)
                                 VALUES
                                    (NOW(), :1, :2, \'B\', \'download_gpxgc\', :3, :4, :5)',
                                $cache_id, $user_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'],
                                ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '' )
                );
                $access_log[$cache_id] = true;
                $_SESSION['CACHE_ACCESS_LOG_GPX_' . $user_id] = $access_log;
            }
        }

        $thisline = $gpxLine;
        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = str_replace('{lat}', $lat, $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = str_replace('{lon}', $lon, $thisline);

        $time = date($gpxTimeFormat, strtotime($r['date_hidden']));
        $thisline = str_replace('{time}', $time, $thisline);
        $thisline = str_replace('{waypoint}', $r['waypoint'], $thisline);
        $thisline = str_replace('{cacheid}', $r['cacheid'], $thisline);
        $thisline = str_replace('{cachename}', cleanup_text($r['name']), $thisline);
        $thisline = str_replace('{country}', tr($r['country']), $thisline);
        $region = XDb::xMultiVariableQueryValue(
            "SELECT `adm3` FROM `cache_location` WHERE `cache_id`= :1 LIMIT 1", 0, $r['cacheid']);

        $thisline = str_replace('{region}', $region, $thisline);
        // modified coords
        if ($r['cache_mod_cords_id'] > 0) { // check if we have user coords
            $thisline = str_replace('{mod_suffix}', '(F)', $thisline);
        } else {
            $thisline = str_replace('{mod_suffix}', '', $thisline);
        }

        if ($r['hint'] == '')
            $thisline = str_replace('{hints}', '', $thisline);
        else
            $thisline = str_replace('{hints}', cleanup_text($r['hint']), $thisline);

        $logpw = ($r['logpw'] == "" ? "" : "" . cleanup_text(tr('search_gpxgc_01')) . " <br />");

        $thisline = str_replace('{shortdesc}', cleanup_text($r['short_desc']), $thisline);
        $thisline = str_replace('{desc}', xmlencode_text($logpw . $r['desc']), $thisline);
        if ($usr == true) {
            $notes_rs = XDb::xSql(
                "SELECT `cache_notes`.`desc` `desc` FROM `cache_notes`
                WHERE `cache_notes` .`user_id`= ? AND `cache_notes`.`cache_id`= ? ",
                $usr['userid'], $r['cacheid']);

            if ($cn = XDb::xFetchArray($notes_rs)) {
                $thisline = str_replace('{personal_cache_note}', cleanup_text("<br/><br/>-- " . cleanup_text(tr('search_gpxgc_02')) . ": -- <br/> " . $cn['desc'] . "<br/>"), $thisline);
            } else {
                $thisline = str_replace('{personal_cache_note}', "", $thisline);
            }
        } else {
            $thisline = str_replace('{personal_cache_note}', "", $thisline);
        }

        // attributes
        $rsAttributes = XDb::xSql(
            "SELECT `caches_attributes`.`attrib_id` FROM `caches_attributes` WHERE `caches_attributes`.`cache_id`= ? ",
            $r['cacheid']);

        $attribentries = '';
        while ($rAttrib = XDb::xFetchArray($rsAttributes)) {
            if (isset($gpxAttribID[$rAttrib['attrib_id']])) {
                $thisattribute = $gpxAttributes;

                $thisattribute = mb_ereg_replace('{attrib_id}', $gpxAttribID[$rAttrib['attrib_id']], $thisattribute);
                $thisattribute = mb_ereg_replace('{attrib_text_long}', $gpxAttribName[$rAttrib['attrib_id']], $thisattribute);
                if (isset($gpxAttribInc[$rAttrib['attrib_id']]))
                    $thisattribute = mb_ereg_replace('{attrib_id}', $gpxAttribInc[$rAttrib['attrib_id']], $thisattribute);
                else
                    $thisattribute = mb_ereg_replace('{attrib_inc}', 1, $thisattribute);

                $attribentries .= $thisattribute . "\n";
            }
        }
        XDb::xFreeResults($rsAttributes);
        $thisline = str_replace('{attributes}', $attribentries, $thisline);

        // start extra info
        $thisextra = "";

        $lang = XDb::xEscape($lang);
        $rsAttributes = XDb::xSql("SELECT `cache_attrib`.`id`, `caches_attributes`.`attrib_id`, `cache_attrib`.`text_long`
                            FROM `caches_attributes`, `cache_attrib`
                            WHERE `caches_attributes`.`cache_id`= ? AND `caches_attributes`.`attrib_id` = `cache_attrib`.`id`
                                AND `cache_attrib`.`language` = '$lang'
                            ORDER BY `caches_attributes`.`attrib_id`", $r['cacheid']);

            if (($r['votes'] > 3) || ($r['topratings'] > 0) || (XDb::xNumRows($rsAttributes) > 0)) {
            $thisextra .= "\n-- " . cleanup_text(tr('search_gpxgc_03')) . ": --\n";
            if (XDb::xNumRows($rsAttributes) > 0) {
                $attributes = '' . cleanup_text(tr('search_gpxgc_04')) . ': ';
                while ($rAttribute = XDb::xFetchArray($rsAttributes)) {
                    $attributes .= cleanup_text(xmlentities($rAttribute['text_long']));
                    $attributes .= " | ";
                }
                $thisextra .= $attributes;
            }

            if ($r['votes'] > 3) {

                $score = cleanup_text(GeoCacheCommons::ScoreNameTranslation($r['score']));
                $thisextra .= "\n" . cleanup_text(tr('search_gpxgc_05')) . ": " . $score . "\n";
            }
            if ($r['topratings'] > 0) {
                $thisextra .= "" . cleanup_text(tr('search_gpxgc_06')) . ": " . $r['topratings'] . "\n";
            }

            // NPA - nature protection areas

            // Parki Narodowe , Krajobrazowe
            $rsArea = XDb::xSql("SELECT `parkipl`.`id` AS `npaId`, `parkipl`.`name` AS `npaname`,`parkipl`.`link` AS `npalink`,`parkipl`.`logo` AS `npalogo`
                    FROM `cache_npa_areas`
                        INNER JOIN `parkipl` ON `cache_npa_areas`.`parki_id`=`parkipl`.`id`
                    WHERE `cache_npa_areas`.`cache_id`= ? AND `cache_npa_areas`.`parki_id`!='0'", $r['cacheid']);

            if (XDb::xNumRows($rsArea) != 0) {
                $thisextra .= "" .cleanup_text( tr('search_gpxgc_07')) . ": ";
                while ($npa = XDb::xFetchArray($rsArea)) {
                    $thisextra .= $npa['npaname'] . "  ";
                }
            }
            // Natura 2000
            $rsArea = XDb::xSql(
                "SELECT `npa_areas`.`id` AS `npaId`, `npa_areas`.`linkid` AS `linkid`,`npa_areas`.`sitename` AS `npaSitename`, `npa_areas`.`sitecode` AS `npaSitecode`, `npa_areas`.`sitetype` AS `npaSitetype`
                FROM `cache_npa_areas`
                INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id`
                WHERE `cache_npa_areas`.`cache_id`= ? AND `cache_npa_areas`.`npa_id`!='0'",
                $r['cacheid']);

            if (XDb::xNumRows($rsArea) != 0) {
                $thisextra .= "\nNATURA 2000: ";
                while ($npa = XDb::xFetchArray($rsArea)) {
                    $thisextra .= " - " . $npa['npaSitename'] . "  " . $npa['npaSitecode'] . " - ";
                }
            }
        }
        $thisline = str_replace('{extra_info}', $thisextra, $thisline);
        // end of extra info

        if ($r['rr_comment'] == '')
            $thisline = str_replace('{rr_comment}', '', $thisline);
        else
            $thisline = str_replace('{rr_comment}', cleanup_text("<br /><br />--------<br />" . $r['rr_comment'] . "<br />"), $thisline);

        $thisline = str_replace('{images}', getPictures($r['cacheid'], false, $r['picturescount']), $thisline);

        if (isset($gpxType[$r['type']]))
            $thisline = str_replace('{type}', $gpxType[$r['type']], $thisline);
        else
            $thisline = str_replace('{type}', $gpxType[1], $thisline);

        if (isset($gpxGeocacheTypeText[$r['type']]))
            $thisline = str_replace('{type_text}', $gpxGeocacheTypeText[$r['type']], $thisline);
        else
            $thisline = str_replace('{type_text}', $gpxGeocacheTypeText[1], $thisline);

        if (isset($gpxContainer[$r['size']]))
            $thisline = str_replace('{container}', $gpxContainer[$r['size']], $thisline);
        else
            $thisline = str_replace('{container}', $gpxContainer[0], $thisline);

        if (isset($gpxAvailable[$r['status']]))
            $thisline = str_replace('{available}', $gpxAvailable[$r['status']], $thisline);
        else
            $thisline = str_replace('{available}', $gpxAvailable[1], $thisline);

        if (isset($gpxArchived[$r['status']]))
            $thisline = str_replace('{archived}', $gpxArchived[$r['status']], $thisline);
        else
            $thisline = str_replace('{archived}', $gpxArchived[1], $thisline);

        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $difficulty = str_replace('.0', '', $difficulty); // garmin devices cannot handle .0 on integer values
        $thisline = str_replace('{difficulty}', $difficulty, $thisline);

        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $terrain = str_replace('.0', '', $terrain);
        $thisline = str_replace('{terrain}', $terrain, $thisline);

        $thisline = str_replace('{owner}', xmlentities(convert_string($r['username'])), $thisline);
        $thisline = str_replace('{owner_id}', xmlentities($r['owner_id']), $thisline);

        // create log list
        if ($options['gpxLogLimit']) {
            $gpxLogLimit = 'LIMIT ' . (intval($options['gpxLogLimit'])) . ' ';
        } else {
            $gpxLogLimit = '';
        }

        $logentries = '';
        $rsLogs = XDb::xSql(
            "SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `cache_logs`.`user_id` `userid`
            FROM `cache_logs`, `user`
            WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=`user`.`user_id`
                AND `cache_logs`.`cache_id`= ?
            ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`id` DESC " . XDb::xEscape($gpxLogLimit),
            $r['cacheid']);

        while ($rLog = XDb::xFetchArray($rsLogs)) {
            $thislog = $gpxLog;

            $thislog = str_replace('{id}', $rLog['id'], $thislog);
            $thislog = str_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
            if (isset($gpxLogType[$rLog['type']]))
                $logtype = $gpxLogType[$rLog['type']];
            else
                $logtype = $gpxLogType[0];
            if ($logtype == 'OC Team Comment') {
                $rLog['username'] = xmlentities(convert_string(tr('cog_user_name')));
                $rLog['userid'] = '0';
            }
            $thislog = str_replace('{username}', xmlentities(convert_string($rLog['username'])), $thislog);
            $thislog = str_replace('{finder_id}', xmlentities($rLog['userid']), $thislog);
            $thislog = str_replace('{type}', $logtype, $thislog);
            $thislog = str_replace('{text}', xmlencode_text($rLog['text']), $thislog);
            $logentries .= $thislog . "\n";
        }
        $thisline = str_replace('{logs}', $logentries, $thisline);

        // Travel Bug - GeoKrety
        $waypoint = $r['waypoint'];
        $geokrety = '';

        $geokret_query = XDb::xSql(
            "SELECT gk_item.id AS id, gk_item.name AS name
            FROM gk_item, gk_item_waypoint
            WHERE gk_item.id = gk_item_waypoint.id
                AND gk_item_waypoint.wp = ?
                AND gk_item.stateid<>1 AND gk_item.stateid<>4
                AND gk_item.stateid <>5 AND gk_item.typeid<>2",
            $waypoint);

        while ($geokret = XDb::xFetchArray($geokret_query)) {

            $thisGeoKret = $gpxGeoKrety;
            $gk_wp = strtoupper(dechex($geokret['id']));
            while (mb_strlen($gk_wp) < 4)
                $gk_wp = '0' . $gk_wp;
            $gkWP = 'GK' . mb_strtoupper($gk_wp);
            $thisGeoKret = str_replace('{geokret_id}', xmlentities($geokret['id']), $thisGeoKret);
            $thisGeoKret = str_replace('{geokret_ref}', $gkWP, $thisGeoKret);
            $thisGeoKret = str_replace('{geokret_name}', cleanup_text(xmlentities($geokret['name'])), $thisGeoKret);

            $geokrety .= $thisGeoKret; // . "\n";
        }
        $thisline = str_replace('{geokrety}', $geokrety, $thisline);

        // Waypoints
        $waypoints = '';

        $lang = XDb::xEscape($lang);
        $rswp = XDb::xSql(
            "SELECT  `longitude`, `cache_id`, `latitude`,`desc`,`stage`, `type`, `status`,`waypoint_type`." . $lang . " `wp_type_name`
            FROM `waypoints`
                INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id)
            WHERE  `waypoints`.`cache_id`=?
            ORDER BY `waypoints`.`stage`",
            $r['cacheid']);

        while ($rwp = XDb::xFetchArray($rswp)) {
            if ($rwp['status'] == 1) {
                $thiswp = $gpxWaypoints;
                $lat = sprintf('%01.5f', $rwp['latitude']);
                $thiswp = str_replace('{wp_lat}', $lat, $thiswp);
                $lon = sprintf('%01.5f', $rwp['longitude']);
                $thiswp = str_replace('{wp_lon}', $lon, $thiswp);
                $thiswp = str_replace('{waypoint}', $waypoint, $thiswp);
                $thiswp = str_replace('{cacheid}', $rwp['cache_id'], $thiswp);
                $thiswp = str_replace('{time}', $time, $thiswp);
                $thiswp = str_replace('{wp_type_name}', cleanup_text($rwp['wp_type_name']), $thiswp);
                if ($rwp['stage'] != 0) {
                    $thiswp = str_replace('{wp_stage}', " " . cleanup_text(tr('stage_wp')) . ": " . $rwp['stage'], $thiswp);
                } else {
                    $thiswp = str_replace('{wp_stage}', $rwp['wp_type_name'], $thiswp);
                }
                $thiswp = str_replace('{desc}', xmlentities(cleanup_text($rwp['desc'])), $thiswp);
                if (isset($wptType[$rwp['type']]))
                    $thiswp = str_replace('{wp_type}', $wptType[$rwp['type']], $thiswp);
                else
                    $thiswp = str_replace('{wp_type}', $wptType[0], $thiswp);
                $waypoints .= $thiswp;
            }
        }
        $thisline = str_replace('{cache_waypoints}', $waypoints, $thisline);

        echo $thisline;
        // DO NOT USE HERE:
        // ob_flush();
    }

    echo $gpxFoot;

    // compress using phpzip
    if ($bUseZip == true) {
        $content = ob_get_clean();
        $phpzip->add_data($sFilebasename . '.gpx', $content);
        $out = $phpzip->save($sFilebasename . '.zip', 'b');

        header("content-type: application/zip");
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
        echo $out;
        ob_end_flush();
    } else {
        header("Content-type: application/gpx");
        header("Content-Disposition: attachment; filename=" . $sFilebasename . ".gpx");
        ob_end_flush();
    }
}

exit();
