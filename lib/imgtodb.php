<?php
    $rootpath = "../";
    require_once('./common.inc.php');

    echo $box = imagettfbbox(1.7*$zoom-11, 0, '../util.sec/bt.ttf', "gafaw");
    print_r($box);
?>
