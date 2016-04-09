<?php

setlocale(LC_TIME, 'pl_PL.UTF-8');

global $content, $bUseZip, $usr, $hide_coords, $dbcSearch, $lang;

set_time_limit(1800);

$wptSize[1] = tr('cacheSize_1'); //'Nano'
$wptSize[2] = tr('cacheSize_2'); //'Micro'
$wptSize[3] = tr('cacheSize_3'); //'Small'
$wptSize[4] = tr('cacheSize_4'); //'Regular'
$wptSize[5] = tr('cacheSize_5'); //'Large'
$wptSize[6] = tr('cacheSize_6'); //'Extra Large'
$wptSize[7] = tr('cacheSize_7'); //'Virtual'

$wptType[1] = 'Unknown Cache';
$wptType[2] = 'Traditional Cache';
$wptType[3] = 'Multi-Cache';
$wptType[4] = 'Virtual Cache';
$wptType[5] = 'Webcam Cache';
$wptType[6] = 'Event Cache';
$wptType[7] = 'Quiz';
$wptType[8] = 'Moving Cache';
$wptType[10] = 'Unknown Cache';

if( $usr || !$hide_coords ) {
    //prepare the output
    $caches_per_page = 20;

    $sql = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $sql .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    } elseif ($usr === false) {
        $sql .= '0 distance, ';
    } else {
        //get the users home coords
        $rs_coords = sql("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
        $record_coords = sql_fetch_array($rs_coords);

        if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {
            $sql .= '0 distance, ';
        } else {
            //TODO: load from the users-profile
            $distance_unit = 'km';
            $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
            $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

            $sql .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
        }
        mysql_free_result($rs_coords);
    }

    $sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`,
        `caches`.`user_id` `user_id`, ';
    if ($usr === false) {
        $sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
    } else {
        $sql .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
            LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
            . $usr['userid'];
    }
    $sql .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

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
    if (!is_numeric($startat)) {
        $startat = 0;
    }

    if (isset($_REQUEST['count'])) {
        $count = $_REQUEST['count'];
    } else {
        $count = $caches_per_page;
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

    // cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
    $dbcSearch->simpleQuery( 'DROP TEMPORARY TABLE IF EXISTS `wptcontent`');
    $dbcSearch->reset();

    // temporäre tabelle erstellen
    $dbcSearch->simpleQuery( 'CREATE TEMPORARY TABLE `wptcontent` ' . $sql . $sqlLimit);
    $dbcSearch->reset();

    $dbcSearch->simpleQuery( 'SELECT COUNT(*) `count` FROM `wptcontent`');
    $rCount = $dbcSearch->dbResultFetch();
    $dbcSearch->reset();

    if ($rCount['count'] == 1) {
        $dbcSearch->simpleQuery('SELECT `caches`.`wp_oc` `wp_oc` FROM `wptcontent`, `caches` WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetch();
        $dbcSearch->reset();

        $sFilebasename = $rName['wp_oc'];
    } elseif ($options['searchtype'] == 'bywatched') {
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
            $sFilebasename = 'ocpl' . $options['queryid'];
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

    if ($bUseZip == true) {
        header('content-type: application/zip');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
    } else {
        header('Content-type: application/wpt');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.wpt');
    }


    // ok, ausgabe ...

/*                                  cacheid
                                    name
                                    lon
                                    lat

                                    archivedflag
                                    type
                                    size
                                    difficulty
                                    terrain
                                    username  */

    $sql = 'SELECT `wptcontent`.`cache_id` `cacheid`, IF(wptcontent.cache_id IN (SELECT cache_id FROM cache_logs WHERE deleted=0 AND user_id='.$usr['userid'].' AND (type=1 OR type=8)),1,0) as found, `wptcontent`.`longitude` `longitude`, `wptcontent`.`latitude` `latitude`, `wptcontent`.cache_mod_cords_id, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`, `cache_size`.`'.$lang.'` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` , `caches`.`size` `size`, `caches`.`status` `status`, `caches`.`type` `type` FROM `wptcontent`, `caches`, `cache_type`, `cache_size`, `user` WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` AND `wptcontent`.`type`=`cache_type`.`id` AND `wptcontent`.`size`=`cache_size`.`id` AND `wptcontent`.`user_id`=`user`.`user_id`';

    $dbcSearch->simpleQuery( $sql );

    appendOutput("OziExplorer Waypoint File Version 1.1\r\n");
    appendOutput("WGS 84\r\n");
    appendOutput("Reserved 2\r\n");
    appendOutput("Reserved 3\r\n");

    while($r = $dbcSearch->dbResultFetch() ) {
        $lat = sprintf('%01.6f', $r['latitude']);
        $lon = sprintf('%01.6f', $r['longitude']);

        //modified coords
        if ($r['cache_mod_cords_id'] > 0) {  //check if we have user coords
            $r['mod_suffix']= '[F]';
        } else {
            $r['mod_suffix']= '';
        }

        $name = convertString(str_replace(',','',$r['mod_suffix'].$r['name']));
        $username = convertString(str_replace(',','',$r['username']));
        $type = $wptType[$r['type']];
        $size = convertString($wptSize[$r['size']]);
        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $cacheid = $r['wp_oc'];
        $id = $r['cacheid'];
        $sql_u = "SELECT user_id FROM caches WHERE cache_id = ".intval($id);
        $date_hidden = $r['date_hidden'];
        $userid = @mysql_result(@mysql_query($sql_u),0);

        $kolor = 16776960;
        if ($userid == $usr['userid']) {
            $kolor = 65280;
        }
        if ($r['status'] == 3 || $r['status'] == 2) {
            $kolor = 255;
        }
        if ($r['found']) {
            $kolor = 65535;
        }
        $sss= "SELECT ozi_filips FROM user WHERE user_id=".$usr['userid'];
        $r['ozi_filips']=@mysql_result(@mysql_query($sss),0);
        if($r['ozi_filips']!=""||$r['ozi_filips']!=null) {
            $attach = $r['ozi_filips']."\\op\\".$r['wp_oc'][2]."\\".$r['wp_oc'][3]."\\".$r['wp_oc'][4].$r['wp_oc'][5].".html";
        } else {
            $attach = "";
        }
        // remove double slashes
        $attach = str_replace("\\\\", "\\", $attach);
        //$line = $name . " by " . $username . " - " . $type . " (" . $difficulty . "/" . $terrain . ")";
        $line = "$cacheid / D:$difficulty / T:$terrain / Size: $size";
//        Ograniczenie opisu do 40 znakow
//        if (strlen($line) > 40) {
//            $line = substr($line, 0, 40);
//        }
//        $wpt_date = floor((strtotime(substr($date_hidden,0,10)) - strtotime("1970-01-01")) / (60*60*24)) + 25570;
//        number,name,lat,lon,date,symbol,stat, map form, fcolor,bcolor,desc(40),p direct,g disp,prox,alit,fsize,fstyle,symb size,prox,prox,prox
//        $record  = "-1,$cacheid,$lat,$lon,$wpt_date,0,1,4,0,16777215,$line,0,0,0,-777,8,0,17,0,10.0,2,$attach,,\r\n";

        $record  = "-1,$name,$lat,$lon,,117,1,4,0,$kolor,$line,0,0,0, -777,8,0,17,0,10.0,2,$attach,,\r\n";

        appendOutput($record);
        ob_flush();
    }
    $dbcSearch->reset();
    unset($cdb);


     // phpzip versenden
     if ($bUseZip == true) {
        $phpzip->add_data($sFilebasename . '.wpt', $content);
        echo $phpzip->save($sFilebasename . '.zip', 'b');
    }

    exit;
}

function convertString($str)
{
    $replace = [
        '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
        '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
        '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
        'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
        'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
        'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
        'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
        'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
        'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'L', 'Ľ' => 'L',
        'Ĺ' => 'L', 'Ļ' => 'L', 'Ŀ' => 'L', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
        'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
        'Ö' => 'Oe', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
        'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
        'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
        'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
        '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
        'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
        'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
        'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
        'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
        'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
        'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
        'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
        'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
        'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
        'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
        'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
        'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
        '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
        'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ś' => 's', 'ş' => 's', 'ţ' => 't', 'ù' => 'u', 'ú' => 'u',
        'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
        'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
        'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
        'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
        'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
        'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
        'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
        'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
        'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
        'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
        'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
        'ю' => 'yu', 'я' => 'ya'
    ];
    return str_replace(array_keys($replace), $replace, $str);
}

function appendOutput($str)
{
    global $content, $bUseZip;

    if ($bUseZip == true) {
        $content .= $str;
    } else {
        echo $str;
    }
}

?>
