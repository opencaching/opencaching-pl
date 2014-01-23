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

    require_once('./lib/lang.inc.php');

?>