<?php

/* * *************************************************************************
  ./removewatch.php
  -------------------
  begin                : July 25 2004
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  remove a watch from the watchlist

 * ************************************************************************** */

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'mywatches.php';
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';

    if ($usr['userid']) {
        if ($cache_id == 'all') {
            $rs = sql('SELECT cache_id FROM cache_watches WHERE user_id = \'' . sql_escape($usr['userid']) . '\'');
            if (mysql_num_rows($rs) > 0) {
                for ($i = 0; $i < mysql_num_rows($rs); $i++) {
                    $record = sql_fetch_array($rs);
                    remove_watch($record['cache_id'], $usr);
                }
            }
        } else {
            // sprawdzenie czy user rzeczywiście obserwuje keszynkę
            // (check if really user is watching specified cache)
            $id_usera = sql_escape($usr['userid']);
            $id_keszynki = sql_escape($cache_id);
            $czy_user_obserwuje_kesz = mysql_num_rows(mysql_query("SELECT `id` FROM `cache_watches` WHERE `cache_id` = $id_keszynki AND `user_id` = $id_usera"));
            // jeśli tak, usuwamy wpisy z bazy
            // (if so proceed to remove from database)
            if ($czy_user_obserwuje_kesz >= 1)
                remove_watch($cache_id, $usr);
        }
    }
    tpl_redirect($target);
}

tpl_BuildTemplate();

function remove_watch($cache_id, $usr)
{
    //remove watch
    sql('DELETE FROM cache_watches WHERE cache_id=\'' . sql_escape($cache_id) . '\' AND user_id=\'' . sql_escape($usr['userid']) . '\'');
    //remove from caches
    $rs = sql('SELECT watcher FROM caches WHERE cache_id=\'' . sql_escape($cache_id) . '\'');
    if (mysql_num_rows($rs) > 0) {
        $record = mysql_fetch_array($rs);
        sql('UPDATE caches SET watcher=\'' . ($record['watcher'] - 1) . '\' WHERE cache_id=\'' . sql_escape($cache_id) . '\'');
        //remove from user
        $rs = sql('SELECT cache_watches FROM user WHERE user_id=\'' . sql_escape($usr['userid']) . '\'');
        $record = mysql_fetch_array($rs);
        sql('UPDATE user SET cache_watches=\'' . ($record['cache_watches'] - 1) . '\' WHERE user_id=\'' . sql_escape($usr['userid']) . '\'');
    }
}

?>
