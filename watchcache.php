<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'mywatches.php';

    if ($usr !== false) {
        //add to caches
        $rs = mysql_query('SELECT watcher FROM caches WHERE cache_id=\'' . sql_escape($cache_id) . '\'');
        if (mysql_num_rows($rs) > 0) {
            // sprawdzenie czy user nie obserwuje już keszynki
            // (check if user is not curently watching specified cache)
            $id_usera = sql_escape($usr['userid']);
            $id_keszynki = sql_escape($cache_id);
            $czy_user_obserwuje_kesz = mysql_num_rows(mysql_query("SELECT `id` FROM `cache_watches` WHERE `cache_id` = $id_keszynki AND `user_id` = $id_usera"));
            // jeśli tak, dodajemy wpis do bazy
            // (if so proceed to remove from database)
            if ($czy_user_obserwuje_kesz < 1) {
                $record = mysql_fetch_array($rs);
                sql('UPDATE caches SET watcher=\'' . ($record['watcher'] + 1) . '\' WHERE cache_id=\'' . sql_escape($cache_id) . '\'');

                //add watch
                sql('INSERT INTO `cache_watches` (`cache_id`, `user_id`, `last_executed`) VALUES (\'' . sql_escape($cache_id) . '\', \'' . sql_escape($usr['userid']) . '\', NOW())');

                //add to user
                $rs = sql('SELECT cache_watches FROM user WHERE user_id=\'' . sql_escape($usr['userid']) . '\'');
                $record = mysql_fetch_array($rs);
                sql('UPDATE user SET cache_watches=\'' . ($record['cache_watches'] + 1) . '\' WHERE user_id=\'' . sql_escape($usr['userid']) . '\'');
            }
            tpl_redirect($target);
        }
    }
}

tpl_BuildTemplate();
?>
