<?php

use src\Controllers\LogEntryController;
use src\Controllers\PictureController;
use src\Models\Coordinates\Coordinates;
use src\Models\OcConfig\OcConfig;
use src\Utils\Database\OcDb;
use src\Utils\DateTime\Year;
use src\Utils\Text\SmilesInText;
use src\Utils\Text\TextConverter;
use src\Utils\Text\UserInputFilter;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;
use src\Models\ApplicationContainer;

require_once(__DIR__ . '/lib/common.inc.php');

//set here the template to process
$view = tpl_getView();
$view->loadFancyBox();
$view->setTemplate('viewlogs');

tpl_set_var('viewcache_js', Uri::getLinkWithModificationTime("/views/viewcache/viewcache.js"));
require(__DIR__ . '/src/Views/lib/icons.inc.php');
require(__DIR__ . '/src/Views/viewcache.inc.php');
require(__DIR__ . '/src/Views/viewlogs.inc.php');

$loggedUser = ApplicationContainer::GetAuthorizedUser();

$view->setVar('isUserAuthorized', is_object($loggedUser));

$cache_id = 0;
if (isset($_REQUEST['cacheid'])) {
    $cache_id = (int)$_REQUEST['cacheid'];
}
if (isset($_REQUEST['logid'])) {
    $logid = (int)$_REQUEST['logid'];
} else {
    $logid = false;
}
$view->setVar('logId', $logid);

$start = 0;
if (isset($_REQUEST['start'])) {
    $start = $_REQUEST['start'];
    if (!is_numeric($start)) {
        $start = 0;
    }
}
$count = 99999;
if (isset($_REQUEST['count'])) {
    $count = $_REQUEST['count'];
    if (!is_numeric($count)) {
        $count = 999999;
    }
}

