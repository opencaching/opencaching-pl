<?php

    require_once("./lib/common.inc.php");

    if (isset($_GET['token']) && isset($_SESSION['logout_cookie']) 
            && $_GET['token'] == $_SESSION['logout_cookie'])
    {
        if(isset($_SESSION['user_id']))
            $login->logout();
    }
    header('Location: ./index.php');

?>