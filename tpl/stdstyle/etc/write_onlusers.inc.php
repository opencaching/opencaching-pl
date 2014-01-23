<?php
    /***************************************************************************
        *
        *   This program is free software; you can redistribute it and/or modify
        *   it under the terms of the GNU General Public License as published by
        *   the Free Software Foundation; either version 2 of the License, or
        *   (at your option) any later version.
        *
        ***************************************************************************/

    /****************************************************************************

   Unicode Reminder ??

        writing /html/newcaches.inc.php and /html/start_newcaches.inc.php
        /html/nextevents.inc.php

    ****************************************************************************/
//ini_set ('display_errors', On);

    global $lang, $rootpath;
    if (!isset($rootpath)) $rootpath = '../../../';
    //include template handling
    require_once($rootpath . 'lib/common.inc.php');

function online_user()
{
// add check users id who want to by username hidden
$rs=sql("SELECT  `user_id` FROM `sys_sessions` WHERE user_id!=1 AND `sys_sessions`.last_login >(NOW()-INTERVAL 10 MINUTE) GROUP BY `user_id`");
$online_users=array();
while ($r=mysql_fetch_array($rs))
{$online_users[]=$r['user_id'];}
return $online_users;
}

                    $onlusers=online_user();
                    $file_content = count($onlusers);
                    $n_file = fopen($dynstylepath . "nonlusers.txt", 'w');
                    fwrite($n_file, $file_content);
                    fclose($n_file);
                    $file_content='';
                    $file_line='';
                    foreach($onlusers as $onluser){
                    $username=sqlValue("SELECT username FROM `user` WHERE user_id='$onluser'", 0);

                    $file_line .='<a class="links-onlusers" href="viewprofile.php?userid='.$onluser.'">'.$username.'</a>,&nbsp;';
                             }
                    $file_content = $file_line;
                    $n_file = fopen($dynstylepath . "onlineusers.html", 'w');
                    fwrite($n_file, $file_content);
                    fclose($n_file);

?>
