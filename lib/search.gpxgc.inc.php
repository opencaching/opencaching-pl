<?php

use src\Utils\Database\XDb;
use src\Utils\Database\OcDb;
use src\Models\GeoCache\CacheNote;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLog;
use src\Utils\I18n\I18n;
use src\Utils\Text\Validator;
use src\Models\OcConfig\OcConfig;
use src\Models\ApplicationContainer;
use src\Utils\Gis\Gis;

global $dbcSearch, $queryFilter;

require_once(__DIR__.'/format.gpx.inc.php');
require_once(__DIR__.'/calculation.inc.php');

set_time_limit(1800);

$user = ApplicationContainer::GetAuthorizedUser();

if (!$user && OcConfig::coordsHiddenForNonLogged()) {
  // user not logged + coords hidden for not logged
  exit;
}

// prepare the output
$caches_per_page = 20;


$query = 'SELECT ';

if (isset($lat_rad, $lon_rad, $multiplier[$distance_unit])) {

    $query .= getCalcDistanceSqlFormula(
        is_object($user), $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';

} else {
    if (!$user || !$homeCoords = $user->getHomeCoordinates()) {
        $query .= '0 distance, ';
    } else {
        // TODO: load from the users-profile
        $distance_unit = 'km';

        $lon_rad = $homeCoords->getLongitude() * Gis::PI / 180;
        $lat_rad = $homeCoords->getLatitude() * Gis::PI / 180;

        $query .= getCalcDistanceSqlFormula(
            is_object($user), $homeCoords->getLatitude(), $homeCoords->getLongitude(), 0, $multiplier[$distance_unit]) . ' `distance`, ';
    }
}

$query .= '`caches`.`cache_id` `cache_id`, `caches`.`wp_oc` `cache_wp`,
           `caches`.`status` `status`, `caches`.`type` `type`,
            IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`,
            IFNULL(`cache_mod_cords`.`latitude`, `caches`.`latitude`) `latitude`,
            IFNULL(cache_mod_cords.longitude,0) as cache_mod_cords_id,
            `caches`.`user_id` `user_id` ,`caches`.`votes` `votes`,
            `caches`.`score` `score`, `caches`.`topratings` `topratings`
        FROM `caches`
            LEFT JOIN `cache_mod_cords`
                ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id`
                AND `cache_mod_cords`.`user_id` = ' . $user->getUserId() . '
        WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

$sortby = $options['sort'];

if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance')) {
    $query .= ' ORDER BY distance ASC';
} else {
    if ($sortby == 'bycreated') {
        $query .= ' ORDER BY date_created DESC';
    } else { // sort by name
        $query .= ' ORDER BY name ASC';
    }
}

if (isset($_REQUEST['startat'])) {
    $startat = XDb::quoteOffset($_REQUEST['startat']);
} else {
    $startat = 0;
}

if (isset($_REQUEST['count'])) {
    $count = XDb::quoteLimit($_REQUEST['count']);
} else {
    $count = $caches_per_page;
}

$query .= ' LIMIT ' . $startat . ', ' . $count;

// cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
$dbcSearch->simpleQuery('DROP TEMPORARY TABLE IF EXISTS `gpxcontent`');

// create temporary table
$dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `gpxcontent` ' . $query);

// count the caches number
$countGPX = $dbcSearch->simpleQueryValue(
    'SELECT COUNT(*) AS rowCount FROM gpxcontent', 0);

if ($countGPX == 1) {
    $sFilebasename = $dbcSearch->simpleQueryValue(
        'SELECT caches.wp_oc FROM gpxcontent LEFT JOIN caches USING (cache_id)', 'wp_oc');
} else {
    switch ($options['searchtype']) {
        case 'bywatched':
            $sFilebasename = 'watched_caches';
            break;
        case 'bylist':
            $sFilebasename = 'cache_list';
            break;
        case 'bypt':
            $sFilebasename = $options['gpxPtFileName'];
            break;
        default:
            $queryName = $dbcSearch->multiVariableQueryValue(
                'SELECT name FROM queries WHERE id = :1 LIMIT 1', '', $options['queryid']);
            if(empty($queryName)) {
                $sFilebasename = 'search' . $options['queryid'];
            } else {
                $sFilebasename = str_replace(" ", "_", trim($queryName));
            }
    } //switch
}

