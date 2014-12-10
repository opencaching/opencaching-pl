<?php

/* * *************************************************************************
  ./lib/eventhandler.inc.php
  -------------------
  begin                : Mon June 28 2004
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

  handler for events like a new cache post or a new log post

  add in the function all neccessary actions to refresh static files

 * ************************************************************************** */

function delete_statpic($userid)
{
    global $dynbasepath;
    $userid = $userid + 0;

    // data changed - delete statpic of user, if exists - will be recreated on next request
    if (file_exists($dynbasepath . 'images/statpics/statpic' . $userid . '.jpg')) {
        unlink($dynbasepath . 'images/statpics/statpic' . $userid . '.jpg');
    }
}

function event_new_cache($userid)
{
    delete_statpic($userid);
}

function event_new_log($cacheid, $userid)
{
    delete_statpic($userid);
}

function event_change_log_type($cacheid, $userid)
{
    delete_statpic($userid);
}

function event_remove_log($cacheid, $userid)
{
    delete_statpic($userid);
}

function event_edit_cache($cacheid, $userid)
{
    delete_statpic($userid);
}

function event_change_statpic($userid)
{
    delete_statpic($userid);
}

function event_notify_new_cache($cache_id)
{
    global $rootpath;

    //prepare the templates and include all neccessary
    require_once($rootpath . 'lib/search.inc.php');

    $rs = sql('SELECT `caches`.`latitude`, `caches`.`longitude`
           FROM `caches`
           WHERE `caches`.`cache_id`=&1', $cache_id);

    $r = sql_fetch_array($rs);

    $latFrom = $r['latitude'];
    $lonFrom = $r['longitude'];

    mysql_free_result($rs);

    $distanceMultiplier = 1;

    // TODO: Seeking pre-select `user`. `latitude` like with max_lon / min_lon / max_lat / min_lat
    sql('INSERT INTO `notify_waiting` (`id`, `cache_id`, `user_id`, `type`)
        SELECT NULL, &4, `user`.`user_id`, &5
        FROM `user`
        WHERE NOT ISNULL(`user`.`latitude`)
          AND NOT ISNULL(`user`.`longitude`)
          AND `user`.`notify_radius` > 0
          AND (acos(cos((90-&1) * 3.14159 / 180) * cos((90-`user`.`latitude`) * 3.14159 / 180) +
              sin((90-&1) * 3.14159 / 180) * sin((90-`user`.`latitude`) * 3.14159 / 180) * cos((&2-`user`.`longitude`) *
              3.14159 / 180)) * 6370 * &3) <= `user`.`notify_radius`', $latFrom, $lonFrom, $distanceMultiplier, $cache_id, notify_new_cache);
}

?>
