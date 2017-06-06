<?php

use Utils\Database\XDb;
use lib\Objects\User\User;
use Utils\Text\TextConverter;

//prepare the templates and include all neccessary
if (!isset($rootpath))
    global $rootpath;
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {

        if ($usr['admin'])
            tpl_set_var('reports', "<b>" . tr('administrating_oc') . ":</b><br />[<a href='viewreports.php'>" . tr('view_reports') . "</a>]");
        else
            tpl_set_var('reports', '');

        require($stylepath . '/myhome.inc.php');
        require($stylepath . '/lib/icons.inc.php');

        $tplname = 'myhome';
        tpl_set_var('username', htmlspecialchars($usr['username'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('userid', htmlspecialchars($usr['userid'], ENT_COMPAT, 'UTF-8'));


        //get user record
        $userid = $usr['userid'];
        $user = new User(array('userId' => $userid));
        $hidden_count=$user->getHiddenGeocachesCount();
        $founds_count=$user->getFoundGeocachesCount();
        $events_count=$user->getEventsAttendsCount();
        $notfounds_count=$user->getNotFoundGeocachesCount();
        $log_notes_count=$user->getLogNotesCount();

        if ($events_count > 0)
            $events = tr('you_have_participated_in') . " " . $events_count . " " . tr('found_x_events') . ".";
        else
            $events = "";

        if ((date('m') == 4) and ( date('d') == 1)) {
            tpl_set_var('founds', tr('you_have_found') . " " . rand(0, 13) . " " . tr('found_beer_caches') . ".");
            tpl_set_var('hidden', $hidden_count);
            tpl_set_var('events', $events);
        } else {
            tpl_set_var('founds', tr('you_have_found') . " " . $founds_count . " " . tr('found_x_caches') . ".");
            tpl_set_var('hidden', $hidden_count);
            tpl_set_var('events', $events);
        }
        //get last logs
        $rs_logs = XDb::xSql("
                    SELECT `cache_logs`.`cache_id` `cache_id`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `caches`.`name` `name`,
                        `log_types`.`icon_small`, `log_types_text`.`text_combo`
                    FROM `cache_logs`, `caches`, `log_types`, `log_types_text`
                    WHERE `cache_logs`.`user_id`= ?
                    AND `cache_logs`.`cache_id`=`caches`.`cache_id`
                    AND `cache_logs`.`deleted`=0
                    AND `log_types`.`id`=`cache_logs`.`type`
                    AND `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`= ?
                    ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC
                    LIMIT 10", $usr['userid'], $lang);

        if (XDb::xNumRows($rs_logs) == 0) {

            tpl_set_var('lastlogs', $no_logs);
        } else {
            $logs = '';
            while( $record_logs = XDb::xFetchArray($rs_logs)){

                $tmp_log = $log_line;
                $tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], ucfirst(tr('logType' . $record_logs['type'])) /* $record_logs['text_combo'] */), $tmp_log);
                $tmp_log = mb_ereg_replace('{logtype}', ucfirst(tr('logType' . $record_logs['type'])) /* $record_logs['text_combo'] */, $tmp_log);
                $tmp_log = mb_ereg_replace('{date}', TextConverter::fixPlMonth(strftime(
                    $GLOBALS['config']['dateformat'], strtotime($record_logs['date']))), $tmp_log);
                $tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
                $tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_log);

                $logs .= "\n" . $tmp_log;
            }
            tpl_set_var('lastlogs', $logs);
        }

        //get last hidden caches
        if (checkField('cache_status', $lang))
            $lang_db = $lang;
        else
            $lang_db = "en";

        $rs_caches = XDb::xSql("  SELECT  `cache_id`, `name`, `date_hidden`, `status`,
                            `cache_status`.`id` AS `cache_status_id`, `cache_status`.`".XDb::xEscape($lang_db)."` AS `cache_status_text`
                        FROM `caches`, `cache_status`
                        WHERE `user_id`= ?
                          AND `cache_status`.`id`=`caches`.`status`
                          AND `caches`.`status` != 5
                        ORDER BY `date_hidden` DESC, `caches`.`date_created` DESC
                        LIMIT 20", $usr['userid']);
        if (XDb::xNumRows($rs_caches) == 0) {
            tpl_set_var('lastcaches', $no_hiddens);
        } else {
            $caches = '';
            while($record_logs = XDb::xFetchArray($rs_caches)) {

                $tmp_cache = $cache_line;

                $tmp_cache = mb_ereg_replace('{cacheimage}', icon_cache_status($record_logs['status'], $record_logs['cache_status_text']), $tmp_cache);
                $tmp_cache = mb_ereg_replace('{cachestatus}', htmlspecialchars($record_logs['cache_status_text'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
                $tmp_cache = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_cache);
                $tmp_cache = mb_ereg_replace('{date}', TextConverter::fixPlMonth(strftime(
                    $GLOBALS['config']['dateformat'], strtotime($record_logs['date_hidden']))), $tmp_cache);
                $tmp_cache = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);

                $caches .= "\n" . $tmp_cache;
            }
            tpl_set_var('lastcaches', $caches);
        }

        //get not published caches
        $rs_caches = XDb::xSql("
                        SELECT  `caches`.`cache_id`, `caches`.`name`,
                            `caches`.`date_hidden`, `caches`.`date_activate`,
                            `caches`.`status`,
                            `cache_status`.`".XDb::xEscape($lang_db)."` AS `cache_status_text`
                        FROM `caches`, `cache_status`
                        WHERE `user_id`= ?
                        AND `cache_status`.`id`=`caches`.`status`
                        AND `caches`.`status` = 5
                        ORDER BY `date_activate` DESC,
                            `caches`.`date_created` DESC ", $usr['userid']);
        if (XDb::xNumRows($rs_caches) == 0) {
            tpl_set_var('notpublishedcaches', $no_notpublished);
        } else {
            $caches = '';
            while ($record_caches = XDb::xFetchArray($rs_caches) ){

                $tmp_cache = $cache_notpublished_line;

                $tmp_cache = mb_ereg_replace('{cacheimage}', icon_cache_status($record_caches['status'], $record_caches['cache_status_text']), $tmp_cache);
                $tmp_cache = mb_ereg_replace('{cachestatus}', htmlspecialchars($record_caches['cache_status_text'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
                $tmp_cache = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_caches['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_cache);
                if (is_null($record_caches['date_activate'])) {
                    $tmp_cache = mb_ereg_replace('{date}', $no_time_set, $tmp_cache);
                } else {
                    $tmp_cache = mb_ereg_replace('{date}', TextConverter::fixPlMonth(strftime(
                        $GLOBALS['config']['datetimeformat'], strtotime($record_caches['date_activate']))), $tmp_cache);
                }
                $tmp_cache = mb_ereg_replace('{cachename}', htmlspecialchars($record_caches['name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);

                $caches .= "\n" . $tmp_cache;
            }
            tpl_set_var('notpublishedcaches', $caches);
        }

        //get last logs in your caches
        $rs_logs = XDb::xSql("
                    SELECT `cache_logs`.`cache_id` `cache_id`, `cache_logs`.`type` `type`,
                            `cache_logs`.`date` `date`, `caches`.`name` `name`,
                            `log_types`.`icon_small`, `log_types_text`.`text_combo`,
                            `cache_logs`.`user_id` `user_id`, `user`.`username` `username`
                    FROM `cache_logs`, `caches`, `log_types`, `log_types_text`, `user`
                    WHERE `caches`.`user_id`= ?
                    AND `cache_logs`.`cache_id`=`caches`.`cache_id`
                    AND `cache_logs`.`deleted`=0
                    AND `user`.`user_id`=`cache_logs`.`user_id`
                    AND `log_types`.`id`=`cache_logs`.`type`
                    AND `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`= ?
                    ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC
                    LIMIT 10", $usr['userid'], $lang);

        if (XDb::xNumRows($rs_logs) == 0) {
            tpl_set_var('last_logs_in_your_caches', $no_logs);
        } else {
            $logs = '';

            while( $record_logs = XDb::xFetchArray($rs_logs) ){

                $tmp_log = $cache_line_my_caches;
                $tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], ucfirst(tr('logType' . $record_logs['type'])) /* $record_logs['text_combo'] */), $tmp_log);
                $tmp_log = mb_ereg_replace('{logtype}', ucfirst(tr('logType' . $record_logs['type'])) /* $record_logs['text_combo'] */, $tmp_log);
                $tmp_log = mb_ereg_replace('{date}', TextConverter::fixPlMonth(strftime(
                    $GLOBALS['config']['dateformat'], strtotime($record_logs['date']))), $tmp_log);
                $tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
                $tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars($record_logs['cache_id'], ENT_COMPAT, 'UTF-8'), $tmp_log);
                // ukrywanie nicka autora komentarza COG
                // (Łza)
                if (($record_logs['type'] == 12) && (!$usr['admin'])) {
                    $tmp_log = mb_ereg_replace('{userid}', htmlspecialchars('0', ENT_COMPAT, 'UTF-8'), $tmp_log);
                    $tmp_log = mb_ereg_replace('{username}', htmlspecialchars('Centrum Obsługi Geocachera', ENT_COMPAT, 'UTF-8'), $tmp_log);
                } else {
                    $tmp_log = mb_ereg_replace('{username}', htmlspecialchars($record_logs['username'], ENT_COMPAT, 'UTF-8'), $tmp_log);
                    $tmp_log = mb_ereg_replace('{userid}', htmlspecialchars($record_logs['user_id'], ENT_COMPAT, 'UTF-8'), $tmp_log);
                }
                // koniec ukrywania nicka autora komentarza COG

                $logs .= "\n" . $tmp_log;
            }
            tpl_set_var('last_logs_in_your_caches', $logs);
        }

        // get number of sent emails
        $emails_sent = '0';
        $resp = XDb::xSql("SELECT COUNT(*) AS `emails_sent` FROM `email_user` WHERE `from_user_id`= ?", $usr['userid']);
        if ($row = XDb::xFetchArray($resp))
            $emails_sent = $row['emails_sent'];

        tpl_set_var('emails_sent', $emails_sent);
    }
}

//make the template and send it out
tpl_BuildTemplate();
