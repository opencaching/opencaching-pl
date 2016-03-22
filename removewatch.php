<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'mywatches.php';
    $cacheId = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';
    $userId = $usr['userid'];

    if ($userId) {
        if ($cacheId == 'all') {
            //stop watching any caches

            //remove from caches
            XDb::xSql('UPDATE caches SET watcher=watcher-1 WHERE watcher > 0 AND
                       `cache_id` IN ( SELECT cache_id FROM cache_watches WHERE user_id = ?)', $userId);

            //remove watch
            XDb::xSql('DELETE FROM cache_watches WHERE user_id= ?', $userId);

            //remove from user
            XDb::xSql('UPDATE user SET cache_watches=0 WHERE user_id=? ',$userId);
        } else {

            // check if user really watching specified cache
            $isWatcher = XDb::xMultiVariableQueryValue(
                "SELECT count(*) FROM `cache_watches` WHERE `cache_id` = :1 AND `user_id` = :2",
                0, $cacheId, $userId);

            // if so proceed to remove stop watching
            if ( $isWatcher >= 1 ){

                //remove watch
                XDb::xSql('DELETE FROM cache_watches
                           WHERE cache_id= ? AND user_id= ?',$cacheId, $userId);

                //remove from caches
                XDb::xSql('UPDATE caches SET watcher=watcher-1
                           WHERE cache_id=? AND watcher > 0',$cacheId);

                //remove from user
                XDb::xSql('UPDATE user SET cache_watches=cache_watches-1
                           WHERE user_id=? AND cache_watches > 0',$userId);

            }
        }
    }
    tpl_redirect($target);
}

tpl_BuildTemplate();
