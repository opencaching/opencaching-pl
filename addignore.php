<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'myignores.php';

    if ($usr !== false) {
        //add to caches
        $rs = XDb::xQuery('SELECT ignorer_count FROM caches WHERE cache_id=\'' . XDb::xEscape($cache_id) . '\'');
        if (XDb::xNumRows($rs) > 0) {
            $record = Xdb::xFetchArray($rs);
            XDb::xSql('UPDATE caches SET ignorer_count=\'' . ($record['ignorer_count'] + 1) . '\'
                       WHERE cache_id=\'' . XDb::xEscape($cache_id) . '\'');

            //add watch
            XDb::xSql('INSERT INTO `cache_ignore` (`cache_id`, `user_id`)
                       VALUES (\'' . XDb::xEscape($cache_id) . '\', \'' . XDb::xEscape($usr['userid']) . '\')');


            //add to user
            $rs = XDb::xSql('SELECT cache_ignores FROM user WHERE user_id=\'' . XDb::xEscape($usr['userid']) . '\'');
            $record = XDb::xFetchArray($rs);
            XDb::xSql('UPDATE user SET cache_ignores=\'' . ($record['cache_ignores'] + 1) . '\' WHERE user_id=\'' . XDb::xEscape($usr['userid']) . '\'');

            tpl_redirect($target);
        }
    }
}

tpl_BuildTemplate();
?>
