<?php

    require_once("./lib/common.inc.php");

    if(isset($_SESSION['user_id']))
        $login->logout();

    header('Location: ./index.php');

?>