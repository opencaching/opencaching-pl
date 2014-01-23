<?php

    require_once("./lib/common.inc.php");

    if(isset($_SESSION['user_id'])) {
        $address="logentry";
        $action=$_SERVER["PHP_SELF"];
        require_once("./lib/findalgo.inc.php");
    }else
        header('Location: ./index.php');

?>