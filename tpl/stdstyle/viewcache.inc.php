<?php

$linkargs = (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y') ? '&amp;print=y' : '';
$linkargs .= (isset($_REQUEST['nocrypt']) && $_REQUEST['nocrypt'] == '1') ? '&amp;nocrypt=1' : '';

if (isset($_REQUEST['showdel'])) {
    $_SESSION['showdel'] = $_REQUEST['showdel']; //use session in order to keep options on if URL changes
}

if (isset($_REQUEST['print'])) {
    if (isset($_REQUEST['showlogsall'])) {
        $logs_to_display = 999;
        $linkargs .= '&amp;showlogsall=y';
    } else if (isset($_REQUEST['showlogs'])) {
        $logs_to_display = intval($_REQUEST['showlogs']);
        $linkargs .= '&amp;showlogs=' . htmlspecialchars($logs_to_display, ENT_COMPAT, 'UTF-8');
    } else if (isset($_REQUEST['logbook']) && $_REQUEST['logbook'] == 'no') {
        $logs_to_display = 0;
        $linkargs .= '&amp;logbook=no';
    } else
        $logs_to_display = 0;
} else
    $logs_to_display = 5;

$pictures = '<p>{picturelines}</p>';

$logpictureline = '<div class="logimage"><div class="img-shadow">'
         .'<a class="example-image-link" href="{longdesc}" data-fancybox="log-picture" data-caption="{title}"><img class="example-image" src="{imgsrc}" alt="{title}" /></a>'
        . '</div><span class="desc">{title}</span>{functions}</div>';

$logpictures = '<div class="viewlogs-logpictures"><span class="info">' . tr('pictures_included') . ':</span><div class="allimages">{lines}</div></div><br style="clear: both" />';

// Waypoints line
$wpline = '<tr>{stagehide_start}<td align="center" valign="middle"><center><strong>{number}</strong></center></td>{stagehide_end}<td align="center" valign="middle"><center><img src="{wp_icon}" alt="" title="{type}" /></center></td><td style="text-align: left; vertical-align: middle;">{type}</td><td align="left" valign="middle"><b><span style="color: rgb(88,144,168)">{lat_lon}</span></b></td><td align="left" valign="middle">{desc}</td></tr>';

$gallery_icon = '<img src="tpl/stdstyle/images/free_icons/photo.png" alt="Photo" class="icon16"/>';
$gallery_tr = tr('gallery');
$gallery_link = '<a href="gallery_cache.php?cacheid={cacheid}">' . tr('gallery_short') . '</a>';


$viewtext_on = tr('enter_text');
$viewtext_off = tr('enter_text_error');
$default_lang = 'PL';
$event_attended_text = " " . tr('attendends');
$event_will_attend_text = " " . tr('will_attend');

$found_icon = '<img src="tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="{{found}}"/>';
$notfound_icon = '<img src="tpl/stdstyle/images/log/16x16-dnf.png" class="icon16" alt="{{not_found}}" />';
$note_icon = '<img src="tpl/stdstyle/images/log/16x16-note.png" class="icon16" alt="{{log_note}}" />';
$exist_icon = '<img src="tpl/stdstyle/images/log/16x16-attend.png" class="icon16" alt="" title="uczestniczył"/>';
$trash_icon = '<img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="" />';
$wattend_icon = '<img src="tpl/stdstyle/images/log/16x16-will_attend.png" class="icon16" alt="" title="będzie uczestniczył"/>';
$hide_del_tr = tr('vc_HideDeletions');
$hide_del_link = '<span style="white-space: nowrap;"><a href="{thispage}?cacheid={cacheid}&amp;showdel=n' . $linkargs . '#log_start" title="' . $hide_del_tr . '">' . '<img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="' . $hide_del_tr . '" title="' . $hide_del_tr . '" /></a>&nbsp;<a href="{thispage}?cacheid={cacheid}&amp;showdel=n' . $linkargs . '#log_start" title="' . $hide_del_tr . '">' . $hide_del_tr . '</a></span>';
$show_del_tr = tr('vc_ShowDeletions');
$show_del_link = '<span style="white-space: nowrap;"><a href="{thispage}?cacheid={cacheid}&amp;showdel=y' . $linkargs . '#log_start" title="' . $show_del_tr . '">' . '<img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="' . $show_del_tr . '" title="' . $show_del_tr . '" /></a>&nbsp;<a href="{thispage}?cacheid={cacheid}&amp;showdel=y' . $linkargs . '#log_start" title="' . $show_del_tr . '">' . $show_del_tr . '</a></span>'; //add trash icon - todo: diff icon for show/hide
//$show_del_link and $hide_del_link are used in both viewlogs and viewcashes .php - so {thispage} is determined for caller


$decrypt_table = ' <font face="Courier" size="2" style="font-family : \'Courier New\', FreeMono, Monospace;">A|B|C|D|E|F|G|H|I|J|K|L|M</font>
                                                <font face="Courier" size="2" style="font-family : \'Courier New\', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>';

$error_coords_not_ok = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;<span class="errormsg">' . tr('bad_coordinates') . '</span>';

