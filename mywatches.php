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
			
			if(isset($_REQUEST['rq']))
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

				//get all caches watched
				//$rs = sql("SELECT `cache_watches`.`cache_id` AS `cache_id`, `caches`.`name` AS `name`, `caches`.`last_found` AS `last_found` FROM `cache_watches` INNER JOIN `caches` ON (`cache_watches`.`cache_id`=`caches`.`cache_id`) WHERE `cache_watches`.`user_id`='&1' ORDER BY `caches`.`name`", $usr['userid']);
				//$query = "SELECT `cache_watches`.`cache_id` AS `cache_id`, `caches`.`name` AS `name`, `caches`.`last_found` AS `last_found` FROM `cache_watches` INNER JOIN `caches` ON (`cache_watches`.`cache_id`=`caches`.`cache_id`) WHERE `cache_watches`.`user_id`= :1 ORDER BY `caches`.`name`";
				$query = "SELECT `cache_watches`.`cache_id` AS `cache_id`, `caches`.`name` AS `name`, `caches`.`last_found` AS `last_found`, 
						log_types.icon_small AS icon_small,
						cl.text AS log_text, user.username AS user_name
						FROM `cache_watches` 
						INNER JOIN `caches` ON (`cache_watches`.`cache_id`=`caches`.`cache_id`)
						INNER JOIN cache_logs as cl ON (caches.cache_id = cl.cache_id)
						INNER JOIN log_types ON (cl.type = log_types.id) 
						INNER JOIN user ON (cl.user_id = user.user_id)
						
						WHERE `cache_watches`.`user_id`= :1 and cl.date =
							( SELECT max( date )
								FROM cache_logs
								WHERE cl.cache_id = cache_id )  
						
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

					
					for ($i = 0; $i < $rowCount; $i++)
					{
						//$record = sql_fetch_array($rs);
						$record = $dbc->dbResultFetch();
						
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
						
												
						//$log_text = "<b>Rhotaxx:</b>Po dojechaniu na miejsce wysiedliśmy z Highwaymana i prawie natychmiast zaatakowały nas Radskorpiony i Centaury. Wycinając sobie drogę za pomocą Bozara i M72 Gauss Rifle'a"; 
								//dotarliśmy przed wejście Krypty."; 
								//Obowiązkowa dawka Rad-X, odrobinę Jeta i wchodzimy do środka. Idąc zgodnie ze wskaz&amp;oacute;wkami dotarliśmy do celu. Dopiąłem Jumpsuit , wczołgałem się do tunelu i już po chwili miałem w rękach G.E.C.K.a. &lt;img title=&quot;Laughing&quot; src=&quot;lib/tinymce/plugins/emotions/img/smiley-laughing.gif&quot; border=&quot;0&quot; alt=&quot;Laughing&quot; /&gt;&lt;br /&gt;";
						$log_text  = htmlspecialchars( $record[ 'log_text'], ENT_COMPAT, 'UTF-8');
						
						$log_text = str_replace("\r\n", " ",$log_text);
						$log_text = str_replace("\n", " ",$log_text);
						$log_text = str_replace("'", "-",$log_text);
						$log_text = str_replace("\"", " ",$log_text);
						
						//$log_text = str_replace("\r", " ",$log_text);
						//$log_text = cleanup_text(str_replace("\r\n", " ", $log_text ));
						//$log_text = str_rot13_html($log_text);
						$log_text = "<b>".$record['user_name'].":</b><br>".$log_text;
						//$log_text = "ala ma kota";
						
						$tmp_watch = mb_ereg_replace('{log_text}', $log_text, $tmp_watch);

						$watches .= $tmp_watch . "\n";
					}
					tpl_set_var('watches', $watches);
					tpl_set_var('print_delete_all_watches', $print_delete_all_watches);
					tpl_set_var('export_all_watches', $export_all_watches);
				}
			}
			
			unset( $dbc );
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
