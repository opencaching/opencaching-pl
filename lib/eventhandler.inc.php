<?php
use lib\Objects\Notify\Notify;

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
    Notify::generateNotifiesForCache($cache_id);
}