<?php

use Utils\Database\XDb;
use Utils\Database\OcDb;
$linkargs = (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y') ? '&amp;print=y' : '';
$linkargs .= (isset($_REQUEST['nocrypt']) && $_REQUEST['nocrypt'] == '1') ? '&amp;nocrypt=1' : '';

if (isset($_REQUEST['showdel'])) {
    $_SESSION['showdel'] = $_REQUEST['showdel']; //use session in order to keep options on if URL changes
}

if (isset($_REQUEST['print'])) {
    if (isset($_REQUEST['showlogsall'])) {
        $logs_to_display = 999;
        $logbook_display = 1;
        $linkargs .= '&amp;showlogsall=y';
    } else if (isset($_REQUEST['showlogs'])) {
        $logs_to_display = intval($_REQUEST['showlogs']);
        $logbook_display = 1;
        $linkargs .= '&amp;showlogs=' . htmlspecialchars($logs_to_display, ENT_COMPAT, 'UTF-8');
    } else if (isset($_REQUEST['logbook']) && $_REQUEST['logbook'] == 'no') {
        $logs_to_display = 0;
        $logbook_display = 0;
        $linkargs .= '&amp;logbook=no';
    } else
        $logs_to_display = 0;
        $logbook_display = 1;
} else
    $logs_to_display = 5;
    $logbook_display = 1;

// $short_desc_title = 'Charakterisierung: ';

$function_log = "<li><a href='log.php?cacheid={cacheid}'>" . tr('write_to_log') . "</a></li>";
$function_edit = "<li'><a href='editcache.php?cacheid={cacheid}'>" . tr('edit') . "</a></li>";
$function_watch = "<li><a href='watchcache.php?cacheid={cacheid}&amp;target=viewcache.php%3Fcacheid%3D{cacheid}'>" . tr('watch') . "</a></li>";
$function_watch_not = "<li><a href='removewatch.php?cacheid={cacheid}&amp;target=viewcache.php%3Fcacheid%3D{cacheid}'>" . tr('watch_not') . "</a></li>";
$function_ignore = "<li><a href='addignore.php?cacheid={cacheid}&amp;target=viewcache.php%3Fcacheid%3D{cacheid}'>" . tr('ignore') . "</a></li>";
$function_ignore_not = "<li><a href='removeignore.php?cacheid={cacheid}&amp;target=viewcache.php%3Fcacheid%3D{cacheid}'>" . tr('ignore_not') . "</a></li>";
$decrypt_link = '<span style="font-weight:400"><a href="viewcache.php?cacheid={cacheid_urlencode}&amp;nocrypt=1&amp;desclang={desclang}' . $linkargs . '#decrypt-info" onclick="return showHint(event);"><span id="decryptLinkStr">' . tr('decrypt') .'</span><span id="encryptLinkStr" style="display:none">' . tr('encrypt') .'</span></a></span>';
$pictureline = '<a href="{link}">{title}</a><br />';
$pictures = '<p>{picturelines}</p>';

$logpictureline = '<div class="logimage"><div class="img-shadow">'
         .'<a class="example-image-link" href="{longdesc}" data-title="{title}" data-lightbox="example-1"><img class="example-image" src="{imgsrc}" alt="{title}" /></a>'
        . '</div><span class="desc">{title}</span>{functions}</div>';

$logpictures = '<div class="viewlogs-logpictures"><span class="info">' . tr('pictures_included') . ':</span><div class="allimages">{lines}</div></div><br style="clear: both" />';

// Waypoints line
$wpline = '<tr>{stagehide_start}<td align="center" valign="middle"><center><strong>{number}</strong></center></td>{stagehide_end}<td align="center" valign="middle"><center><img src="{wp_icon}" alt="" title="{type}" /></center></td><td style="text-align: left; vertical-align: middle;">{type}</td><td align="left" valign="middle"><b><span style="color: rgb(88,144,168)">{lat_lon}</span></b></td><td align="left" valign="middle">{desc}</td></tr>';
$cache_log_pw = '<br/>' . tr('password_required');
$viewlogs_last = '<a href="viewlogs.php?cacheid={cacheid_urlencode}"><img src="tpl/stdstyle/images/action/16x16-showall.png" class="icon16" alt=""/></a>&nbsp;<a href="' . (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y' ? 'viewcache' : 'viewlogs') . '.php?cacheid={cacheid_urlencode}&amp;showlogs=4' . $linkargs . '">' . tr('last_log_entries') . '</a>';
$viewlogs_tr = tr('show_all_log_entries');
$viewlogs = '<a href="viewlogs.php?cacheid={cacheid_urlencode}" ><img src="tpl/stdstyle/images/action/16x16-showall.png" class="icon16" alt="' . $viewlogs_tr . '" title="' . $viewlogs_tr . '"/></a>&nbsp;<a title="' . $viewlogs_tr . '" href="' . (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y' ? 'viewcache' : 'viewlogs') . '.php?cacheid={cacheid_urlencode}' . $linkargs . '&amp;showlogsall=y">' . tr("show_all_log_entries_short") . '</a>';

$gallery_icon = '<img src="tpl/stdstyle/images/free_icons/photo.png" class="icon16"/>';
$gallery_tr = tr('gallery');
$gallery_link = '<a href="gallery_cache.php?cacheid={cacheid}" title="' . $gallery_tr . '" alt="' . $gallery_tr . '">' . tr('gallery_short') . '</a>';

$new_log_entry_tr = tr('new_log_entry');
$new_log_entry_link = '<a href="log.php?cacheid={cacheid}" title="' . $new_log_entry_tr . '"><img src="images/actions/new-entry-18.png" title="' . $new_log_entry_tr . '" alt="' . $new_log_entry_tr . '"></a>&nbsp;<a href="log.php?cacheid={cacheid}" title="' . $new_log_entry_tr . '">' . tr('new_log_entry_short') . '</a>';
$difficulty_text_diff = tr('task_difficulty') . ": %01.1f " . tr('out_of') . " 5.0";
$difficulty_text_terr = tr('terrain_difficulty') . ": %01.1f " . tr('out_of') . " 5.0";
$viewtext_on = tr('enter_text');
$viewtext_off = tr('enter_text_error');
$listed_only_oc = tr('only_these');
$default_lang = 'PL';
$event_attendance_list = '<span class="participants"><img src="tpl/stdstyle/images/blue/meeting.png" width="22" height="22" alt=""/>&nbsp;<a href="#" onclick="javascript:window.open(\'event_attendance.php?id={id}&amp;popup=y\',\'Lista_zapisanych_uczestnikow\',\'width=320,height=440,resizable=no,scrollbars=1\')">' . tr('list_of_participants') . '</a></span>';
$event_attended_text = " " . tr('attendends');
$event_will_attend_text = " " . tr('will_attend');
$cache_found_text = "x " . tr('found');
$cache_notfound_text = "x " . tr('not_found');
$rating_stat_show_singular = '<img src="images/rating-star.png" alt="{{recomendation}}" /> {ratings} ' . tr('recommendation') . '<br />';
$rating_stat_show_plural = '<img src="images/rating-star.png" alt="{{recommendation}}" /> {ratings} ' . tr('recommendations') . '<br />';
$found_icon = '<img src="tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="{{found}}"/>';
$moved_icon = '<img src="tpl/stdstyle/images/log/16x16-moved.png" class="icon16" alt="moved" />';
$notfound_icon = '<img src="tpl/stdstyle/images/log/16x16-dnf.png" class="icon16" alt="{{not_found}}" />';
$note_icon = '<img src="tpl/stdstyle/images/log/16x16-note.png" class="icon16" alt="{{log_note}}" />';
$notes_icon = '<img src="tpl/stdstyle/images/free_icons/note_edit.png" class="icon16" alt="" />';
$vote_icon = '<img src="tpl/stdstyle/images/free_icons/thumb_up.png" class="icon16" alt="" />';
$gk_icon = '<img src="images/gk.png" class="icon16" alt="" title="GeoKrety visited" />';
$score_icon = '<img src="images/cache-rate.png" class="icon16" alt="" />';
$watch_icon = '<img src="tpl/stdstyle/images/action/16x16-watch.png" class="icon16" alt="" />';
$search_icon = '<img src="tpl/stdstyle/images/action/16x16-search.png" class="icon16" alt="" />';
$save_icon = '<img src="tpl/stdstyle/images/action/16x16-save.png" class="icon16" alt="" />';
$visit_icon = '<img src="tpl/stdstyle/images/free_icons/vcard.png" class="icon16" alt="" />';
$exist_icon = '<img src="tpl/stdstyle/images/log/16x16-attend.png" class="icon16" alt="" title="uczestniczył"/>';
$trash_icon = '<img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="" />';
$wattend_icon = '<img src="tpl/stdstyle/images/log/16x16-will_attend.png" class="icon16" alt="" title="będzie uczestniczył"/>';
$hide_del_tr = tr('vc_HideDeletions');
$hide_del_link = '<span style="white-space: nowrap;"><a href="{thispage}?cacheid={cacheid}&amp;showdel=n' . $linkargs . '#log_start" title="' . $hide_del_tr . '">' . '<img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="' . $hide_del_tr . '" title="' . $hide_del_tr . '" /></a>&nbsp;<a href="{thispage}?cacheid={cacheid}&amp;showdel=n' . $linkargs . '#log_start" title="' . $hide_del_tr . '">' . $hide_del_tr . '</a></span>';
$show_del_tr = tr('vc_ShowDeletions');
$show_del_link = '<span style="white-space: nowrap;"><a href="{thispage}?cacheid={cacheid}&amp;showdel=y' . $linkargs . '#log_start" title="' . $show_del_tr . '">' . '<img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="' . $show_del_tr . '" title="' . $show_del_tr . '" /></a>&nbsp;<a href="{thispage}?cacheid={cacheid}&amp;showdel=y' . $linkargs . '#log_start" title="' . $show_del_tr . '">' . $show_del_tr . '</a></span>'; //add trash icon - todo: diff icon for show/hide
//$show_del_link and $hide_del_link are used in both viewlogs and viewcashes .php - so {thispage} is determined for caller

$decrypt_icon = ' <img src="tpl/stdstyle/images/blue/decrypt.png" class="icon32" alt="" />';

$decrypt_table = ' <font face="Courier" size="2" style="font-family : \'Courier New\', FreeMono, Monospace;">A|B|C|D|E|F|G|H|I|J|K|L|M</font>
                                                <font face="Courier" size="2" style="font-family : \'Courier New\', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>';

$spoiler_disable_msg = tr('vc_spoiler_disable_msg');

$error_coords_not_ok = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;<span class="errormsg">' . tr('bad_coordinates') . '</span>';

// MP3 Files table
function viewcache_getmp3table($cacheid, $mp3count)
{
    $nCol = 0;
    $retval = '';

    $rs = XDb::xSql(
        'SELECT uuid, title, url
        FROM mp3
        WHERE object_id= ?
            AND object_type=2
            AND display=1
        ORDER BY seq, date_created',
        $cacheid);

    while ($r = XDb::xFetchArray($rs)) {
        if ($nCol == 4) {
            $nCol = 0;
        }

        $retval .= '<div class="viewcache-pictureblock">';
        $retval .= '<div class="img-shadow"><a href="' . $r['url'] . '" target="_blank">';
        $retval .= '<img src="tpl/stdstyle/images/blue/32x32-get-mp3.png" alt="" title="Download MP3 file"/>';
        $retval .= '</a></div>';
        $retval .= '<span class="title">' . $r['title'] . '</span>';
        $retval .= '</div>';
        $nCol++;
    }

    XDb::xFreeResults($rs);
    return $retval;
}

// gibt eine tabelle für viewcache mit thumbnails von allen bildern zurück
function viewcache_getpicturestable($cacheid, $viewthumbs = true, $viewtext = true, $spoiler_only = false, $showspoiler = false, $picturescount, $disable_spoiler = false)
{
    $db = OcDb::instance();
    $retval = '';
    global $thumb_max_width;
    global $thumb_max_height;
    global $spoiler_disable_msg;
    $nCol = 0;
    if ($spoiler_only){
        $spoiler_only = 'spoiler=1 AND';
    }else{
        $spoiler_only = "";
    }
    $db->multiVariableQuery('
        SELECT uuid, title, url, spoiler FROM pictures
        WHERE ' . $spoiler_only . ' object_id=:1
            AND object_type=2 AND display=1
        ORDER BY seq, date_created',
        $cacheid);

    if ($disable_spoiler == false) {
        $spoiler_onclick = "enlarge(this);";
    } else {
        $spoiler_onclick = "alert('" . $spoiler_disable_msg . "'); return false;";
    }
    foreach ($db->dbResultFetchAll() as $key => $r) {
        if ($viewthumbs) {
            if ($nCol == 4) {
                $nCol = 0;
                $retval .= '<br style="clear: left;" />';
            }

            if ($showspoiler)
                $showspoiler = "showspoiler=1&amp;";
            else
                $showspoiler = "";
            $retval .= '<div class="viewcache-pictureblock">';

            if (isset($_REQUEST['print'])) {
                $reqPrint = $_REQUEST['print'];
            } else {
                $reqPrint = '';
            }

            if ($r['spoiler'] == 1) {
                if ($disable_spoiler == true) {
                    $r['url'] = 'tpl\stdstyle\images\thumb\thumbspoiler.gif';
                } //hide URL so cannot be viewed
            }

            if ($reqPrint != 'y') {
                $retval .= '<div class="img-shadow">';
                $retval .= '<a class="example-image-link" href="'.str_replace("images/uploads", "upload", $r['url']).'" data-lightbox="example-1" data-title="'.htmlspecialchars($r['title']).'"><img class="example-image" src="thumbs.php?' . $showspoiler . 'uuid=' . urlencode($r['uuid']) . '" alt="' . htmlspecialchars($r['title']) . '" /></a>';
            } else {
                if ($disable_spoiler == true && $r['spoiler'] == 1) {
                    $retval .= '<div><BR><strong>' . $spoiler_disable_msg . '</strong><BR><BR>';
                } else {
                    $retval .= '<div class="img-shadow"><a href="' . $r['url'] . '" title="' . htmlspecialchars($r['title']) . '" >';
                    $retval .= '<img src="thumbs.php?' . $showspoiler . 'uuid=' . urlencode($r['uuid']) . '" alt="' . htmlspecialchars($r['title']) . '" title="' . htmlspecialchars($r['title']) . '" /></a>';
                }
            }
            $retval .= '</div>';
            if ($viewtext){
                $retval .= '<span class="title">' . $r['title'] . '</span>';
            }
            $retval .= '</div>';

            $nCol++;
        }
        else { // only text
            $retval .= '<a href="' . $r['url'] . '" title="' . $r['title'] . '">';
            $retval .= $r['title'];
            $retval .= "</a>\n";
        }
    }

    return $retval;
}

// Hmm, is this references at all?
function viewcache_getfullsizedpicturestable($cacheid, $viewtext = true, $spoiler_only = false, $picturescount)
{
    global $thumb_max_width;
    global $thumb_max_height;

    $nCol = 0;
    if ($spoiler_only)
        $spoiler_only = 'spoiler=1 AND';
    else
        $spoiler_only = "";

    $rs = XDb::xSql(
        'SELECT uuid, title, url, spoiler
        FROM pictures
        WHERE ' . $spoiler_only . ' object_id = ?
            AND object_type=2 AND display=1
        ORDER BY date_created', $cacheid);

    while ($r = XDb::xFetchArray($rs)) {
        $retval .= '<div style="display: block; float: left; margin: 3px;">';
        if ($viewtext)
            $retval .= '<div style=""><p>' . $r['title'] . '</p></div>';
        $retval .= '<img style="max-width: 600px;" src="' . $r['url'] . '" alt="' . $r['title'] . '" title="' . $r['title'] . '" />';

        $retval .= '</div>';
    }

    XDb::xFreeResults($rs);
    return $retval;
}


