<?php

require_once("./lib/common.inc.php");

function find_news($start, $end)
{

    global $tpl;
    global $lang;
    global $znalezione;

    $wp = mysql_real_escape_string($_GET['wp']);

    $query = "select id,type,user_id,date,text,deleted from cache_logs where cache_id = (select cache_id from caches where wp_oc = '" . $wp . "') order by date desc limit " . $start . "," . $end;
    $wynik = db_query($query);

    $query = "select name,cache_id from caches where cache_id = (select cache_id from caches where wp_oc = '" . $wp . "');";
    $wynik2 = db_query($query);
    $caches = mysql_fetch_assoc($wynik2);

    $tpl->assign("name", $caches['name']);

    // detailed cache access logging
    global $enable_cache_access_logs;
    if (@$enable_cache_access_logs) {
        if (!isset($dbc)) {
            $dbc = new dataBase();
        };
        $cache_id = $caches['cache_id'];
        $user_id = @$_SESSION['user_id'] > 0 ? $_SESSION['user_id'] : null;
        $access_log = @$_SESSION['CACHE_ACCESS_LOG_VL_' . $user_id];
        if ($access_log === null) {
            $_SESSION['CACHE_ACCESS_LOG_VL_' . $user_id] = array();
            $access_log = $_SESSION['CACHE_ACCESS_LOG_VL_' . $user_id];
        }
        if (@$access_log[$cache_id] !== true) {
            $dbc->multiVariableQuery(
                    'INSERT INTO CACHE_ACCESS_LOGS
                        (event_date, cache_id, user_id, source, event, ip_addr, user_agent, forwarded_for)
                     VALUES
                        (NOW(), :1, :2, \'M\', \'view_logs\', :3, :4, :5)', $cache_id, $user_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_X_FORWARDED_FOR']
            );
            $access_log[$cache_id] = true;
            $_SESSION['CACHE_ACCESS_LOG_VL_' . $user_id] = $access_log;
        }
    }


    $znalezione = array();

    while ($logs = mysql_fetch_assoc($wynik)) {

        if ($logs['deleted'] == 0) {

            $query = "select username from user where user_id = '" . $logs['user_id'] . "';";
            $wynik3 = db_query($query);
            $user = mysql_fetch_row($wynik3);

            $logs2['id'] = $logs['id'];
            $logs2['user_id'] = $logs['user_id'];
            $logs2['newtype'] = $logs['type'];
            $logs2['newdate'] = date('j.m.Y', strtotime($logs['date']));
            $logs2['username'] = $user[0];
            $logs2['newtext'] = html2log($logs['text']);

            $znalezione [] = $logs2;
        }
    }

    $tpl->assign("wp_oc", $wp);
    $tpl->assign("logs", $znalezione);
}

if (isSet($_GET['wp']) && !empty($_GET['wp']) && $_GET['wp'] != "OP") {

    db_connect();

    $wp = mysql_real_escape_string($_GET['wp']);

    $na_stronie = 10;

    $query = "select count(*) from cache_logs where cache_id = (select cache_id from caches where wp_oc = '" . $wp . "') and deleted='0' order by date desc;";
    $wynik = db_query($query);
    $ile = mysql_fetch_row($wynik);
    $ile = $ile[0];

    $url = $_SERVER['REQUEST_URI'];

    $tpl->assign("ile", $ile);

    $max = ceil($ile / $na_stronie);

    if ($max == '0')
        $max = '1';

    $tpl->assign('max', $max);

    require_once("./lib/paging.inc.php");

    $tpl->assign('next_page', $next_page);
    $tpl->assign('prev_page', $prev_page);
}else {
    header('Location: ./index.php');
    exit;
}

$tpl->display('tpl/logs.tpl');
?>