<?php

require_once('./lib/common.inc.php');
require($stylepath . '/lib/icons.inc.php');
require($stylepath . '/viewcache.inc.php');
require($stylepath . '/viewlogs.inc.php');
require($stylepath . '/smilies.inc.php');

if(isset($_REQUEST['geocacheId']) && $_REQUEST['geocacheId'] != ''){
    $geocacheId = $_REQUEST['geocacheId'];
} else {
    ddd('error');
}
if(isset($_REQUEST['owner_id']) && $_REQUEST['owner_id'] != ''){
    $owner_id = $_REQUEST['owner_id'];
} else {
    ddd('error - owner_id');
}
if(isset($_REQUEST['offset']) && $_REQUEST['offset'] != ''){
    $offset = (int) $_REQUEST['offset'];
} else {
    $offset = 0;
}
if(isset($_REQUEST['limit']) && $_REQUEST['limit'] != ''){
    $limit = (int) $_REQUEST['limit'];
} else {
    $limit = 5;
}



if ($usr == false && $hide_coords) {
    $disable_spoiler_view = true; //hide any kind of spoiler if usr not logged in
} else {
    $disable_spoiler_view = false;
}

if(isset($_REQUEST['includeDeletedLogs']) && $_REQUEST['includeDeletedLogs'] == 1){
    $includeDeletedLogs = true;
} else {
    $includeDeletedLogs = false;
}

$logEnteryController = new \lib\Controllers\LogEnteryController();
$logEneries = $logEnteryController->loadLogsFromDb($geocacheId, $includeDeletedLogs, $offset, $limit);
$result = '';
foreach ($logEneries as $record) {
    $record['text_listing'] = ucfirst(tr('logType' . $record['type'])); //add new attrib 'text_listing based on translation (instead of query as before)'

    $show_deleted = "";
    $processed_text = "";
    if (isset($record['deleted']) && $record['deleted']) {
        if ($usr['admin']) {
            $show_deleted = "show_deleted";
            $processed_text = $record['text'];
        } else {
            // Boguś z Polska, 2014-11-15
            // for 'Needs maintenance', 'Ready to search' and 'Temporarly unavailable' log types
            if ($record['type'] == 5 || $record['type'] == 10 || $record['type'] == 11) {
                // hide if user is not logged in
                if (!isset($usr)) {
                    if ($show_one_log != '') {
                        exit;
                    } else {
                        continue;
                    }
                }
                // hide if user is neither a geocache owner nor log author
                if ($owner_id != $usr['userid'] && $record['userid'] != $usr['userid']) {
                    if ($show_one_log != '') {
                        exit;
                    } else {
                        continue;
                    }
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
                        $comm_replace.=" " . tr('vl_by_COG');
                        $delByCOG = true;
                    }
                }
                if (!isset($byCOG) || $delByCOG == false) {
                    $comm_replace.=" " . tr('vl_by_user') . " " . $record['del_by_username'];
                }
            };
            if (isset($record['last_deleted'])) {
                $comm_replace.=" " . tr('vl_on_date') . " " . fixPlMonth(htmlspecialchars(strftime($dateformat, strtotime($record['last_deleted'])), ENT_COMPAT, 'UTF-8'));
            }
            $comm_replace.=".";
            $processed_text = $comm_replace;
        }
    } else {
        $processed_text = $record['text'];
    }


    // add edit footer if record has been modified
    $record_date_create = date_create($record['date_created']);

    if ($record['edit_count'] > 0) {
        //check if editted at all
        $edit_footer = "<div><small>" . tr('vl_Recently_modified_on') . " " . fixPlMonth(htmlspecialchars(strftime($datetimeformat, strtotime($record['last_modified'])), ENT_COMPAT, 'UTF-8'));
        if (!$usr['admin'] && isset($record['edit_by_admin'])) {
            if ($record['edit_by_username'] == $record['username']) {
                $byCOG = false;
            } else {
                $edit_footer.=" " . tr('vl_by_COG');
                $byCOG = true;
            }
        }
        if (!isset($byCOG) || $byCOG == false) {
            $edit_footer.=" " . tr('vl_by_user') . " " . $record['edit_by_username'];
        }
        if ($record_date_create > $edit_count_date_from) { //check if record created after implementation date (to avoid false readings for record changed before) - actually nor in use
            $edit_footer.=" - " . tr('vl_totally_modified') . " " . $record['edit_count'] . " ";
            if ($record['edit_count'] > 1) {
                $edit_footer.=tr('vl_count_plural');
            } else {
                $edit_footer.=tr('vl_count_singular');
            }
        }

        $edit_footer.=".</small></div>";
    } else {
        $edit_footer = "";
    }



    $tmplog = read_file($stylepath . '/viewcache_log.tpl.php');
