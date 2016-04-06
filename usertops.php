<?php

use Utils\Database\XDb;
use Utils\Database\OcDb;

require('./lib/common.inc.php');
require($stylepath . '/usertops.inc.php');

if ($error == false) {
    $userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] + 0 : 0;

    $tplname = 'usertops';

    $username = XDb::xMultiVariableQueryValue(
        "SELECT `username` FROM `user` WHERE `user_id`= :1 LIMIT 1", null, $userid);

    if(!is_null($username)){
        // user found
        tpl_set_var('userid', $userid);
        tpl_set_var('username', $username);
    }else{
        // user not found
        tpl_set_var('userid', 0);
        tpl_set_var('username', '-Not Found-');
        $userid = 0;
        $username = "-Not found-";
        $notop5 = $user_notfound;
    }

    $i = 0;
    $content = '';

    $dbc = OcDb::instance();
    $dbc->multiVariableQuery(
        "SELECT `cache_rating`.`cache_id` AS `cache_id`, `caches`.`name` AS `cachename`,
                `user`.`username` AS `ownername`, `user`.`user_id` AS `owner_id`
        FROM `cache_rating`, `caches`, `user`
        WHERE `cache_rating`.`cache_id` = `caches`.`cache_id`
            AND `caches`.`user_id`=`user`.`user_id`
            AND `cache_rating`.`user_id`= :1 ORDER BY `caches`.`name` ASC", $userid);

    if ($dbc->rowCount() != 0) {
        while ($r = $dbc->dbResultFetch()) {
            $thisline = $viewtop5_line;

            $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{cacheid}', htmlspecialchars($r['cache_id'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{ownername}', htmlspecialchars($r['ownername'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{owner_id}', htmlspecialchars($r['owner_id'], ENT_COMPAT, 'UTF-8'), $thisline);

            if (($i % 2) == 1)
                $thisline = mb_ereg_replace('{bgcolor}', $bgcolor2, $thisline);
            else
                $thisline = mb_ereg_replace('{bgcolor}', $bgcolor1, $thisline);

            $content .= $thisline;
            $i++;
        }
        unset($dbc);
    }
    else {
        $content = mb_ereg_replace('{username}', $username, $notop5);
    }

    tpl_set_var('top5', $content);
    tpl_BuildTemplate();
}
