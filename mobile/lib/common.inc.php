<?php

use lib\Objects\OcConfig\OcConfig;

session_start();

require_once('../lib/settingsGlue.inc.php');
require_once('./lib/OcSmarty.class.php');
require_once('./lib/functions.inc.php');
require_once("./lib/cookie.class.php");
require_once("./lib/login.class.php");
require_once('../lib/ClassPathDictionary.php');

$tpl = new OcSmarty;

if (!(isset($_SESSION['logout_cookie']))) {
    $_SESSION['logout_cookie'] = mt_rand(1000, 9999) . mt_rand(1000, 9999);
}

$show_coords = false;
if (!$hide_coords || (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0)) {
    $show_coords = true;
}
$tpl->assign('show_coords', $show_coords);
$tpl->assign('absolute_server_url', rtrim($absolute_server_URI, '/'));
$tpl->assign('site_name', rtrim(OcConfig::getSiteName(), '/'));
