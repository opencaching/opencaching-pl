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

     remove a cache log

     GET/POST-Parameter: logid

 ****************************************************************************/
if(!isset($rootpath)) $rootpath = '';
require_once('./lib/common.inc.php');

function removelog($log_id, $language, $lang)
{
    global $tplname, $usr, $lang, $stylepath, $oc_nodeid, $error_wrong_node, $removed_message_title, $removed_message_end, $emailheaders, $rootpath, $cacheid, $log_record, $cache_types, $cache_size, $cache_status, $dblink, $octeamEmailsSignature;
    $log_rs = sql("SELECT   `cache_logs`.`node` AS `node`, `cache_logs`.`uuid` AS `uuid`, `cache_logs`.`cache_id` AS `cache_id`, `caches`.`user_id` AS `cache_owner_id`,
                        `caches`.`name` AS `cache_name`, `cache_logs`.`text` AS `log_text`, `cache_logs`.`type` AS `log_type`,
                        `cache_logs`.`user_id` AS `log_user_id`, `cache_logs`.`date` AS `log_date`,
                        `log_types`.`icon_small` AS `icon_small`,
                        `log_types_text`.`text_listing` AS `text_listing`,
                        `user`.`username` as `log_username`
                     FROM `log_types`, `log_types_text`, `cache_logs`, `caches`, `user`
                    WHERE `cache_logs`.`id`='&1'
                      AND `cache_logs`.`user_id`=`user`.`user_id`
                      AND `caches`.`cache_id`=`cache_logs`.`cache_id`
                      AND `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`='&2'
                        AND `cache_logs`.`deleted` = &3
                      AND `log_types`.`id`=`cache_logs`.`type`", $log_id, $lang, 0);

            //log exists?
            if (mysql_num_rows($log_rs) == 1)
            {
                $log_record = sql_fetch_array($log_rs);
                mysql_free_result($log_rs);

                include($stylepath . '/removelog.inc.php');

                if ($log_record['node'] != $oc_nodeid)
                {
                    tpl_errorMsg('removelog', $error_wrong_node);
                    exit;
                }

                //cache-owner or log-owner
                if (($log_record['log_user_id'] == $usr['userid']) || ($log_record['cache_owner_id'] == $usr['userid']) || $usr['admin'])
                {
                    //Daten lesen
                    if( $usr['admin'] && isset($_POST['userid']))
                        $commit = 1;
                    else
                        $commit = isset($_REQUEST['commit']) ? $_REQUEST['commit'] : 0;

                    //we are the logger
                    if ($log_record['log_user_id'] == $usr['userid'])
                    {
                        $tplname = 'removelog_logowner';
                    }
                    else
                    {
                        $tplname = 'removelog_cacheowner';

                        if ($commit == 1)
                        {
                            //send email to logowner schicken
                            $email_content = read_file($stylepath . '/email/removed_log.email');

                            $message = isset($_POST['logowner_message']) ? $_POST['logowner_message'] : '';
                            if ($message != '')
                            {
                                //message to logowner
                                $message = $removed_message_title . "\n" . $message . "\n" . $removed_message_end;
                            }

                            //get cache owner name
                            $cache_owner_rs = sql("SELECT `username` FROM `user` WHERE `user_id`='&1'", $log_record['cache_owner_id']);
                            $cache_owner_record = sql_fetch_array($cache_owner_rs);

                            //get email address of logowner
                            $log_user_rs = sql("SELECT `email`, `username` FROM `user` WHERE `user_id`='&1'", $log_record['log_user_id']);
                            $log_user_record = sql_fetch_array($log_user_rs);

                            $email_content = mb_ereg_replace('{log_owner}', $log_user_record['username'], $email_content);
                            $email_content = mb_ereg_replace('{cache_owner}', $cache_owner_record['username'], $email_content);
                            $email_content = mb_ereg_replace('{cache_name}', $log_record['cache_name'], $email_content);
                            $email_content = mb_ereg_replace('{log_entry}', $log_record['log_text'], $email_content);
                            $email_content = mb_ereg_replace('{comment}', $message, $email_content);
                            $email_content = mb_ereg_replace('{removedLog_01}', tr('removedLog_01'), $email_content);
                            $email_content = mb_ereg_replace('{removedLog_02}', tr('removedLog_02'), $email_content);
                            $email_content = mb_ereg_replace('{removedLog_03}', tr('removedLog_03'), $email_content);
			    $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);
                            $email_content = mb_ereg_replace('{removedLog_04}', tr('removedLog_04'), $email_content);

                            //send email (only on single removement)

                            mb_send_mail($log_user_record['email'], $removed_log_title, $email_content, $emailheaders);
                        }
                    }

                    if ($commit == 1)
                    {

                        // do not acually delete logs - just mark them as deleted.
    //START: edit by FelixP - 2013'10
                        sql("UPDATE `cache_logs` SET deleted = 1, `del_by_user_id` =".$usr['userid']." , `last_modified`=NOW(), `last_deleted`=NOW() WHERE `cache_logs`.`id`='&1' LIMIT 1", $log_id);
                        // requires: ALTER TABLE `cache_logs` ADD `del_by_user_id` INT( 11 ) NULL ;
                        //requires: ALTER TABLE `cache_logs` ADD `last_deleted` DATETIME NULL DEFAULT NULL ;
    //END: edit by FelixP - 2013'10
                        recalculateUserStats($log_record['log_user_id']);

                        // remove from cache_moved for log "MOVED" (mobilniaki by Łza)
                        // (kod istniejący wcześniej, zaadaptowany)
                        if ($log_record['log_type'] == 4)
                        {
                         // jesli log jest ostatni - przywrocenie kordow z przedostatniego "przeniesiona"
                         $check_cml = sql("SELECT `latitude`,`longitude`,`id` FROM `cache_moved` WHERE `log_id`='&1'",$log_id);
                         if (mysql_num_rows($check_cml)!=0)
                            {
                             $xy_log = sql_fetch_array($check_cml);
                             $check_cmc = sql("SELECT `latitude`,`longitude` FROM `caches` WHERE `cache_id`='&1'",$log_record['cache_id']);
                             if (mysql_num_rows($check_cmc) !=0)
                                {
                                 $xy_cache = sql_fetch_array($check_cmc);
                                 if ($xy_cache['latitude']==$xy_log['latitude'] && $xy_cache['longitude']==$xy_log['longitude'])
                                    {
                                     sql("DELETE FROM `cache_moved` WHERE `log_id`='&1' LIMIT 1", $log_id);
                                     $get_xy = sql("SELECT `latitude`,`longitude` FROM `cache_moved` WHERE `cache_id`='&1' ORDER BY `date` DESC LIMIT 1",$log_record['cache_id']);

                                        $old_xy = sql_fetch_array($get_xy);
                                        if (($old_xy['longitude'] != '') && ($old_xy['latitude'] != '')) sql("UPDATE `caches` SET `last_modified`=NOW(), `longitude`='&1', `latitude`='&2' WHERE `cache_id`='&3'", $old_xy['longitude'], $old_xy['latitude'], $log_record['cache_id']);

                                     }
                                 else
                                     sql("DELETE FROM `cache_moved` WHERE `log_id`='&1' LIMIT 1", $log_id);
                                }
                             else sql("DELETE FROM `cache_moved` WHERE `log_id`='&1' LIMIT 1", $log_id);
                            }
                        }


                        if ($log_record['log_type'] == 1 || $log_record['log_type'] == 7)
                        {

                            // remove cache from users top caches, because the found log was deleted for some reason
                            sql("DELETE FROM `cache_rating` WHERE `user_id` = '&1' AND `cache_id` = '&2'", $log_record['log_user_id'], $log_record['cache_id']);

                            // Notify OKAPI's replicate module of the change.
                            // Details: https://code.google.com/p/opencaching-api/issues/detail?id=265
                            require_once($rootpath.'okapi/facade.php');
                            \okapi\Facade::schedule_user_entries_check($log_record['cache_id'], $log_record['log_user_id']);
                            \okapi\Facade::disable_error_handling();

                            // recalc scores for this cache
                            sql("DELETE FROM `scores` WHERE `user_id` = '&1' AND `cache_id` = '&2'", $log_record['log_user_id'], $log_record['cache_id']);
                            $sql = "SELECT count(*) FROM scores WHERE cache_id='".sql_escape($log_record['cache_id'])."'";
                            $liczba = mysql_result(mysql_query($sql),0);
                            $sql = "SELECT SUM(score) FROM scores WHERE cache_id='".sql_escape($log_record['cache_id'])."'";
                            $suma = @mysql_result(@mysql_query($sql),0)+0;

                            // obliczenie nowej sredniej
                            if( $liczba != 0)
                            {
                                $srednia = $suma / $liczba;
                            }
                            else
                            {
                                $srednia = 0;
                            }

                            $sql = "UPDATE caches SET votes='".sql_escape($liczba)."', score='".sql_escape($srednia)."' WHERE cache_id='".sql_escape($log_record['cache_id'])."'";
                            mysql_query($sql);
                        }

                        //call eventhandler
                        require_once($rootpath . 'lib/eventhandler.inc.php');
                        event_remove_log($cacheid, $usr['userid']+0);

                        //update cache-stat if type or log_date changed
                        $cache_rs = sql("SELECT `founds`, `notfounds`, `notes` FROM `caches` WHERE `cache_id`='&1'", $log_record['cache_id']);
                        $cache_record = sql_fetch_array($cache_rs);
                        mysql_free_result($cache_rs);

                        if ($log_record['log_type'] == 1 || $log_record['log_type'] == 7)
                        {
                            $cache_record['founds']--;
                        }
                        elseif ($log_record['log_type'] == 2 || $log_record['log_type'] == 8)
                        {
                            $cache_record['notfounds']--;
                        }
                        elseif ($log_record['log_type'] == 3)
                        {
                            $cache_record['notes']--;
                        }

                        //Update last found
                        $last_tmp = $log_record['cache_id'];
                        $lastfound_rs = sql("SELECT MAX(`cache_logs`.`date`) AS `date` FROM `cache_logs` WHERE ((cache_logs.`type`=1) AND (cache_logs.`cache_id`='$last_tmp'))");
                        $lastfound_record = sql_fetch_array($lastfound_rs);

                        if ($lastfound_record['date'] === NULL)
                        {
                            $lastfound = 'NULL';
                        }
                        else
                        {
                            $lastfound = $lastfound_record['date'] ;
                        }

                        sql("UPDATE `caches` SET `last_found`='&1', `founds`='&2', `notfounds`='&3', `notes`='&4' WHERE `cache_id`='&5'", $lastfound, $cache_record['founds'], $cache_record['notfounds'], $cache_record['notes'], $log_record['cache_id']);
                        unset($cache_record);

                        if( !(isset($_POST['userid'])))
                        {
                            //cache anzeigen
                            $_GET['cacheid'] = $log_record['cache_id'];
                            $_REQUEST['cacheid'] = $log_record['cache_id'];
                            require('viewcache.php');
                        }
                    }
                    else
                    {
                        tpl_set_var('cachename', htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('cacheid', htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('logid_urlencode', htmlspecialchars(urlencode($log_id), ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('logid', htmlspecialchars($log_id, ENT_COMPAT, 'UTF-8'));

                        $log = read_file($stylepath . '/viewcache_log.tpl.php');


                        $log = mb_ereg_replace('{date}', htmlspecialchars(strftime("%d %B %Y", strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8'), $log);

                        if (isset($log_record['recommended']) && $log_record['recommended'] == 1)
                            $log = mb_ereg_replace('{ratingimage}', $rating_picture, $log);
                        else
                            $log = mb_ereg_replace('{ratingimage}', '', $log);


                        $log = mb_ereg_replace('{username}', htmlspecialchars($log_record['log_username'], ENT_COMPAT, 'UTF-8'), $log);
                        $log = mb_ereg_replace('{userid}', htmlspecialchars($log_record['log_user_id'] + 0, ENT_COMPAT, 'UTF-8'), $log);
                        tpl_set_var('log_user_name', htmlspecialchars($log_record['log_username'], ENT_COMPAT, 'UTF-8'));


                        $log = mb_ereg_replace('{type}', htmlspecialchars($log_record['text_listing'], ENT_COMPAT, 'UTF-8'), $log);

                        $log = mb_ereg_replace('{logimage}', icon_log_type($log_record['icon_small'], ""), $log);
                        $log = mb_ereg_replace('{logfunctions}', '', $log);
                        $log = mb_ereg_replace('{logpictures}', '', $log);
                        $log = mb_ereg_replace('{logtext}', $log_record['log_text'], $log);
                        $log = mb_ereg_replace('{username_aktywnosc}','',$log);
                        $log = mb_ereg_replace('{kordy_mobilniaka}','',$log);
                        tpl_set_var('log', $log);
                        //make the template and send it out
                        tpl_BuildTemplate();
                    }
                }
                else
                {
                    //TODO: hm ... no permission to remove the log
                }
            }
            else
            {
                //TODO: log doesn't exist
            }
}

   //prepare the templates and include all neccessary

    require_once($stylepath . '/lib/icons.inc.php');

    //Preprocessing
    if ($error == false)
    {
        //cacheid
        $log_id = 0;
        if (isset($_REQUEST['logid']))
        {
            $log_id = intval($_REQUEST['logid']);
        }

        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {
            /* This part of code removes all logs of specified user.
             * swithed off for security reasons.

            if( $usr['admin']==2619 && isset($_REQUEST['userid']) )
            {
                $sql = "SELECT username FROM user WHERE user_id = '".sql_escape(intval($_REQUEST['userid']))."'" ;
                $username = mysql_result(mysql_query($sql),0);

                if( !isset($_POST['submit']))
                echo '
                    <font color="red"><b><h1>UWAGA!!!</h1></b>Po wciśnięciu "Potwierdzam" nastąpi nieodwracalne usunięcie WSZYSTKICH wpisów użytkownika "'.$username.'".<br/><br/>
                    <form action="removelog.php" method="POST">
                    <input type="submit" name="submit" value="Potwierdzam"/>
                    <input type="hidden" name="userid" value="'.intval($_REQUEST['userid']).'"/>
                    </form>
                ';
                else
                {
                    $logs_rs = sql( "SELECT id FROM cache_logs WHERE user_id = '&1'", intval($_REQUEST['userid']));

                    while( $log_to_remove = sql_fetch_array($logs_rs) )
                    {
                        removelog($log_to_remove['id'],$language, $lang);
                    }
                    mysql_free_result($logs_rs);
                    echo 'Wszystkie logi użytkownika "'.$username.'" zostały usunięte...';
                }
            }
            else
            */
            removelog($log_id, $language, $lang);
        }
    }


/**
 * after delete a log it is a good idea to full recalculate stats of user, that can avoid
 * possible errors which used to appear when was calculated old method.
 *
 * by Andrzej Łza Woźniak, 10-2013
 *
 */
function recalculateUserStats($userId){
    $query = "
        UPDATE `user`
        SET `founds_count`   = (SELECT count(*) FROM `cache_logs` WHERE `user_id` =:1 AND TYPE =1 AND `deleted` =0 ),
            `notfounds_count`= (SELECT count(*) FROM `cache_logs` WHERE `user_id` =:1 AND TYPE =2 AND `deleted` =0 ),
            `log_notes_count`= (SELECT count(*) FROM `cache_logs` WHERE `user_id` =:1 AND TYPE =3 AND `deleted` =0 )
        WHERE `user_id` =:1
    ";

    $db = new dataBase;
    $db->multiVariableQuery($query, $userId);
}
?>
