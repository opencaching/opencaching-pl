<?php

use lib\Objects\GeoCache\PrintList;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'mylist.php';
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';

    if ($usr['userid']) {
        if ($cache_id == 'all') {
            PrintList::Flush();
        } else {
            PrintList::RemoveCache($cache_id);
        }
    }
    tpl_redirect($target);
}

tpl_BuildTemplate();

