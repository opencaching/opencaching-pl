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
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'myignores.php';
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';

    if ($usr['userid']) {
        //remove watch
        sql('DELETE FROM cache_ignore WHERE cache_id=\'' . sql_escape($cache_id) . '\' AND user_id=\'' . sql_escape($usr['userid']) . '\'');

        //remove from caches
        $rs = sql('SELECT ignorer_count FROM caches WHERE cache_id=\'' . sql_escape($cache_id) . '\'');
        if (mysql_num_rows($rs) > 0) {
            $record = mysql_fetch_array($rs);
            sql('UPDATE caches SET ignorer_count=\'' . ($record['ignorer_count'] - 1) . '\' WHERE cache_id=\'' . sql_escape($cache_id) . '\'');

            //remove from user
            $rs = sql('SELECT cache_ignores FROM user WHERE user_id=\'' . sql_escape($usr['userid']) . '\'');
            $record = mysql_fetch_array($rs);
            sql('UPDATE user SET cache_ignores=\'' . ($record['cache_ignores'] - 1) . '\' WHERE user_id=\'' . sql_escape($usr['userid']) . '\'');
        }
    }

    tpl_redirect($target);
}

tpl_BuildTemplate();
?>
