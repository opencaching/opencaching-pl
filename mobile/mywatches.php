<?php
use Utils\Database\XDb;
use Utils\I18n\I18n;
require_once("./lib/common.inc.php");

if (isset($_SESSION['user_id'])) {



    function find_news($start, $end)
    {
        global $ile;
        global $url;
        global $znalezione;

        $query = "select cache_id from cache_watches where user_id=" . $_SESSION['user_id'] . " limit " . $start . "," . $end;
        $wynik = XDb::xSql($query);
        $ile2 = XDb::xNumRows($wynik);

        if ($ile2 > 0) {
            $znalezione = array();
            while ($rek = XDb::xFetchArray($wynik)) {

                $query = "select status,cache_id,name, score, latitude, longitude, wp_oc, user_id, type from caches where cache_id=" . $rek['cache_id'] . " order by name";
                $wynik2 = XDb::xSql($query);

                while ($rekord = XDb::xFetchArray($wynik2)) {

                    if (isset($_SESSION['user_id'])) {
                        $query2 = "select 1 from cache_logs where user_id = '" . $_SESSION['user_id'] . "' and type = '1' and deleted='0' and cache_id ='" . $rekord['cache_id'] . "';";
                        $wynik2 = XDb::xSql($query2);
                        $if_found = XDb::xFetchArray($wynik2);

                        if ($if_found[0] != '1') {
                            $query2 = "select 2 from cache_logs where user_id = '" . $_SESSION['user_id'] . "' and type = '2' and deleted='0' and cache_id ='" . $rekord['cache_id'] . "';";
                            $wynik2 = XDb::xSql($query2);
                            $if_found = XDb::xFetchArray($wynik2);
                        }

                        $if_found = $if_found[0];
                    }

                    $query = "select username from user where user_id = " . $rekord['user_id'] . ";";
                    $wynik2 = XDb::xSql($query);
                    $wiersz = XDb::xFetchArray($wynik2);

                    $query = "select " . I18n::getCurrentLang() . " from cache_type where id = " . $rekord['type'] . ";";
                    $wynik2 = XDb::xSql($query);
                    $wiersz2 = XDb::xFetchArray($wynik2);
                    $rekord['if_found'] = $if_found;
                    $rekord['username'] = $wiersz['username'];

                    $rekord['N'] = cords($rekord['latitude']);
                    $rekord['E'] = cords($rekord['longitude']);
                    $rekord['typetext'] = $wiersz2[0];

                    $znalezione [] = $rekord;
                }
            }
        }
    }

    $query = "select wp_oc from caches inner join cache_watches on caches.cache_id=cache_watches.cache_id where cache_watches.user_id=" . $_SESSION['user_id'];
    $wynik = XDb::xSql($query);
    $ile = XDb::xNumRows($wynik);

    while ($rekord = XDb::xFetchArray($wynik))
        $lista[] = $rekord['wp_oc'];

    $tpl->assign('lista', $lista);

    $na_stronie = 10;
    $url = $_SERVER['REQUEST_URI'] . '?a=1';
    $address = 'viewcache';

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
} else
    header('Location: ./index.php');
?>