<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

    require('./lib/web.inc.php');

    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'myignore.php';
    $cache_id = isset($_GET['cacheid']) ? $_GET['cacheid']+0 : 0;
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    // cache_id valid?
    if (sql_value("SELECT COUNT(*) FROM `caches` WHERE `cache_id`='&1'", 0, $cache_id) == 0)
        $tpl->error(ERROR_CACHE_NOT_EXISTS);

    // action valid
    if (($action != 'addignore') && ($action != 'removeignore'))
        $tpl->error(ERROR_INVALID_OPERATION);

    // user valid
    if ($login->userid == 0)
        $tpl->error(ERROR_LOGIN_REQUIRED);

    switch ($action)
    {
        case 'addignore':
            sql("INSERT IGNORE INTO `cache_ignore` (`cache_id`, `user_id`) VALUES ('&1', '&2')", $cache_id, $login->userid);
            break;
        case 'removeignore':
            sql("DELETE FROM `cache_ignore` WHERE `cache_id`='&1' AND `user_id`='&2'", $cache_id, $login->userid);
            break;
    }

    tpl_redirect($target);

    tpl_BuildTemplate();
?>
