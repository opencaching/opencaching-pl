<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

function onTheList($theArray, $item)
{
    for ($i = 0; $i < count($theArray); $i++) {
        if ($theArray[$i] == $item)
            return $i;
    }
    return -1;
}

//Preprocessing
if ($error == false) {
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'mylist.php';
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';

    if ($usr['userid']) {
        if ($cache_id == 'all') {
            unset($_SESSION['print_list']);
            $_SESSION['print_list'] = array();
        } else {
            while (onTheList($_SESSION['print_list'], $cache_id) != -1)
                unset($_SESSION['print_list'][onTheList($_SESSION['print_list'], $cache_id)]);
            $_SESSION['print_list'] = array_values($_SESSION['print_list']);
        }
    }
    tpl_redirect($target);
}

tpl_BuildTemplate();
?>
