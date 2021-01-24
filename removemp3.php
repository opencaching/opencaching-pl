<?php

use src\Utils\Database\XDb;
use src\Models\OcConfig\OcConfig;
use src\Models\ApplicationContainer;

require_once (__DIR__.'/lib/common.inc.php');

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();
if (!$loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}


        $tplname = 'removemp3'; // gibt es nicht ...
        require_once (__DIR__.'/src/Views/removemp3.inc.php');

        $uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : '';

        $allok = true;
        if ($uuid == '')
            $allok = false;

        if ($allok == true) {
            $rs = XDb::xSql("SELECT `object_type`, `object_id`, `user_id`, `id`, `url` FROM `mp3` WHERE `uuid`= ? ", $uuid);
            if (!$r = XDb::xFetchArray($rs))
                $allok = false;
        }

        if ($allok == true) {
            XDb::xFreeResults($rs);

            $type = $r['object_type'];
            $objectid = $r['object_id'];
            $user_id = $r['user_id'];
            $localid = $r['id'];
            $url = $r['url'];

            if ($user_id != $loggedUser->getUserId() && !$loggedUser->hasOcTeamRole()) {
                $allok = false;
            }
        }

        if ($allok == true) {
            // ok, we have a valid uuid and are the owner ...
            $fna = mb_split('\\.', $url);
            $extension = mb_strtolower($fna[count($fna) - 1]);

            // remove file and DB entry
            @unlink(OcConfig::getPicUploadFolder(true) . '/' . $uuid . '.' . $extension);
            XDb::xSql("DELETE FROM `mp3` WHERE `uuid`= ? LIMIT 1", $uuid);
            XDb::xSql(
                "INSERT INTO `removed_objects` (`localID`, `uuid`, `type`, `removed_date`, `node`)
                VALUES ( ?, ?, 6, NOW(), ?)",
                $localid, $uuid, OcConfig::getSiteNodeId());

            switch ($type) {
                // log
                case 1:
                    XDb::xSql("UPDATE `cache_logs` SET `mp3count`=`mp3count`-1 WHERE `id`= ? ", $objectid);

                    $rs = XDb::xSql(
                        "SELECT `cache_id` FROM `cache_logs`, `last_modified`=NOW()
                        WHERE `deleted`=0 AND `id`= ? ", $objectid);

                    $r = XDb::xFetchArray($rs);
                    XDb::xFreeResults($rs);

                    tpl_redirect('viewlogs.php?cacheid=' . urlencode($r['cache_id']));
                    break;

                // cache
                case 2:
                    XDb::xSql(
                        "UPDATE `caches` SET `mp3count`=`mp3count`-1, `last_modified`=NOW()
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
