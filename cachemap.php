<?php


require_once('./lib/common.inc.php');
$tplname = 'cachemap';

	$no_tpl_build = false;

	//Preprocessing
	if ($error == false)
	{
		//user logged in?
		if ($usr == false)
		{
			$target = urlencode(substr($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], 1));
			header('Location: login.php?target='.$target);
		}
		else
		{
			$errors = false; // set if there was any errors


tpl_set_var('hide_caches_type', $language[$lang]['hide_caches_type']);
tpl_set_var('unknown_type', $language[$lang]['unknown_type']);
tpl_set_var('traditional', $language[$lang]['traditional']);
tpl_set_var('multicache', $language[$lang]['multicache']);
tpl_set_var('virtual', $language[$lang]['virtual']);
tpl_set_var('webcam', $language[$lang]['webcam']);
tpl_set_var('event', $language[$lang]['event']);
tpl_set_var('quiz', $language[$lang]['quiz']);
tpl_set_var('moving', $language[$lang]['moving']);
tpl_set_var('hide_caches', $language[$lang]['hide_caches']);
tpl_set_var('ignored', $language[$lang]['ignored']);
tpl_set_var('not_yet_found', $language[$lang]['not_yet_found']);
tpl_set_var('temp_unavailable', $language[$lang]['temp_unavailable']);
tpl_set_var('ready_to_find', $language[$lang]['ready_to_find']);
tpl_set_var('filter', $language[$lang]['filter']);
tpl_set_var('own', $language[$lang]['own']);
tpl_set_var('founds', $language[$lang]['founds']);
tpl_set_var('only_founds', $language[$lang]['only_founds']);
tpl_set_var('founds', $language[$lang]['founds']);
tpl_set_var('show_caches', $language[$lang]['show_caches']);
tpl_set_var('archived_plural', $language[$lang]['archived_plural']);
tpl_set_var('only_new', $language[$lang]['only_new']);


tpl_set_var('bodyMod', ' onload="load()" onunload="GUnload()"');
// opencaching.pl
tpl_set_var('cachemap_header', '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$googlemap_key.'" type="text/javascript"></script>');
// opencaching.iq.pl
//tpl_set_var('cachemap_header', '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAKzfMHoyn1s1VSuNTwlFfzhT7YrJl6P6favHAFSkijBN-GH3d8BR-pycn_ygkiHYG5vTHvoFwChk5ig" type="text/javascript"></script>');

// ocpl.geofizyka
// tpl_set_var('cachemap_header', '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAKzfMHoyn1s1VSuNTwlFfzhSRMuL-fI-htGim57KraPYYNDyBGhQHUJ6I66y6Gy8yktTPOVkdwx2bHA" type="text/javascript"></script>');

global $usr;

$where_clause_map = ' AND caches.status !=5';
if($usr==true) {
$rs = mysql_query("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='$usr[userid]'");
$record = mysql_fetch_array($rs);
$coordsXY="$record[latitude],$record[longitude]";
$coordsX="$record[latitude]";
if ($coordsX=="") $coordsXY="52.5,19.2";
tpl_set_var('coords', $coordsXY);

//$rs2 = mysql_query("SELECT `user_id` FROM cache_logs WHERE type=1");
//$record2 = mysql_fetch_array($rs2);
//while($w=mysql_fetch_array($rs2)) {
//if($w['user_id']==$usr['userid']) $users_cachef .= ", 1"; else $users_cachef .= ", 0";
//echo " $w[user_id]";
//}
//echo $coordsXY;

//$rs = mysql_query("SELECT `username`, `email`, `country`, `latitude`, `longitude`, `date_created`, `pmr_flag`, `permanent_login_flag`, `no_htmledit_flag`, `notify_radius` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
//$record = mysql_fetch_array($rs);
//echo $record[latitude];

	if(!isset($_REQUEST['leave_found'])) {
		$where_clause_map .= ' AND cache_id NOT IN (SELECT cache_id FROM cache_logs WHERE user_id='.$usr['userid'].' AND type=1)';
	}
	if(!isset($_REQUEST['leave_ignored'])) {
		$where_clause_map .= ' AND cache_id NOT IN (SELECT cache_id FROM cache_ignore WHERE user_id='.$usr['userid'].')';
	}
	if(isset($_REQUEST['show_found'])) {
		$where_clause_map .= ' AND cache_id IN (SELECT cache_id FROM cache_logs WHERE user_id='.$usr['userid'].' AND type=1)';
	}
	if(!isset($_REQUEST['leave_own'])) {
		$where_clause_map .= ' AND caches.user_id !='.$usr['userid'];
	}
//	$where_clause_map .= ' INNER JOIN cache_logs ON cache_logs.type';

}
if(isset($_REQUEST['leave_unknown'])) {
	$where_clause_map .= ' AND caches.type != 1';
}
if(isset($_REQUEST['leave_traditional'])) {
	$where_clause_map .= ' AND caches.type != 2';
}
if(isset($_REQUEST['leave_multi'])) {
	$where_clause_map .= ' AND caches.type != 3';
}
if(isset($_REQUEST['leave_virtual'])) {
	$where_clause_map .= ' AND caches.type != 4';
}
if(isset($_REQUEST['leave_webcam'])) {
	$where_clause_map .= ' AND caches.type != 5';
}
if(isset($_REQUEST['leave_event'])) {
	$where_clause_map .= ' AND caches.type != 6';
}
if(isset($_REQUEST['leave_quiz'])) {
	$where_clause_map .= ' AND caches.type != 7';
}
if(isset($_REQUEST['leave_math'])) {
	$where_clause_map .= ' AND caches.type != 8';
}
if(isset($_REQUEST['leave_moving'])) {
	$where_clause_map .= ' AND caches.type != 9';
}
if(isset($_REQUEST['leave_drivein'])) {
	$where_clause_map .= ' AND caches.type != 10';
}
if(isset($_REQUEST['leave_active'])) {
	$where_clause_map .= ' AND caches.status != 1';
}
if(!isset($_REQUEST['leave_unavailable'])) {
	$where_clause_map .= ' AND caches.status != 2';
}
if(! isset($_REQUEST['show_archived'])) {
	$where_clause_map .= ' AND caches.status != 3';
}
if(isset($_REQUEST['show_onlynew'])) {
	$where_clause_map .= ' HAVING old <= 10';	//this must be last (not changing where!)
}
$query=mysql_query("SELECT cache_id, name, username, caches.latitude, caches.longitude, caches.type, caches.status, datediff(now(), caches.date_hidden) as old, caches.user_id FROM user, caches WHERE (caches.user_id = user.user_id) AND (caches.status != 4)".$where_clause_map);
//next variables are storing js fields
$souradnice_lat = "0";		//lat coords js field
$souradnice_lon = "0";		//lon coords js field
$name = "\"null\"";			//cache names
$authors = "\"null\"";		//cache owners
$users_cache = "0";			//0 if cache is not users own, 1 if it is own cache
$users_cachef = "0";			//0 if cache is not users own, 1 if it is own cache
$cache_id = "0";			//cache id's
$cache_type = "\"null\"";		//cache type (by character -> T=traditional etc.)
$days_hidden = "\"0\"";		//cache oldness in days
$pocet = 0;				//number of caches showed

while($zaz=mysql_fetch_array($query)) {
	$souradnice_lat .= ", ".$zaz['latitude'];
	$souradnice_lon .= ", ".$zaz['longitude'];
	$name .= ", \"".htmlspecialchars($zaz['name'])."\"";
	$authors .= ", \"".htmlspecialchars($zaz['username'])."\"";
	switch($zaz['type']) {
	case 1: $cache_type .= ", \"U\""; break;		//unknown
	case 2: $cache_type .= ", \"T\""; break;		//traditional
	case 3: $cache_type .= ", \"M\""; break;		//multi
	case 4: $cache_type .= ", \"V\""; break;		//virtual
	case 5: $cache_type .= ", \"W\""; break;		//webcam
	case 6: $cache_type .= ", \"E\""; break;		//event
	case 7: $cache_type .= ", \"Q\""; break;		//quiz
	case 8: $cache_type .= ", \"C\""; break;		//math
	case 9: $cache_type .= ", \"O\""; break;		//mOving
	case 10: $cache_type .= ", \"D\""; break;	//Drive-in
	}
	$cache_id .= ", ".$zaz['cache_id'];
	$days_hidden .= ", ".$zaz['old'];
	if($zaz['user_id']==$usr['userid']) $users_cache .= ", 1"; else $users_cache .= ", 0";
//echo " $cache_id";
//$rs_ucf="SELECT `cache_id`='$cache_id' FROM cache_logs WHERE `user_id`='$usr[userid]' AND type=1";
//if (($wynik=mysql_query($rs_ucf))==True) $users_cachef .= ", $cache_id"; else $users_cachef .= ", 0";

// echo " $users_cachef";
//echo $numr;
//if ($rs_ucf==False) {echo "OK;}
//$rec = mysql_fetch_array($rs_ucf);
//echo $rec;
//	if(isset($_REQUEST['show_found'])) 
		
	$pocet++;
}

//$rs_ucf=mysql_query("SELECT `cache_id`, `user_id` FROM cache_logs WHERE type=1");
//while($wynik=mysql_fetch_array($rs_ucf)) {

//if (($wynik=mysql_query($rs_ucf))==True) $users_cachef .= ", $cache_id"; else $users_cachef .= ", 0";
//$wynik=mysql_fetch_array($rs_ucf);
//	if($wynik['user_id']==$usr['userid']) $users_cachef .= ", 1"; else $users_cachef .= ", 0";
//echo $wynik[cach];

//echo " $users_cachef";
//}
tpl_set_var('cachemap_count', $pocet);
tpl_set_var('cachemap_lat', "var lat = [".$souradnice_lat."];\n");
tpl_set_var('cachemap_lon', "var lon = [".$souradnice_lon."];\n");
tpl_set_var('cachemap_label', "var label = [".$name."];\n");
tpl_set_var('cachemap_cacheid', "var cache_id = [".$cache_id."];\n");
tpl_set_var('cachemap_author', "var author = [".$authors."];\n");
tpl_set_var('cachemap_userid', "var c_user_id = [".$users_cache."];\n");
tpl_set_var('cachemap_icon', "var cache_icon = [".$cache_type."];\n");
tpl_set_var('cachemap_old', "var cache_old = [".$days_hidden."];\n");
tpl_set_var('cachemap_c_u_f', "var c_u_f = [".$users_cachef."];\n");

if(isset($_REQUEST['leave_found'])) tpl_set_var('cachemap_f_found', 'checked');
if(isset($_REQUEST['leave_own'])) tpl_set_var('cachemap_f_own', 'checked');
if(isset($_REQUEST['leave_ignored'])) tpl_set_var('cachemap_f_ignored', 'checked');
if(isset($_REQUEST['leave_unknown'])) tpl_set_var('cachemap_f_unknown', 'checked');
if(isset($_REQUEST['leave_traditional'])) tpl_set_var('cachemap_f_traditional', 'checked');
if(isset($_REQUEST['leave_multi'])) tpl_set_var('cachemap_f_multi', 'checked');
if(isset($_REQUEST['leave_virtual'])) tpl_set_var('cachemap_f_virtual', 'checked');
if(isset($_REQUEST['leave_webcam'])) tpl_set_var('cachemap_f_webcam', 'checked');
if(isset($_REQUEST['leave_event'])) tpl_set_var('cachemap_f_event', 'checked');
if(isset($_REQUEST['leave_quiz'])) tpl_set_var('cachemap_f_quiz', 'checked');
if(isset($_REQUEST['leave_math'])) tpl_set_var('cachemap_f_math', 'checked');
if(isset($_REQUEST['leave_moving'])) tpl_set_var('cachemap_f_moving', 'checked');
if(isset($_REQUEST['leave_drivein'])) tpl_set_var('cachemap_f_drivein', 'checked');
if(isset($_REQUEST['leave_active'])) tpl_set_var('cachemap_f_active', 'checked');
if(isset($_REQUEST['leave_unavailable'])) tpl_set_var('cachemap_f_unavailable', 'checked');
if(isset($_REQUEST['show_archived'])) tpl_set_var('cachemap_f_archived', 'checked');
if(isset($_REQUEST['show_onlynew'])) tpl_set_var('cachemap_f_newonly', 'checked');
if(isset($_REQUEST['show_found'])) tpl_set_var('cachemap_f_ofound', 'checked');
}
}
	if ($no_tpl_build == false)
	{
		//make the template and send it out
		tpl_BuildTemplate();
	}


?>

