<?php
use Utils\Database\XDb;
use Utils\Database\OcDb;

require_once("./lib/common.inc.php");

if (isSet($_GET['wp']) && !empty($_GET['wp']) && $_GET['wp'] != "OP") {



    $wp = XDb::xEscape($_GET['wp']);

    $query = "select votes,cache_id,topratings,user_id,type,size,status,cache_id,name,longitude,latitude,date_hidden,wp_oc,score,founds,notfounds,notes,picturescount";
    $query .= " from caches where wp_oc = '" . $wp . "';";
    $wynik = XDb::xSql($query);
    $caches = XDb::xFetchArray($wynik);

    // dodałem sprawdzanie statusu
    if (empty($caches) || $caches['status'] == 4 || $caches['status'] == 5 || $caches['status'] == 6)
        $tpl->assign("error", "1");

    else {

        // detailed cache access logging
        global $enable_cache_access_logs;
        if (@$enable_cache_access_logs) {

            $dbc = OcDb::instance();

            $cache_id = $caches['cache_id'];
            $user_id = @$_SESSION['user_id'] > 0 ? $_SESSION['user_id'] : null;
            $access_log = @$_SESSION['CACHE_ACCESS_LOG_VC_' . $user_id];
            if ($access_log === null) {
                $_SESSION['CACHE_ACCESS_LOG_VC_' . $user_id] = array();
                $access_log = $_SESSION['CACHE_ACCESS_LOG_VC_' . $user_id];
            }
            if (@$access_log[$cache_id] !== true) {
                $dbc->multiVariableQuery(
                        'INSERT INTO CACHE_ACCESS_LOGS
                            (event_date, cache_id, user_id, source, event, ip_addr, user_agent, forwarded_for)
                         VALUES
                            (NOW(), :1, :2, \'M\', \'view_cache\', :3, :4, :5)',
                        $cache_id, $user_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'],
                        ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '' )
                );
                $access_log[$cache_id] = true;
                $_SESSION['CACHE_ACCESS_LOG_VC_' . $user_id] = $access_log;
            }
        }

        $query = "select username from user where user_id = " . $caches['user_id'] . ";";
        $wynik = XDb::xSql($query);
        $user = XDb::xFetchArray($wynik);

        $query = "select cache_desc.desc,hint,short_desc from cache_desc where cache_id ='" . $caches['cache_id'] . '\'';
        $query.=' order by field(`language`, \'pl\', \'en\', \'de\', \'nl\') ASC;';

        $wynik = XDb::xSql($query);

        $i = 0;

        while ($rekord = XDb::xFetchArray($wynik)) {

            if ($i > 0)
                $cache_desc['desc'].="<br/><br/>";
            $cache_desc['desc'].=$rekord['desc'];
            if ($i > 0)
                $cache_desc['short_desc'].="<br/>";
            $cache_desc['short_desc'].=$rekord['short_desc'];
            if ($i > 0)
                $cache_desc['hint'].="\\n\\n";
            $cache_desc['hint'].=$rekord['hint'];

            $i++;
        }

        $query = "select attrib_id from caches_attributes where cache_id ='" . $caches['cache_id'] . '\';';
        $cache_attributes = XDb::xSql($query);

        $query = "select " . $lang . " from cache_type where id =" . $caches['type'] . ';';
        $wynik = XDb::xSql($query);
        $cache_type = XDb::xFetchArray($wynik);

        $query = "select " . $lang . " from cache_size where id =" . $caches['size'] . ';';
        $wynik = XDb::xSql($query);
        $cache_size = XDb::xFetchArray($wynik);

        $query = "select " . $lang . " from cache_status where id =" . $caches['status'] . ';';
        $wynik = XDb::xSql($query);
        $cache_status = XDb::xFetchArray($wynik);

        if (isset($_SESSION['user_id'])) {
            $query2 = "select 1 from cache_logs where user_id = '" . $_SESSION['user_id'] . "' and type = '1' and deleted='0' and cache_id ='" . $caches['cache_id'] . "';";
            $wynik2 = XDb::xSql($query2);
            $if_found = XDb::xFetchArray($wynik2);

            if ($if_found[0] != '1') {
                $query2 = "select 2 from cache_logs where user_id = '" . $_SESSION['user_id'] . "' and type = '2' and deleted='0' and cache_id ='" . $caches['cache_id'] . "';";
                $wynik2 = XDb::xSql($query2);
                $if_found = XDb::xFetchArray($wynik2);
            }

            $if_found = $if_found[0];
        }

        $cache_info = array();

        $cache_info['if_found'] = $if_found;

        if ($caches['votes'] > 3)
            $cache_info['score'] = score2ratingnum($caches['score']);
        else
            $cache_info['score'] = 5;

        if (isset($_SESSION['user_id'])) {
            $query3 = "select id from cache_watches where user_id = '" . $_SESSION['user_id'] . "' and cache_id ='" . $caches['cache_id'] . "';";
            $wynik3 = XDb::xSql($query3);
            $watched = XDb::xFetchArray($wynik3);
            $watched = $watched[0];
            if (!empty($watched))
                $cache_info['watched'] = $watched;
            else
                $cache_info['watched'] = -1;
        }

        $cache_info['cache_id'] = $caches['cache_id'];
        $cache_info['name'] = $caches['name'];
        $cache_info['short_desc'] = $cache_desc['short_desc'];
        $cache_info['longitude'] = number_format($caches['longitude'], 5);
        $cache_info['latitude'] = number_format($caches['latitude'], 5);
        $cache_info['N'] = cords($caches['latitude']);
        $cache_info['E'] = cords($caches['longitude']);
        $cache_info['type'] = $cache_type[0];
        $cache_info['size'] = $cache_size[0];
        $cache_info['status2'] = $caches['status'];
        $cache_info['status'] = $cache_status[0];
        $cache_info['hidden_date'] = date('j.m.Y', strtotime($caches['date_hidden']));
        $cache_info['wp_oc'] = $caches['wp_oc'];
        $cache_info['owner'] = $user[0];
        $cache_info['user_id'] = $caches['user_id'];
        $cache_info['founds'] = $caches['founds'];
        $cache_info['notfounds'] = $caches['notfounds'];
        $cache_info['notes'] = $caches['notes'];
        $cache_info['desc'] = html2desc($cache_desc['desc']);
        $cache_info['hint'] = html2hint($cache_desc['hint']);

        $cache_info['picturescount'] = $caches['picturescount'];
        $cache_info['topratings'] = $caches['topratings'];

        $tpl->assign("cache", $cache_info);

        if ($caches['picturescount'] > 0) {

            $query = "select url, title, spoiler from pictures where object_id = '" . $caches['cache_id'] . "' and display=1 and object_type=2;";
            $wynik = XDb::xSql($query);

            $znalezione = array();

            while ($rekord = XDb::xFetchArray($wynik))
                $photos[] = $rekord;

            $tpl->assign("photos_list", $photos);
        }

        if (!empty($cache_attributes)) {

            $attr_text = "";

            while ($rekord = XDb::xFetchArray($cache_attributes)) {

                $query = "select text_long from cache_attrib where id ='" . $rekord['attrib_id'] . "' and language = '" . $lang . "';";
                $wynik = XDb::xSql($query);
                $attr = XDb::xFetchArray($wynik);
                $attr_text .= "\\n" . $attr[0] . "\\n";
            }

            $tpl->assign("attr_text", $attr_text);
        }

        $gk = "";

//          $query="select name,distancetravelled from gk_item where latitude ='".$caches['latitude']."' and longitude='".$caches['longitude']."';";
        $query = "SELECT gk_item.id, name, distancetravelled FROM gk_item INNER JOIN gk_item_waypoint ON (gk_item.id = gk_item_waypoint.id) WHERE gk_item_waypoint.wp = '" . $caches['wp_oc'] . "' AND stateid<>1 AND stateid<>4 AND stateid <>5 AND typeid<>2 AND missing=0";
        $wynik = XDb::xSql($query);
        //print XDb::xNumRows($wynik);
        while ($rekord = XDb::xFetchArray($wynik)) {
            //print $rekord['name'];
            $gk.=$rekord['name'] . " - " . $rekord['distancetravelled'] . " km\\n\\n";
        }

        $tpl->assign("gk", $gk);
    }
} else {
    header('Location: ./index.php');
    exit;
}

$tpl->display('tpl/viewcache.tpl');
?>
