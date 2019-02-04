<?php

use lib\Objects\GeoCache\PrintList;

require_once (__DIR__.'/lib/common.inc.php');


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
