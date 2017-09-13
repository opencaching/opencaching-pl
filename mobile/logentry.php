<?php
use Utils\Database\XDb;
use okapi\Facade;

require_once("./lib/common.inc.php");

if (isset($_SESSION['user_id']) && isset($_GET['wp']) && !empty($_GET['wp'])) {

    $wp = XDb::xEscape($_GET['wp']);

    $query = "select name,cache_id,user_id,votes,score,logpw,type from caches where wp_oc = '" . $wp . "' and status='1';";
    $wynik = XDb::xSql($query);
    $caches = XDb::xFetchArray($wynik);

    if (!empty($caches)) {

        // Prevent https://github.com/opencaching/opencaching-pl/issues/228
        XDb::xSql("start transaction");
        XDb::xSql("
            select 1
            from cache_logs
            where
                user_id = '".XDb::xEscape($_SESSION['user_id'])."'
                and cache_id = '".XDb::xEscape($caches['cache_id'])."'
            for update
        ");
        if ($caches['type'] == 6)
            $query = "select 1 from cache_logs where user_id = '" . $_SESSION['user_id'] . "' and type = '7' and deleted='0' and cache_id ='" . $caches['cache_id'] . "';";
        else
            $query = "select 1 from cache_logs where user_id = '" . $_SESSION['user_id'] . "' and type = '1' and deleted='0' and cache_id ='" . $caches['cache_id'] . "';";
        $wynik = XDb::xSql($query);
        $if_found = XDb::xFetchArray($wynik);

        $is_mine = ($_SESSION['user_id'] == $caches['user_id']) ? 1 : 0;

        $temp_found = ($if_found[0] == 1 || $is_mine == 1) ? 1 : 0;

        $query = "SELECT floor( founds_count /10 ) FROM user WHERE user_id =" . $_SESSION['user_id'] . ";";
        $wynik = XDb::xSql($query);
        $dostepne = XDb::xFetchArray($wynik);

        $query = "select count(*) from cache_rating where user_id=" . $_SESSION['user_id'] . ";";
        $wynik = XDb::xSql($query);
        $przyznanych = XDb::xFetchArray($wynik);

        $dowykorzystania = $dostepne[0] - $przyznanych[0];
        if ($dowykorzystania > 0 && $caches['type'] != 6)
            $topratingav = 1;
        else
            $topratingav = 0;

        if (isset($_POST['entry']) && $_POST['entry'] == 'true') {

            $rodzaj = XDb::xEscape($_POST['rodzaj']);
            $date_d = XDb::xEscape($_POST['date_d']);
            $date_m = XDb::xEscape($_POST['date_m']);
            $date_Y = XDb::xEscape($_POST['date_Y']);
            $date_H = XDb::xEscape($_POST['date_H']);
            $date_i = XDb::xEscape($_POST['date_i']);
            $rekomendacja = XDb::xEscape($_POST['rekomendacja']);
            $ocena = XDb::xEscape($_POST['ocena']);
            $logpw = XDb::xEscape($_POST['logpw']);
            $tekst = XDb::xEscape($_POST['tekst']);
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
            elseif ($temp_found == 1 && ($rodzaj == 1 || $rodzaj == 2 || $rodzaj == 7))
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
                XDb::xSql($query);

                switch ($rodzaj) {

                    case 1:
                        $query = "update caches set founds=founds+1 where cache_id = " . $caches['cache_id'];
                        XDb::xSql($query);
                        $query = "update user set founds_count=founds_count+1 where user_id = " . $_SESSION['user_id'];
                        XDb::xSql($query);
                        break;
                    case 2:
                        $query = "update caches set notfounds=notfounds+1 where cache_id = " . $caches['cache_id'];
                        XDb::xSql($query);
                        $query = "update user set notfounds_count=notfounds_count+1 where user_id = " . $_SESSION['user_id'];
                        XDb::xSql($query);
                        break;
                    case 3:
                        $query = "update caches set notes=notes+1 where cache_id = " . $caches['cache_id'];
                        XDb::xSql($query);
                        $query = "update user set log_notes_count=log_notes_count+1 where user_id = " . $_SESSION['user_id'];
                        XDb::xSql($query);
                        break;
                    case 7:
                        $query = "update caches set founds=founds+1 where cache_id = " . $caches['cache_id'];
                        XDb::xSql($query);
                        break;
                    case 8:
                        $query = "update caches set notfounds=notfounds+1 where cache_id = " . $caches['cache_id'];
                        XDb::xSql($query);
                        break;
                }

                if ($rodzaj == 1) {
                    if ($topratingav == 1 && $rekomendacja == 'on') {
                        $query = "insert ignore into cache_rating(cache_id, user_id) values (" . $caches['cache_id'] . "," . $_SESSION['user_id'] . ");";
                        XDb::xSql($query);

                        // Notify OKAPI's replicate module of the change.
                        // Details: https://github.com/opencaching/okapi/issues/265
                        Facade::schedule_user_entries_check($caches['cache_id'], $_SESSION['user_id']);
                        Facade::disable_error_handling();

                        // don't use this query! This update is generated by trigger "cacheRatingAfterInsert" on MySQL
                        // ==== Limak 10.02.2012 ===
                        //$query="update caches set topratings=topratings+1 where cache_id=".$caches['cache_id'];
                        //XDb::xSql($query);
                    }
                    if ($ocena >= '-3' && $ocena <= '3') {
                        $query = "insert into scores(cache_id,user_id,score) values (" . $caches['cache_id'] . "," . $_SESSION['user_id'] . "," . $ocena . ");";
                        XDb::xSql($query);

                        $query = "update caches set votes=votes+1 ,score=(SELECT round( avg( score ) , 1 ) FROM scores WHERE cache_id = " . $caches['cache_id'] . ") where cache_id = " . $caches['cache_id'];
                        XDb::xSql($query);
                    }
                }
                XDb::xSql("commit");
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
$tpl->assign('cache_type', $caches['type']);
$tpl->assign('logpw', $caches['logpw']);
$tpl->assign('wp_oc', $wp);
$tpl->assign('date_d', date(d));
$tpl->assign('date_m', date(m));
$tpl->assign('date_Y', date(Y));
$tpl->assign('date_H', date(H));
$tpl->assign('date_i', date(i));
$tpl->display('tpl/logentry.tpl');


