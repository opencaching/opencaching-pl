<?php
/***************************************************************************
                                                                ./myhome.php
                                                            -------------------
        begin                : Mon June 14 2004
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

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

     the users home

     used template(s): myhome
     parameter(s):     none

 ****************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {
            $dbc = new dataBase();

            if( $usr['admin'] )
                tpl_set_var('reports',"<b>".tr('manage_ocpl')."</b><br />[<a href='viewreports.php'>".tr("browse_problem_reports")."</a>]");
            else
                tpl_set_var('reports','');
            require($stylepath . '/myhome2.inc.php');
            require($stylepath . '/lib/icons.inc.php');

            $tplname = 'myhome2';
            tpl_set_var('username', htmlspecialchars($usr['username'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('userid', htmlspecialchars($usr['userid'], ENT_COMPAT, 'UTF-8'));

            //get user record
            $userid = $usr['userid'];
            $sql = "SELECT COUNT(*) cnt FROM caches WHERE user_id=:1";
            $dbc->multiVariableQuery($sql, $userid );
            $odp = $dbc->dbResultFetch();


            if( $dbc->rowCount() )
                $hidden_count = $odp[ 'cnt' ];
            else
                $hidden_count = 0;
            unset( $dbc );


            $dbc = new dataBase();
            $sql = "SELECT COUNT(*) cnt
                            FROM cache_logs
                            WHERE user_id=:1 AND type=1 AND deleted=0";

            $dbc->multiVariableQuery($sql, $userid );
            $odp = $dbc->dbResultFetch();

            if( $dbc->rowCount() )
                $founds_count = $odp[ 'cnt' ];
            else
                $founds_count = 0;

            unset( $dbc );


            $dbc = new dataBase();
            $sql = "SELECT COUNT(*) cnt
                            FROM cache_logs
                            WHERE user_id=$userid AND type=7 AND deleted=0";
            $dbc->multiVariableQuery($sql, $userid );
            $odp = $dbc->dbResultFetch();

            if( $dbc->rowCount() )
                $events_count = $odp[ 'cnt' ];
            else
                $events_count = 0;

            unset( $dbc );


            $dbc = new dataBase();
            $sql = "SELECT COUNT(*) cnt
                            FROM cache_logs
                            WHERE user_id=$userid AND type=2 AND deleted=0";

            $dbc->multiVariableQuery($sql, $userid );
            $odp = $dbc->dbResultFetch();

            if( $dbc->rowCount() )
                $notfounds_count = $odp[ 'cnt' ];
            else
                $notfounds_count = 0;
            unset( $dbc );

            $dbc = new dataBase();
            $sql = "SELECT COUNT(*) cnt
                            FROM cache_logs
                            WHERE user_id=$userid AND type=3 AND deleted=0";

            $dbc->multiVariableQuery($sql, $userid );
            $odp = $dbc->dbResultFetch();

            if( $dbc->rowCount() )
                $log_notes_count = $odp[ 'cnt' ];
            else
                $log_notes_count = 0;
            unset( $dbc );


            if( $events_count > 0 )
                $events = "Uczestniczyłeś w ".$events_count." spotkaniach.";
            else $events = "";

            tpl_set_var('founds', $founds_count);
            tpl_set_var('hidden', $hidden_count);
            tpl_set_var('events', $events);

            $dbc = new dataBase();
            //get last logs
            $sql = " SELECT `cache_logs`.`cache_id` `cache_id`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `caches`.`name` `name`,
                        `log_types`.`icon_small`, `log_types_text`.`text_combo`
                    FROM `cache_logs`, `caches`, `log_types`, `log_types_text`
                    WHERE `cache_logs`.`user_id`=:1
                    AND `cache_logs`.`deleted`=0
                    AND `cache_logs`.`cache_id`=`caches`.`cache_id`
                    AND `log_types`.`id`=`cache_logs`.`type`
                    AND `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`=:2
                    ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC ";

            $dbc->multiVariableQuery($sql, $usr['userid'], $lang );


            if( $dbc->rowCount() == 0 )
            {
                tpl_set_var('lastlogs', $no_logs);
            }
            else
            {
                $logs = '';
                for ($i = 0; $i < $dbc->rowCount(); $i++)
                {
                    $bgcolor = ( $i% 2 )? $bgcolor1 : $bgcolor2;


                    $record_logs = $dbc->dbResultFetch();

                    $tmp_log = $log_line;


                    $tmp_log = mb_ereg_replace('{bgcolor}', $bgcolor, $tmp_log);
                    $tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], ucfirst(tr('logType'.$record_logs['type'])) /*$record_logs['text_combo']*/), $tmp_log);
                    $tmp_log = mb_ereg_replace('{logtype}', ucfirst(tr('logType'.$record_logs['type'])) /*$record_logs['text_combo']*/, $tmp_log);
                    $tmp_log = mb_ereg_replace('{date}', fixPlMonth(strftime($dateformat , strtotime($record_logs['date']))), $tmp_log);
                    $tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
                    $tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_log);

                    $logs .= "\n" . $tmp_log;
                }
                tpl_set_var('lastlogs', $logs);
            }

            unset( $dbc );

            //get last hidden caches
            $dbc = new dataBase();
             $sql = "   SELECT  `cache_id`, `name`, `date_hidden`, `status`,
                            `cache_status`.`id` AS `cache_status_id`, `cache_status`.{lang} AS `cache_status_text`
                        FROM `caches`, `cache_status`
                        WHERE `user_id`=:1
                          AND `cache_status`.`id`=`caches`.`status`
                          AND `caches`.`status` != 5
                        ORDER BY `date_hidden` DESC, `caches`.`date_created` DESC
                        LIMIT 20";

            $sql = mb_ereg_replace('{lang}', $lang, $sql);

            $dbc->multiVariableQuery($sql, $usr['userid'] );


            if( $dbc->rowCount() == 0 )
            {
                tpl_set_var('lastcaches', $no_hiddens);
            }
            else
            {
                $caches = '';
                for ($i = 0; $i < $dbc->rowCount(); $i++)
                {
                    $bgcolor = ( $i% 2 )? $bgcolor1 : $bgcolor2;

                    $record_logs = $dbc->dbResultFetch();

                    $tmp_cache = $cache_line;

                    $tmp_cache = mb_ereg_replace('{bgcolor}', $bgcolor, $tmp_cache);
                    $tmp_cache = mb_ereg_replace('{cacheimage}', icon_cache_status($record_logs['status'], $record_logs['cache_status_text']), $tmp_cache);
                    $tmp_cache = mb_ereg_replace('{cachestatus}', htmlspecialchars($record_logs['cache_status_text'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
                    $tmp_cache = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_cache);
                    $tmp_cache = mb_ereg_replace('{date}', strftime($dateformat , strtotime($record_logs['date_hidden'])), $tmp_cache);
                    $tmp_cache = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);

                    $caches .= "\n" . $tmp_cache;
                }
                tpl_set_var('lastcaches', $caches);
            }
            unset( $dbc );

            //get not published caches
            $dbc = new dataBase();
            $sql = "    SELECT  `caches`.`cache_id`, `caches`.`name`, `caches`.`date_hidden`, `caches`.`date_activate`, `caches`.`status`, `cache_status`.{lang} AS `cache_status_text`
                        FROM `caches`, `cache_status`
                        WHERE `user_id`=:1
                        AND `cache_status`.`id`=`caches`.`status`
                        AND `caches`.`status` = 5
                        ORDER BY `date_activate` DESC, `caches`.`date_created` DESC";

            $sql = mb_ereg_replace('{lang}', $lang, $sql);

            $dbc->multiVariableQuery($sql, $usr['userid'] );


            if( $dbc->rowCount() == 0 )
            {
                tpl_set_var('notpublishedcaches', $no_notpublished);
            }
            else
            {
                $caches = '';
                for ($i = 0; $i < $dbc->rowCount(); $i++)
                {
                    $record_logs = $dbc->dbResultFetch();

                    $tmp_cache = $cache_notpublished_line;

                    $tmp_cache = mb_ereg_replace('{cacheimage}', icon_cache_status($record_caches['status'], $record_caches['cache_status_text']), $tmp_cache);
                    $tmp_cache = mb_ereg_replace('{cachestatus}', htmlspecialchars($record_caches['cache_status_text'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
                    $tmp_cache = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_caches['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_cache);
                    if(is_null($record_caches['date_activate']))
                    {
                        $tmp_cache = mb_ereg_replace('{date}', $no_time_set, $tmp_cache);
                    }
                    else
                    {
                        $tmp_cache = mb_ereg_replace('{date}', strftime($datetimeformat , strtotime($record_caches['date_activate'])), $tmp_cache);
                    }
                    $tmp_cache = mb_ereg_replace('{cachename}', htmlspecialchars($record_caches['name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);

                    $caches .= "\n" . $tmp_cache;
                }
                tpl_set_var('notpublishedcaches', $caches);


            }
            unset( $dbc );

            // get number of sent emails
            $dbc = new dataBase();
            $emails_sent = '0';
            $sql = "SELECT COUNT(*) AS `emails_sent` FROM `email_user` WHERE `from_user_id`=:1";
            $dbc->multiVariableQuery($sql, $usr['userid'] );
            $row = $dbc->dbResultFetch();


            if( $dbc->rowCount() )
                $emails_sent = $row['emails_sent'];

            tpl_set_var('emails_sent', $emails_sent);

            unset( $dbc );
        }
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
