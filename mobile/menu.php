<?php

require_once("./lib/common.inc.php");

if (isset($_SESSION['user_id']))
    $tpl->display('tpl/menu.tpl');
else
    header('Location: ./index.php');
?>