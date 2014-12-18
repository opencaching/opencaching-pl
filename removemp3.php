<?php

/* * *************************************************************************
  ./removepic.php
  -------------------
  begin                : Wed August 19 2005
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

  Unicode Reminder ăĄă˘

  remove a mp3 from "my profile", caches, logs etc.

  requiered page-arguments
  - logged in (userid)
  - objectid                ... cacheid, logid, etc
  - type                    ... type of the object

  userid must be owner of the object referenced by objectid

 * ************************************************************************** */

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'removemp3'; // gibt es nicht ...
        require_once($stylepath . '/removemp3.inc.php');

        $uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : '';

        $allok = true;
        if ($uuid == '')
            $allok = false;

        if ($allok == true) {
            $rs = sql("SELECT `object_type`, `object_id`, `user_id`, `id`, `url` FROM `mp3` WHERE `uuid`='&1'", $uuid);
            if (mysql_num_rows($rs) == 0)
                $allok = false;
        }

        if ($allok == true) {
            $r = sql_fetch_array($rs);
            mysql_free_result($rs);
            $type = $r['object_type'];
            $objectid = $r['object_id'];
            $user_id = $r['user_id'];
            $localid = $r['id'];
            $url = $r['url'];

            if ($user_id != $usr['userid'] && !$usr['admin'])
                $allok = false;
        }

        if ($allok == true) {
            //ok, wir haben eine gĂźltige uuid und sind der owner ...
            $fna = mb_split('\\.', $url);
            $extension = mb_strtolower($fna[count($fna) - 1]);

            // datei und in DB lĂśschen
            @unlink($picdir . '/' . $uuid . '.' . $extension);
            sql("DELETE FROM `mp3` WHERE `uuid`='&1'", $uuid);
            sql("INSERT INTO `removed_objects` (`localID`, `uuid`, `type`, `removed_date`, `node`) VALUES ('&1', '&2', 6, NOW(), '&3')", $localid, $uuid, $oc_nodeid);

            switch ($type) {
                // log
                case 1:
                    sql("UPDATE `cache_logs` SET `mp3count`=`mp3count`-1 WHERE `id`='&1'", $objectid);

                    $rs = sql("SELECT `cache_id` FROM `cache_logs` WHERE `deleted`=0 AND `id`='&1'", $objectid);
                    $r = sql_fetch_array($rs);
                    mysql_free_result($rs);

                    tpl_redirect('viewlogs.php?cacheid=' . urlencode($r['cache_id']));
                    break;

                // cache
                case 2:
                    sql("UPDATE `caches` SET `mp3count`=`mp3count`-1 WHERE `cache_id`='&1'", $objectid);

                    tpl_redirect('editcache.php?cacheid=' . urlencode($objectid));
                    break;
            }

            tpl_redirect('index.php');
            exit;
        } else {
            $tplname = 'message';
            tpl_set_var('messagetitle', $message_title_internal);
            tpl_set_var('message_start', '');
            tpl_set_var('message_end', '');
            tpl_set_var('message', $message_internal);
        }
    }
}

//make the template and send it out
tpl_BuildTemplate();
?>
