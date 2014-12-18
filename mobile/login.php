<?php

require_once("./lib/common.inc.php");

if (!isset($_SESSION['user_id'])) {

    if (!isset($_SESSION['target']))
        $_SESSION['target'] = $_SERVER['HTTP_REFERER'];
    if (empty($_SESSION['target']))
        $_SESSION['target'] = "./index.php";

    $cookie->set('test', '1');
    $cookie->header();

    if (isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['pass']) && !empty($_POST['pass'])) {

        if ($cookie->is_set_cookie() && $cookie->is_set('test') && $cookie->get('test') == '1') {

            $cookie->un_set('test');

            db_connect();

            $username = mysql_real_escape_string($_POST['username']);
            $pass = mysql_real_escape_string($_POST['pass']);

            $remember = (isset($_POST['remember']) ? 1 : 0);

            $login->try_login($username, $pass, $remember);

            if ($login->userid == '0')
                $tpl->assign("error", "1");
            else {
                $temp_target = $_SESSION['target'];
                unset($_SESSION['target']);
                header('Location: ' . $temp_target);
                exit;
            }
        } else
            $tpl->assign("error", "2");
    }
}else {
    header('Location: ./index.php');
    exit;
}

$tpl->display('tpl/login.tpl');
?>