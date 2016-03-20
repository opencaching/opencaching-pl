<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'myignores.php';
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';

    if ($usr['userid']) {
        //remove watch
        XDb::xSql('DELETE FROM cache_ignore
                   WHERE cache_id=\'' . XDb::xEscape($cache_id) . '\'
                        AND user_id=\'' . XDb::xEscape($usr['userid']) . '\'');

        //remove from caches
        $rs = XDb::xSql('SELECT ignorer_count FROM caches
                         WHERE cache_id=\'' . XDb::xEscape($cache_id) . '\'');
        if (XDb::xNumRows($rs) > 0) {
            $record = XDb::xFetchArray($rs);
            XDb::xSql('UPDATE caches SET ignorer_count=\'' . ($record['ignorer_count'] - 1) . '\'
                       WHERE cache_id=\'' . XDb::xEscape($cache_id) . '\'');

            //remove from user
            $rs = XDb::xSql('SELECT cache_ignores FROM user WHERE user_id=\'' . XDb::xEscape($usr['userid']) . '\'');
            $record = XDb::xFetchArray($rs);
            XDb::xSql('UPDATE user SET cache_ignores=\'' . ($record['cache_ignores'] - 1) . '\'
                       WHERE user_id=\'' . XDb::xEscape($usr['userid']) . '\'');
        }
    }

    tpl_redirect($target);
}

tpl_BuildTemplate();
