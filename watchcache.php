<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'mywatches.php';

    if ($usr !== false) {
        //add to caches
        $geocacheExist = XDb::xMultiVariableQueryValue('SELECT user_id FROM caches WHERE cache_id= :1', -1,  $cache_id);

        if ($geocacheExist > -1) {
            // (check if user is not curently watching specified cache)
            $isWatched = XDb::xMultiVariableQueryValue(
                "SELECT COUNT(*) FROM `cache_watches` WHERE `cache_id` = :1 AND `user_id` = :2", 1, $cache_id, $usr['userid']);

            // if so proceed to add to database
            if ( $isWatched < 1) {

                //add watch
                XDb::xSql('INSERT INTO `cache_watches` (`cache_id`, `user_id`, `last_executed`) VALUES (?, ?, NOW())',
                    $cache_id, $usr['userid']);

             }
            tpl_redirect($target);
        }
    }
}

tpl_BuildTemplate();