//END: same code ->viewlogs.php / viewcache.php
    $tmplog_username = htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8');
    $tmplog_date = fixPlMonth(htmlspecialchars(strftime($dateformat, strtotime($record['date'])), ENT_COMPAT, 'UTF-8'));
    // replace smilies in log-text with images

    $dateTimeTmpArray = explode(' ', $record['date']);
    $tmplog = mb_ereg_replace('{time}', substr($dateTimeTmpArray[1], 0, -3), $tmplog);



    // display user activity (by Łza 2012)
    if ((date('m') == 4) and ( date('d') == 1)) {
        $tmplog_username_aktywnosc = ' (<img src="tpl/stdstyle/images/blue/thunder_ico.png" alt="user activity" width="13" height="13" border="0" title="' . tr('viewlog_aktywnosc') . '"/>' . rand(1, 9) . ') ';
    } else {
        $tmplog_username_aktywnosc = ' (<img src="tpl/stdstyle/images/blue/thunder_ico.png" alt="user activity" width="13" height="13" border="0" title="' . tr('viewlog_aktywnosc') . ' [' . $record['znalezione'] . '+' . $record['nieznalezione'] . '+' . $record['ukryte'] . ']"/>' . ($record['ukryte'] + $record['znalezione'] + $record['nieznalezione']) . ') ';
    }

    // hide nick of athor of COG(OC Team) for user
    if ($record['type'] == 12 && !$usr['admin']) {
        $record['userid'] = '0';
        $tmplog_username_aktywnosc = '';
        $tmplog_username = tr('cog_user_name');
    }

    $tmplog = mb_ereg_replace('{username_aktywnosc}', $tmplog_username_aktywnosc, $tmplog);

    // mobile caches by Łza
    if (($record['type'] == 4) && ($record['mobile_latitude'] != 0)) {
        $tmplog_kordy_mobilnej = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($record['mobile_latitude']), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($record['mobile_longitude']), ENT_COMPAT, 'UTF-8'));
        $tmplog = mb_ereg_replace('{kordy_mobilniaka}', $record['km'] . ' km [<img src="tpl/stdstyle/images/blue/szczalka_mobile.png" title="' . tr('viewlog_kordy') . '" />' . $tmplog_kordy_mobilnej . ']', $tmplog);
    } else
        $tmplog = mb_ereg_replace('{kordy_mobilniaka}', ' ', $tmplog);

    if ($record['text_html'] == 0) {
        $processed_text = htmlspecialchars($processed_text, ENT_COMPAT, 'UTF-8');
        $processed_text = help_addHyperlinkToURL($processed_text);
    } else {
        $processed_text = userInputFilter::purifyHtmlStringAndDecodeHtmlSpecialChars($processed_text);
    }
    $processed_text = str_replace($smileytext, $smileyimage, $processed_text);

    $tmplog_text = $processed_text . $edit_footer;

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
    if (!isset($record['deleted'])){
        $record['deleted'] = false;
    }
    if ($record['deleted'] != 1) {
        if ($record['user_id'] == $usr['userid']) {
            $logfunctions = $functions_start . $tmpedit . $functions_middle;
            if ($record['type'] != 12 && ($usr['userid'] == $owner_id || $usr['admin'] == false)) {
                $logfunctions .=$tmpremove . $functions_middle;
            }
            if ($usr['admin']) {
                $logfunctions .= $tmpremove . $functions_middle;
            }

            $logfunctions .= $tmpnewpic . $functions_end;
        } else if ($usr['admin']) {
            $logfunctions = $functions_start . $tmpedit . $functions_middle . $tmpremove . $functions_middle . $functions_end;
        } elseif ($owner_id == $usr['userid']) {

            $logfunctions = $functions_start;
            if ($record['type'] != 12) {
                $logfunctions .= $tmpremove;
            }
            $logfunctions .= $functions_end;
        }
    } else if ($usr['admin']) {
        $logfunctions = $functions_start . $tmpedit . $functions_middle . $tmpRevert . $functions_middle . $functions_end;
    }

    $tmplog = mb_ereg_replace('{logfunctions}', $logfunctions, $tmplog);

    // pictures
    //START: edit by FelixP - 2013'10
    if (($record['picturescount'] > 0) && (($record['deleted'] == false) || ($usr['admin']))) { // show pictures if (any added) and ((not deleted) or (user is admin))
        //END: edit by FelixP - 2013'10
        $logpicturelines = '';
        $append_atag = '';
        if (!isset($dbc)) {
            $dbc = new dataBase();
        }
        $thatquery = "SELECT `url`, `title`, `uuid`, `user_id`, `spoiler` FROM `pictures` WHERE `object_id`=:1 AND `object_type`=1";
        $dbc->multiVariableQuery($thatquery, $record['logid']);
        $pic_count = $dbc->rowCount();
        for ($j = 0; $j < $pic_count; $j++) {
            if (!isset($showspoiler)){
               $showspoiler = '';
            }
            $pic_record = $dbc->dbResultFetch();
            $thisline = $logpictureline;

            if ($disable_spoiler_view && intval($pic_record['spoiler']) == 1) {  // if hide spoiler (due to user not logged in) option is on prevent viewing pic link and show alert
                $thisline = mb_ereg_replace('{log_picture_onclick}', "alert('" . $spoiler_disable_msg . "'); return false;", $thisline);
                $thisline = mb_ereg_replace('{link}', 'index.php', $thisline);
                $thisline = mb_ereg_replace('{longdesc}', 'index.php', $thisline);
            } else {
                $thisline = mb_ereg_replace('{log_picture_onclick}', "enlarge(this)", $thisline);
                $thisline = mb_ereg_replace('{link}', $pic_record['url'], $thisline);
                $thisline = mb_ereg_replace('{longdesc}', str_replace("images/uploads", "upload", $pic_record['url']), $thisline);
            };

            $thisline = mb_ereg_replace('{imgsrc}', 'thumbs2.php?' . $showspoiler . 'uuid=' . urlencode($pic_record['uuid']), $thisline);
            $thisline = mb_ereg_replace('{title}', htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8'), $thisline);


            if ($pic_record['user_id'] == $usr['userid'] || $usr['admin']) {
                $thisfunctions = $remove_picture;
                $thisfunctions = mb_ereg_replace('{uuid}', urlencode($pic_record['uuid']), $thisfunctions);
                $thisline = mb_ereg_replace('{functions}', $thisfunctions, $thisline);
            } else
                $thisline = mb_ereg_replace('{functions}', '', $thisline);

            $logpicturelines .= $thisline;
        }
        $logpicturelines = mb_ereg_replace('{lines}', $logpicturelines, $logpictures);
        $tmplog = mb_ereg_replace('{logpictures}', $logpicturelines, $tmplog);
    } else {
        $tmplog = mb_ereg_replace('{logpictures}', '', $tmplog);
    }
        $result .= $tmplog;
}

echo $result;