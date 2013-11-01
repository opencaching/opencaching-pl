<?php
/***************************************************************************
																./mywatches.php
															-------------------
		begin                : July 17 2004
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

	 display all watches of this user

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('./lib/db.php');
	
function CleanSpecChars( $log, $flg_html )
{	
	$log_text = $log;
	
	if ( $flg_html == 1)
	{ $log_text = htmlspecialchars( $log_text, ENT_COMPAT, 'UTF-8');}
	
	$log_text = str_replace("\r\n", " ",$log_text);
	$log_text = str_replace("\n", " ",$log_text);
	$log_text = str_replace("'", "-",$log_text);
	$log_text = str_replace("\"", " ",$log_text);
	
	return $log_text;
}	
	
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
			$RQ = "";
			if ( isset($_REQUEST['rq']) )
				$RQ = $_REQUEST['rq'];
			
			if( $RQ == "properties" )
			{
				include($stylepath . '/mywatches_properties.inc.php');
				$tplname = 'mywatches_properties';

				// submit?
				if (isset($_REQUEST['submit']))
				{
					$nHour = isset($_REQUEST['hour']) ? $_REQUEST['hour'] : "0";
					$nDay = isset($_REQUEST['weekday']) ? $_REQUEST['weekday'] : "1";
					$nMode = $_REQUEST['interval'];

					if (is_numeric($nHour) && is_numeric($nDay) && is_numeric($nMode))
						$bOK = true;
					else
						$bOK = false;

					if ($bOK == true)
					{
						if (($nHour < 24) && ($nHour >= 0) && ($nDay < 8) && ($nDay > 0) && ($nMode < 4) && ($nMode >= 0))
							$bOK = true;
						else
							$bOK = false;
					}

					if ($bOK == true)
					{
						/*sql("UPDATE `user` SET `watchmail_mode`='&1', `watchmail_hour`='&2', `watchmail_day`='&3' WHERE `user_id`='&4'",
									$nMode,
									$nHour,
									$nDay,
									$usr['userid']);*/
						
						$query = "UPDATE `user` SET `watchmail_mode`=:1, `watchmail_hour`=:2, `watchmail_day`=:3 WHERE `user_id`=:4";
						$dbc->multiVariableQuery($query,$nMode, $nHour, $nDay, $usr['userid']);

						tpl_set_var('commit', $commit);
					}
					else
					{
						tpl_set_var('commit', $commiterr);
					}
				}
				else
					tpl_set_var('commit', '');


				// einstellungen auslesen
				$rs = sql("SELECT `watchmail_mode`, `watchmail_hour`, `watchmail_day` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
				$r = sql_fetch_array($rs);
				mysql_free_result($rs);

				$tmpOptions = "";
				for ($i = 0; $i < 24; $i++)
				{
					$tmpOptions .= sprintf("<option value='%d' %s>%02d:00</option>\n",
							$i, $i ==  $r['watchmail_hour'] ? "selected='selected'" : "", $i);
				}
				tpl_set_var('houroptions', $tmpOptions);

                                // table indices of $intervalls are misplaced accordingly to
                                // ones used in runwatch.php script that performs the real check
                                // there: immediately=1, daily=0, and weekly=2
                                // thus cannot use $intervalls with its indices
                                $tmpOptions = sprintf("<option value='1' %s>".$intervalls[0]."</option>\n",
                                              1 ==  $r['watchmail_mode'] ? "selected='selected'" : "");
                                $tmpOptions .= sprintf("<option value='0' %s>".$intervalls[1]."</option>\n",
                                              0 ==  $r['watchmail_mode'] ? "selected='selected'" : "");
                                $tmpOptions .= sprintf("<option value='2' %s>".$intervalls[2]."</option>\n",
                                              2 ==  $r['watchmail_mode'] ? "selected='selected'" : "");				
                                tpl_set_var('intervalls', $tmpOptions);

				$tmpOptions = '';
				for ($i = 1; $i < count($weekday) + 1; $i++)
				{
					$tmpOptions .= sprintf("<option value='%d' %s>%s</option>\n",
							$i, $i == $r['watchmail_day'] ? "selected='selected'" : "", $weekday[$i]);

				}
				tpl_set_var('weekdays', $tmpOptions);
			}
			else
			{
				include($stylepath . '/mywatches.inc.php');
				$tplname = 'mywatches';

				$bml_id = 0;
				tpl_set_var('title_text', $standard_title);

				
				if( $RQ == "map" )
				{
					//JG - domek	
					$usrlatitude = 54.400;
					$usrlongitude = 18.650;
					
					$dbc->multiVariableQuery("SELECT `latitude` FROM user WHERE user_id=:1", sql_escape($usr['userid']) );
					$record = $dbc->dbResultFetch();
					if ( $dbc->rowCount() )
						$usrlatitude  = $record["latitude"];
						
					$dbc->multiVariableQuery("SELECT `longitude` FROM user WHERE user_id=:1", sql_escape($usr['userid']) );
					$record = $dbc->dbResultFetch();
					if ( $dbc->rowCount() )
						$usrlongitude= $record["longitude"];
				}
				
				
				//get all caches watched
				//$rs = sql("SELECT `cache_watches`.`cache_id` AS `cache_id`, `caches`.`name` AS `name`, `caches`.`last_found` AS `last_found` FROM `cache_watches` INNER JOIN `caches` ON (`cache_watches`.`cache_id`=`caches`.`cache_id`) WHERE `cache_watches`.`user_id`='&1' ORDER BY `caches`.`name`", $usr['userid']);
				//$query = "SELECT `cache_watches`.`cache_id` AS `cache_id`, `caches`.`name` AS `name`, `caches`.`last_found` AS `last_found` FROM `cache_watches` INNER JOIN `caches` ON (`cache_watches`.`cache_id`=`caches`.`cache_id`) WHERE `cache_watches`.`user_id`= :1 ORDER BY `caches`.`name`";
				$query = "SELECT 
						`cache_watches`.`cache_id` AS `cache_id`, 						
						`caches`.`name` AS `name`, `caches`.`last_found` AS `last_found`,
						`caches`.`latitude` `latitude`,
						`caches`.`longitude` `longitude`,
						caches.wp_oc AS wp,						
						cache_type.icon_small AS cache_icon_small,
						user.username AS user_name,
						log_types.icon_small AS icon_small,
						cl.text AS log_text, 
						cl.type AS log_type,
						cl.user_id AS luser_id,
						cl.date AS log_date,
						cl.id
						FROM `cache_watches` 
						INNER JOIN `caches` ON (`cache_watches`.`cache_id`=`caches`.`cache_id`)
						INNER JOIN cache_logs as cl ON (caches.cache_id = cl.cache_id)
						INNER JOIN log_types ON (cl.type = log_types.id) 
						INNER JOIN user ON (cl.user_id = user.user_id)
						INNER JOIN cache_type ON (caches.type = cache_type.id)
						
						WHERE `cache_watches`.`user_id`= :1 and cl.id =
							( SELECT id
								FROM cache_logs cl_id
								WHERE cl.cache_id = cl_id.cache_id and cl_id.date =
						
									( SELECT max( date )
										FROM cache_logs
										WHERE cl.cache_id = cache_id 
									)
								limit 1
							)  
						
						ORDER BY `caches`.`name`";
						
				
				
				$dbc->multiVariableQuery($query, $usr['userid'] );
				
				//if (mysql_num_rows($rs) == 0)
				$rowCount = $dbc->rowCount();
				if ( !$rowCount )
				{
					tpl_set_var('watches', $no_watches);
					tpl_set_var('print_delete_all_watches', '');
					tpl_set_var('export_all_watches', '');
				}
				else
				{
					$watches = '';
					$markers = '';
					
					if( $RQ == "map" ) 
					{ 
							$tplname = 'mywatches_map';
					}
					
					for ($i = 0; $i < $rowCount; $i++)
					{
						//$record = sql_fetch_array($rs);
						$record = $dbc->dbResultFetch();
						

						if( $RQ == "map" )
						{
							$rlat = $record[ 'latitude' ];
							$rlon = $record[ 'longitude' ];
													
							$rusername="\"".$record[ 'user_name' ]."\"";
							
							$rcache_icon_small = "\"".$record['cache_icon_small']."\"";
							$rwp = "\"".$record['wp']."\"";
							$rid = "\"".$record['id']."\"";
							$ricon_small = "\"".$record['icon_small']."\"";
							$rluser_id = "\"".$record['luser_id']."\"";
							$rlog_date="\"".htmlspecialchars(date("Y-m-d", strtotime($record['log_date'])), ENT_COMPAT, 'UTF-8')."\"";							
							$rcache_name="\"".CleanSpecChars( $record['name'], 0 )."\"";
							$icon = '"tpl/stdstyle/images/google_maps/gmblue.png"';
							$rlog_text  = "\"".CleanSpecChars( $record[ 'log_text'], 0 )."\"";

							 $markers .= "AddMarker(new google.maps.LatLng($rlat, $rlon), $icon, $rcache_icon_small, $rwp, $rcache_name, $rid, $ricon_small,$rluser_id, $rusername, $rlog_date, $rlog_text );\r\n";
							 //"addMarker(".$x.",".$y.",icon".$record['log_type'].",'".$record['cache_icon_small']."','".$record['wp']."','".$cache_name."','".$record['id']."','".$record['icon_small']."','".$record['luser_id']."','".$username."','".$log_date."');\n";
							 
							 
							 
						}
						else
						{
							//$tmp_watch = $i % 2 == 0 ? $watche : $watcho;
							$bgcolor = ( $i% 2 )? $bgcolor1 : $bgcolor2; 						
							$tmp_watch = $watch;
							$tmp_watch = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $tmp_watch);
	
							if ($record['last_found'] == NULL || $record['last_found'] == '0000-00-00 00:00:00')
							{
								$tmp_watch = mb_ereg_replace('{lastfound}', htmlspecialchars($no_found_date, ENT_COMPAT, 'UTF-8'), $tmp_watch);
							}
							else
							{
								$tmp_watch = mb_ereg_replace('{lastfound}', htmlspecialchars(strftime($dateformat, strtotime($record['last_found'])), ENT_COMPAT, 'UTF-8'), $tmp_watch);
							}
	
							$tmp_watch = mb_ereg_replace('{urlencode_cacheid}', htmlspecialchars(urlencode($record['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_watch);
							$tmp_watch = mb_ereg_replace('{cacheid}', htmlspecialchars($record['cache_id'], ENT_COMPAT, 'UTF-8'), $tmp_watch);
							$tmp_watch = mb_ereg_replace('{icon_name}', $record['icon_small'], $tmp_watch);
							$tmp_watch = mb_ereg_replace('{bgcolor}', $bgcolor, $tmp_watch);
							
							$log_text  = CleanSpecChars( $record[ 'log_text'], 1 );
							
							/*$log_text  = htmlspecialchars( $record[ 'log_text'], ENT_COMPAT, 'UTF-8');
							$log_text = str_replace("\r\n", " ",$log_text);
							$log_text = str_replace("\n", " ",$log_text);
							$log_text = str_replace("'", "-",$log_text);
							$log_text = str_replace("\"", " ",$log_text);*/
							
							//$log_text = str_replace("\r", " ",$log_text);
							//$log_text = cleanup_text(str_replace("\r\n", " ", $log_text ));
							//$log_text = str_rot13_html($log_text);
							$log_text = "<b>".$record['user_name'].":</b><br>".$log_text;
							//$log_text = "ala ma kota";
							
							$tmp_watch = mb_ereg_replace('{log_text}', $log_text, $tmp_watch);
	
							$watches .= $tmp_watch . "\n";
						}
					}
					tpl_set_var('watches', $watches);
					tpl_set_var('print_delete_all_watches', $print_delete_all_watches);
					tpl_set_var('export_all_watches', $export_all_watches);
					
					if( $RQ == "map" )
					{
						tpl_set_var('markers', $markers);
						tpl_set_var('api_key', $googlemap_key);
						tpl_set_var('latitude', $usrlatitude);
						tpl_set_var('longitude', $usrlongitude);
						tpl_set_var('cachemap_header', '<script src="//maps.googleapis.com/maps/api/js?sensor=false&amp;language=pl" type="text/javascript"></script>');
					}
					
					
				}
			}
			
			unset( $dbc );
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
