<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //get the news
    $tplname = 'html/ratings';
}

//make the template and send it out
tpl_BuildTemplate();
?>
