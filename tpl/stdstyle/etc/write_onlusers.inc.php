<?php

use Utils\Database\OcDb;

//include template handling
$rootpath = '../../../';
require_once($rootpath . 'lib/common.inc.php');

// add check users id who want to by username hidden
$db = OcDb::instance();
$stmt = $db->simpleQuery(
    "SELECT `sys_sessions`.`user_id` AS `user_id`, `user`.`username` AS `username`
        FROM `sys_sessions`
        LEFT JOIN `user`
        ON `user`.`user_id` = `sys_sessions`.`user_id`
        WHERE `sys_sessions`.`user_id` != 1 AND `sys_sessions`.`last_login` > (NOW() - INTERVAL 10 MINUTE) ");

$n_file = fopen($dynstylepath . "nonlusers.txt", 'w');
fwrite($n_file, $db->rowCount($stmt));
fclose($n_file);

$n_file = fopen($dynstylepath . "onlineusers.html", 'w');
$first = true;
while ($record = $db->dbResultFetch($stmt)) {
    if ($first) {
        $first = false;
    } else {
        fwrite($n_file, ', ');
    }
    fwrite($n_file, '<a class="links-onlusers" href="viewprofile.php?userid=' . $record['user_id'] . '">' . $record['username'] . '</a>');
}
fclose($n_file);
