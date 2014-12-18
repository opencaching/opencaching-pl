<?php

require_once("./lib/common.inc.php");

if (isset($_SESSION['user_id'])) {

    db_connect();

    if (isset($_POST['username_find']) && !empty($_POST['username_find'])) {

        $username_find = mysql_real_escape_string($_POST['username_find']);
        $query = "select user_id from user where username = '" . $username_find . "'";
        $wynik = db_query($query);
        $wiersz = mysql_fetch_assoc($wynik);

        if (empty($wiersz['user_id']))
            $tpl->assign('error', 1);
        else {
            $adres = './user.php?id=' . $wiersz['user_id'];
            header('Location: ' . $adres);
        }
    } elseif (isset($_GET['id']) && !empty($_GET['id']) && preg_match("/^\d+$/", $_GET['id'])) {

        $id = mysql_real_escape_string($_GET['id']);
        $query = "select username,country,date_created,hidden_count,log_notes_count,notfounds_count  from user where user_id = '" . $id . "'";
        $wynik = db_query($query);
        $wiersz = mysql_fetch_assoc($wynik);

        $query = "select count(*) from cache_logs where cache_logs.user_id = '" . $id . "' and cache_logs.type = '1' and cache_logs.deleted='0'";
        $wynik = db_query($query);
        $wiersz2 = mysql_fetch_row($wynik);

        if (empty($wiersz['username']))
            $tpl->assign('error', 1);
        else {
            $tpl->assign('user_id', $id);
            $tpl->assign('username', $wiersz['username']);
            $tpl->assign('country', $wiersz['country']);
            $tpl->assign('date_created', date("d-m-Y", strtotime($wiersz['date_created'])));
            $tpl->assign('hidden_count', $wiersz['hidden_count']);
            $tpl->assign('log_notes_count', $wiersz['log_notes_count']);
            $tpl->assign('founds_count', $wiersz2[0]);
            $tpl->assign('notfounds_count', $wiersz['notfounds_count']);
        }
    }

    $tpl->display('tpl/user.tpl');
} else
    header('Location: ./login.php');
?>