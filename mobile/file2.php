<?php

    require_once("./lib/common.inc.php");

    if(!isset($_GET['wp']) || empty($_GET['wp'])){
        header('Location: ./index.php');
        exit;
    }

    $tpl -> display('tpl/file.tpl');

?>