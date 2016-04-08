<?php

use Utils\Database\XDb;
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'removepic'; // gibt es nicht ...
        require_once($stylepath . '/removepic.inc.php');

        $uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : '';

        $allok = true;
        if ($uuid == '')
            $allok = false;

        if ($allok == true) {
            $rs = XDb::xSql(
                "SELECT `object_type`, `object_id`, `user_id`, `id`, `url` FROM `pictures` WHERE `uuid`= ? ", $uuid);
            if (! $r = XDb::xFetchArray($rs))
                $allok = false;
        }

        if ($allok == true) {
            XDb::xFreeResults($rs);
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
            XDb::xSql("DELETE FROM `pictures` WHERE `uuid`= ? ", $uuid);
            XDb::xSql(
                "INSERT INTO `removed_objects` (`localID`, `uuid`, `type`, `removed_date`, `node`)
                VALUES (?, ?, 6, NOW(), ?)",
                $localid, $uuid, $oc_nodeid);

            switch ($type) {
                // log
                case 1:
                    XDb::xSql(
                        "UPDATE `cache_logs` SET `picturescount`=`picturescount`-1, `last_modified`=NOW() 
                        WHERE `id`= ? ", $objectid);

                    $rs = XDb::xSql("SELECT `cache_id` FROM `cache_logs` WHERE `deleted`=0 AND `id`= ? ", $objectid);
                    $r = XDb::xFetchArray($rs);
                    XDb::xFreeResults($rs);

                    tpl_redirect('viewlogs.php?cacheid=' . urlencode($r['cache_id']));
                    break;

                // cache
                case 2:
                    XDb::xSql(
                        "UPDATE `caches` SET `picturescount`=`picturescount`-1, `last_modified`=NOW() 
                        WHERE `cache_id`= ? ", $objectid);

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

