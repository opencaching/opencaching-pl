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
        $watchers = XDb::xMultiVariableQueryValue('SELECT watcher FROM caches WHERE cache_id= :1', false, $cache_id);

        if ($watchers !== false) {
            // (check if user is not curently watching specified cache)
            $isWatched = XDb::xMultiVariableQueryValue(
                "SELECT COUNT(*) FROM `cache_watches` WHERE `cache_id` = :1 AND `user_id` = :2", 1, $cache_id, $usr['userid']);

            // if so proceed to add to database
            if ( $isWatched < 1) {

                // increase this cache watchers count
                XDb::xSql('UPDATE caches SET watcher=watcher+1  WHERE cache_id= ? ',$cache_id);

                //add watch
                XDb::xSql('INSERT INTO `cache_watches` (`cache_id`, `user_id`, `last_executed`) VALUES (?, ?, NOW())',
                    $cache_id, $usr['userid']);

                //add to user
                XDb::xSql('UPDATE user SET cache_watches=cache_watches+1 WHERE user_id= ? ',$usr['userid']);
            }
            //tpl_redirect($target);
        }
    }
}

tpl_BuildTemplate();
