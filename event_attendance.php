<?php

use Utils\Database\XDb;
//prepare the templates and include all neccessary

$tplname = 'event_attendance';
require_once('./lib/common.inc.php');

require($stylepath . '/event_attendance.inc.php');

tpl_set_var('nocacheid_start', '<!--');
tpl_set_var('nocacheid_end', '-->');
tpl_set_var('owner', '');
tpl_set_var('cachename', '');
tpl_set_var('event_date', '');


$cache_id = isset($_REQUEST['id']) ? $_REQUEST['id'] + 0 : 0;
if ($cache_id != 0) {
    $rs = XDb::xSql(
            "SELECT `caches`.`name`, `user`.`username`, `caches`.`date_hidden`
            FROM `caches`
            INNER JOIN `user` ON (`user`.`user_id`=`caches`.`user_id`)
            WHERE `caches`.`cache_id`= ? LIMIT 1", $cache_id);

    $rr = XDb::xSql(
        "SELECT `caches`.`date_hidden` `date_hidden`, CURDATE() `date_current`
        FROM `caches` WHERE `caches`.`cache_id`=? LIMIT 1", $cache_id);

    $dd = XDb::xFetchArray($rr);

    $v1 = strtotime($dd['date_hidden']);
    $v2 = strtotime($dd['date_current']);

    if ("$v1" < "$v2") {
        if ($r = XDb::xFetchArray($rs)) {
            tpl_set_var('nocacheid_start', '');
            tpl_set_var('nocacheid_end', '');

            tpl_set_var('owner', htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('cachename', htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('event_date', htmlspecialchars(strftime($dateformat, strtotime($r['date_hidden'])), ENT_COMPAT, 'UTF-8'));
        }

        // log_type 8 will attended, 7 attended
        $rs = XDb::xSql(
            "SELECT DISTINCT `user`.`username`
            FROM `cache_logs`
            INNER JOIN `user` ON (`user`.`user_id`=`cache_logs`.`user_id`)
            WHERE `cache_logs`.`type`=7
                AND `cache_logs`.`deleted`=0
                AND `cache_logs`.`cache_id`=?
            ORDER BY `user`.`username`", $cache_id);

        $attendants = '';
        $count = 0;
        while ($r = XDb::xFetchArray($rs)) {
            $attendants .= $r['username'] . '<br />';
            $count++;
        }
    } else {
        if ($r = XDb::xFetchArray($rs)) {
            tpl_set_var('nocacheid_start', '');
            tpl_set_var('nocacheid_end', '');

            tpl_set_var('owner', htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('cachename', htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('event_date', htmlspecialchars(strftime($dateformat, strtotime($r['date_hidden'])), ENT_COMPAT, 'UTF-8'));
        }

        // log_type 8 will attended, 7 attended

        $rs = XDb::xSql(
               "SELECT DISTINCT `user`.`username`
               FROM `cache_logs`
               INNER JOIN `user` ON (`user`.`user_id`=`cache_logs`.`user_id`)
               WHERE `cache_logs`.`type`=8
                    AND `cache_logs`.`deleted`=0
                    AND `cache_logs`.`cache_id`= ?
               ORDER BY `user`.`username`", $cache_id);
        $attendants = '';
        $count = 0;
        while ($r = XDb::xFetchArray($rs)) {
            $attendants .= $r['username'] . '<br />';
            $count++;
        }
    }
    tpl_set_var('attendants', $attendants);
    tpl_set_var('att_count', $count);
}

//make the template and send it out
tpl_BuildTemplate();
