<?php

require_once("./lib/common.inc.php");

if (isset($_SESSION['user_id']) && isset($_GET['wp']) && !empty($_GET['wp'])) {

    db_connect();

    $wp = mysql_real_escape_string($_GET['wp']);

    $query = "select name,cache_id,user_id,votes,score,logpw,type from caches where wp_oc = '" . $wp . "' and status='1';";
    $wynik = db_query($query);
    $caches = mysql_fetch_assoc($wynik);

    if (!empty($caches)) {

        // Prevent https://github.com/opencaching/opencaching-pl/issues/228
        db_query("start transaction");
        db_query("
            select 1
            from cache_logs
            where
                user_id = '".mysql_real_escape_string($_SESSION['user_id'])."'
                and cache_id = '".mysql_real_escape_string($caches['cache_id'])."'
            for update
        ");

        $query = "select 1 from cache_logs where user_id = '" . $_SESSION['user_id'] . "' and type = '1' and deleted='0' and cache_id ='" . $caches['cache_id'] . "';";
        $wynik = db_query($query);
        $if_found = mysql_fetch_row($wynik);

        $is_mine = ($_SESSION['user_id'] == $caches['user_id']) ? 1 : 0;

        $temp_found = ($if_found[0] == 1 || $is_mine == 1) ? 1 : 0;

        $query = "SELECT floor( founds_count /10 ) FROM user WHERE user_id =" . $_SESSION['user_id'] . ";";
        $wynik = db_query($query);
        $dostepne = mysql_fetch_row($wynik);

        $query = "select count(*) from cache_rating where user_id=" . $_SESSION['user_id'] . ";";
        $wynik = db_query($query);
        $przyznanych = mysql_fetch_row($wynik);

        $dowykorzystania = $dostepne[0] - $przyznanych[0];
        if ($dowykorzystania > 0)
            $topratingav = 1;
        else
            $topratingav = 0;

        if (isset($_POST['entry']) && $_POST['entry'] == 'true') {

            $rodzaj = mysql_real_escape_string($_POST['rodzaj']);
            $date_d = mysql_real_escape_string($_POST['date_d']);
            $date_m = mysql_real_escape_string($_POST['date_m']);
            $date_Y = mysql_real_escape_string($_POST['date_Y']);
            $date_H = mysql_real_escape_string($_POST['date_H']);
            $date_i = mysql_real_escape_string($_POST['date_i']);
            $rekomendacja = mysql_real_escape_string($_POST['rekomendacja']);
            $ocena = mysql_real_escape_string($_POST['ocena']);
            $logpw = mysql_real_escape_string($_POST['logpw']);
            $tekst = mysql_real_escape_string($_POST['tekst']);
            $tpl->assign('tresc', $tekst);
            $tpl->assign('rodz_select', $rodzaj);

            $datetime = @mktime($date_H, $date_i, 0, $date_m, $date_d, $date_Y);
            $dzis = date("Y-m-d H:i:00", $datetime);

            if ($datetime == '')
                $tpl->assign('error', '2');
            elseif ($datetime > time())
                $tpl->assign('error', '3');
            elseif (($rodzaj < '1' || $rodzaj > '5') && $caches['type'] != 6)
                $tpl->assign('error', '4');
            elseif ($rodzaj != '3' && $rodzaj != '7' && $rodzaj != '8' && $caches['type'] == 6)
                $tpl->assign('error', '4');
            elseif ($temp_found == 0 && !preg_match("/^((-4)|(-3)|(-1.5)|(0)|(1.5)|(3)){1}$/", $ocena))
                $tpl->assign('error', '4');
            elseif ($temp_found == 1 && ($rodzaj == 1 || $rodzaj == 2))
                $tpl->assign('error', '4');
            elseif ($temp_found == 1 && !empty($rekomendacja))
                $tpl->assign('error', '4');
            elseif ($temp_found == 1 && !empty($ocena))
                $tpl->assign('error', '4');
            elseif (!empty($caches['logpw']) && $caches['logpw'] != $logpw && $rodzaj == 1) {
                $tpl->assign('error', '5');
            } else {

                $uuid = mb_strtoupper(md5(uniqid(rand(), true)));
                $query = "insert into cache_logs (uuid, cache_id, user_id, type, date, text, last_modified, date_created, node) ";
                $query.="values ('" . $uuid . "','" . $caches['cache_id'] . "','" . $_SESSION['user_id'] . "','" . $rodzaj . "','" . $dzis . "','" . $tekst . "',now(),now(), '2');";
                db_query($query);

                switch ($rodzaj) {

                    case 1:
                        $query = "update caches set founds=founds+1 where cache_id = " . $caches['cache_id'];
                        db_query($query);
                        $query = "update user set founds_count=founds_count+1 where user_id = " . $_SESSION['user_id'];
                        db_query($query);
                        break;
                    case 2:
                        $query = "update caches set notfounds=notfounds+1 where cache_id = " . $caches['cache_id'];
                        db_query($query);
                        $query = "update user set notfounds_count=notfounds_count+1 where user_id = " . $_SESSION['user_id'];
                        db_query($query);
                        break;
                    case 3:
                        $query = "update caches set notes=notes+1 where cache_id = " . $caches['cache_id'];
                        db_query($query);
                        $query = "update user set log_notes_count=log_notes_count+1 where user_id = " . $_SESSION['user_id'];
                        db_query($query);
                        break;
                    case 7:
                        $query = "update caches set founds=founds+1 where cache_id = " . $caches['cache_id'];
                        db_query($query);
                        break;
                    case 8:
                        $query = "update caches set notfounds=notfounds+1 where cache_id = " . $caches['cache_id'];
                        db_query($query);
                        break;
                }

                if ($rodzaj == 1) {
                    if ($topratingav == 1 && $rekomendacja == 'on') {
                        $query = "insert into cache_rating(cache_id, user_id) values (" . $caches['cache_id'] . "," . $_SESSION['user_id'] . ");";
                        db_query($query);

                        // Notify OKAPI's replicate module of the change.
                        // Details: https://github.com/opencaching/okapi/issues/265
                        require_once($rootpath . 'okapi/facade.php');
                        \okapi\Facade::schedule_user_entries_check($caches['cache_id'], $_SESSION['user_id']);
                        \okapi\Facade::disable_error_handling();

                        // don't use this query! This update is generated by trigger "cacheRatingAfterInsert" on MySQL
                        // ==== Limak 10.02.2012 ===
                        //$query="update caches set topratings=topratings+1 where cache_id=".$caches['cache_id'];
                        //db_query($query);
                    }
                    if ($ocena >= '-3' && $ocena <= '3') {
                        $query = "insert into scores(cache_id,user_id,score) values (" . $caches['cache_id'] . "," . $_SESSION['user_id'] . "," . $ocena . ");";
                        db_query($query);

                        $query = "update caches set votes=votes+1 ,score=(SELECT round( avg( score ) , 1 ) FROM scores WHERE cache_id = " . $caches['cache_id'] . ") where cache_id = " . $caches['cache_id'];
                        db_query($query);
                    }
                }
                db_query("commit");
                header('Location: ./viewcache.php?wp=' . $wp);
                exit;
            }
        }
    } else
        $tpl->assign('error', '1');
}
else {
    header('Location: ./index.php');
    exit;
}

$tpl->assign('topratingav', $topratingav);
$tpl->assign('temp_found', $temp_found);
$tpl->assign('cache_name', $caches['name']);
$tpl->assign('logpw', $caches['logpw']);
$tpl->assign('wp_oc', $wp);
$tpl->assign('date_d', date(d));
$tpl->assign('date_m', date(m));
$tpl->assign('date_Y', date(Y));
$tpl->assign('date_H', date(H));
$tpl->assign('date_i', date(i));
$tpl->display('tpl/logentry.tpl');
?>
