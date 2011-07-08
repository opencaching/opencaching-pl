<?php
function getMapType($value)
{
	switch( $value ) 
	{
		case 0:
			return "G_NORMAL_MAP";
		case 1:
			return "G_SATELLITE_MAP";
		case 2:
			return "G_HYBRID_MAP";
		case 3:
			return "G_PHYSICAL_MAP";
		default:
			return "G_NORMAL_MAP";
	}
}



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

$tplname = 'cacheguides';
tpl_set_var('bodyMod', ' onload="initialize()" onunload="GUnload()"');;
global $usr;
global $get_userid;

//user logged in?
	session_start();


	$uLat = sqlValue("SELECT `user`.`latitude`  FROM `user` WHERE `user_id`='".sql_escape($usr[userid]) ."'", 0);
	$uLon = sqlValue("SELECT `user`.`longitude`  FROM `user` WHERE `user_id`='". sql_escape($usr[userid]) ."'",0);

	if (($uLat==NULL || $uLat==0) && ($uLon==NULL || $uLon==0)) {
	
	tpl_set_var('mapzoom', 6);
	tpl_set_var('mapcenterLat', 52.057);
	tpl_set_var('mapcenterLon', 19.07);	

	} else {
	tpl_set_var('mapzoom', 11);
	tpl_set_var('mapcenterLat', $uLat);
	tpl_set_var('mapcenterLon', $uLon);
}
	
			$rscp = sql("SELECT `user`.`latitude` `latitude`,`user`.`longitude` `longitude`,`user`.`username` `username`,
					`user`.`user_id` `userid` FROM `user` WHERE `user`.`guru`!=0 AND (user.user_id IN (SELECT cache_logs.user_id FROM cache_logs WHERE `cache_logs`.`type`=1 AND `cache_logs`.`date_created`>DATE_ADD(NOW(), INTERVAL -90 DAY)) OR user.user_id IN (SELECT caches.user_id FROM caches WHERE (`caches`.`status`=1 OR `caches`.`status`=2 OR `caches`.`status`=3) AND `caches`.`date_created`>DATE_ADD(NOW(), INTERVAL -90 DAY))) AND `user`.`longitude` IS NOT NULL AND `user`.`latitude` IS NOT NULL GROUP BY user.username");


			$point="";
			$nrows=mysql_num_rows($rscp);
			tpl_set_var('nguides', $nrows);
			for ($i = 0; $i < mysql_num_rows($rscp); $i++)
			{
				$record = sql_fetch_array($rscp);
				$username=$record['username'];
				$y=$record['longitude'];
				$x=$record['latitude'];


				$nrec=sql("SELECT COUNT('cache_id') as ncaches, SUM(topratings) as nrecom FROM caches WHERE `caches`.`type` <> 6 AND caches.status<>4 AND caches.status<>5 AND caches.status<>6 AND `caches`.`user_id`=&1",$record['userid']);
				$nr = sql_fetch_array($nrec);
				$nf=sqlValue("SELECT COUNT(*) number FROM cache_logs WHERE type=1 AND deleted='0' AND user_id='" . $record['userid'] . "'", 0);

			$point .=" var point = new GLatLng(" . $x . "," . $y . ");\n";
			$number=$i+1;
			$point .="var marker".$number." = new GMarker(point,icon1); map0.addOverlay(marker".$number.");\n\n";

			$point .="GEvent.addListener(marker".$number.", \"click\", function() {marker".$number.".openInfoWindowHtml('<span style=\"color:blue;\"><table><tr><td><img src=\"tpl/stdstyle/images/free_icons/vcard.png\" alt=\"img\"><b>&nbsp;<a class=\"links\" href=\"viewprofile.php?userid=".$record['userid']."\">".$username."</a></td></tr><tr><td><img src=\"tpl/stdstyle/images/cache/16x16-traditional.png\" alt=\"liczba skrzynek załozonych\" title=\"liczba założonych skrzynek\"><b>&nbsp;".$nr['ncaches']."&nbsp;&nbsp;<img src=\"tpl/stdstyle/images/log/16x16-found.png\" alt=\"liczba znalezionych\" title=\"liczba znalezionych\"><b>&nbsp;".$nf."&nbsp;&nbsp;<img src=\"images/rating-star.png\" alt=\"rekomendacje\" title=\"rekomendacje\"><b>&nbsp;".$nr['nrecom']."</td></tr><tr><td><img src=\"tpl/stdstyle/images/free_icons/email.png\" alt=\"img\"><b>&nbsp;<a class=\"links\" href=\"mailto.php?userid=".$record['userid']."\">Napisz E-mail</a></b></td></tr></table></span>');});\n";
			}

		tpl_set_var('points', $point);	

	
	tpl_set_var("map_type", "G_NORMAL_MAP");
	;

		mysql_free_result($rscp);

	/*SET YOUR MAP CODE HERE*/
	tpl_set_var('cachemap_header', '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$googlemap_key.'" type="text/javascript"></script>');

}
}
	tpl_BuildTemplate(); 

?>
