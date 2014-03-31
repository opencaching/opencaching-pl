<?php

    session_start();

    require_once('../lib/settings.inc.php');
    require_once('./lib/smarty/Smarty.class.php');
    require_once('./lib/functions.inc.php');
    require_once("./lib/cookie.class.php");
    require_once("./lib/login.class.php");

    $tpl = new Smarty;
    $tpl -> template_dir = $dynbasepath . 'lib/templates/';
    $tpl -> compile_dir = $dynbasepath .'lib/templates_c/';

    $show_coords = false;
    if (!$hide_coords || (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0)){
        $show_coords = true;
    }
    $tpl -> assign('show_coords', $show_coords);
    $tpl -> assign('absolute_server_url', rtrim($absolute_server_URI, '/'));
    
    require_once('./lib/lang.inc.php');

?>