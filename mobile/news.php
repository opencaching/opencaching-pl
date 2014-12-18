<?php

require_once("./lib/common.inc.php");

db_connect();

function find_news($start, $end)
{

    global $tpl;
    $query = "SELECT id,date_posted,content FROM `news` order by id desc limit " . $start . "," . $end;
    $wynik = db_query($query);
    $ile = mysql_num_rows($wynik);
    $znalezione = Array();

    while ($odp2 = mysql_fetch_assoc($wynik)) {
        $odp['date_posted'] = $odp2['date_posted'];
        //$odp['content']=strip_tags($odp2['content'],'<b></b><p></p><a></a><br><br/>');
        $odp['content'] = html2desc($odp2['content']);
        $znalezione[] = $odp;
    }

    $tpl->assign('news', $znalezione);
    return $ile;
}

$na_stronie = 5;

$query = "select count(*) from news";
$wynik = db_query($query);
$ile = mysql_fetch_row($wynik);
$ile = $ile[0];
$tpl->assign('ile', $ile);

if ($ile <= $na_stronie)
    find_news(0, $na_stronie);
else {
    if (!isset($_GET['page'])) {
        find_news(0, $na_stronie);
        $next_page = '2';
    } elseif (isset($_GET['page']) && !empty($_GET['page']) && preg_match("/^\d+$/", $_GET['page'])) {

        $ile_wynikow = find_news(($_GET['page'] - 1) * $na_stronie, $na_stronie);

        if ($ile_wynikow == 0) {
            header('Location: ./news.php?page=1');
            exit;
        } else {
            if (((($_GET['page'] - 1) * $na_stronie) + $na_stronie) <= $ile)
                $next_page = $_GET['page'] + 1;
            $prev_page = $_GET['page'] - 1;
        }
    }
}

$tpl->assign('max', ceil($ile / $na_stronie));
$tpl->assign('next_page', $next_page);
$tpl->assign('prev_page', $prev_page);

$tpl->display('tpl/news.tpl');
?>