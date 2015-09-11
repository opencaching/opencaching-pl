<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    if (isset($_GET['token']) && isset($_SESSION['logout_cookie']) && $_GET['token'] == $_SESSION['logout_cookie']) {
        //load language specific variables
        require_once($stylepath . '/login.inc.php');
        if (auth_logout() == true) {
            $_SESSION = array();
            session_destroy();
        }
    }
}

$target = isset($_GET['target']) ? $target : "index.php";
header('Location: ' . urlencode($target));
die()
?>
