<?php
/**
 * This script is used (can be loaded) by /search.php
 */

ob_start();

use src\Models\ApplicationContainer;
use src\Models\Coordinates\Coordinates;
use src\Models\GeoCache\CacheNote;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\OcConfig\OcConfig;
use src\Utils\Database\XDb;
use src\Utils\Log\CacheAccessLog;
use src\Utils\Text\Rot13;

global $content, $bUseZip, $dbcSearch;

set_time_limit(1800);

$loggedUser = ApplicationContainer::GetAuthorizedUser();

require_once __DIR__ . '/../lib/calculation.inc.php';

$txtLine = chr(239) . chr(187) . chr(191) . tr('search_text_01') . ' {mod_suffix}{cachename} ' . tr('search_text_02') . ' {owner}
' . tr('search_text_03') . ' {lat} {lon}
' . tr('search_text_04') . ' {status}

' . tr('search_text_05') . ' {{time}}
' . tr('search_text_06') . ' {{waypoint}}
' . tr('search_text_07') . ' {country}
' . tr('search_text_08') . ' {type}
' . tr('search_text_09') . ' {container}
Z/T: {difficulty}/{terrain}
Online: ' . $absolute_server_URI . 'viewcache.php?wp={{waypoint}}

' . tr('search_text_10') . ' {shortdesc}

' . tr('search_text_11') . ' {htmlwarn}:
<===================>
{desc}
{rr_comment}
{personal_cache_note}
<===================>

' . tr('search_text_12') . '
<===================>
{hints}
<===================>
A|B|C|D|E|F|G|H|I|J|K|L|M
N|O|P|Q|R|S|T|U|V|W|X|Y|Z

' . tr('search_text_13') . '
{logs}
';

$txtLogs = '<===================>
{username} / {date} / {type}

{{text}}
';

