<?php

use Utils\Database\XDb;
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //cacheid
    $cache_id = 0;
    if (isset($_REQUEST['cacheid'])) {
        $cache_id = $_REQUEST['cacheid'];
    }
    $desclang = '';
    if (isset($_REQUEST['desclang'])) {
        $desclang = $_REQUEST['desclang'];
    }
    $remove_commit = 0;
    if (isset($_REQUEST['commit'])) {
        $remove_commit = $_REQUEST['commit'];
    }

    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $cache_rs = XDb::xSql(
            "SELECT `user_id`, `name` FROM `caches` WHERE `cache_id`= ? LIMIT 1", $cache_id);

        if ( $cache_record = XDb::xFetchArray($cache_rs)) {

            if ($cache_record['user_id'] == $usr['userid'] || $usr['admin']) {

                $desc_rs = XDb::xSql(
                    "SELECT `id`, `uuid` FROM `cache_desc` WHERE `cache_id`= ? AND `language`= ? LIMIT 1", $cache_id, $desclang);
                if ($desc_record = XDb::xFetchArray($desc_rs)) {

                    XDb::xFreeResults($desc_rs);
                    require($stylepath . '/removedesc.inc.php');

                    if ($remove_commit == 1) {
                        //add to removed_objects
                        XDb::xSql(
                            "INSERT INTO `removed_objects` (`id`, `localID`, `uuid`, `type`, `removed_date`, `node`)
                            VALUES ('', ?, ?, '3', NOW(), ?)",
                            $desc_record['id'], $desc_record['uuid'], $oc_nodeid);

                        //remove it from cache_desc
                        XDb::xSql(
                            "DELETE FROM `cache_desc` WHERE `cache_id`= ? AND `language`= ? LIMIT 1", $cache_id, $desclang);

                        // update cache-record, including last modification date
                        setCacheDefaultDescLang($cache_id);

                        tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
                        exit;
                    } else {
                        //commit the removement
                        $tplname = 'removedesc';

                        tpl_set_var('desclang_name', db_LanguageFromShort($desclang));
                        tpl_set_var('cachename', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('desclang_urlencode', htmlspecialchars(urlencode($desclang), ENT_COMPAT, 'UTF-8'));
                    }
                } else {
                    //TODO: desc not exist
                }
            } else {
                //TODO: not the owner
            }
        } else {
            //TODO: cache not exist
        }
    }
}

//make the template and send it out
tpl_BuildTemplate();

