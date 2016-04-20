<?php

use Utils\Database\OcDb;
/** www.opencaching.pl *********************************************************
 * revertlog.php
 * reverts log from deleted. (undelete log).
 *
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 */
if (!isset($rootpath))
    $rootpath = '';
require_once('./lib/common.inc.php');

/* * *************************************************
 * Function reverts log from deleted. (undelete log).
 *
 * @author: Andrzej Łza Woźniak, 2013-03
 */

function revertLog($log_id, $language, $lang)
{
    // set $debug = true to display debug messages (or false to hide).
    $debug = false;

    global $tplname, $usr, $lang, $stylepath, $oc_nodeid, $error_wrong_node, $removed_message_title, $removed_message_end, $rootpath, $cacheid, $log_record, $cache_types, $cache_size, $cache_status;

    $logRs = OcDb::instance();

    $logRsQuery = "SELECT   `cache_logs`.`node` AS `node`, `cache_logs`.`uuid` AS `uuid`, `cache_logs`.`cache_id` AS `cache_id`, `caches`.`user_id` AS `cache_owner_id`,
                            `caches`.`name` AS `cache_name`, `cache_logs`.`text` AS `log_text`, `cache_logs`.`type` AS `log_type`,
                            `cache_logs`.`user_id` AS `log_user_id`, `cache_logs`.`date` AS `log_date`,
                            `log_types`.`icon_small` AS `icon_small`,
                            `log_types_text`.`text_listing` AS `text_listing`,
                            `user`.`username` as `log_username`
                     FROM   `log_types`, `log_types_text`, `cache_logs`, `caches`, `user`
                    WHERE   `cache_logs`.`id`=:log_id
                      AND   `cache_logs`.`user_id`=`user`.`user_id`
                      AND   `caches`.`cache_id`=`cache_logs`.`cache_id`
                      AND   `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`=:lang
                      AND   `cache_logs`.`deleted` = 1
                      AND   `log_types`.`id`=`cache_logs`.`type`";

    $s = $logRs->paramQuery($logRsQuery, array('log_id' => array('value' => $log_id, 'data_type' => 'integer'), 'lang' => array('value' => $lang, 'data_type' => 'string'),));
    //log exists?
    if ($logRs->rowCount($s) == 1) {

        $log_record = $logRs->dbResultFetch($s);

        if ($log_record['node'] != $oc_nodeid) {
            tpl_errorMsg('removelog', $error_wrong_node);
            exit;
        }

        //cache-owner or log-owner
        if (($log_record['log_user_id'] == $usr['userid']) || ($log_record['cache_owner_id'] == $usr['userid']) || $usr['admin']) {
            // revert the log.
            $revert = new dataBase($debug);
            $query = "UPDATE `cache_logs` SET deleted = 0 , `last_modified`=NOW() WHERE `cache_logs`.`id`=:log_id LIMIT 1";
            $revert->paramQuery($query, array('log_id' => array('value' => $log_id, 'data_type' => 'i'),));
            unset($revert);

            //user stats update
            $statUpd = OcDb::instance();
            $query = "SELECT `founds_count`, `notfounds_count`, `log_notes_count` FROM `user` WHERE `user_id`=:user_id";
            $s = $statUpd->paramQuery($query, array('user_id' => array('value' => $log_record['log_user_id'], 'data_type' => 'i'),));
            $user_record = $statUpd->dbResultFetch($s);

            if ($log_record['log_type'] == 1 || $log_record['log_type'] == 7) {
                $user_record['founds_count'] ++;
            } elseif ($log_record['log_type'] == 2) {
                $user_record['notfounds_count'] ++;
            } elseif ($log_record['log_type'] == 3) {
                $user_record['log_notes_count'] ++;
            }

            $updateUser = OcDb::instance();
            $query = "UPDATE `user` SET `founds_count`=:var1, `notfounds_count`=:var2, `log_notes_count`=:var3 WHERE `user_id`=:var4";
            $params = array(
                'var1' => array('value' => $user_record['founds_count'], 'data_type' => 'i'),
                'var2' => array('value' => $user_record['notfounds_count'], 'data_type' => 'i'),
                'var3' => array('value' => $user_record['log_notes_count'], 'data_type' => 'i'),
                'var4' => array('value' => $log_record['log_user_id'], 'data_type' => 'i')
            );
            $updateUser->paramQuery($query, $params);
            unset($params, $user_record);

            //call eventhandler
            require_once($rootpath . 'lib/eventhandler.inc.php');
            event_remove_log($cacheid, $usr['userid'] + 0);

            //update cache-stat if type or log_date changed
            $cachStat = new dataBase($debug);
            $query = "SELECT `founds`, `notfounds`, `notes` FROM `caches` WHERE `cache_id`=:var1 LIMIT 1";
            $s = $cachStat->paramQuery($query, array('var1' => array('value' => $log_record['cache_id'], 'data_type' => 'i'),));
            $cache_record = $cachStat->dbResultFetchOneRowOnly($s);

            if ($log_record['log_type'] == 1 || $log_record['log_type'] == 7) {
                $cache_record['founds'] ++;
            } elseif ($log_record['log_type'] == 2 || $log_record['log_type'] == 8) {
                $cache_record['notfounds'] ++;
            } elseif ($log_record['log_type'] == 3) {
                $cache_record['notes'] ++;
            }

            //Update last found

            $lastF = OcDb::instance();
            $query = "SELECT MAX(`cache_logs`.`date`) AS `date` FROM `cache_logs` WHERE ((cache_logs.`type`=1) AND (cache_logs.`cache_id`=:last_tmp))";

            $s = $lastF->paramQuery($query, array('last_tmp' => array('value' => $log_record['cache_id'], 'data_type' => 'i'),));
            $lastfound_record = $lastF->dbResultFetchOneRowOnly($s);
            unset($statUpd);

            if ($lastfound_record['date'] === NULL) {
                $lastfound = 'NULL';
            } else {
                $lastfound = $lastfound_record['date'];
            }

            $updateCache = new dataBase;
            $query = "UPDATE `caches` SET `last_found`=:var1, `founds`=:var2, `notfounds`=:var3, `notes`=:var4 WHERE `cache_id`=:var5";
            $params = array(
                'var1' => array('value' => $lastfound, 'data_type' => 'string'),
                'var2' => array('value' => $cache_record['founds'], 'data_type' => 'i'),
                'var3' => array('value' => $cache_record['notfounds'], 'data_type' => 'i'),
                'var4' => array('value' => $cache_record['notes'], 'data_type' => 'i'),
                'var5' => array('value' => $log_record['cache_id'], 'data_type' => 'i'),
            );
            $updateCache->paramQuery($query, $params);
            unset($updateCache, $params, $cache_record);

            $_GET['cacheid'] = $log_record['cache_id'];
            $_REQUEST['cacheid'] = $log_record['cache_id'];
            require('viewcache.php');
        } else {
            //TODO: hm ... no permission to revert the log
            $_GET['cacheid'] = $log_record['cache_id'];
            $_REQUEST['cacheid'] = $log_record['cache_id'];
            require('viewcache.php');
        }
    } else {
        //TODO: log doesn't exist
        $_GET['cacheid'] = $log_record['cache_id'];
        $_REQUEST['cacheid'] = $log_record['cache_id'];
        require('viewcache.php');
    }
}

//prepare the templates and include all neccessary

require_once($stylepath . '/lib/icons.inc.php');

//Preprocessing
if ($error == false) {
    //cacheid
    $log_id = 0;
    if (isset($_REQUEST['logid'])) {
        $log_id = intval($_REQUEST['logid']);
    }

    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        revertLog($log_id, $language, $lang);
    }
}
?>
