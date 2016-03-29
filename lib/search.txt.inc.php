<?php

    global $content, $bUseZip, $sqldebug, $hide_coords, $usr, $lang, $dbcSearch;
    set_time_limit(1800);
    $cache = cache::instance();
    $cacheSizes = $cache->getCacheSizes();
    $cacheTypesArr = $cache->getCacheTypeIcons();
    $cacheStatusArr = $cache->getCacheStatuses();

    $txtLine = chr(239) . chr(187) . chr(191) .tr('search_text_01')." {mod_suffix}{cachename} ".tr('search_text_02')." {owner}
".tr('search_text_03')." {lat} {lon}
".tr('search_text_04')." {status}

".tr('search_text_05')." {{time}}
".tr('search_text_06')." {{waypoint}}
".tr('search_text_07')." {country}
".tr('search_text_08')." {type}
".tr('search_text_09')." {container}
Z/T: {difficulty}/{terrain}
Online: ".$absolute_server_URI."viewcache.php?wp={{waypoint}}

".tr('search_text_10')." {shortdesc}

".tr('search_text_11')." {htmlwarn}:
<===================>
{desc}
{rr_comment}
{personal_cache_note}
<===================>

".tr('search_text_12')."
<===================>
{hints}
<===================>
A|B|C|D|E|F|G|H|I|J|K|L|M
N|O|P|Q|R|S|T|U|V|W|X|Y|Z

".tr('search_text_13')."
{logs}
";

    $txtLogs = "<===================>
{username} / {date} / {type}

{{text}}
";

