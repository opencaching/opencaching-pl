<?php
/***************************************************************************
												  ./tpl/stdstyle/viewcache.inc.php
															-------------------
		begin                : Mon July 2 2004
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

   Unicode Reminder ??

	 set template specific variables

 ****************************************************************************/

 $linkargs = (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y') ? '&amp;print=y' : '';
 $linkargs .= (isset($_REQUEST['nocrypt']) && $_REQUEST['nocrypt'] == '1') ? '&amp;nocrypt=1' : '';

 if(isset($_REQUEST['print']))
 {
	if(isset($_REQUEST['showlogsall']))
	{
		$logs_to_display = 999;
		$linkargs .= '&amp;showlogsall=y';
	}
	else if(isset($_REQUEST['showlogs']))
	{
		$logs_to_display = intval($_REQUEST['showlogs']);
		$linkargs .= '&amp;showlogs='.htmlspecialchars($logs_to_display, ENT_COMPAT, 'UTF-8');
	}
 	else
		$logs_to_display = 0;

 }
 else
	$logs_to_display = 4;

 // $short_desc_title = 'Charakterisierung: ';

 $function_log = "<li><a href='log.php?cacheid={cacheid}'>".$language[$lang]['write_to_log']."</a></li>";

 $function_edit = "<li'><a href='editcache.php?cacheid={cacheid}'>".$language[$lang]['edit']."</a></li>";

 $function_watch = "<li><a href='watchcache.php?cacheid={cacheid}&amp;target=viewcache.php%3Fcacheid%3D{cacheid}'>".$language[$lang]['watch']."</a></li>";

 $function_watch_not = "<li><a href='removewatch.php?cacheid={cacheid}&amp;target=viewcache.php%3Fcacheid%3D{cacheid}'>".$language[$lang]['watch_not']."</a></li>";

$function_ignore = "<li><a href='addignore.php?cacheid={cacheid}&amp;target=viewcache.php%3Fcacheid%3D{cacheid}'>".$language[$lang]['ignore']."</a></li>";

$function_ignore_not = "<li><a href='removeignore.php?cacheid={cacheid}&amp;target=viewcache.php%3Fcacheid%3D{cacheid}'>".$language[$lang]['ignore_not']."</a></li>";

 $decrypt_link = '<span style="font-weight:400"><a href="viewcache.php?cacheid={cacheid_urlencode}&amp;nocrypt=1&amp;desclang={desclang}'.  $linkargs.'">'.$language[$lang]['decrypt'].'</a></span>';

//  $logtype_found = 'den Cache gefunden';
//  $logtype_notfound = 'den Cache nicht gefunden';
//  $logtype_note = 'f&uuml;r den Cache eine Bemerkung geschrieben';
// $logtype_stop = "den Cache gesperrt";
// $logtype_go = "den Cache wieder freigegeben";
// $logtype_delete = "den Cache archiviert";

 //$cryptedhints = '<h1>Hinweise {decrypt_link}</h1><p>{hints}</p>';

 $pictureline = '<a href="{link}" rel="lytebox[vacation]">{title}</a><br />';
 $pictures = '<p>{picturelines}</p>';

 $logpictureline = '<a href="{link}" title="{title}" rel="lytebox[vacation]">{title}</a>{functions}<br />';
 $logpictures = '<tr><td><b>'.$language[$lang]['pictures_included'].':</b><br />{lines}</td></tr>';

 //$cache_watchers = '<br/>Dieser Cache wird von {watcher} Opencaching.de Nutzern beobachtet.';
 $cache_log_pw = '<br/>'.$language[$lang]['password_required'];

 $viewlogs_last = '<a href="viewlogs.php?cacheid={cacheid_urlencode}"><img src="tpl/stdstyle/images/action/16x16-showall.png" width="16" height="16" align="middle" border="0" align="left"/></a>&nbsp;<a href="'.(isset($_REQUEST['print']) && $_REQUEST['print'] == 'y' ? 'viewcache' : 'viewlogs') .'.php?cacheid={cacheid_urlencode}&amp;showlogs=4'.$linkargs.'">'.$language[$lang]['last_log_entries'].'</a>';

 $viewlogs = '<a href="viewlogs.php?cacheid={cacheid_urlencode}"><img src="tpl/stdstyle/images/action/16x16-showall.png" width="16" height="16" align="middle" border="0" align="left"/></a>&nbsp;<a href="'.(isset($_REQUEST['print']) && $_REQUEST['print'] == 'y' ? 'viewcache' : 'viewlogs') .'.php?cacheid={cacheid_urlencode}'.$linkargs.'&amp;showlogsall=y">Wszystkie wpisy do logu</a>';


 $difficulty_text_diff = $language[$lang]['task_difficulty'].": %01.1f ".$language[$lang]['out_of']." 5.0";
 $difficulty_text_terr = $language[$lang]['terrain_difficulty'].": %01.1f ".$language[$lang]['out_of']." 5.0";

 $viewtext_on = $language[$lang]['enter_text'];
 $viewtext_off = $language[$lang]['enter_text_error'];

 $listed_only_oc = $language[$lang]['only_these'];

 $default_lang = 'PL';

 $event_attendance_list = '<br /><b><font color="blue"><a href="#" onclick="javascript:window.open(\'event_attendance.php?id={id}&amp;popup=y\',\'Lista_zapisanych_uczestnikow\',\'width=320,height=440,resizable=no,scrollbars=1\')">'.$language[$lang]['list_of_participants'].'</a></b></font>';
# $event_attendance_list = '<br /><a href="#" onclick="javascript:window.open(\'event_attendance.php?id={id}&amp;popup=y\',\'Lista_zapisanych_uczestnikow\',\'width=320,height=440,resizable=no,scrollbars=1\')">Lista zapisanych uczestnikow</a>';
 
 $event_attended_text = " ".$language[$lang]['attendends'];
 $event_will_attend_text = " ".$language[$lang]['will_attend'];

 $cache_found_text = "x ".$language[$lang]['found'];
 $cache_notfound_text = "x ".$language[$lang]['not_found'];

 $recommend_link = '&nbsp;&nbsp;<a href="recommendations.php?cacheid={cacheid}"/>('.$language[$lang]['show_recommended'].')</a>';
 $rating_stat_show_singular = '<img src="images/rating-star.gif" border="0"/> {ratings} '.$language[$lang]['recommendation'].'<br />';
 $rating_stat_show_plural = '<img src="images/rating-star.gif" border="0"/> {ratings} '.$language[$lang]['recommendations'].'<br />';

$found_icon = '<img src="tpl/stdstyle/images/log/16x16-found.png" width="16" height="16" border="0"/>';
$notfound_icon = '<img src="tpl/stdstyle/images/log/16x16-dnf.png" width="16" height="16" border="0"/>';
$note_icon = '<img src="tpl/stdstyle/images/log/16x16-note.png" width="16" height="16" border="0"/>';
$vote_icon = '<img src="tpl/stdstyle/images/action/16x16-adddesc.png" width="16" height="16" border="0"/>';
$watch_icon = '<img src="tpl/stdstyle/images/action/16x16-watch.png" width="16" height="16" border="0"/>';
$visit_icon = '<img src="tpl/stdstyle/images/description/16x16-visitors.png" width="16" height="16" border="0"/>';
$exist_icon = '<img src="tpl/stdstyle/images/log/16x16-go.png" width="16" height="16" border="0"/>';
$trash_icon = '<img src="tpl/stdstyle/images/log/16x16-trash.png" width="16" height="16" border="0"/>';

// gibt eine tabelle für viewcache mit thumbnails von allen bildern zurück
function viewcache_getpicturestable($cacheid, $viewthumbs = true, $viewtext = true, $spoiler_only = false, $showspoiler = false, $picturescount)
{
	global $dblink;
	global $thumb_max_width;
	global $thumb_max_height;

	$nCol = 0;
	$retval = "<table>\n<tr>\n";

	if($spoiler_only) $spoiler_only = 'spoiler=1 AND';
		else $spoiler_only = "";
	$sql = 'SELECT uuid, title, url, spoiler FROM pictures WHERE '.$spoiler_only.' object_id=\'' . sql_escape($cacheid) . '\' AND object_type=2 AND display=1 ORDER BY date_created';
	

	$rs = sql($sql);
	while ($r = sql_fetch_array($rs))
	{
		if($viewthumbs)
		{
			if ($nCol == 4)
			{
				$retval .= "<td>&nbsp;</td></tr>\n<tr>\n";
				$nCol = 0;
			}

			if( $showspoiler )
				$showspoiler = "showspoiler=1&amp;";
			else $showspoiler = "";
			
			$retval .= '<td width="180" align="center" valign="top">';
			$retval .= '<a href="'.$r['url'].'" title="'.$r['title'].'" rel="lytebox[vacation]">';
			$retval .= '<img src="thumbs.php?'.$showspoiler.'uuid=' . urlencode($r['uuid']) . '" border="0" alt="'.$r['title'].'" title="'.$r['title'].'" align="bottom"/>';
			$retval .= '</a><br>';
			if($viewtext)
				$retval .= $r['title'];
			$retval .= "</td>\n";

			$nCol++;
		}
		else // only text
		{
			$retval .= '<a href="'.$r['url'].'" title="'.$r['title'].'">';
			$retval .= $r['title'];
			$retval .= "</a><br />\n";
		}
	}

	mysql_free_result($rs);

	if($nCol > 0 && $nCol < 4)
		$retval .= "<td colspan='". (4 - $nCol + 1) . "'>&nbsp;</td>\n";
	else
		$retval .= "<td>&nbsp;</td>\n";
	$retval .= "</tr>\n</table>\n";

	return $retval;

/*
		$thisline = $pictureline;

		$thisline = mb_ereg_replace('{link}', $pic_record['url'], $thisline);
		$thisline = mb_ereg_replace('{title}', htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8'), $thisline);
*/
}

function viewcache_getfullsizedpicturestable($cacheid, $viewtext = true, $spoiler_only = false, $picturescount)
{
	global $dblink;
	global $thumb_max_width;
	global $thumb_max_height;

	$nCol = 0;
	$retval = "<table>\n";
	if($spoiler_only) $spoiler_only = 'spoiler=1 AND';
		else $spoiler_only = "";
	
	$sql = 'SELECT uuid, title, url, spoiler FROM pictures WHERE '.$spoiler_only.' object_id=\'' . sql_escape($cacheid) . '\' AND object_type=2 AND display=1 ORDER BY date_created';
	//if($spoiler_only) $sql .= ' AND spoiler=1 ';

	$rs = sql($sql);
	while ($r = sql_fetch_array($rs))
	{
		$retval .= '<tr><td width="180" align="center" valign="top">';
		$retval .= '<img src="'.$r['url'].'" border="0" alt="'.$r['title'].'" title="'.$r['title'].'" align="bottom"/>';
		$retval .= '<br>';
		if($viewtext)
			$retval .= $r['title'];
		$retval .= "</td></tr>\n";
	}

	mysql_free_result($rs);

	$retval .= "\n</table>\n";

	return $retval;

/*
		$thisline = $pictureline;

		$thisline = mb_ereg_replace('{link}', $pic_record['url'], $thisline);
		$thisline = mb_ereg_replace('{title}', htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8'), $thisline);
*/
}

?>