$time = date($gpxTimeFormat, time());

$gpxHead = str_replace('{time}', $time, $gpxHead);

// check if any cache has waypoint


$hasWaypoints = $dbcSearch->simpleQueryValue(
    'SELECT cache_id FROM waypoints
     WHERE status = 1
        AND cache_id IN (SELECT cache_id FROM gpxcontent)
     LIMIT 1', false);

if($hasWaypoints) {
    $children = '(HasChildren)';
} else {
    $children = '';
}
$gpxHead = str_replace('{wpchildren}', $children, $gpxHead);



// start display
header("Content-type: application/gpx");
header("Content-Disposition: attachment; filename=" . $sFilebasename . ".gpx");

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
            `cache_desc`.`rr_comment`, `caches`.`logpw`, `caches`.`votes` `votes`, `caches`.`score` `score`,
            `caches`.`topratings` `topratings`, `caches`.`search_time`, `caches`.`way_length`,
            `caches`.`wp_gc`, `caches`.`wp_nc`, `caches`.`wp_ge`, `caches`.`wp_tc`
    FROM `gpxcontent`, `caches`, `user`, `cache_desc`
    WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id`
        AND `caches`.`cache_id`=`cache_desc`.`cache_id`
        AND `caches`.`default_desclang`=`cache_desc`.`language`
        AND `gpxcontent`.`user_id`=`user`.`user_id`');

while ( $r = XDb::xFetchArray($stmt) ) {

    if (OcConfig::isSiteCacheAccessLogEnabled()) {
        // add ACCESS_LOG record
        $dbc = OcDb::instance();
        $cache_id = $r['cacheid'];
        $user_id = ($user) ? $user->getUserId() : null;
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

    if ($r['hint'] == '') {
        $thisline = str_replace('{hints}', '', $thisline);
    } else {
        $thisline = str_replace('{hints}', cleanup_text($r['hint']), $thisline);
    }

    $logpw = ($r['logpw'] == "" ? "" : "" . cleanup_text(tr('search_gpxgc_01')) . " <br />");

    $thisline = str_replace('{shortdesc}', cleanup_text($r['short_desc']), $thisline);
    $thisline = str_replace('{desc}', xmlencode_text($logpw . $r['desc']), $thisline);

    if ($user) {
        $cacheNote = CacheNote::getNote($user->getUserId(), $r['cacheid']);

        if (!empty($cacheNote)) {
            $thisline = str_replace('{personal_cache_note}',
                cleanup_text("<br/><br/>-- " . cleanup_text(tr('search_gpxgc_02')) .
                    ": -- <br/> " . $cacheNote . "<br/>"), $thisline);
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
    if (isset($gpxNodemap[OcConfig::getSiteNodeId()]) && isset($gpxAI[$gpxNodemap[OcConfig::getSiteNodeId()]])) {
        $nodeCode = $gpxNodemap[OcConfig::getSiteNodeId()];
    } else {
        $nodeCode = '';
    }

    while ($rAttrib = XDb::xFetchArray($rsAttributes)) {
        $attrib_id = $rAttrib['attrib_id'];
        if (isset($gpxAttribID[$attrib_id])) {
            # common attribute definition
            $gpx_id = (int) $gpxAttribID[$attrib_id];
            $gpx_inc = (($gpxAttribID[$attrib_id] - 9000) > 0 ? '0' : '1');
            $gpx_name = $gpxAttribName[$attrib_id];
        } else {
            # definition is missing
            $gpx_id = 0;
        }

        if ($gpx_id > 0) {
            $thisattribute = $gpxAttribute;
            $thisattribute = mb_ereg_replace('{attrib_id}', $gpx_id, $thisattribute);
            $thisattribute = mb_ereg_replace('{attrib_inc}', $gpx_inc, $thisattribute);
            $thisattribute = mb_ereg_replace('{attrib_text_long}', $gpx_name, $thisattribute);
            $attribentries .= $thisattribute . "\n";
        }
    } // while-attributes

    XDb::xFreeResults($rsAttributes);
    $thisline = str_replace('{attributes}', $attribentries, $thisline);

    // start extra info
    $thisextra = "";

    $language = I18n::getCurrentLang();
    $rsAttributes = XDb::xSql(
        "SELECT `cache_attrib`.`id`, `caches_attributes`.`attrib_id`, `cache_attrib`.`text_long`
        FROM `caches_attributes`, `cache_attrib`
        WHERE `caches_attributes`.`cache_id`= ? AND `caches_attributes`.`attrib_id` = `cache_attrib`.`id`
            AND `cache_attrib`.`language` = '$language'
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
    }

    // NPA - nature protection areas - Parki Narodowe , Krajobrazowe
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


    $thisline = str_replace('{extra_info}', $thisextra, $thisline);
    // end of extra info


    if ($r['rr_comment'] == '') {
        $thisline = str_replace('{rr_comment}', '', $thisline);
    } else {
        $thisline = str_replace('{rr_comment}', cleanup_text("<br /><br />--------<br />" . $r['rr_comment'] . "<br />"), $thisline);
    }

    $thisline = str_replace('{images}', getPictures($r['cacheid'], false, $r['picturescount']), $thisline);

    if (isset($gpxType[$r['type']])) {
        $thisline = str_replace('{type}', $gpxType[$r['type']], $thisline);
    } else {
        $thisline = str_replace('{type}', $gpxType[1], $thisline);
    }

    if (isset($gpxGeocacheTypeText[$r['type']])) {
        $thisline = str_replace('{type_text}', $gpxGeocacheTypeText[$r['type']], $thisline);
    } else {
        $thisline = str_replace('{type_text}', $gpxGeocacheTypeText[1], $thisline);
    }

    if (isset($gpxContainer[$r['size']])) {
        $thisline = str_replace('{container}', $gpxContainer[$r['size']], $thisline);
    } else {
        $thisline = str_replace('{container}', $gpxContainer[1], $thisline);
    }

    if (isset($gpxAvailable[$r['status']])) {
        $thisline = str_replace('{available}', $gpxAvailable[$r['status']], $thisline);
    } else {
        $thisline = str_replace('{available}', $gpxAvailable[1], $thisline);
    }

    if (isset($gpxArchived[$r['status']])) {
        $thisline = str_replace('{archived}', $gpxArchived[$r['status']], $thisline);
    } else {
        $thisline = str_replace('{archived}', $gpxArchived[1], $thisline);
    }

    $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
    $difficulty = str_replace('.0', '', $difficulty); // garmin devices cannot handle .0 on integer values
    $thisline = str_replace('{difficulty}', $difficulty, $thisline);

    $terrain = sprintf('%01.1f', $r['terrain'] / 2);
    $terrain = str_replace('.0', '', $terrain);
    $thisline = str_replace('{terrain}', $terrain, $thisline);

    $thisline = str_replace('{owner}', xmlentities(convert_string($r['username'])), $thisline);
    $thisline = str_replace('{owner_id}', xmlentities($r['owner_id']), $thisline);

    // OC GPX extension
    if (isset($gpxOcType[$r['type']])) {
        $thisline = str_replace('{oc_type}', $gpxOcType[$r['type']], $thisline);
    } else {
        $thisline = str_replace('{oc_type}', $gpxOcType[1], $thisline);
    }

    if (isset($gpxOcSize[$r['size']])) {
        $thisline = str_replace('{oc_size}', $gpxOcSize[$r['size']], $thisline);
    } else {
        $thisline = str_replace('{oc_size}', $gpxOcSize[1], $thisline);
    }

    if ($r['search_time'] > 0) {
        $trip_time = mb_ereg_replace('{time}', $r['search_time'], $gpxOcTripTime);
    } else {
        $trip_time = '';
    }

    $thisline = mb_ereg_replace('{oc_trip_time}', $trip_time, $thisline);

    if ($r['way_length'] > 0) {
        $trip_distance = mb_ereg_replace('{distance}', $r['way_length'], $gpxOcTripDistance);
    } else {
        $trip_distance = '';
    }

    $thisline = mb_ereg_replace('{oc_trip_distance}', $trip_distance, $thisline);

    $thisline = mb_ereg_replace('{oc_password}', $r['logpw'] != '' ? 'true' : 'false', $thisline);

    $other_codes = [];
    foreach (['gc', 'tc', 'nc', 'ge'] as $platform) {
        $code = Validator::xxWaypoint($platform, $r['wp_'.$platform]);
        if ($code) {
            $other_codes[] = mb_ereg_replace('{code}', $code, $gpxOcOtherCode);
        }
    }
    $thisline = mb_ereg_replace('{oc_other_codes}', implode("\n", $other_codes), $thisline);

    // create log list
    if ($options['gpxLogLimit']) {
        $gpxLogLimit = 'LIMIT ' . (intval($options['gpxLogLimit'])) . ' ';
    } else {
        $gpxLogLimit = '';
    }

    $gs_logentries = '';
    $oc_logentries = '';
    $rsLogs = XDb::xSql(
        "SELECT `cache_logs`.`id`, `cache_logs`.`uuid`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `cache_logs`.`user_id` `userid`
        FROM `cache_logs`, `user`
        WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=`user`.`user_id`
            AND `cache_logs`.`cache_id`= ?
        ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`id` DESC " . XDb::xEscape($gpxLogLimit),
        $r['cacheid']);

    while ($rLog = XDb::xFetchArray($rsLogs)) {
        // groundspeak:log
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
        $gs_logentries .= $thislog . "\n";

        // oc:log
        $thislog = $gpxOcLog;
        $thislog = str_replace('{id}', $rLog['id'], $thislog);
        $thislog = str_replace('{uuid}', $rLog['uuid'], $thislog);
        if ($rLog['type'] == GeoCacheLog::LOGTYPE_ADMINNOTE) {
            $thislog = str_Replace('{oc_team_entry}', $gpxOcIsTeamEntry, $thislog);
        } else {
            $thislog = str_Replace('{oc_team_entry}', '', $thislog);
        }
        $oc_logentries .= $thislog . "\n";
    }
    $thisline = str_replace('{gs_logs}', $gs_logentries, $thisline);
    $thisline = str_replace('{oc_logs}', $oc_logentries, $thisline);

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

    $rswp = XDb::xSql(
        "SELECT  `longitude`, `cache_id`, `latitude`,`desc`,`stage`, `type`, `status`,`waypoint_type`." . $language . " `wp_type_name`
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

    ob_flush(); // flush this part to the browser

} // while-each-cache-from-search

echo $gpxFoot;






function getPictures($cacheid, $picturescount)
{
    $rs = XDb::xSql(
        'SELECT uuid, title, url, spoiler
        FROM pictures
        WHERE object_id= ? AND object_type=2 AND display=1
        ORDER BY date_created',
        $cacheid);

    $retval = '';
    while ($r = XDb::xFetchArray($rs)) {
        $retval .= '&lt;img src="' . $r['url'] . '"&gt;&lt;br&gt;' . cleanup_text($r['title']) . '&lt;br&gt;';
    }

    return $retval;
}