if (!$loggedUser && $hide_coords) {
    $disable_spoiler_view = true; //hide any kind of spoiler if usr not logged in
} else {
    $disable_spoiler_view = false;
}
$dbc = OcDb::instance();
if ($cache_id != 0) {
    //get cache record

    $s = $dbc->multiVariableQuery(
        "SELECT `user_id`, `name`, `founds`, `notfounds`, `notes`, `status`, `type` FROM `caches`
            WHERE `caches`.`cache_id`=:1 LIMIT 1", $cache_id);

    if ($dbc->rowCount($s) == 0) {
        $cache_id = 0;
    } else {
        $cache_record = $dbc->dbResultFetchOneRowOnly($s);
        // check if the cache is published, if not only the owner is allowed to view the log
        if (($cache_record['status'] == 4 || $cache_record['status'] == 5 || $cache_record['status'] == 6) &&
            ($loggedUser && $cache_record['user_id'] != $loggedUser->getUserId() && !$loggedUser->hasOcTeamRole())) {
            $cache_id = 0;
        }
    }
} else {

    //get cache record

    $s = $dbc->multiVariableQuery(
        "SELECT `cache_logs`.`cache_id`,`caches`.`user_id`, `caches`.`name`, `caches`.`founds`,
                    `caches`.`notfounds`, `caches`.`notes`, `caches`.`status`, `caches`.`type`
            FROM `caches`,`cache_logs`
            WHERE `cache_logs`.`id`=:1
                AND `caches`.`cache_id`=`cache_logs`.`cache_id`
            LIMIT 1", $logid);

    if ($dbc->rowCount($s) == 0) {
        $cache_id = 0;
    } else {
        $cache_record = $dbc->dbResultFetchOneRowOnly($s);
        // check if the cache is published, if not only the owner is allowed to view the log
        if (($cache_record['status'] == 4 || $cache_record['status'] == 5 || $cache_record['status'] == 6) &&
            ($loggedUser && $cache_record['user_id'] != $loggedUser->getUserId() && !$loggedUser->hasOcTeamRole())) {
            $cache_id = 0;
        } else {
            $cache_id = $cache_record['cache_id'];
        }
    }
}

if ($cache_id != 0) {

    // detailed cache access logging
    if (@$enable_cache_access_logs) {
        if (!isset($dbc)) {
            $dbc = OcDb::instance();
        }
        $user_id = $loggedUser ? $loggedUser->getUserId() : null;
        $access_log = @$_SESSION['CACHE_ACCESS_LOG_VL_' . $user_id];
        if ($access_log === null) {
            $_SESSION['CACHE_ACCESS_LOG_VL_' . $user_id] = array();
            $access_log = $_SESSION['CACHE_ACCESS_LOG_VL_' . $user_id];
        }
        if (@$access_log[$cache_id] !== true) {
            $dbc->multiVariableQuery(
                'INSERT INTO CACHE_ACCESS_LOGS
                            (event_date, cache_id, user_id, source, event, ip_addr, user_agent, forwarded_for)
                         VALUES
                            (NOW(), :1, :2, \'B\', \'view_logs\', :3, :4, :5)',
                $cache_id, $user_id, $_SERVER['REMOTE_ADDR'],
                (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''),
                (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '')
            );
            $access_log[$cache_id] = true;
            $_SESSION['CACHE_ACCESS_LOG_VL_' . $user_id] = $access_log;
        }
    }

    //ok, cache is here, let's process
    $owner_id = $cache_record['user_id'];

    //cache data
    $cache_name = htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8');
    $view->setSubtitle($cache_name . ' - ');
    tpl_set_var('cachename', $cache_name);
    tpl_set_var('cacheid', $cache_id);
    $view->setVar('cacheType', $cache_record['type']);

    if ($cache_record['type'] == 6) {
        tpl_set_var('found_icon', $exist_icon);
        tpl_set_var('notfound_icon', $wattend_icon);
    } else {
        tpl_set_var('found_icon', $found_icon);
        tpl_set_var('notfound_icon', $notfound_icon);
    }
    tpl_set_var('note_icon', $note_icon);

    tpl_set_var('founds', htmlspecialchars($cache_record['founds'], ENT_COMPAT, 'UTF-8'));
    tpl_set_var('notfounds', htmlspecialchars($cache_record['notfounds'], ENT_COMPAT, 'UTF-8'));
    tpl_set_var('notes', htmlspecialchars($cache_record['notes'], ENT_COMPAT, 'UTF-8'));
    tpl_set_var('total_number_of_logs', htmlspecialchars($cache_record['notes'] + $cache_record['notfounds'] + $cache_record['founds'], ENT_COMPAT, 'UTF-8'));

    //check number of pictures in logs
    $rspiclogs = $dbc->multiVariableQueryValue("SELECT COUNT(*) FROM `pictures`,`cache_logs` WHERE `pictures`.`object_id`=`cache_logs`.`id` AND `pictures`.`object_type`=1 AND `cache_logs`.`cache_id`= :1", 0, $cache_id);

    if ($rspiclogs != 0) {
        tpl_set_var('gallery', $gallery_icon . '&nbsp;' . $rspiclogs . 'x&nbsp;' . mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $gallery_link));
    } else {
        tpl_set_var('gallery', '');
    }

    if (isset($_REQUEST['showdel'])) {
        $showDel = $_REQUEST["showdel"];
    } else if (isset($_SESSION["showdel"])) {
        $showDel = $_SESSION["showdel"];
    }
    if (($loggedUser && $loggedUser->hasOcTeamRole()) || $logid) {
        $showhidedel_link = ""; //no need to hide/show deletion icon for COG (they always see deletions) or this is single log call
    } else {
        $del_count = $dbc->multiVariableQueryValue("SELECT count(*) number FROM `cache_logs` WHERE `deleted`=1 and `cache_id`=:1", 0, $cache_id);
        if ($del_count == 0) {
            $showhidedel_link = ""; //don't show link if no deletion '
        } else {
            if (isset($showDel) && $showDel == 'y') {
                $showhidedel_link = $hide_del_link;
            } else {
                $showhidedel_link = $show_del_link;
            }

            $showhidedel_link = str_replace('{thispage}', 'viewlogs.php', $showhidedel_link); //$show_del_link is defined in viecache.inc.php - for both viewlogs and viewcashe .php
        }
    }

    tpl_set_var('showhidedel_link', mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $showhidedel_link));

    isset($showDel) && $showDel == 'y' ? $HideDeleted = false : $HideDeleted = true;
    $includeDeletedLogs = true;
    If (($HideDeleted && !$logid && !($loggedUser && $loggedUser->hasOcTeamRole()))) {
        //hide deletions if (hide_deletions opotions is on and this is single_log call=not and user is not COG)
        $includeDeletedLogs = false;
    }

    $logs = '';

    $logEntryController = new LogEntryController();
    if ($logid) { /* load and display one log only */
        $logEneries = $logEntryController->loadLogsFromDb($cache_id, $includeDeletedLogs, 0, 1, $logid);
    } else {
        $logEneries = $logEntryController->loadLogsFromDb($cache_id, $includeDeletedLogs, 0, 9999);
    }

    $logfilterConfig = OcConfig::instance()->getLogfilterConfig();
    $view->setVar(
        'enableLogsFiltering',
        !empty($logfilterConfig['enable_logs_filtering'])
    );
    $tmpSrcLog = file_get_contents(__DIR__ . '/src/Views/viewcache_log.tpl.php');

    foreach ($logEneries as $record) {
        $record['text_listing'] = ucfirst(tr('logType' . $record['type'])); //add new attrib 'text_listing based on translation (instead of query as before)'

        $show_deleted = "";
        $processed_text = "";
        if (isset($record['deleted']) && $record['deleted']) {
            if ($loggedUser && $loggedUser->hasOcTeamRole()) {
                $show_deleted = "show_deleted";
                $processed_text = $record['text'];
                $processed_text .= "[" . tr('vl_Record_deleted');
                if (isset($record['del_by_username']) && $record['del_by_username']) {
                    $processed_text .= " " . tr('vl_by_user') . " " . $record['del_by_username'];
                }
                if (isset($record['last_deleted'])) {
                    $processed_text .= " " . tr('vl_on_date') . " " . TextConverter::fixPlMonth(htmlspecialchars(strftime(
                            $GLOBALS['config']['dateformat'], strtotime($record['last_deleted'])), ENT_COMPAT, 'UTF-8'));
                }
                $processed_text .= "]";
            } else {
                // Boguś z Polska, 2014-11-15
                // for 'Needs maintenance', 'Ready to search' and 'Temporarly unavailable' log types
                if ($record['type'] == 5 || $record['type'] == 10 || $record['type'] == 11) {
                    // hide if user is not logged in
                    if (!$loggedUser) {
                        continue;
                    }
                    // hide if user is neither a geocache owner nor log author
                    if (!$loggedUser || ($owner_id != $loggedUser->getUserId() && $record['userid'] != $loggedUser->getUserId())) {
                        continue;
                    }
                }

                $record['icon_small'] = "log/16x16-trash.png"; //replace record icon with trash icon
                $comm_replace = tr('vl_Record_of_type') . " [" . $record['text_listing'] . "] " . tr('vl_deleted');
                $record['text_listing'] = tr('vl_Record_deleted'); ////replace type of record
                if (isset($record['del_by_username']) && $record['del_by_username']) {
                    if ($record['del_by_admin'] == 1) { //if deleted by Admin
                        if (($record['del_by_username'] == $record['username']) && ($record['type'] != 12)) { // show username in case maker and deleter are same and comment is not Commnent by COG
                            $delByCOG = false;
                        } else {
                            $comm_replace .= " " . tr('vl_by_COG');
                            $delByCOG = true;
                        }
                    }
                    if (!isset($delByCOG) || $delByCOG == false) {
                        $comm_replace .= " " . tr('vl_by_user') . " " . $record['del_by_username'];
                    }
                }
                if (isset($record['last_deleted'])) {
                    $comm_replace .= " " . tr('vl_on_date') . " " . TextConverter::fixPlMonth(htmlspecialchars(strftime(
                            $GLOBALS['config']['dateformat'], strtotime($record['last_deleted'])), ENT_COMPAT, 'UTF-8'));
                }
                $comm_replace .= ".";
                $processed_text = $comm_replace;
            }
        } else {
            $processed_text = $record['text'];
        }

        // add edit footer if record has been modified
        $record_date_create = date_create($record['date_created']);

        if ($record['edit_count'] > 0) {
            //check if editted at all
            $edit_footer = "<div><small>" . tr('vl_Recently_modified_on') . " " . TextConverter::fixPlMonth(htmlspecialchars(
                    strftime($GLOBALS['config']['datetimeformat'], strtotime($record['last_modified'])), ENT_COMPAT, 'UTF-8'));
            if (!($loggedUser && $loggedUser->hasOcTeamRole()) &&
                $record['edit_by_admin'] == true && $record['type'] == 12) {

                $edit_footer .= " " . tr('vl_by_COG');
            } else {
                $edit_footer .= " " . tr('vl_by_user') . " " . $record['edit_by_username'];
            }

            if ($record_date_create > date_create('2005-01-01 00:00')) { //check if record created after implementation date (to avoid false readings for record changed before) - actually nor in use
                $edit_footer .= " - " . tr('vl_totally_modified') . " " . $record['edit_count'] . " ";
                if ($record['edit_count'] > 1) {
                    $edit_footer .= tr('vl_count_plural');
                } else {
                    $edit_footer .= tr('vl_count_singular');
                }
            }

            $edit_footer .= ".</small></div>";
        } else {
            $edit_footer = "";
        }

        $tmplog = $tmpSrcLog;
//END: same code ->viewlogs.php / viewcache.php
        $tmplog_username = htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8');
        $tmplog_date = TextConverter::fixPlMonth(htmlspecialchars(strftime($GLOBALS['config']['dateformat'], strtotime($record['date'])), ENT_COMPAT, 'UTF-8'));

        $dateTimeTmpArray = explode(' ', $record['date']);
        $tmplog = mb_ereg_replace('{time}', substr($dateTimeTmpArray[1], 0, -3), $tmplog);

        // display user activity
        if (Year::isPrimaAprilisToday() && OcConfig::isPAUserStatsRandEnabled()) {
            $tmplog_username_aktywnosc = ' (<img src="/images/blue/thunder_ico.png" alt="user activity" width="13" height="13" border="0" title="' . tr('viewlog_aktywnosc') . '"/>' . rand(1, 9) . ') ';
        } else {
            $tmplog_username_aktywnosc = ' (<img src="/images/blue/thunder_ico.png" alt="user activity" width="13" height="13" border="0" title="' . tr('viewlog_aktywnosc') . ' [' . $record['znalezione'] . '+' . $record['nieznalezione'] . '+' . $record['ukryte'] . ']"/>' . ($record['ukryte'] + $record['znalezione'] + $record['nieznalezione']) . ') ';
        }

        // hide nick of athor of COG(OC Team) for user
        if ($record['type'] == 12 && !($loggedUser && $loggedUser->hasOcTeamRole())) {
            $record['userid'] = '0';
            $tmplog_username_aktywnosc = '';
            $tmplog_username = tr('cog_user_name');
        }

        $tmplog = mb_ereg_replace('{username_aktywnosc}', $tmplog_username_aktywnosc, $tmplog);

        // mobile caches
        if (($record['type'] == 4) && ($record['mobile_latitude'] != 0) && !$disable_spoiler_view) {
            $tmplog_kordy_mobilnej = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(Coordinates::donNotUse_latToDegreeStr($record['mobile_latitude']), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(Coordinates::donNotUse_lonToDegreeStr($record['mobile_longitude']), ENT_COMPAT, 'UTF-8'));
            $tmplog = mb_ereg_replace('{kordy_mobilniaka}', $record['km'] . ' km [<img src="/images/blue/arrow_mobile.png" title="' . tr('viewlog_kordy') . '" />' . $tmplog_kordy_mobilnej . ']', $tmplog);
        } else
            $tmplog = mb_ereg_replace('{kordy_mobilniaka}', ' ', $tmplog);

        if ($record['text_html'] == 0) {
            $processed_text = htmlspecialchars($processed_text, ENT_COMPAT, 'UTF-8');
            $processed_text = TextConverter::addHyperlinkToURL($processed_text);
        } else {
            $processed_text = UserInputFilter::purifyHtmlStringAndDecodeHtmlSpecialChars($processed_text, $record['text_html']);
        }
        $processed_text = SmilesInText::process($processed_text);

        $tmplog_text = $processed_text . $edit_footer;

        $logClasses = (!empty($logfilterConfig['mark_currentuser_logs']) && $loggedUser &&
            $record['userid'] == $loggedUser->getUserId()) ? " currentuser-log" : "";

        $tmplog = mb_ereg_replace('{log_classes}', $logClasses, $tmplog);

        $filterable = "";
        if (!empty($logfilterConfig['enable_logs_filtering'])) {
            $filterable = ':' . $record['type'] . ':';
            if ($record['userid'] == 0) {
                $filterable .= 'octeam';
            } else if ($loggedUser && $record['userid'] == $loggedUser->getUserId()) {
                $filterable .= 'current';
            } else if ($record['userid'] == $owner_id) {
                $filterable .= 'owner';
            }
            $filterable .= ':';
        }
        $tmplog = mb_ereg_replace('{filterable}', $filterable, $tmplog);

        $tmplog = mb_ereg_replace('{show_deleted}', $show_deleted, $tmplog);
        $tmplog = mb_ereg_replace('{username}', $tmplog_username, $tmplog);
        $tmplog = mb_ereg_replace('{userid}', $record['userid'], $tmplog);
        $tmplog = mb_ereg_replace('{date}', $tmplog_date, $tmplog);
        $tmplog = mb_ereg_replace('{type}', $record['text_listing'], $tmplog);
        $tmplog = mb_ereg_replace('{logtext}', $tmplog_text, $tmplog);
        $tmplog = mb_ereg_replace('{logimage}', '<a href="viewlogs.php?logid=' . $record['logid'] . '">' . icon_log_type($record['icon_small'], $record['logid']) . '</a>', $tmplog);
        $tmplog = mb_ereg_replace('{log_id}', $record['logid'], $tmplog);

        //$rating_picture
        if ($record['recommended'] == 1 && $record['type'] == 1)
            $tmplog = mb_ereg_replace('{ratingimage}', '<img src="images/rating-star.png" alt="' . tr('recommendation') . '" />', $tmplog);
        else
            $tmplog = mb_ereg_replace('{ratingimage}', '', $tmplog);

        //user der owner
        $logfunctions = '';
        $tmpedit = mb_ereg_replace('{logid}', $record['logid'], $edit_log);
        $tmpremove = mb_ereg_replace('{logid}', $record['logid'], $remove_log);
        $tmpRevert = mb_ereg_replace('{logid}', $record['logid'], $revertLog);
        $tmpnewpic = mb_ereg_replace('{logid}', $record['logid'], $upload_picture);
        if (!isset($record['deleted'])) {
            $record['deleted'] = false;
        }
        if ($record['deleted'] != 1) {
            if ($loggedUser && $record['user_id'] == $loggedUser->getUserId() && ($record['type'] != 12 ||
                ($loggedUser && $loggedUser->hasOcTeamRole()))) {

                // User is author of log. Can edit, remove and add pictures. If it is OC Team log - user MUST be ACTIVE admin AND owner of this log
                $logfunctions = $functions_start . $tmpedit . $functions_middle . $tmpremove . $functions_middle . $tmpnewpic . $functions_end;
            } elseif ($loggedUser && $owner_id == $loggedUser->getUserId() && $record['type'] != 12) {
                // Cacheowner can only delete logs. Except of OC Team log.
                $logfunctions = $functions_start . $tmpremove . $functions_end;
            } elseif ($loggedUser && $loggedUser->hasOcTeamRole()) {
                // Active admin can remove any log. But not edit or add photos.
                $logfunctions = $functions_start . $tmpremove . $functions_end;
            }
        } else if ($loggedUser && $loggedUser->hasOcTeamRole()) {
            $logfunctions = $functions_start . $tmpRevert . $functions_end;
        }

        $tmplog = mb_ereg_replace('{logfunctions}', $logfunctions, $tmplog);

        // pictures
        if (($record['picturescount'] > 0) && (($record['deleted'] == false) ||
            ($loggedUser && $loggedUser->hasOcTeamRole()))) {
            // show pictures if (any added) and ((not deleted) or (user is admin))

            $logpicturelines = '';
            if (!isset($dbc)) {
                $dbc = OcDb::instance();
            }
            $thatquery = "SELECT `url`, `title`, `uuid`, `user_id`, `spoiler` FROM `pictures` WHERE `object_id`=:1 AND `object_type`=1";
            $s = $dbc->multiVariableQuery($thatquery, $record['logid']);
            $pic_count = $dbc->rowCount($s);
            if (!isset($showspoiler)) {
                $showspoiler = '';
            }
            $pictures = 0;
            while ($pic_record = $dbc->dbResultFetch($s)) {

                if (++$pictures > 4) {
                    $logpicturelines .= '<div style="clear:both"></div>';
                    $pictures -= 4;
                }

                $thisline = $logpictureline;

                if ($disable_spoiler_view && intval($pic_record['spoiler']) == 1) {  // if hide spoiler (due to user not logged in) option is on prevent viewing pic link and show alert
                    $thisline = mb_ereg_replace('{link}', 'index.php', $thisline);
                    $thisline = mb_ereg_replace('{longdesc}', 'index.php', $thisline);
                } else {
                    $thisline = mb_ereg_replace('{link}', $pic_record['url'], $thisline);
                    $thisline = mb_ereg_replace('{longdesc}', str_replace("images/uploads", "upload", $pic_record['url']), $thisline);
                }

                $thisline = mb_ereg_replace(
                    '{imgsrc}',
                    SimpleRouter::getLink(PictureController::class, 'thumbSizeSmall', [$pic_record['uuid']]),
                    $thisline);

                $thisline = mb_ereg_replace('{title}', htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8'), $thisline);

                if ($loggedUser && ($pic_record['user_id'] == $loggedUser->getUserId() || $loggedUser->hasOcTeamRole())) {

                    $thisfunctions =
                        '<span class="removepic">
                                <img src="/images/log/16x16-trash.png" class="icon16" alt="Trash icon">
                                &nbsp;
                                <a class="links" href="' . SimpleRouter::getLink(PictureController::class, 'remove', [$pic_record['uuid']]) . '">'
                        . tr("delete") .
                        '</a>
                              </span>';

                    $thisfunctions = mb_ereg_replace('{uuid}', urlencode($pic_record['uuid']), $thisfunctions);
                    $thisline = mb_ereg_replace('{functions}', $thisfunctions, $thisline);
                } else
                    $thisline = mb_ereg_replace('{functions}', '', $thisline);

                $logpicturelines .= $thisline;
            }
            $logpicturelines = mb_ereg_replace('{lines}', $logpicturelines, $logpictures);
            $tmplog = mb_ereg_replace('{logpictures}', $logpicturelines, $tmplog);
        } else
            $tmplog = mb_ereg_replace('{logpictures}', '', $tmplog);

        $logs .= $tmplog . "\n";
    }
    tpl_set_var('logs', $logs);
} else {
    exit;
}

unset($dbc);
//make the template and send it out
$view->buildView();
