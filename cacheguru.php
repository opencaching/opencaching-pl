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

$tplname = 'cacheguru';
tpl_set_var('bodyMod', ' onload="initialize()" onunload="GUnload()"');;
global $usr;
global $get_userid;

//user logged in?
	session_start();


	$uLat = sqlValue("SELECT `user`.`latitude`  FROM `user` WHERE `user_id`='".sql_escape($usr[userid]) ."'", 0);
	$uLon = sqlValue("SELECT `user`.`longitude`  FROM `user` WHERE `user_id`='". sql_escape($usr[userid]) ."'",0);

			$rscp = sql("SELECT `user`.`latitude` `latitude`,`user`.`longitude` `longitude`,`user`.`username` `username`,
					`user`.`user_id` `userid` FROM `cache_logs`,`user` WHERE `user`.`guru`!=0 AND (`cache_logs`.`type`=1 AND `cache_logs`.`date_created`>DATE_ADD(NOW(), INTERVAL -90 DAY)) AND `user`.`longitude` IS NOT NULL AND `user`.`latitude` IS NOT NULL GROUP BY user.username");

/*
SELECT `user`.`latitude` `latitude`,`user`.`longitude` `longitude`,`user`.`username` `username`,
					`user`.`user_id` `userid` FROM `cache_logs`,`user` WHERE `user`.`guru`!=0 AND `cache_logs`.`type`=1 AND `cache_logs`.`date_created`>DATE_ADD(NOW(), INTERVAL -90 DAY) AND `user`.`longitude` IS NOT NULL AND `user`.`latitude` IS NOT NULL GROUP BY .username
*/
			$point="";
			$nrows=mysql_num_rows($rscp);
			for ($i = 0; $i < mysql_num_rows($rscp); $i++)
			{
				$record = sql_fetch_array($rscp);
				$username=$record['username'];
				$y=$record['longitude'];
				$x=$record['latitude'];

			$point .=" var point = new GLatLng(" . $x . "," . $y . ");\n";
			$icon="icon1";
			$number=$i+1;
			$point .="var marker".$number." = new GMarker(point,".$icon."); map0.addOverlay(marker".$number.");\n\n";

			$point .="GEvent.addListener(marker".$number.", \"click\", function() {marker".$number.".openInfoWindowHtml('<br/><span style=\"font-size:12px;color:blue;\"><table><tr><td><img src=\"tpl/stdstyle/images/free_icons/book_open.png\" alt=\"img\"> <b>Przewodnik/Guru: <a href=\"viewprofile.php?userid=".$record['userid']."\">".$username."</a></td></tr><tr><td><img src=\"tpl/stdstyle/images/free_icons/email.png\" alt=\"img\"><b> Kontakt: <a href=\"mailto.php?userid=".$record['userid']."\">Napisz E-mail</a></b></td></tr></table></span>');});\n bounds.extend(point);\n";
			}

		tpl_set_var('points', $point);	


	tpl_set_var('mapcenterLat', $uLat);
	tpl_set_var('mapcenterLon', $uLon);


	tpl_set_var('zoom', 6);

	
	
	tpl_set_var("map_type", "G_NORMAL_MAP");
	;

		mysql_free_result($rscp);

	/*SET YOUR MAP CODE HERE*/
	tpl_set_var('cachemap_header', '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$googlemap_key.'" type="text/javascript"></script>');

}
}
	tpl_BuildTemplate(); 

?>
