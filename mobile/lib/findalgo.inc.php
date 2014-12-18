<?php

global $action;

if (isSet($_GET['nazwa']) && !empty($_GET['nazwa']) ||
        isSet($_GET['wp']) && !empty($_GET['wp']) && $_GET['wp'] != "OP" ||
        isSet($_GET['owner']) && !empty($_GET['owner']) ||
        isSet($_GET['finder']) && !empty($_GET['finder'])) {

    db_connect();

    function find_news($start, $end)
    {

        global $lang;
        global $ile;
        global $url;
        global $tpl;
        global $znalezione;

        if (isSet($_GET['nazwa'])) {
            $nazwa = mysql_real_escape_string($_GET['nazwa']);
            $query = "select votes,cache_id,name, status, score, latitude, longitude, wp_oc, user_id, type from caches where name like '%" . $nazwa . "%' and caches.status in ('1','2','3') order by name limit " . $start . "," . $end;
            $czykilka = 1;
            $url = "./find.php?nazwa=" . $nazwa;
        }

        if (isSet($_GET['wp'])) {
            $wp = mysql_real_escape_string($_GET['wp']);
            $query = "select votes,cache_id,name, status, score, latitude, longitude, wp_oc, user_id, type from caches where wp_oc = '" . $wp . "' and caches.status in ('1','2','3') order by name limit " . $start . "," . $end;
            $czykilka = 0;
            $url = "./find.php?wp=" . $wp;
        }

        if (isSet($_GET['owner'])) {
            $owner = mysql_real_escape_string($_GET['owner']);
            $query = "select votes,cache_id,name, status, score, latitude, longitude, wp_oc, user_id, type from caches where user_id = (select user_id from user where username ='";
            $query .= $owner . "') and caches.status in ('1','2','3') order by name limit " . $start . "," . $end;
            $czykilka = 1;
            $url = "./find.php?owner=" . $owner;
        }

        if (isSet($_GET['finder'])) {
            $finder = mysql_real_escape_string($_GET['finder']);
            $query = "select caches.votes,caches.cache_id,name, status, score, latitude, longitude, wp_oc, caches.user_id, caches.type from caches inner join cache_logs on caches.cache_id=cache_logs.cache_id where cache_logs.user_id = (select user.user_id from user where username ='";
            $query .= $finder . "') and cache_logs.type = '1' and cache_logs.deleted=0 and caches.status in ('1','2','3') order by cache_logs.id desc limit " . $start . "," . $end;
            $czykilka = 1;
            $url = "./find.php?finder=" . $finder;
        }

        $wynik = db_query($query);
        $ilewyn = mysql_num_rows($wynik);


        if ($czykilka == 0) {
            if ($ilewyn > 0) {
                global $address;
                $wiersz = mysql_fetch_assoc($wynik);
                $adres = "./" . $address . ".php?wp=" . $wiersz['wp_oc'];
                header('Location: ' . $adres);
                exit;
            } else {
                $tpl->assign("error", "1");
            }
        }

        if ($czykilka == 1) {

            $znalezione = array();

            while ($rekord = mysql_fetch_assoc($wynik)) {

                if (isset($_SESSION['user_id'])) {
                    $query2 = "select 1 from cache_logs where user_id = '" . $_SESSION['user_id'] . "' and type = '1' and deleted='0' and cache_id ='" . $rekord['cache_id'] . "';";
                    $wynik2 = db_query($query2);
                    $if_found = mysql_fetch_row($wynik2);

                    if ($if_found[0] != '1') {
                        $query2 = "select 2 from cache_logs where user_id = '" . $_SESSION['user_id'] . "' and type = '2' and deleted='0' and cache_id ='" . $rekord['cache_id'] . "';";
                        $wynik2 = db_query($query2);
                        $if_found = mysql_fetch_row($wynik2);
                    }

                    $if_found = $if_found[0];
                }

                $query = "select username from user where user_id = " . $rekord['user_id'] . ";";
                $wynik2 = db_query($query);
                $wiersz = mysql_fetch_assoc($wynik2);

                $query = "select " . $lang . " from cache_type where id = " . $rekord['type'] . ";";
                $wynik2 = db_query($query);
                $wiersz2 = mysql_fetch_row($wynik2);


                if ($rekord['votes'] > 3)
                    $rekord['score'] = score2ratingnum($rekord['score']);
                else
                    $rekord['score'] = 5;

                $rekord['username'] = $wiersz['username'];

                $rekord['if_found'] = $if_found;
                $rekord['N'] = cords($rekord['latitude']);
                $rekord['E'] = cords($rekord['longitude']);
                $rekord['typetext'] = $wiersz2[0];

                $znalezione [] = $rekord;
            }
        }
    }

    if (isSet($_GET['nazwa']) && strlen($_GET['nazwa']) < 3) {
        $tpl->assign("error", "2");
    } else {

        global $tpl;

        $na_stronie = 10;

        if (isSet($_GET['nazwa'])) {
            $nazwa = mysql_real_escape_string($_GET['nazwa']);
            $query2 = "select wp_oc from caches where name like '%" . $nazwa . "%' and caches.status in ('1','2','3')";
            $wynik = db_query($query2);
            $ile = mysql_num_rows($wynik);
        }

        if (isSet($_GET['wp'])) {
            $wp = mysql_real_escape_string($_GET['wp']);
            $query2 = "select wp_oc from caches where wp_oc = '" . $wp . "' and caches.status in ('1','2','3')";
            $wynik = db_query($query2);
            $ile = mysql_num_rows($wynik);
        }

        if (isSet($_GET['owner'])) {
            $owner = mysql_real_escape_string($_GET['owner']);
            $query2 = "select wp_oc from caches where user_id = (select user_id from user where username ='";
            $query2 .= $owner . "') and caches.status in ('1','2','3')";
            $wynik = db_query($query2);
            $ile = mysql_num_rows($wynik);
        }

        if (isSet($_GET['finder'])) {
            $finder = mysql_real_escape_string($_GET['finder']);
            $query2 = "select DISTINCT wp_oc from caches inner join cache_logs on caches.cache_id = cache_logs.cache_id where cache_logs.user_id = (select user.user_id from user where username ='";
            $query2 .= $finder . "') and cache_logs.type = '1' and cache_logs.deleted='0' and caches.status in ('1','2','3')";
            $wynik = db_query($query2);
            $ile = mysql_num_rows($wynik);
        }

        while ($rekord = mysql_fetch_assoc($wynik))
            $lista[] = $rekord['wp_oc'];

        $tpl->assign('lista', $lista);

        require_once("./lib/paging.inc.php");

        $tpl->assign('action', $action);
        $tpl->assign('url', $url);
        $tpl->assign('next_page', $next_page);
        $tpl->assign('prev_page', $prev_page);
        $tpl->assign("address", $address);
        $tpl->assign("znalezione", $znalezione);
        $tpl->assign("ile", $ile);

        $max = ceil($ile / $na_stronie);

        if ($max == '0')
            $max = '1';

        $tpl->assign('max', $max);

        $tpl->display('tpl/find2.tpl');

        exit;
    }
}

$tpl->assign('action', $action);
$tpl->display('tpl/find.tpl');
?>