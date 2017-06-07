<?php

require_once('./lib/common.inc.php');

if (isset($_GET['token']) && isset($_SESSION['logout_cookie']) && $_GET['token'] == $_SESSION['logout_cookie']) {

    global $login, $usr;

    if ($login->userid != 0) {
        $login->logout();
    }

    $usr = false;
    $_SESSION = array();
    session_destroy();
}

$target = isset($_GET['target']) ? urldecode($target) : "index.php";
tpl_redirect($target);

