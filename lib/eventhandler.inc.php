<?php

use Utils\Database\XDb;
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

    $rs = XDb::xSql(
        'SELECT `caches`.`latitude`, `caches`.`longitude`
        FROM `caches`
        WHERE `caches`.`cache_id`= ? ', $cache_id);

    $r = XDb::xFetchArray($rs);

    $latFrom = $r['latitude'];
    $lonFrom = $r['longitude'];

    XDb::xFreeResults($rs);

    $distanceMultiplier = 1;

    // TODO: Seeking pre-select `user`. `latitude` like with max_lon / min_lon / max_lat / min_lat
    XDb::xSql(
        'INSERT INTO `notify_waiting` (`id`, `cache_id`, `user_id`, `type`)
        SELECT NULL, '.XDb::xEscape($cache_id).', `user`.`user_id`, '.NOTIFY_NEW_CACHES.'
        FROM `user`
        WHERE NOT ISNULL(`user`.`latitude`)
          AND NOT ISNULL(`user`.`longitude`)
          AND `user`.`notify_radius` > 0
          AND (acos(cos((90 - ? ) * PI() / 180) * cos((90-`user`.`latitude`) * PI() / 180) +
              sin((90-?) * PI() / 180) * sin((90-`user`.`latitude`) * PI() / 180) * cos(( ? -`user`.`longitude`) *
              PI() / 180)) * 6370 * ?) <= `user`.`notify_radius`',
        $latFrom, $latFrom, $lonFrom, $distanceMultiplier);

}
