<?php
/**
 * This script is used (can be loaded) by /search.php
 */

use Utils\Database\XDb;
use Utils\Database\OcDb;

global $content, $bUseZip, $usr, $hide_coords, $dbcSearch, $queryFilter;
require_once ('lib/common.inc.php');
set_time_limit(1800);

function getPictures($cacheid, $picturescount)
{
    global $thumb_max_width;
    global $thumb_max_height;

    $rs = XDb::xSql('SELECT uuid, title, url, spoiler FROM pictures
            WHERE object_id= ? AND object_type=2 AND display=1
            ORDER BY date_created', $cacheid);

    if (! isset($retval))
        $retval = '';
    while ($r = XDb::xFetchArray($rs)) {
        $retval .= '&lt;img src="' . $r['url'] . '"&gt;&lt;br&gt;' . cleanup_text($r['title']) . '&lt;br&gt;';
    }

    XDb::xFreeResults($rs);
    return $retval;
}

// sitename and slogan international handling
$nodeDetect = substr($absolute_server_URI, - 3, 2);

$gpxHead = '<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://geocaching.com.au/geocache/1 http://geocaching.com.au/geocache/1/geocache.xsd http://www.gsak.net/xmlv1/5 http://www.gsak.net/xmlv1/5/gsak.xsd"
     xmlns="http://www.topografix.com/GPX/1/0" version="1.0" creator="' . $site_name . '">
  <desc>Cache Listing Generated from ' . $site_name . ' {wpchildren}</desc>
  <author>' . $site_name . '</author>
  <url>' . $absolute_server_URI . '</url>
  <urlname>' . $site_name . ' - ' . tr('oc_subtitle_on_all_pages_' . $nodeDetect) . '</urlname>
  <time>{time}</time>
';

$gpxLine = '   <wpt lat="{lat}" lon="{lon}">
    <time>{time}</time>
    <name>{waypoint}</name>
    <desc>{mod_suffix}{cachename} ' . tr('from') . ' {owner}, {type_text} ({difficulty}/{terrain})</desc>
    <src>' . $absolute_server_URI . '</src>
    <url>' . $absolute_server_URI . 'viewcache.php?wp={waypoint}</url>
    <urlname>{mod_suffix}{cachename}</urlname>
    <sym>Geocache</sym>
    <type>Geocache|{geocache_type}</type>
    <geocache status="{status}" xmlns="http://geocaching.com.au/geocache/1">
            <name>{mod_suffix}{cachename}</name>
            <owner>{owner}</owner>
            <locale></locale>
            <state>{region}</state>
            <country>{country}</country>
            <type>{type}</type>
            <container>{container}</container>
            <attributes>{attributes}</attributes>
            <difficulty>{difficulty}</difficulty>
            <terrain>{terrain}</terrain>
            <summary html="false">{shortdesc}</summary>
            <description html="true">{desc}{rr_comment}&lt;br&gt;{{images}}&lt;br&gt;{personal_cache_note}&lt;br&gt;{extra_info}</description>
            {hints}
            <licence></licence>
            <logs>
                {logs}
            </logs>
            <geokrety>
            {geokrety}
            </geokrety>
        </geocache>
    </wpt>
    {cache_waypoints}
';

$gpxAttributes = '<attribute id="{attrib_id}" inc="1">{attrib_text_long}</attribute>';

$gpxGeoKrety = '<geokret id="{geokret_id}" ref="{geokret_ref}">
            <gkname>{geokret_name}</gkname>
            </geokret> ';

$gpxLog = '
<log id="{id}">
    <time>{date}</time>
    <geocacher>{username}</geocacher>
    <type>{type}</type>
    <text>{text}</text>
</log>
';
$gpxWaypoints = '<wpt lat="{wp_lat}" lon="{wp_lon}">
    <time>{time}</time>
    <name>{waypoint} {wp_stage}</name>
    <cmt>{desc}</cmt>
    <desc>{wp_type_name}</desc>
    <url>' . $absolute_server_URI . 'viewcache.php?wp={waypoint}</url>
    <urlname>{waypoint} {wp_stage}</urlname>
    <sym>{wp_type}</sym>
    <type>Waypoint|{wp_type}</type>
    <gsak:wptExtension xmlns:gsak="http://www.gsak.net/xmlv1/5">
    <gsak:Parent>{waypoint}</gsak:Parent>
    <gsak:Code>{waypoint} {wp_stage}</gsak:Code>
    <gsak:Child_Flag>false</gsak:Child_Flag>
    <gsak:Child_ByGSAK>false</gsak:Child_ByGSAK>
    </gsak:wptExtension>
  </wpt>
';

$gpxFoot = '</gpx>';

$gpxTimeFormat = 'Y-m-d\TH:i:s\Z';

$gpxStatus[0] = 'Unavailable'; // andere
$gpxStatus[1] = 'Available';
$gpxStatus[2] = 'Unavailable';
$gpxStatus[3] = 'Archived';

$gpxContainer[0] = 'Other';
$gpxContainer[2] = 'Micro';
$gpxContainer[3] = 'Small';
$gpxContainer[4] = 'Regular';
$gpxContainer[5] = 'Large';
$gpxContainer[6] = 'Large';
$gpxContainer[7] = 'Virtual';

// known by gpx
$gpxType[1] = 'Other';
$gpxType[2] = 'Traditional';
$gpxType[3] = 'Multi';
$gpxType[4] = 'Virtual';
$gpxType[5] = 'Webcam';
$gpxType[6] = 'Event';
// unknown ... converted
$gpxType[7] = 'Multi';
$gpxType[8] = 'Multi';
$gpxType[9] = 'Traditional';
$gpxType[10] = 'Traditional';

// nazwy skrzynek zgodne z Geocaching / Garmin
$gpxGeocacheType[1] = 'Unknown Cache';
$gpxGeocacheType[2] = 'Traditional Cache';
$gpxGeocacheType[3] = 'Multi-Cache';
$gpxGeocacheType[4] = 'Virtual Cache';
$gpxGeocacheType[5] = 'Webcam Cache';
$gpxGeocacheType[6] = 'Event Cache';
$gpxGeocacheType[7] = 'Multi-cache';
$gpxGeocacheType[8] = 'Multi-cache';
$gpxGeocacheType[9] = 'Unknown Cache';

// nazwy skrzynek do description
$gpxGeocacheTypeText[1] = 'Unknown Cache';
$gpxGeocacheTypeText[2] = 'Traditional Cache';
$gpxGeocacheTypeText[3] = 'Multi-Cache';
$gpxGeocacheTypeText[4] = 'Virtual Cache';
$gpxGeocacheTypeText[5] = 'Webcam Cache';
$gpxGeocacheTypeText[6] = 'Event Cache';
$gpxGeocacheTypeText[7] = 'Quiz';
$gpxGeocacheTypeText[8] = 'Moving Cache';
$gpxGeocacheTypeText[9] = 'Podcast cache';

$gpxLogType[0] = 'Write note'; // OC: Other
$gpxLogType[1] = 'Found it'; // OC: Found
$gpxLogType[2] = 'Didn\'t find it'; // OC: Not Found
$gpxLogType[3] = 'Write note'; // OC: Note
$gpxLogType[4] = 'Write note'; // OC: Note
$gpxLogType[5] = 'Needs Maintenance'; // OC: Note
$gpxLogType[6] = 'Needs Archived'; // OC: Other
$gpxLogType[7] = 'Attended'; // OC: Found
$gpxLogType[8] = 'Will Attend'; // OC: Not Found
$gpxLogType[9] = 'Archive'; // OC: Note
$gpxLogType[10] = 'Enable Listing'; // OC: Note
$gpxLogType[11] = 'Temporarily Disable Listing'; // OC: Note
$gpxLogType[12] = 'Post Reviewer Note'; // OC: Note

if ($usr || ! $hide_coords) {
    // 1st set of attributes - attributes that correlate to GC attributes
    $gpxAttribID[6] = '106';
    $gpxAttribName[6] = 'Only logable at Opencaching';
    $gpxAttribID[12] = '22';
    $gpxAttribName[12] = 'Hunting';
    $gpxAttribID[13] = '39';
    $gpxAttribName[13] = 'Thorns';
    $gpxAttribID[14] = '19';
    $gpxAttribName[14] = 'Ticks';
    $gpxAttribID[18] = '25';
    $gpxAttribName[18] = 'Parking available';
    $gpxAttribID[25] = '57';
    $gpxAttribName[25] = 'Significant Hike';
    $gpxAttribID[30] = '8';
    $gpxAttribName[30] = 'Point of interest';
    $gpxAttribID[38] = '13';
    $gpxAttribName[38] = 'Available at all times';
    $gpxAttribID[40] = '53';
    $gpxAttribName[40] = 'Park and Grab';
    $gpxAttribID[41] = '6';
    $gpxAttribName[41] = 'Recommended for kids';
    $gpxAttribID[44] = '24';
    $gpxAttribName[44] = 'Wheelchair accessible';
    $gpxAttribID[45] = '40';
    $gpxAttribName[45] = 'Stealth required';
    $gpxAttribID[52] = '60';
    $gpxAttribName[52] = 'Wireless Beacon';
    $gpxAttribID[61] = '54';
    $gpxAttribName[61] = 'Abandoned Structure';
    $gpxAttribID[82] = '44';
    $gpxAttribName[82] = 'Flashlight required';
    $gpxAttribID[83] = '51';
    $gpxAttribName[83] = 'Special Tool Required';
    $gpxAttribID[85] = '32';
    $gpxAttribName[85] = 'Bicycles';
    $gpxAttribID[86] = '4';
    $gpxAttribName[86] = 'Boat';
    $gpxAttribID[90] = '23';
    $gpxAttribName[90] = 'Dangerous area';
    $gpxAttribID[91] = '14';
    $gpxAttribName[91] = 'Recommended at night';

    // 2nd set of attributes - OC only attributes, changed ID (+100) to be save in oc-gc-mixed environments
    // $gpxAttribID[6] = '106';
    // $gpxAttribName[6] = 'Only loggable at Opencaching';
    // $gpxAttribID[43] = '143';
    // $gpxAttribName[43] = 'GeoHotel';
    // $gpxAttribID[47] = '147';
    // $gpxAttribName[47] = 'Compass';

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
            $rs_coords = XDb::xSql("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? LIMIT 1", $usr['userid']);
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

    $query .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, `caches`.`votes` `votes`, `caches`.`score` `score`, `caches`.`topratings` `topratings`, ';
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
    // temporäre tabelle erstellen
    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `gpxcontent` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery('SELECT COUNT(*) `count` FROM `gpxcontent`');
    $rCount = $dbcSearch->dbResultFetch($s);
    $countGPX = $rCount['count'];

    if ($countGPX == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc`, `caches`.`name` `name` FROM `gpxcontent`, `caches`
            WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetchOneRowOnly($s);

        if (isset($_GET['realname']) && $_GET['realname'] == 1)
            $sFilebasename = str_replace(" ", "", PLConvert('UTF-8', 'POLSKAWY', $rName['name']));
        else
            $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } elseif ($options['searchtype'] == 'bypt') {
            $sFilebasename = $options['gpxPtFileName'];
        } else {
            $rsName = XDb::xSql('SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= ? LIMIT 1', $options['queryid']);
            $rName = XDb::xFetchArray($rsName);
            XDb::xFreeResults($rsName);
            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
                $sFilebasename = str_replace(" ", "_", $sFilebasename);
            } else {
                $sFilebasename = '' . $short_sitename . '' . $options['queryid'];
            }
        }
    }

    $bUseZip = ($rCount['count'] > 50);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    $bUseZip = false; // workaround for timeouts with big files
    if ($bUseZip == true) {
        $content = '';
        require_once ($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('', 6);
    }

    if ($bUseZip == true) {
        header("content-type: application/zip");
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
    } else {
        header("Content-type: application/gpx");
        header("Content-Disposition: attachment; filename=" . $sFilebasename . ".gpx");
    }

    $children = '';
    $gpxHead = str_replace('{time}', date($gpxTimeFormat, time()), $gpxHead);
    $stmt = $dbcSearch->simpleQuery('SELECT `gpxcontent`.`cache_id` `cacheid` FROM `gpxcontent`');

    while ($rs = $dbcSearch->dbResultFetch($stmt)) {
        $rwp = XDb::xSql(
            "SELECT  `status` FROM `waypoints`
            WHERE  `waypoints`.`cache_id`= ?
                AND `waypoints`.`status`='1'", $rs['cacheid']);
        if ( XDb::xFetchArray($rwp) ) {
            $children = "(HasChildren)";
        }
    }

    $gpxHead = str_replace('{wpchildren}', $children, $gpxHead);
    append_output($gpxHead);


    $stmt = XDb::xSql('SELECT `gpxcontent`.`cache_id` `cacheid`, `gpxcontent`.`longitude` `longitude`, `gpxcontent`.`latitude` `latitude`, `gpxcontent`.cache_mod_cords_id, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`picturescount` `picturescount`, `caches`.`name` `name`, `caches`.`country` `country`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, `caches`.`size` `size`, `caches`.`type` `type`, `caches`.`status` `status`, `user`.`username` `username`, `gpxcontent`.`user_id` `owner_id`,`cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`rr_comment`, `caches`.`logpw`, `caches`.`votes` `votes`, `caches`.`score` `score`, `caches`.`topratings` `topratings` FROM `gpxcontent`, `caches`, `user`, `cache_desc` WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id` AND `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`default_desclang`=`cache_desc`.`language` AND `gpxcontent`.`user_id`=`user`.`user_id`');
    while ($r = XDb::xFetchArray($stmt)) {
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
                            (NOW(), :1, :2, \'B\', \'download_gpx\', :3, :4, :5)',
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
            $thisline = str_replace('{hints}', '<hints>' . cleanup_text($r['hint']) . '</hints>', $thisline);

        $logpw = ($r['logpw'] == "" ? "" : "" . tr('search_gpxgc_01') . " <br />");
        $thisline = str_replace('{shortdesc}', cleanup_text($r['short_desc']), $thisline);
        $thisline = str_replace('{desc}', cleanup_text($logpw . $r['desc']), $thisline);

        // add personal cache info if user login to OC
        if ($usr == true) {
            $notes_rs = XDb::xSql("SELECT  `cache_notes`.`desc` `desc` FROM `cache_notes`
                    WHERE `cache_notes` .`user_id`= ? AND `cache_notes`.`cache_id`= ? ", $usr['userid'], $r['cacheid']);

            if ($cn = XDb::xFetchArray($notes_rs)) {
                $thisline = str_replace('{personal_cache_note}', cleanup_text("<br/><br/>-- " . tr('search_gpxgc_02') . ": --<br/> " . $cn['desc'] . "<br/>"), $thisline);
            } else {
                $thisline = str_replace('{personal_cache_note}', "", $thisline);
            }
        } else {
            $thisline = str_replace('{personal_cache_note}', "", $thisline);
        }

        // attributes
        $rsAttributes = XDb::xSql("SELECT `caches_attributes`.`attrib_id` FROM `caches_attributes`
            WHERE `caches_attributes`.`cache_id`= ? ", $r['cacheid']);
        $attribentries = '';
        while ($rAttrib = XDb::xFetchArray($rsAttributes)) {
            if (isset($gpxAttribID[$rAttrib['attrib_id']])) {
                $thisattribute = $gpxAttributes;
                $thisattribute_id = $gpxAttribID[$rAttrib['attrib_id']];
                $thisattribute_name = $gpxAttribName[$rAttrib['attrib_id']];

                $thisattribute = mb_ereg_replace('{attrib_id}', $thisattribute_id, $thisattribute);
                $thisattribute = mb_ereg_replace('{attrib_text_long}', $thisattribute_name, $thisattribute);

                $attribentries .= $thisattribute . "\n";
            }
        }
        XDb::xFreeResults($rsAttributes);
        $thisline = str_replace('{attributes}', $attribentries, $thisline);

        // start extra info
        $thisextra = "";
        $rsAttributes = XDb::xSql("SELECT `caches_attributes`.`attrib_id`, `cache_attrib`.`text_long`
                FROM `caches_attributes`, `cache_attrib`
                WHERE `caches_attributes`.`cache_id`= ?
                    AND `caches_attributes`.`attrib_id` = `cache_attrib`.`id`
                    AND `cache_attrib`.`language` = '$lang'
                ORDER BY `caches_attributes`.`attrib_id`", $r['cacheid']);

        if (($r['votes'] > 3) || ($r['topratings'] > 0) || (XDb::xNumRows($rsAttributes) > 0)) {
            $thisextra .= "\n-- " . tr('search_gpxgc_03') . ": --\n";
            if (XDb::xNumRows($rsAttributes) > 0) {
                $attributes = '' . tr('search_gpxgc_04') . ': ';
                while ($rAttribute = XDb::xFetchArray($rsAttributes)) {
                    $attributes .= cleanup_text(xmlentities($rAttribute['text_long']));
                    $attributes .= " | ";
                }
                $thisextra .= $attributes;
            }

            if ($r['votes'] > 3) {

                $score = cleanup_text(score2rating($r['score']));
                $thisextra .= "\n" . tr('search_gpxgc_05') . ": " . $score . "\n";
            }
            if ($r['topratings'] > 0) {
                $thisextra .= "" . tr('search_gpxgc_06') . ": " . $r['topratings'] . "\n";
            }

            // NPA - nature protection areas

            // Parki Narodowe , Krajobrazowe
            $rsArea = XDb::xSql("SELECT `parkipl`.`id` AS `npaId`, `parkipl`.`name` AS `npaname`,`parkipl`.`link` AS `npalink`,`parkipl`.`logo` AS `npalogo`
            FROM `cache_npa_areas`
                INNER JOIN `parkipl` ON `cache_npa_areas`.`parki_id`=`parkipl`.`id`
            WHERE `cache_npa_areas`.`cache_id`= ?
                AND `cache_npa_areas`.`parki_id`!='0'", $r['cacheid']);

            if (XDb::xNumRows($rsArea) != 0) {
                $thisextra .= "" . tr('search_gpxgc_07') . ": ";
                while ($npa = XDb::xFetchArray($rsArea)) {
                    $thisextra .= $npa['npaname'] . "  ";
                }
            }
            // Natura 2000
            $rsArea = XDb::xSql("SELECT `npa_areas`.`id` AS `npaId`, `npa_areas`.`linkid` AS `linkid`,`npa_areas`.`sitename` AS `npaSitename`, `npa_areas`.`sitecode` AS `npaSitecode`, `npa_areas`.`sitetype` AS `npaSitetype`
            FROM `cache_npa_areas`
                INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id`
            WHERE `cache_npa_areas`.`cache_id`= ? AND `cache_npa_areas`.`npa_id`!='0'", $r['cacheid']);

            if (XDb::xNumRows($rsArea) != 0){
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

        $thisline = str_replace('{{images}}', getPictures($r['cacheid'], false, $r['picturescount']), $thisline);

        if (isset($gpxType[$r['type']]))
            $thisline = str_replace('{type}', $gpxType[$r['type']], $thisline);
        else
            $thisline = str_replace('{type}', $gpxType[1], $thisline);

        if (isset($gpxGeocacheType[$r['type']]))
            $thisline = str_replace('{geocache_type}', $gpxGeocacheType[$r['type']], $thisline);
        else
            $thisline = str_replace('{geocache_type}', $gpxGeocacheType[1], $thisline);

        if (isset($gpxGeocacheTypeText[$r['type']]))
            $thisline = str_replace('{type_text}', $gpxGeocacheTypeText[$r['type']], $thisline);
        else
            $thisline = str_replace('{type_text}', $gpxGeocacheTypeText[1], $thisline);

        if (isset($gpxContainer[$r['size']]))
            $thisline = str_replace('{container}', $gpxContainer[$r['size']], $thisline);
        else
            $thisline = str_replace('{container}', $gpxContainer[0], $thisline);

        if (isset($gpxStatus[$r['status']]))
            $thisline = str_replace('{status}', $gpxStatus[$r['status']], $thisline);
        else
            $thisline = str_replace('{status}', $gpxStatus[0], $thisline);

        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $difficulty = str_replace('.0', '', $difficulty); // garmin devices cannot handle .0 on integer values
        $thisline = str_replace('{difficulty}', $difficulty, $thisline);

        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $terrain = str_replace('.0', '', $terrain);
        $thisline = str_replace('{terrain}', $terrain, $thisline);

        $thisline = str_replace('{owner}', xmlentities($r['username']), $thisline);
        $thisline = str_replace('{owner_id}', xmlentities($r['owner_id']), $thisline);

        $lang = XDb::xEscape($lang);
        $rsAttributes = XDb::xSql("SELECT `caches_attributes`.`attrib_id`, `cache_attrib`.`text_long`
                FROM `caches_attributes`, `cache_attrib`
                WHERE `caches_attributes`.`cache_id`= ? AND `caches_attributes`.`attrib_id` = `cache_attrib`.`id`
                    AND `cache_attrib`.`language` = '$lang'
                ORDER BY `caches_attributes`.`attrib_id`", $r['cacheid']);

        // create log list
        if ($options['gpxLogLimit']) {
            $gpxLogLimit = 'LIMIT ' . $options['gpxLogLimit'] . ' ';
        } else {
            $gpxLogLimit = '';
        }

        $logentries = '';
        $rsLogs = XDb::xSql("SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`,
                        `user`.`username`, `cache_logs`.`user_id` `userid`
                FROM `cache_logs`, `user`
                WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=`user`.`user_id`
                    AND `cache_logs`.`cache_id`= ?
                ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`id` DESC " . XDb::xEscape($gpxLogLimit), $r['cacheid']);

        while ($rLog = XDb::xFetchArray($rsLogs)) {
            $thislog = $gpxLog;

            $thislog = str_replace('{id}', $rLog['id'], $thislog);
            $thislog = str_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
            $thislog = str_replace('{username}', xmlentities($rLog['username']), $thislog);
            $thislog = str_replace('{finder_id}', xmlentities($rLog['userid']), $thislog);
            if (isset($gpxLogType[$rLog['type']]))
                $logtype = $gpxLogType[$rLog['type']];
            else
                $logtype = $gpxLogType[0];

            $thislog = str_replace('{type}', $logtype, $thislog);
            $thislog = str_replace('{text}', cleanup_text($rLog['text']), $thislog);
            $logentries .= $thislog . "\n";
        }
        $thisline = str_replace('{logs}', $logentries, $thisline);

        // Travel Bug GeoKrety
        $waypoint = $r['waypoint'];
        $geokrety = '';

        $geokret_query = XDb::xSql("SELECT gk_item.id AS id, gk_item.name AS name
                FROM gk_item, gk_item_waypoint
                WHERE gk_item.id = gk_item_waypoint.id
                    AND gk_item_waypoint.wp = '" . XDb::xEscape($waypoint) . "'
                    AND gk_item.stateid<>1 AND gk_item.stateid<>4
                    AND gk_item.stateid <>5 AND gk_item.typeid<>2");

        while ($geokret = XDb::xFetchArray($geokret_query)) {

            $thisGeoKret = $gpxGeoKrety;

            $gk_wp = strtoupper(dechex($geokret['id']));
            while (mb_strlen($gk_wp) < 4)
                $gk_wp = '0' . $gk_wp;
            $gkWP = 'GK' . mb_strtoupper($gk_wp);
            $thisGeoKret = str_replace('{geokret_id}', xmlentities($geokret['id']), $thisGeoKret);
            $thisGeoKret = str_replace('{geokret_ref}', $gkWP, $thisGeoKret);
            $thisGeoKret = str_replace('{geokret_name}', xmlentities($geokret['name']), $thisGeoKret);
            $geokrety .= $thisGeoKret; // . "\n";
        }
        $thisline = str_replace('{geokrety}', $geokrety, $thisline);

        // Waypoints
        $waypoints = '';
        $rswp = XDb::xSql("SELECT  `longitude`, `cache_id`, `latitude`,`desc`,`stage`, `type`, `status`,`waypoint_type`.`pl` `wp_type_name`
                FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id)
                WHERE  `waypoints`.`cache_id`= ?
                ORDER BY `waypoints`.`stage`", $r['cacheid']);

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
                $thiswp = str_replace('{wp_type_name}', $rwp['wp_type_name'], $thiswp);
                if ($rwp['stage'] != 0) {
                    $thiswp = str_replace('{wp_stage}', " Etap" . $rwp['stage'], $thiswp);
                } else {
                    $thiswp = str_replace('{wp_stage}', $rwp['wp_type_name'], $thiswp);
                }
                $thiswp = str_replace('{desc}', cleanup_text($rwp['desc']), $thiswp);
                if ($rwp['type'] == 5) {
                    $thiswp = str_replace('{wp_type}', "Parking Area", $thiswp);
                }
                if ($rwp['type'] == 1) {
                    $thiswp = str_replace('{wp_type}', "Flag, Green", $thiswp);
                }
                if ($rwp['type'] == 2) {
                    $thiswp = str_replace('{wp_type}', "Flag, Green", $thiswp);
                }
                if ($rwp['type'] == 3) {
                    $thiswp = str_replace('{wp_type}', "Flag, Red", $thiswp);
                }
                if ($rwp['type'] == 4) {
                    $thiswp = str_replace('{wp_type}', "Circle with X", $thiswp);
                }
                $waypoints .= $thiswp;
            }
        }
        $thisline = str_replace('{cache_waypoints}', $waypoints, $thisline);

        append_output($thisline);
        ob_flush();
    }

    append_output($gpxFoot);

    // phpzip versenden
    if ($bUseZip == true) {
        $phpzip->add_data($sFilebasename . '.gpx', $content);
        echo $phpzip->save($sFilebasename . '.zip', 'b');
    }
}

exit();

function xmlentities($str)
{
    $from[0] = '&';
    $to[0] = '&amp;';
    $from[1] = '<';
    $to[1] = '&lt;';
    $from[2] = '>';
    $to[2] = '&gt;';
    $from[3] = '"';
    $to[3] = '&quot;';
    $from[4] = '\'';
    $to[4] = '&apos;';
    $from[5] = ']]>';
    $to[5] = ']] >';

    for ($i = 0; $i <= 4; $i ++)
        $str = str_replace($from[$i], $to[$i], $str);
    $str = preg_replace('/[[:cntrl:]]/', '', $str);
    return $str;
}

function cleanup_text($str)
{
    // $str= tidy_html_description($str);
    $str = PLConvert('UTF-8', 'POLSKAWY', $str);

    $str = strip_tags($str, "<p><br /><li>");
    // <p> -> nic
    // </p>, <br /> -> nowa linia
    $from[] = '<p>';
    $to[] = '';
    $from[] = '</p>';
    $to[] = "\n";
    $from[] = '<br />';
    $to[] = "\n";
    $from[] = '<br>';
    $to[] = "\n";
    $from[] = '<br>';
    $to[] = "\n";

    $from[] = '<li>';
    $to[] = " - ";
    $from[] = '</li>';
    $to[] = "\n";

    $from[] = '&oacute;';
    $to[] = 'o';
    $from[] = '&quot;';
    $to[] = '"';
    $from[] = '&[^;]*;';
    $to[] = '';

    $from[] = '&';
    $to[] = '&amp;';
    $from[] = '<';
    $to[] = '&lt;';
    $from[] = '>';
    $to[] = '&gt;';
    $from[] = ']]>';
    $to[] = ']] >';
    $from[] = '';
    $to[] = '';

    for ($i = 0; $i < count($from); $i ++)
        $str = str_replace($from[$i], $to[$i], $str);
    $str = preg_replace('/[[:cntrl:]]/', '', $str);
    return $str;
}

function append_output($str)
{
    global $content, $bUseZip;

    if ($bUseZip == true)
        $content .= $str;
    else
        echo $str;
}


/*
 Funkcja do konwersji polskich znakow miedzy roznymi systemami kodowania.
 Zwraca skonwertowany tekst.

 Argumenty:
 $source - string - źródłowe kodowanie
 $dest - string - źródłowe kodowanie
 $tekst - string - tekst do konwersji

 Obsługiwane formaty kodowania to:
 POLSKAWY (powoduje zamianę polskich liter na ich łacińskie odpowiedniki)
 ISO-8859-2
 WINDOWS-1250
 UTF-8
 ENTITIES (zamiana polskich znaków na encje html)

 Przyklad:
 echo(PlConvert('UTF-8','ISO-8859-2','Zażółć gęślą jaźń.'));
 */
function PLConvert($source,$dest,$tekst)
{
    $source=strtoupper($source);
    $dest=strtoupper($dest);
    if($source==$dest) return $tekst;

    $chars['POLSKAWY']    =array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z');
    $chars['ISO-8859-2']  =array("\xB1","\xE6","\xEA","\xB3","\xF1","\xF3","\xB6","\xBC","\xBF","\xA1","\xC6","\xCA","\xA3","\xD1","\xD3","\xA6","\xAC","\xAF");
    $chars['WINDOWS-1250']=array("\xB9","\xE6","\xEA","\xB3","\xF1","\xF3","\x9C","\x9F","\xBF","\xA5","\xC6","\xCA","\xA3","\xD1","\xD3","\x8C","\x8F","\xAF");
    $chars['UTF-8']       =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');
    $chars['ENTITIES']    =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');

    if(!isset($chars[$source])) return false;
    if(!isset($chars[$dest])) return false;

    $tekst = str_replace('a', 'a', $tekst);
    $tekst = str_replace('é', 'e', $tekst);

    return str_replace($chars[$source],$chars[$dest],$tekst);
}
