<?php

use lib\Objects\User\UserAuthorization;

require_once('./lib/common.inc.php');

if (isset($_GET['token']) && isset($_SESSION['logout_cookie'])
    && $_GET['token'] == $_SESSION['logout_cookie']) {

    UserAuthorization::logout();
}

$target = isset($_GET['target']) ? urldecode($target) : "index.php";
tpl_redirect($target);