if( $usr || !$hide_coords ) {
    //prepare the output
    $caches_per_page = 20;

    $sql = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $sql .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    } else {
        if ($usr === false) {
            $sql .= '0 distance, ';
        } else {
            //get the users home coords
            if ((($usr['latitude'] == NULL) || ($usr['longitude'] == NULL)) || (($usr['latitude'] == 0) || ($usr['longitude'] == 0))) {
                $sql .= '0 distance, ';
            } else {
                $distance_unit = 'km';

                $lon_rad = $usr['longitude'] * 3.14159 / 180;
                $lat_rad = $usr['latitude'] * 3.14159 / 180;

                $sql .= getCalcDistanceSqlFormula($usr !== false, $usr['longitude'], $usr['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
            }
        }
    }
    $sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';
    if ($usr === false) {
        $sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id
                FROM `caches` ';
    } else {
        $sql .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                  `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
                  LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
                  . $usr['userid'];
    }
    $sql .= ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')';

    $sortby = $options['sort'];
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance')) {
        $sql .= ' ORDER BY distance ASC';
    } elseif ($sortby == 'bycreated') {
       $sql .= ' ORDER BY date_created DESC';
    } else { // by name
        $sql .= ' ORDER BY name ASC';
    }

    //startat?
    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
    if (!is_numeric($startat)) $startat = 0; {
        if (isset($_REQUEST['count'])) {
            $count = $_REQUEST['count'];
        } else {
            $count = $caches_per_page;
        }
    }
    $maxlimit = 1000000000;

    if ($count == 'max') {
        $count = $maxlimit;
    }
    if (!is_numeric($count)) {
        $count = 0;
    }
    if ($count < 1) {
        $count = 1;
    }
    if ($count > $maxlimit) {
        $count = $maxlimit;
    }

    $sqlLimit = ' LIMIT ' . $startat . ', ' . $count;

    // temporĂ¤re tabelle erstellen
    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `txtcontent` ' . $sql . $sqlLimit);
    $dbcSearch->reset();

    $dbcSearch->simpleQuery('SELECT COUNT(*) `count` FROM `txtcontent`');
    $rCount = $dbcSearch->dbResultFetch();
    $dbcSearch->reset();

    if ($rCount['count'] == 1) {
        $dbcSearch->simpleQuery('SELECT `caches`.`wp_oc` `wp_oc` FROM `txtcontent`, `caches` WHERE `txtcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetch();
        $dbcSearch->reset();

        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } else {
            $rsName = sql('SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= &1 LIMIT 1', $options['queryid']);
            $rName = sql_fetch_array($rsName);
            mysql_free_result($rsName);
            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
                $sFilebasename = str_replace(" ", "_", $sFilebasename);
            } else {
                $sFilebasename = "$short_sitename" . $options['queryid'];
            }
        }
    }

    $bUseZip = ($rCount['count'] > 50);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    $bUseZip = false;
    if ($bUseZip == true) {
        $content = '';
        require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('',6);
    }

    // ok, ausgabe starten
    if ($sqldebug == false) {
        if ($bUseZip == true) {
            header("content-type: application/zip");
            header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
        } else {
            header("Content-type: text/plain");
            header("Content-Disposition: attachment; filename=" . $sFilebasename . ".txt");
        }
    }

    $dbcSearch->simpleQuery('SELECT `txtcontent`.`cache_id` `cacheid`, `txtcontent`.`longitude` `longitude`, `txtcontent`.`latitude` `latitude`, `txtcontent`.cache_mod_cords_id, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`country` `country`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, `cache_size`.`id` `size`, `caches`.`type` `type_id`, `caches`.`status` `status`, `user`.`username` `username`, `cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`desc_html` `html`, `cache_desc`.`rr_comment`, `caches`.`logpw` FROM `txtcontent`, `caches`, `user`, `cache_desc`, `cache_size` WHERE `txtcontent`.`cache_id`=`caches`.`cache_id` AND `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`default_desclang`=`cache_desc`.`language` AND `txtcontent`.`user_id`=`user`.`user_id` AND `caches`.`size`=`cache_size`.`id`');

    while($r = $dbcSearch->dbResultFetch()) {
        if (@$enable_cache_access_logs) {
            if (!isset($dbc)) {
                $dbc = new dataBase();
            }
            $cache_id = $r['cacheid'];
            $user_id = $usr !== false ? $usr['userid'] : null;
            $access_log = @$_SESSION['CACHE_ACCESS_LOG_TXT_'.$user_id];
            if ($access_log === null) {
                $_SESSION['CACHE_ACCESS_LOG_TXT_'.$user_id] = array();
                $access_log = $_SESSION['CACHE_ACCESS_LOG_TXT_'.$user_id];
            }
            if (@$access_log[$cache_id] !== true) {
                $dbc->multiVariableQuery(
                    'INSERT INTO CACHE_ACCESS_LOGS
                    (event_date, cache_id, user_id, source, event, ip_addr, user_agent, forwarded_for)
                    VALUES
                    (NOW(), :1, :2, \'B\', \'download_txt\', :3, :4, :5)',
                    $cache_id, $user_id,
                    $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_X_FORWARDED_FOR']
                    );
                $access_log[$cache_id] = true;
                $_SESSION['CACHE_ACCESS_LOG_TXT_'.$user_id] = $access_log;
            }
        }

        $thisline = $txtLine;

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = str_replace('{lat}', help_latToDegreeStr($lat), $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = str_replace('{lon}', help_lonToDegreeStr($lon), $thisline);

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
            $thisline = str_replace('{hints}', str_rot13_html(strip_tags($r['hint'])), $thisline);
        }

        $logpw = ($r['logpw']==""?"":"".tr('search_text_14')." <br/>");

        $thisline = str_replace('{shortdesc}', $r['short_desc'], $thisline);

        if ($r['html'] == 0) {
            $thisline = str_replace('{htmlwarn}', '', $thisline);
            $thisline = str_replace('{desc}', strip_tags($logpw.$r['desc']), $thisline);
        } else {
            $thisline = str_replace('{htmlwarn}', ''.tr('search_text_15').'', $thisline);
            $thisline = str_replace('{desc}', html2txt($logpw.$r['desc']), $thisline);
        }

        if ($usr == true) {
            $notes_rs = sql("SELECT  `cache_notes`.`desc` `desc` FROM `cache_notes` WHERE `cache_notes` .`user_id`=&1 AND `cache_notes`.`cache_id`=&2", $usr['userid'],$r['cacheid']);
            if (mysql_num_rows($notes_rs) != 0) {
                $cn = sql_fetch_array($notes_rs);
                $thisline = str_replace('{personal_cache_note}', html2txt("<br/><br/>-- ".tr('search_text_16')." --<br/> ".$cn['desc']."<br/>"), $thisline);
            } else {
                $thisline = str_replace('{personal_cache_note}', "", $thisline);
            }
        } else {
            $thisline = str_replace('{personal_cache_note}', "", $thisline);
        }

        if( $r['rr_comment'] == '' ) {
            $thisline = str_replace('{rr_comment}', '', $thisline);
        } else {
            $thisline = str_replace('{rr_comment}', html2txt("<br /><br />--------<br />".$r['rr_comment']), $thisline);
        }
        $thisline = str_replace('{type}', tr($cacheTypesArr[$r['type_id']]['translation']), $thisline);
        $thisline = str_replace('{container}', tr($cacheSizes[$r['size']]['translation']), $thisline);
        $thisline = str_replace('{status}', tr($cacheStatusArr[$r['status']]['translation']), $thisline);

        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $thisline = str_replace('{difficulty}', $difficulty, $thisline);

        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $thisline = str_replace('{terrain}', $terrain, $thisline);

        $thisline = str_replace('{owner}', $r['username'], $thisline);

        // logs ermitteln
        $logentries = '';
        $rsLogs = sql("SELECT `cache_logs`.`id`, `cache_logs`.`text_html`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username` FROM `cache_logs`, `user` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=`user`.`user_id`AND `cache_logs`.`cache_id`=&1 ORDER BY `cache_logs`.`date` DESC LIMIT 20", $r['cacheid']);
        while ($rLog = sql_fetch_array($rsLogs)) {
            $thislog = $txtLogs;

            $thislog = str_replace('{id}', $rLog['id'], $thislog);
            $thislog = str_replace('{date}', date('d.m.Y', strtotime($rLog['date'])), $thislog);
            $thislog = str_replace('{username}', $rLog['username'], $thislog);

            $logtype = tr('logType'.$rLog['type']);

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

        if($bUseZip == false) {
            echo $thisline;
        } else {
            $phpzip->add_data($r['waypoint'] . '.txt', $thisline);
        }
        ob_flush();
    }
    unset($dbc);
    $dbcSearch->simpleQuery('DROP TABLE `txtcontent` ');
    $dbcSearch->reset();
    if ($sqldebug == true) {
        sqldbg_end();
    }

    // phpzip versenden
    if ($bUseZip == true) {
        echo $phpzip->save($sFilebasename . '.zip', 'b');
    }
}
exit;

function html2txt($html)
{
    $str = preg_replace('/[[:cntrl:]]/', '', $html);
    $str = str_replace("\r\n", '', $str);
    $str = str_replace("\n", '', $str);
    $str = str_replace('<br />', "\n", $str);
    $str = str_replace('<br>', "\n", $str);
    $str = str_replace('</p>', "\n", $str);
    $str = str_replace('<li>', "-", $str);
    $str = str_replace('&quot;', '"', $str);
    $str = str_replace('&amp;', '&', $str);
    $str = str_replace('&lt;', '<', $str);
    $str = str_replace('&gt;', '>', $str);
    $str = strip_tags($str);

    return $str;
}

function lf2crlf($str)
{
    return str_replace("\r\r\n" ,"\r\n" , str_replace("\n" ,"\r\n" , $str));
}
?>
