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

   Unicode Reminder ăĄă˘

     view all logs of a cache

     used template(s): viewlogs

     GET Parameter: cacheid, start, count

 ****************************************************************************/

  //prepare the templates and include all neccessary
    if(!isset($rootpath)) $rootpath = '';
    require_once('./lib/common.inc.php');
    if ($error == false)
    //Preprocessing
    {
        global $usr;

        $cache_id = null;
        if (isset($_REQUEST['cacheId']))
        {
            $cache_id = (int) $_REQUEST['cacheId'];
        }
        $user_id = null;
        if (isset($_REQUEST['userId']))
        {
            $user_id = (int) $_REQUEST['userId'];
        }
        if ($usr !== false) {
            $user_id = $usr['userid'];
        }
        
        // detailed cache access logging
        if (@$enable_cache_access_logs && $cache_id !== null)
        {
            if (!isset($dbc)) {$dbc = new dataBase();};
            unset($_POST['cacheId']);
            unset($_POST['userId']);
            $info_text = json_encode($_POST);
            if ($user_id !== null){
                $count = $dbc->multiVariableQueryValue(
                    'SELECT count(1) 
                       FROM CACHE_ACCESS_LOGS cal
                      WHERE 
                          user_id = :1
                          and source = \'J\'
                          and event = \'java_hit\'
                          and ip_addr = :2
                          and info_text = :3
                          and date_sub(now(), interval 1 hour) < cal.event_date
                    ', -1, $user_id, $_SERVER['REMOTE_ADDR'], $info_text);
            } else {
                $count = $dbc->multiVariableQueryValue(
                    'SELECT count(1) 
                       FROM CACHE_ACCESS_LOGS cal
                      WHERE 
                          cache_id = :1
                          and user_id is null
                          and source = \'J\'
                          and event = \'java_hit\'
                          and ip_addr = :2
                          and info_text = :3
                          and date_sub(now(), interval 1 hour) < cal.event_date
                    ', -1, $cache_id, $_SERVER['REMOTE_ADDR'], $info_text);
            }
            if ($count <= 0){
                $dbc->multiVariableQuery(
                    'INSERT INTO CACHE_ACCESS_LOGS 
                        (event_date, cache_id, user_id, source, event, ip_addr, user_agent, forwarded_for, info_text) 
                     VALUES 
                        (NOW(), :1, :2, \'J\', \'java_hit\', :3, :4, :5, :6)',
                        $cache_id, $user_id, 
                        $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_X_FORWARDED_FOR'],
                        $info_text
                );
            }
        }
    }   
    unset( $dbc );
    header('HTTP/1.1 404 Not Found');
?><!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL /mapv4_eligibility_status.php was not found on this server.</p>
<hr>
<address>Apache Server at opencaching.pl Port 80</address>
</body></html>