if ($loggedUser || ! OcConfig::coordsHiddenForNonLogged()) {
    //prepare the output
    $caches_per_page = 20;

    $query = 'SELECT ';

    if (isset($lat_rad, $lon_rad)) {
        $query .= getCalcDistanceSqlFormula(
            is_object($loggedUser),
            $lon_rad * 180 / 3.14159,
            $lat_rad * 180 / 3.14159,
            0,
            $multiplier[$distance_unit]
        ) . ' `distance`, ';
    } else {
        if (! $loggedUser) {
            $query .= '0 distance, ';
        } else {
            //get the users home coords
            $homeCoords = $loggedUser->getHomeCoordinates();

            if ($homeCoords->getLatitude() == null || $homeCoords->getLongitude() == null) {
                $query .= '0 distance, ';
            } else {
                $distance_unit = 'km';

                $lon_rad = $homeCoords->getLongitude() * 3.14159 / 180;
                $lat_rad = $homeCoords->getLatitude() * 3.14159 / 180;

                $query .= getCalcDistanceSqlFormula(
                    is_object($loggedUser),
                    $homeCoords->getLongitude(),
                    $homeCoords->getLatitude(),
                    0,
                    $multiplier[$distance_unit]
                ) . ' `distance`, ';
            }
        }
    }
    $query .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';

    if (! $loggedUser) {
        $query .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id
                FROM `caches` ';
    } else {
        $query .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                  `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.latitude,0) as cache_mod_cords_id FROM `caches`
                  LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
            . $loggedUser->getUserId();
    }
    $query .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

    $sortby = $options['sort'];

    if (isset($lat_rad, $lon_rad) && ($sortby == 'bydistance')) {
        $query .= ' ORDER BY distance ASC';
    } elseif ($sortby == 'bycreated') {
        $query .= ' ORDER BY date_created DESC';
    } else { // by name
        $query .= ' ORDER BY name ASC';
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

    $queryLimit = ' LIMIT ' . $startat . ', ' . $count;

    // temporary table
    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `txtcontent` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery('SELECT COUNT(*) `count` FROM `txtcontent`');
    $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);

    if ($rCount['count'] == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc` FROM `txtcontent`, `caches`
            WHERE `txtcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1'
        );
        $rName = $dbcSearch->dbResultFetchOneRowOnly($s);

        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } else {
            $rsName = XDb::xSql(
                'SELECT `queries`.`name` `name` FROM `queries`
                WHERE `queries`.`id`= ? LIMIT 1',
                $options['queryid']
            );

            $rName = XDb::xFetchArray($rsName);
            XDb::xFreeResults($rsName);

            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
                $sFilebasename = str_replace(' ', '_', $sFilebasename);
            } else {
                $sFilebasename = 'search' . $options['queryid'];
            }
        }
    }

    $bUseZip = ($rCount['count'] > 50);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    $bUseZip = false;

    if ($bUseZip == true) {
        $content = '';

        require_once __DIR__ . '/../src/Libs/PhpZip/ss_zip.class.php';
        $phpzip = new ss_zip('', 6);
    }

    $stmt = XDb::xSql('SELECT `txtcontent`.`cache_id` `cacheid`, `txtcontent`.`longitude` `longitude`, `txtcontent`.`latitude` `latitude`, `txtcontent`.cache_mod_cords_id, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`country` `country`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, `caches`.`size` `size`, `caches`.`type` `type_id`, `caches`.`status` `status`, `user`.`username` `username`, `cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`desc_html` `html`, `cache_desc`.`rr_comment`, `caches`.`logpw` FROM `txtcontent`, `caches`, `user`, `cache_desc` WHERE `txtcontent`.`cache_id`=`caches`.`cache_id` AND `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`default_desclang`=`cache_desc`.`language` AND `txtcontent`.`user_id`=`user`.`user_id`');

    $user_id = $loggedUser ? $loggedUser->getUserId() : null;

    while ($r = XDb::xFetchArray($stmt)) {
        CacheAccessLog::logCacheAccess(
            $r['cacheid'],
            $user_id,
            CacheAccessLog::EVENT_DOWNLOAD_GPX,
            CacheAccessLog::SOURCE_BROWSER
        );

        $thisline = $txtLine;

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = str_replace('{lat}', Coordinates::donNotUse_latToDegreeStr($lat), $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = str_replace('{lon}', Coordinates::donNotUse_lonToDegreeStr($lon), $thisline);

        $time = date('d.m.Y', strtotime($r['date_hidden']));
        $thisline = str_replace('{{time}}', $time, $thisline);
        $thisline = str_replace('{{waypoint}}', $r['waypoint'], $thisline);
        $thisline = str_replace('{cacheid}', $r['cacheid'], $thisline);
        $thisline = str_replace('{cachename}', $r['name'], $thisline);
        $thisline = str_replace('{country}', tr($r['country']), $thisline);

        //modified coords
        if ($r['cache_mod_cords_id'] > 0) {  //check if we have user coords
            $thisline = str_replace('{mod_suffix}', '[F]', $thisline);
        } else {
            $thisline = str_replace('{mod_suffix}', '', $thisline);
        }

        if ($r['hint'] == '') {
            $thisline = str_replace('{hints}', '', $thisline);
        } else {
            $thisline = str_replace('{hints}', Rot13::withoutHtml(strip_tags($r['hint'])), $thisline);
        }

        $logpw = ($r['logpw'] == '' ? '' : '' . tr('search_text_14') . ' <br/>');

        $thisline = str_replace('{shortdesc}', $r['short_desc'], $thisline);

        if ($r['html'] == 0) {
            $thisline = str_replace('{htmlwarn}', '', $thisline);
            $thisline = str_replace('{desc}', strip_tags($logpw . $r['desc']), $thisline);
        } else {
            $thisline = str_replace('{htmlwarn}', '' . tr('search_text_15') . '', $thisline);
            $thisline = str_replace('{desc}', html2txt($logpw . $r['desc']), $thisline);
        }

        if ($loggedUser) {
            $cacheNote = CacheNote::getNote($loggedUser->getUserId(), $r['cacheid']);

            if (! empty($cacheNote)) {
                $thisline = str_replace(
                    '{personal_cache_note}',
                    html2txt('<br/><br/>-- ' . tr('search_text_16') . ' --<br/> '
                        . $cacheNote . '<br/>'),
                    $thisline
                );
            } else {
                $thisline = str_replace('{personal_cache_note}', '', $thisline);
            }
        } else {
            $thisline = str_replace('{personal_cache_note}', '', $thisline);
        }

        if ($r['rr_comment'] == '') {
            $thisline = str_replace('{rr_comment}', '', $thisline);
        } else {
            $thisline = str_replace('{rr_comment}', html2txt('<br /><br />--------<br />' . $r['rr_comment']), $thisline);
        }
        $thisline = str_replace('{type}', tr(GeoCacheCommons::CacheTypeTranslationKey($r['type_id'])), $thisline);
        $thisline = str_replace('{container}', tr(GeoCacheCommons::CacheSizeTranslationKey($r['size'])), $thisline);
        $thisline = str_replace('{status}', tr(GeoCacheCommons::CacheStatusTranslationKey($r['status'])), $thisline);

        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $thisline = str_replace('{difficulty}', $difficulty, $thisline);

        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $thisline = str_replace('{terrain}', $terrain, $thisline);

        $thisline = str_replace('{owner}', $r['username'], $thisline);

        $logentries = '';
        $rsLogs = XDb::xSql(
            'SELECT `cache_logs`.`id`, `cache_logs`.`text_html`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`
            FROM `cache_logs`, `user`
            WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=`user`.`user_id`
                AND `cache_logs`.`cache_id`= ?
            ORDER BY `cache_logs`.`date` DESC LIMIT 20',
            $r['cacheid']
        );

        while ($rLog = XDb::xFetchArray($rsLogs)) {
            $thislog = $txtLogs;

            $thislog = str_replace('{id}', $rLog['id'], $thislog);
            $thislog = str_replace('{date}', date('d.m.Y', strtotime($rLog['date'])), $thislog);
            $thislog = str_replace('{username}', $rLog['username'], $thislog);

            $logtype = tr('logType' . $rLog['type']);

            $thislog = str_replace('{type}', $logtype, $thislog);

            if ($rLog['text_html'] == 0) {
                $thislog = str_replace('{{text}}', $rLog['text'], $thislog);
            } else {
                $thislog = str_replace('{{text}}', html2txt($rLog['text']), $thislog);
            }

            $logentries .= $thislog . "\n";
        }
        $thisline = str_replace('{logs}', $logentries, $thisline);

        $thisline = lf2crlf($thisline);

        if ($bUseZip == false) {
            echo $thisline;
        } else {
            $phpzip->add_data($r['waypoint'] . '.txt', $thisline);
            ob_flush();
        }
    }
    $dbcSearch->simpleQuery('DROP TABLE `txtcontent` ');

    // compress using phpzip
    if ($bUseZip == true) {
        header('content-type: application/zip');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
        $out = $phpzip->save($sFilebasename . '.zip', 'b');
        echo $out;
    } else {
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.txt');
    }
    ob_end_flush();
}

exit;
