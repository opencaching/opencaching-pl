<?php
/***************************************************************************
																./search.php
															-------------------
		begin                : July 25 2004
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

   Unicode Reminder メモ

  search and export page for caches, users, logs and pictures possible output
  formats are currently XHTML and XML. The search options can be loaded from
  stored query in the database, dump of the options in HTTP-POST/GET variable
  or HTML form fields

	TODO:
	- fehlermeldungen bei falschen koordinaten
	- entfernungsberechnung "auslagern" (getSqlDistanceFormula überall verwenden)
	- nochmals alles testen

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('./lib/search.inc.php');
	
	// SQL-Debug?
	$sqldebug = false;
	global $sql_debug;
	$sql_debug = $sqldebug;
	
	if ($sql_debug == true)
	{
		require_once('./lib/sqldebugger.inc.php');
		sqldbg_begin();
	}

	//Preprocessing
	if ($error == false)
	{
		$tplname = 'search';
		require($stylepath . '/search.inc.php');
		require($rootpath . 'lib/caches.inc.php');
		
		//km => target-unit
		$multiplier['km'] = 1;
		$multiplier['sm'] = 0.62137;
		$multiplier['nm'] = 0.53996;

		if (isset($_REQUEST['queryid']) || isset($_REQUEST['showresult']))
		{
			$bCookieQueryid = false;
			$queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] : 0;
		}
		else
		{
			$bCookieQueryid = true;
			$queryid = get_cookie_setting('lastqueryid');
			if ($queryid == false) $queryid = 0;
			
			if ($queryid != 0)
			{
				// check if query exists
				$rsCount = sql("SELECT COUNT(*) `count` FROM `queries` WHERE id='&1'", $queryid);
				$rCount = sql_fetch_array($rsCount);
				mysql_free_result($rsCount);
				
				if ($rCount['count'] == 0)
					$queryid = 0;
			}

			if ($queryid == 0)
			{
				// das Suchformular wird initialisiert (keine Vorbelegungen vorhanden)
				$_REQUEST['cache_attribs'] = '';
				$rs = sql('SELECT `id` FROM `cache_attrib` WHERE `default`=1');
				while ($r = sql_fetch_assoc($rs))
				{
					if ($_REQUEST['cache_attribs'] != '') $_REQUEST['cache_attribs'] .= ';';
					$_REQUEST['cache_attribs'] .= $r['id'];
				}
				mysql_free_result($rs);
				
				$_REQUEST['cache_attribs_not'] = '';
				$rs = sql('SELECT `id` FROM `cache_attrib` WHERE `default`=2');
				while ($r = sql_fetch_assoc($rs))
				{
					if ($_REQUEST['cache_attribs_not'] != '') $_REQUEST['cache_attribs_not'] .= ';';
					$_REQUEST['cache_attribs_not'] .= $r['id'];
				}
				mysql_free_result($rs);
			}
		}
		$queryid = $queryid + 0;

		if ($queryid != 0)
		{
			//load options from db
			$query_rs = sql("SELECT `user_id`, `options` FROM `queries` WHERE id='&1' AND (`user_id`=0 OR `user_id`='&2')", $queryid, $usr['userid']+0);
			
			if (mysql_num_rows($query_rs) == 0)
			{
				$tplname = 'error';
				tpl_set_var('tplname', 'search.php');
				tpl_set_var('error_msg', $error_query_not_found);
				tpl_BuildTemplate();
				exit;
			}
			else
			{
				$record = sql_fetch_array($query_rs);
				$options = unserialize($record['options']);
				if ($record['user_id'] != 0)
					$options['userid'] = $record['user_id'];
				mysql_free_result($query_rs);

				$options['queryid'] = $queryid;

				sql("UPDATE `queries` SET `last_queried`=NOW() WHERE `id`='&1'", $queryid);

				// änderbare werte überschreiben
				if (isset($_REQUEST['output']))
					$options['output'] =  $_REQUEST['output'];
					
				if (isset($_REQUEST['showresult']))
				{
					$options['showresult'] = $_REQUEST['showresult'];
				}
				else
				{
					if ($bCookieQueryid == true)
					{
						$options['showresult'] = 0;
					}
				}

				// finderid in finder umsetzen
				$options['finderid'] = isset($options['finderid']) ? $options['finderid'] + 0 : 0;
				if(isset($options['finder']) && $options['finderid'] > 0)
				{
					$rs_name = sql("SELECT `username` FROM `user` WHERE `user_id`='&1'", $options['finderid']);
					if(mysql_num_rows($rs_name) == 1)
					{
						$record_name = sql_fetch_array($rs_name);
						$options['finder'] = $record_name['username'];
					}
					unset($record_name);
					mysql_free_result($rs_name);
				}

				// ownerid in owner umsetzen
				$options['ownerid'] = isset($options['ownerid']) ? $options['ownerid'] + 0 : 0;
				if(isset($options['owner']) && $options['ownerid'] > 0)
				{
					$rs_name = sql("SELECT `username` FROM `user` WHERE `user_id`='&1'", $options['ownerid']);
					if(mysql_num_rows($rs_name) == 1)
					{
						$record_name = sql_fetch_array($rs_name);
						$options['owner'] = $record_name['username'];
					}
					unset($record_name);
					mysql_free_result($rs_name);
				}
			}
		}
		else
		{
			// hack
			if(isset($_REQUEST['searchto']) && ($_REQUEST['searchto'] != '')) 
			{
				unset($_REQUEST['searchbyname']);
				unset($_REQUEST['searchbydistance']);
				unset($_REQUEST['searchbyowner']);
				unset($_REQUEST['searchbyfinder']);
				unset($_REQUEST['searchbyplz']);
				unset($_REQUEST['searchbyort']);
				unset($_REQUEST['searchbyfulltext']);
				$_REQUEST[$_REQUEST['searchto']] = "hoho";
			}

			//get the taken search options and backup them in the queries table (to view "the next page")
			$options['f_userowner'] = isset($_REQUEST['f_userowner']) ? $_REQUEST['f_userowner'] : 0;
			$options['f_userfound'] = isset($_REQUEST['f_userfound']) ? $_REQUEST['f_userfound'] : 0;
			$options['f_inactive'] = isset($_REQUEST['f_inactive']) ? $_REQUEST['f_inactive'] : 1;
			$options['expert'] = isset($_REQUEST['expert']) ? $_REQUEST['expert'] : 0;
			$options['showresult'] = isset($_REQUEST['showresult']) ? $_REQUEST['showresult'] : 0;
			$options['output'] = isset($_REQUEST['output']) ? $_REQUEST['output'] : 'HTML';
			
			if (isset($_REQUEST['cache_attribs']))
			{
				if ($_REQUEST['cache_attribs'] != '')
					$options['cache_attribs'] = mb_split(';', $_REQUEST['cache_attribs']);
				else
					$options['cache_attribs'] = array();
			}
			else
				$options['cache_attribs'] = array();

			if (isset($_REQUEST['cache_attribs_not']))
			{
				if ($_REQUEST['cache_attribs_not'] != '')
					$options['cache_attribs_not'] = mb_split(';', $_REQUEST['cache_attribs_not']);
				else
					$options['cache_attribs_not'] = array();
			}
			else
				$options['cache_attribs_not'] = array();

			if (!isset($_REQUEST['unit']))
			{
				$options['unit'] = 'km';
			}
			elseif (mb_strtolower($_REQUEST['unit']) == 'sm')
			{
				$options['unit'] = 'sm';
			}
			elseif (mb_strtolower($_REQUEST['unit']) == 'nm')
			{
				$options['unit'] = 'nm';
			}
			else
			{
				$options['unit'] = 'km';
			}

			if (isset($_REQUEST['searchbyname']))
			{
				$options['searchtype'] = 'byname';
				$options['cachename'] = isset($_REQUEST['cachename']) ? stripslashes($_REQUEST['cachename']) : '';
			}
			elseif (isset($_REQUEST['searchbyowner']))
			{
				$options['searchtype'] = 'byowner';

				$options['ownerid'] = isset($_REQUEST['ownerid']) ? $_REQUEST['ownerid'] : 0;
				$options['owner'] = isset($_REQUEST['owner']) ? stripslashes($_REQUEST['owner']) : '';
			}
			elseif (isset($_REQUEST['searchbyfinder']))
			{
				$options['searchtype'] = 'byfinder';
				
				$options['finderid'] = isset($_REQUEST['finderid']) ? $_REQUEST['finderid'] : 0;
				$options['finder'] = isset($_REQUEST['finder']) ? stripslashes($_REQUEST['finder']) : '';
			}
			elseif (isset($_REQUEST['searchbyort']))
			{
				$options['searchtype'] = 'byort';
				
				$options['ort'] = isset($_REQUEST['ort']) ? stripslashes($_REQUEST['ort']) : '';
				$options['locid'] = isset($_REQUEST['locid']) ? $_REQUEST['locid'] : 0;
				$options['locid'] = $options['locid'] + 0;
			}
			elseif (isset($_REQUEST['searchbyplz']))
			{
				$options['searchtype'] = 'byplz';
				
				$options['plz'] = isset($_REQUEST['plz']) ? stripslashes($_REQUEST['plz']) : '';
				$options['locid'] = isset($_REQUEST['locid']) ? $_REQUEST['locid'] : 0;
				$options['locid'] = $options['locid'] + 0;
			}
			elseif (isset($_REQUEST['searchbydistance']))
			{
				$options['searchtype'] = 'bydistance';

				$options['latNS'] = isset($_REQUEST['latNS']) ? $_REQUEST['latNS'] : 'N';
				$options['lonEW'] = isset($_REQUEST['lonEW']) ? $_REQUEST['lonEW'] : 'E';
				
				$options['lat_h'] = isset($_REQUEST['lat_h']) ? $_REQUEST['lat_h'] : 0;
				$options['lon_h'] = isset($_REQUEST['lon_h']) ? $_REQUEST['lon_h'] : 0;
				$options['lat_min'] = isset($_REQUEST['lat_min']) ? $_REQUEST['lat_min'] : 0;
				$options['lon_min'] = isset($_REQUEST['lon_min']) ? $_REQUEST['lon_min'] : 0;

				$options['distance'] = isset($_REQUEST['distance']) ? $_REQUEST['distance'] : 0;
			}
			elseif (isset($_REQUEST['searchbyfulltext']))
			{
				$options['searchtype'] = 'byfulltext';

				$options['ft_name'] = isset($_REQUEST['ft_name']) ? $_REQUEST['ft_name']+0 : 0;
				$options['ft_desc'] = isset($_REQUEST['ft_desc']) ? $_REQUEST['ft_desc']+0 : 0;
				$options['ft_logs'] = isset($_REQUEST['ft_logs']) ? $_REQUEST['ft_logs']+0 : 0;
				$options['ft_pictures'] = isset($_REQUEST['ft_pictures']) ? $_REQUEST['ft_pictures']+0 : 0;

				$options['fulltext'] = isset($_REQUEST['fulltext']) ? $_REQUEST['fulltext'] : '';
			}
			elseif (isset($_REQUEST['searchbycacheid']))
			{
				$options['searchtype'] = 'bycacheid';
				$options['cacheid'] = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : 0;
				if (!is_numeric($options['cacheid'])) $options['cacheid'] = 0;
			}
			else
			{
				if (isset($_REQUEST['showresult']))
					tpl_errorMsg('search', 'Unbekannter Suchtyp');
				else
				{
					$options['searchtype'] = 'byname';
					$options['cachename'] = '';
				}
			}
			
			$options['sort'] = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'bydistance';

			$options['country'] = isset($_REQUEST['country']) ? $_REQUEST['country'] : '';
			$options['cachetype'] = isset($_REQUEST['cachetype']) ? $_REQUEST['cachetype'] : '';
			
			if ($options['showresult'] != 0)
			{
				//save the search-options in the database
				if (isset($options['queryid']) && (isset($options['userid'])))
				{
					if ($options['userid'] != 0)
						sql("UPDATE `queries` SET `options`='&1', `last_queried`=NOW() WHERE `id`='&2' AND `user_id`='&3'", serialize($options), $options['queryid'], $options['userid']);
				}
				else
				{
					sql('INSERT INTO `queries` (`user_id`, `options`, `uuid`, `last_queried`) VALUES (0, \'&1\', UUID(), NOW())', serialize($options));
					$options['queryid'] = mysql_insert_id();
				}
			}
			else
			{
				$options['queryid'] = 0;
			}
		}
		set_cookie_setting('lastqueryid', $options['queryid']);

		//remove old queries (after 1 hour without use)
		$removedate = date('Y-m-d H:i:s', time() - 3600);
		sql('DELETE FROM `queries` WHERE `last_queried` < \'&1\' AND `user_id`=0', $removedate);

		//prepare output
		if(!isset($options['showresult'])) $options['showresult']='0';
		if ($options['showresult'] == 1)
		{
			if(!isset($options['output'])) $options['output']='';
			if ((mb_strpos($options['output'], '.') !== false) || 
			    (mb_strpos($options['output'], '/') !== false) || 
		    	(mb_strpos($options['output'], '\\') !== false)
			   )
			{
				$options['output'] = 'HTML';
			}
			
			//make a list of cache-ids that are in the result
			if(!isset($options['expert'])) $options['expert']='';
			if ($options['expert'] == 0)
			{
				$sql_select = array();
				$sql_from = array();
				$sql_join = array();
				$sql_where = array();
				$sql_having = array();
				$sql_group = array();

				// show only published caches
				$sql_where[] = '`caches`.`status` != 4';
				$sql_where[] = '`caches`.`status` != 5';
				if(!$usr['admin'])
				{
					$sql_where[] = '`caches`.`status` != 6';
				}
				
				//check the entered data and build SQL
				if(!isset($options['searchtype'])) $options['searchtype']='';
				if ($options['searchtype'] == 'byname')
				{
					$sql_select[] = '`caches`.`cache_id` `cache_id`';
					$sql_from[] = '`caches`';
					$sql_where[] = '`caches`.`name` LIKE \'%' . sql_escape($options['cachename']) . '%\'';
				}
				elseif ($options['searchtype'] == 'byowner')
				{
					if ($options['ownerid'] != 0)
					{
						$sql_select[] = '`caches`.`cache_id` `cache_id`';
						$sql_from[] = '`caches`';
						$sql_where[] = '`user_id`=\'' . sql_escape($options['ownerid']) . '\'';
					}
					else
					{
						$sql_select[] = '`caches`.`cache_id` `cache_id`';
						$sql_from[] = '`caches`, `user`';
						$sql_where[] = '`caches`.`user_id`=`user`.`user_id`';
						$sql_where[] = '`user`.`username`=\'' . sql_escape($options['owner']) . '\'';
					}
				}
				elseif (($options['searchtype'] == 'byplz') || ($options['searchtype'] == 'byort'))
				{
					$locid = $options['locid'];
					
					if ($options['searchtype'] == 'byplz')
					{
						if ($locid == 0)
						{
							$plz = $options['plz'];

							$sql = "SELECT `loc_id` FROM `geodb_textdata` WHERE `text_type`=500300000 AND `text_val`='" . sql_escape($plz) . "'";
							$rs = sql($sql);
							if (mysql_num_rows($rs) == 0)
							{
								$options['error_plz'] = true;
								outputSearchForm($options);
								exit;
							}
							elseif (mysql_num_rows($rs) == 1)
							{
								$r = sql_fetch_array($rs);
								mysql_free_result($rs);
								$locid = $r['loc_id'];
							}
							else
							{
								// ok, viele locations ... alle auflisten ...
								outputLocidSelectionForm($sql, $options);
								exit;
							}
						}

						// ok, wir haben einen ort ... koordinaten ermitteln
						$locid = $locid + 0;
						$rs = sql('SELECT `lon`, `lat` FROM `geodb_coordinates` WHERE `loc_id`=' . $locid . ' AND coord_type=200100000');
						if ($r = sql_fetch_array($rs))
						{
							// ok ... wir haben koordinaten ...
							
							$lat = $r['lat'] + 0;
							$lon = $r['lon'] + 0;

							$distance_unit = 'km';
							$distance = 150;
							
							// ab hier selber code wie bei bydistance ... TODO: in funktion auslagern
							
							//all target caches are between lat - max_lat_diff and lat + max_lat_diff
							$max_lat_diff = $distance / (111.12 * $multiplier[$distance_unit]);
							
							//all target caches are between lon - max_lon_diff and lon + max_lon_diff
							//TODO: check!!!
							$max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378 * $multiplier[$distance_unit] * 3.14159);
							
							$lon_rad = $lon * 3.14159 / 180;
							$lat_rad = $lat * 3.14159 / 180;

							sql('CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY 
													SELECT 
														(' . getSqlDistanceFormula($lon, $lat, $distance, $multiplier[$distance_unit]) . ') `distance`,
														`caches`.`cache_id` `cache_id`
													FROM `caches` FORCE INDEX (`latitude`)
													WHERE `longitude` > ' . ($lon - $max_lon_diff) . ' 
														AND `longitude` < ' . ($lon + $max_lon_diff) . ' 
														AND `latitude` > ' . ($lat - $max_lat_diff) . ' 
														AND `latitude` < ' . ($lat + $max_lat_diff) . '
													HAVING `distance` < ' . $distance);
							sql('ALTER TABLE result_caches ADD PRIMARY KEY ( `cache_id` )');

							$sql_select[] = '`result_caches`.`cache_id`';
							$sql_from[] = '`result_caches`, `caches`';
							$sql_where[] = '`caches`.`cache_id`=`result_caches`.`cache_id`';
						}
						else
						{
							$options['error_locidnocoords'] = true;
							outputSearchForm($options);
							exit;
						}
					}
					else if ($options['searchtype'] == 'byort')
					{
						if ($locid == 0)
						{
							require_once($rootpath . 'lib/search.inc.php');
						
							$ort = $options['ort'];
							$simpletexts = search_text2sort($ort);
							$simpletextsarray = explode_multi($simpletexts, ' -/,');

							$sqlhashes = '';
							$wordscount = 0;
							foreach ($simpletextsarray AS $text)
							{
								if ($text != '')
								{
									$searchstring = search_text2simple($text);

									if ($sqlhashes != '') $sqlhashes .= ' OR ';
									$sqlhashes .= '`gns_search`.`simplehash`=' . sprintf("%u", crc32($searchstring));
									
									$wordscount++;
								}
							}
							
							if ($sqlhashes == '')
							{
								$options['error_noort'] = true;
								outputSearchForm($options);
							}

							// temporäre tabelle erstellen und dann einträge entfernen, die nicht mindestens so oft vorkommen wie worte gegeben wurden
							sql('CREATE TEMPORARY TABLE tmpuniids (`uni_id` int(11) NOT NULL, `cnt` int(11) NOT NULL, `olduni` int(11) NOT NULL, `simplehash` int(11) NOT NULL) ENGINE=MEMORY SELECT `gns_search`.`uni_id` `uni_id`, 0 `cnt`, 0 `olduni`, `simplehash` FROM `gns_search` WHERE ' . $sqlhashes);
							sql('ALTER TABLE `tmpuniids` ADD INDEX (`uni_id`)');
//	BUGFIX: dieser Code sollte nur ausgeführt werden, wenn mehr als ein Suchbegriff eingegeben wurde
//					damit alle Einträge gefiltert, die nicht alle Suchbegriffe enthalten
//					nun wird dieser Quellcode auch ausgeführt, um mehrfache uni_id's zu filtern
//          Notwendig, wenn nach Baden gesucht wird => Baden-Baden war doppelt in der Liste
//							if ($wordscount > 1)
//							{
								sql('CREATE TEMPORARY TABLE `tmpuniids2` (`uni_id` int(11) NOT NULL, `cnt` int(11) NOT NULL, `olduni` int(11) NOT NULL) ENGINE=MEMORY SELECT `uni_id`, COUNT(*) `cnt`, 0 olduni FROM `tmpuniids` GROUP BY `uni_id` HAVING `cnt` >= ' . $wordscount);
								sql('ALTER TABLE `tmpuniids2` ADD INDEX (`uni_id`)');
								sql('DROP TABLE `tmpuniids`');
								sql('ALTER TABLE `tmpuniids2` RENAME `tmpuniids`');
//							}
							
//    add: SELECT g2.uni FROM `tmpuniids` JOIN gns_locations g1 ON tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!='N' AND g2.nt='N'
// remove: SELECT g1.uni FROM `tmpuniids` JOIN gns_locations g1 ON tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!='N' AND g2.nt='N'

							// und jetzt noch alle englischen bezeichnungen durch deutsche ersetzen (wo möglich) ...
							sql('CREATE TEMPORARY TABLE `tmpuniidsAdd` (`uni` int(11) NOT NULL, `olduni` int(11) NOT NULL, PRIMARY KEY  (`uni`)) ENGINE=MEMORY SELECT g2.uni uni, g1.uni olduni FROM `tmpuniids` JOIN gns_locations g1 ON tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!=\'N\' AND g2.nt=\'N\' GROUP BY uni');
							sql('CREATE TEMPORARY TABLE `tmpuniidsRemove` (`uni` int(11) NOT NULL, PRIMARY KEY  (`uni`)) ENGINE=MEMORY SELECT DISTINCT g1.uni uni FROM `tmpuniids` JOIN gns_locations g1 ON tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!=\'N\' AND g2.nt=\'N\'');
							sql('DELETE FROM tmpuniids WHERE uni_id IN (SELECT uni FROM tmpuniidsRemove)');
							sql('DELETE FROM tmpuniidsAdd WHERE uni IN (SELECT uni_id FROM tmpuniids)');
							sql('INSERT INTO tmpuniids (uni_id, olduni) SELECT uni, olduni FROM tmpuniidsAdd');
							sql('DROP TABLE tmpuniidsAdd');
							sql('DROP TABLE tmpuniidsRemove');

							$rs = sql('SELECT `uni_id` FROM tmpuniids');
							if (mysql_num_rows($rs) == 0)
							{
								mysql_free_result($rs);

								$options['error_ort'] = true;
								outputSearchForm($options);
								exit;
							}
							elseif (mysql_num_rows($rs) == 1)
							{
								$r = sql_fetch_array($rs);
								mysql_free_result($rs);

								// wenn keine 100%ige übereinstimmung nochmals anzeigen
								$locid = $r['uni_id'] + 0;
								$rsCmp = sql('SELECT `full_name` FROM `gns_locations` WHERE `uni`=' . $locid . ' LIMIT 1');
								$rCmp = sql_fetch_array($rsCmp);
								mysql_free_result($rsCmp);
								
								if (mb_strtolower($rCmp['full_name']) != mb_strtolower($ort))
								{
									outputUniidSelectionForm('SELECT `uni_id`, `olduni` FROM `tmpuniids`', $options);
								}
							}
							else
							{
								mysql_free_result($rs);
								outputUniidSelectionForm('SELECT `uni_id`, `olduni` FROM `tmpuniids`', $options);
								exit;
							}
						}
						
								
						// ok, wir haben einen ort ... koordinaten ermitteln
						$locid = $locid + 0;
						$rs = sql('SELECT `lon`, `lat` FROM `gns_locations` WHERE `uni`=' . $locid . ' LIMIT 1');
						if ($r = sql_fetch_array($rs))
						{
							// ok ... wir haben koordinaten ...
							
							$lat = $r['lat'] + 0;
							$lon = $r['lon'] + 0;

							$lon_rad = $lon * 3.14159 / 180;
							$lat_rad = $lat * 3.14159 / 180;

							$distance_unit = 'km';
							$distance = 150;
							
							// ab hier selber code wie bei bydistance ... TODO: in funktion auslagern
							
							//all target caches are between lat - max_lat_diff and lat + max_lat_diff
							$max_lat_diff = $distance / (111.12 * $multiplier[$distance_unit]);
							
							//all target caches are between lon - max_lon_diff and lon + max_lon_diff
							//TODO: check!!!
							$max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378 * $multiplier[$distance_unit] * 3.14159);
							
							sql('CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY 
													SELECT 
														(' . getSqlDistanceFormula($lon, $lat, $distance, $multiplier[$distance_unit]) . ') `distance`,
														`caches`.`cache_id` `cache_id`
													FROM `caches` FORCE INDEX (`latitude`)
													WHERE `longitude` > ' . ($lon - $max_lon_diff) . ' 
														AND `longitude` < ' . ($lon + $max_lon_diff) . ' 
														AND `latitude` > ' . ($lat - $max_lat_diff) . ' 
														AND `latitude` < ' . ($lat + $max_lat_diff) . '
													HAVING `distance` < ' . $distance);
							sql('ALTER TABLE result_caches ADD PRIMARY KEY ( `cache_id` )');

							$sql_select[] = '`result_caches`.`cache_id`';
							$sql_from[] = '`result_caches`, `caches`';
							$sql_where[] = '`caches`.`cache_id`=`result_caches`.`cache_id`';
						}
						else
						{
							$options['error_locidnocoords'] = true;
							outputSearchForm($options);
							exit;
						}
					}
				}
				elseif ($options['searchtype'] == 'byfinder')
				{
					if ($options['finderid'] != 0)
					{
						$finder_id = $options['finderid'];
					}
					else
					{
						//get the userid
						$rs = sql("SELECT `user_id` FROM `user` WHERE `username`='&1'", $options['finder']);
						$finder_record = sql_fetch_array($rs);
						$finder_id = $finder_record['user_id'];
						mysql_free_result($rs);
					}

					$sql_select[] = '`caches`.`cache_id` `cache_id`';
					$sql_from[] = '`caches`, `cache_logs`';
					$sql_where[] = '`caches`.`cache_id`=`cache_logs`.`cache_id`';
					$sql_where[] = '`cache_logs`.`deleted`=0';
					$sql_where[] = '`cache_logs`.`user_id`=\'' . sql_escape($finder_id) . '\'';
					$sql_where[] = '(`cache_logs`.`type`=1 OR `cache_logs`.`type`=7)'; // found und attended
				}
				elseif ($options['searchtype'] == 'bydistance')
				{
					//check the entered data
					$latNS = $options['latNS'];
					$lonEW = $options['lonEW'];
					
					$lat_h = $options['lat_h'];
					$lon_h = $options['lon_h'];
					$lat_min = $options['lat_min'];
					$lon_min = $options['lon_min'];
					
					$distance = $options['distance'];
					$distance_unit = $options['unit'];
					
					if (is_numeric($lon_h) && is_numeric($lon_min))
					{
						if (($lon_h >= 0) && ($lon_h < 90) && ($lon_min >= 0) && ($lon_min < 90))
						{
							$lon = $lon_h + $lon_min / 60;
							if ($lonEW == 'W') $lon = -$lon;
						}
					}

					if (is_numeric($lat_h) && is_numeric($lat_min))
					{
						if (($lat_h >= 0) && ($lat_h < 90) && ($lat_min >= 0) && ($lat_min < 90))
						{
							$lat = $lat_h + $lat_min / 60;
							if ($latNS == 'S') $lat = -$lat;
						}
					}
					
					if ((!isset($lon)) || (!isset($lat)) || (!is_numeric($distance)))
					{
						outputSearchForm($options);
						exit;
					}
					
					//make the sql-String
					
					//all target caches are between lat - max_lat_diff and lat + max_lat_diff
					$max_lat_diff = $distance / (111.12 * $multiplier[$distance_unit]);
					
					//all target caches are between lon - max_lon_diff and lon + max_lon_diff
					//TODO: check!!!
					$max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378 * $multiplier[$distance_unit] * 3.14159);
					
					$lon_rad = $lon * 3.14159 / 180;
					$lat_rad = $lat * 3.14159 / 180;

					sql('CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY 
											SELECT 
												(' . getSqlDistanceFormula($lon, $lat, $distance, $multiplier[$distance_unit]) . ') `distance`,
												`caches`.`cache_id` `cache_id`
											FROM `caches` FORCE INDEX (`latitude`)
											WHERE `longitude` > ' . ($lon - $max_lon_diff) . ' 
												AND `longitude` < ' . ($lon + $max_lon_diff) . ' 
												AND `latitude` > ' . ($lat - $max_lat_diff) . ' 
												AND `latitude` < ' . ($lat + $max_lat_diff) . '
											HAVING `distance` < ' . $distance);
					sql('ALTER TABLE result_caches ADD PRIMARY KEY ( `cache_id` )');

					$sql_select[] = '`result_caches`.`cache_id`';
					$sql_from[] = '`result_caches`, `caches`';
					$sql_where[] = '`caches`.`cache_id`=`result_caches`.`cache_id`';
				}
				elseif ($options['searchtype'] == 'bycacheid')
				{
					$sql_select[] = '`caches`.`cache_id` `cache_id`';
					$sql_from[] = '`caches`';
					$sql_where[] = '`caches`.`cache_id`=\'' . sql_escape($options['cacheid']) . '\'';
				}
				elseif ($options['searchtype'] == 'byfulltext')
				{
					require_once($rootpath . 'lib/ftsearch.inc.php');

					$fulltext = $options['fulltext'];
					$hashes = ftsearch_hash($fulltext);

					if (count($hashes) == 0)
					{
						$options['error_nofulltext'] = true;
						outputSearchForm($options);
					}

					$ft_types = array();
					if (isset($options['ft_name']) && $options['ft_name'])
						$ft_types[] = 2;
					if (isset($options['ft_logs']) && $options['ft_logs'])
						$ft_types[] = 1;
					if (isset($options['ft_desc']) && $options['ft_desc'])
						$ft_types[] = 3;
					if (isset($options['ft_pictures']) && $options['ft_pictures'])
						$ft_types[] = 6;
					if (count($ft_types) == 0)
						$ft_types[] = 0;

					$sql_select[] = '`caches`.`cache_id` `cache_id`';

					$n = 1;
					foreach ($hashes AS $k => $h)
					{
						$sql_from[] = '`search_index` `s' . $n . '`';
						
						if ($n > 1)
							$sql_where[] = '`s' . ($n-1) . '`.`cache_id`=`s' . $n . '`.`cache_id`';

						$sql_where[] = '`s' . $n . '`.`hash`=\'' . sql_escape($h) . '\'';
						$sql_where[] = '`s' . $n . '`.`object_type` IN (' . implode(',', $ft_types) . ')';

						$n++;
					}
					
					$sql_from[] = '`caches`';
					$sql_where[] = '`s1`.`cache_id`=`caches`.`cache_id`';

					$sqlFilter = 'SELECT DISTINCT ' . implode(',', $sql_select) .
							' FROM ' . implode(',', $sql_from) .
							' WHERE ' . implode(' AND ', $sql_where);

					sql('CREATE TEMPORARY TABLE `tmpFTCaches` (`cache_id` int (11) PRIMARY KEY) ' . $sqlFilter);

					$sql_select = array();
					$sql_from = array();
					$sql_where = array();

					$sql_select[] = '`caches`.`cache_id` `cache_id`';
					$sql_from[] = '`tmpFTCaches`';
					$sql_from[] = '`caches`';
					$sql_where[] = '`caches`.`cache_id`=`tmpFTCaches`.`cache_id`';
				}
				else
				{
					tpl_errorMsg('search', 'Unbekannter Suchtyp');
				}

				// additional options
				if(!isset($options['f_userowner'])) $options['f_userowner']='0';
				if($options['f_userowner'] != 0) { $sql_where[] = '`caches`.`user_id`!=\'' . $usr['userid'] .'\''; }

				if(!isset($options['f_userfound'])) $options['f_userfound']='0';
				if($options['f_userfound'] != 0) 
				{ 
					$sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_logs`.`cache_id` FROM `cache_logs` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=\'' . sql_escape($usr['userid']) . '\' AND `cache_logs`.`type` IN (1, 7))';
				}
				if(!isset($options['f_inactive'])) $options['f_inactive']='0';
				if($options['f_inactive'] != 0)  $sql_where[] = '`caches`.`status`=1';
 
				if(!isset($options['country'])) $options['country']='';
				if($options['country'] != '')
				{
					$sql_where[] = '`caches`.`country`=\'' . sql_escape($options['country']) . '\'';
				}

				if(!isset($options['cachetype'])) $options['cachetype']='';
				if($options['cachetype'] != '')
				{
					$sql_where[] = '`caches`.`type`=\'' . sql_escape($options['cachetype']) . '\'';
				}

				if(isset($options['cache_attribs']) && count($options['cache_attribs']) > 0)
				{
					for($i=0; $i < count($options['cache_attribs']); $i++)
					{
						$sql_from[] = '`caches_attributes` `a' . ($options['cache_attribs'][$i]+0) . '`';
						$sql_where[] = '`a' . ($options['cache_attribs'][$i]+0) . '`.`cache_id`=`caches`.`cache_id`';
						$sql_where[] = '`a' . ($options['cache_attribs'][$i]+0) . '`.`attrib_id`=' . ($options['cache_attribs'][$i]+0);
					}
				}

				if(isset($options['cache_attribs_not']) && count($options['cache_attribs_not']) > 0)
				{
					for($i=0; $i < count($options['cache_attribs_not']); $i++)
					{
						$sql_where[] = 'NOT EXISTS (SELECT `caches_attributes`.`cache_id` FROM `caches_attributes` WHERE `caches_attributes`.`cache_id`=`caches`.`cache_id` AND `caches_attributes`.`attrib_id`=\'' . sql_escape($options['cache_attribs_not'][$i]) . '\')';
					}
				}

				//do the search
				$join = sizeof($sql_join) ? ' LEFT JOIN ' . implode(' AND ', $sql_join) : '';
				$group = sizeof($sql_group) ? ' GROUP BY ' . implode(', ', $sql_group) : '';
				$having = sizeof($sql_having) ? ' HAVING ' . implode(' AND ', $sql_having) : '';

				$sqlFilter = 'SELECT ' . implode(',', $sql_select) .
						' FROM ' . implode(',', $sql_from) .
						$join .
						' WHERE ' . implode(' AND ', $sql_where) .
						$group .
						$having;
			}
			else
			{
			}
			
			//go to final output preparation
			if (!file_exists($rootpath . 'lib/search.' . mb_strtolower($options['output']) . '.inc.php'))
			{
				tpl_set_var('tplname', $tplname);
				$tplname = 'error';
				tpl_set_var('error_msg', $outputformat_notexist);
			}
			else
			{
				//process and output the search result
				require($rootpath . 'lib/search.' . mb_strtolower($options['output']) . '.inc.php');
				exit;
			}
		}
		else
		{
			$options['show_all_countries'] = isset($_REQUEST['show_all_countries']) ? $_REQUEST['show_all_countries'] : 0;

			if (isset($_REQUEST['show_all_countries_submit']))
			{
				$options['show_all_countries'] = 1;
			}

			//return the search form
			if ($options['expert'] == 1)
			{
				//expert mode
				tpl_set_var('formmethod', 'post');
			}
			else
			{
				outputSearchForm($options);
				exit;
			}
		}
	}
	
	tpl_BuildTemplate();
	
function outputSearchForm($options)
{
	global $stylepath, $usr, $error_plz, $error_locidnocoords, $error_ort, $error_noort, $error_nofulltext;
	global $default_lang, $search_all_countries, $search_all_cachetypes, $cache_attrib_jsarray_line, $cache_attrib_img_line;

	//simple mode (only one easy filter)
	$filters = read_file($stylepath . '/search.simple.tpl.php');
	tpl_set_var('filters', $filters, false);
	tpl_set_var('formmethod', 'get');

	// checkboxen
	if (isset($options['sort']))
		$bBynameChecked = ($options['sort'] == 'byname');
	else
		$bBynameChecked = ($usr['userid'] == 0);
	tpl_set_var('byname_checked', ($bBynameChecked == true) ? ' checked="checked"' : '');

	if (isset($options['sort']))
		$bBydistanceChecked = ($options['sort'] == 'bydistance');
	else
		$bBydistanceChecked = ($usr['userid'] != 0);
	tpl_set_var('bydistance_checked', ($bBydistanceChecked == true) ? ' checked="checked"' : '');

	if (isset($options['sort']))
		$bBycreatedChecked = ($options['sort'] == 'bycreated');
	else
		$bBycreatedChecked = ($usr['userid'] == 0);
	tpl_set_var('bycreated_checked', ($bBycreatedChecked == true) ? ' checked="checked"' : '');
	tpl_set_var('hidopt_sort', $options['sort']);

	tpl_set_var('f_userfound_disabled', ($usr['userid'] == 0) ? ' disabled="disabled"' : '');
	if ($usr['userid'] != 0)
		tpl_set_var('f_userfound_disabled', ($options['f_userfound'] == 1) ? ' checked="checked"' : '');
	tpl_set_var('hidopt_userfound', ($options['f_userfound'] == 1) ? '1' : '0');

	tpl_set_var('f_userowner_disabled', ($usr['userid'] == 0) ? ' disabled="disabled"' : '');
	if ($usr['userid'] != 0)
		tpl_set_var('f_userowner_disabled', ($options['f_userowner'] == 1) ? ' checked="checked"' : '');
	tpl_set_var('hidopt_userowner', ($options['f_userowner'] == 1) ? '1' : '0');

	tpl_set_var('f_inactive_checked', ($options['f_inactive'] == 1) ? ' checked="checked"' : '');
	tpl_set_var('hidopt_inactive', ($options['f_inactive'] == 1) ? '1' : '0');

	if (isset($options['country']))
	{
		tpl_set_var('country', htmlspecialchars($options['country'], ENT_COMPAT, 'UTF-8'));
	}
	else
	{
		tpl_set_var('country', '');
	}

	if (isset($options['cachetype']))
	{
		tpl_set_var('cachetype', htmlspecialchars($options['cachetype'], ENT_COMPAT, 'UTF-8'));
	}
	else
	{
		tpl_set_var('cachetype', '');
	}

	// cachename
	tpl_set_var('cachename', isset($options['cachename']) ? htmlspecialchars($options['cachename'], ENT_COMPAT, 'UTF-8') : '');

	// koordinaten
	if (!isset($options['lat_h']))
	{
		if ($usr !== false)
		{
			$rs = sql('SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`=\'' . sql_escape($usr['userid']) . '\'');
			$record = sql_fetch_array($rs);
			$lon = $record['longitude'];
			$lat = $record['latitude'];
			mysql_free_result($rs);

			if ($lon < 0)
			{
				tpl_set_var('lonE_sel', '');
				tpl_set_var('lonW_sel', ' selected="selected"');
				$lon = -$lon;
			}
			else
			{
				tpl_set_var('lonE_sel', ' selected="selected"');
				tpl_set_var('lonW_sel', '');
			}

			if ($lat < 0)
			{
				tpl_set_var('latN_sel', '');
				tpl_set_var('latS_sel', ' selected="selected"');
				$lat = -$lat;
			}
			else
			{
				tpl_set_var('latN_sel', ' selected="selected"');
				tpl_set_var('latS_sel', '');
			}

			$lon_h = floor($lon);
			$lat_h = floor($lat);
			$lon_min = ($lon - $lon_h) * 60;
			$lat_min = ($lat - $lat_h) * 60;
			
			tpl_set_var('lat_h', $lat_h);
			tpl_set_var('lon_h', $lon_h);
			tpl_set_var('lat_min', sprintf("%02.3f", $lat_min));
			tpl_set_var('lon_min', sprintf("%02.3f", $lon_min));
		}
		else
		{
			tpl_set_var('lat_h', '00');
			tpl_set_var('lon_h', '000');
			tpl_set_var('lat_min', '00.000');
			tpl_set_var('lon_min', '00.000');
		}
	}
	else
	{
		tpl_set_var('lat_h', isset($options['lat_h']) ? $options['lat_h'] : '00');
		tpl_set_var('lon_h', isset($options['lon_h']) ? $options['lon_h'] : '000');
		tpl_set_var('lat_min', isset($options['lat_min']) ? $options['lat_min'] : '00.000');
		tpl_set_var('lon_min', isset($options['lon_min']) ? $options['lon_min'] : '00.000');

		if ($options['lonEW'] == 'W')
		{
			tpl_set_var('lonE_sel', '');
			tpl_set_var('lonW_sel', 'selected="selected"');
		}
		else
		{
			tpl_set_var('lonE_sel', 'selected="selected"');
			tpl_set_var('lonW_sel', '');
		}

		if ($options['latNS'] == 'S')
		{
			tpl_set_var('latS_sel', 'selected="selected"');
			tpl_set_var('latN_sel', '');
		}
		else
		{
			tpl_set_var('latS_sel', '');
			tpl_set_var('latN_sel', 'selected="selected"');
		}
	}
	tpl_set_var('distance', isset($options['distance']) ? $options['distance'] : 150);

	if (!isset($options['unit'])) $options['unit'] = 'km';
	if ($options['unit'] == 'km')
	{
		tpl_set_var('sel_km', 'selected="selected"');
		tpl_set_var('sel_sm', '');
		tpl_set_var('sel_nm', '');
	}
	else if ($options['unit'] == 'sm')
	{
		tpl_set_var('sel_km', '');
		tpl_set_var('sel_sm', 'selected="selected"');
		tpl_set_var('sel_nm', '');
	}
	else if ($options['unit'] == 'nm')
	{
		tpl_set_var('sel_km', '');
		tpl_set_var('sel_sm', '');
		tpl_set_var('sel_nm', 'selected="selected"');
	}
	
	// plz
	tpl_set_var('plz', isset($options['plz']) ? htmlspecialchars($options['plz'], ENT_COMPAT, 'UTF-8') : '');
	tpl_set_var('ort', isset($options['ort']) ? htmlspecialchars($options['ort'], ENT_COMPAT, 'UTF-8') : '');
	
	// owner
	tpl_set_var('owner', isset($options['owner']) ? htmlspecialchars($options['owner'], ENT_COMPAT, 'UTF-8') : '');

	// finder
	tpl_set_var('finder', isset($options['finder']) ? htmlspecialchars($options['finder'], ENT_COMPAT, 'UTF-8') : '');

	//countryoptions
	$countriesoptions = $search_all_countries;

	$rs = sql('SELECT `&1`, `short` FROM `countries` WHERE `short` IN (SELECT DISTINCT `country` FROM `caches`) ORDER BY `sort_' . sql_escape($default_lang) . '` ASC', $default_lang);

	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);

		if ($record['short'] == $options['country'])
			$countriesoptions .= '<option value="' . htmlspecialchars($record['short'], ENT_COMPAT, 'UTF-8') . '" selected="selected">' . htmlspecialchars($record[$default_lang], ENT_COMPAT, 'UTF-8') . '</option>';
		else
			$countriesoptions .= '<option value="' . htmlspecialchars($record['short'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record[$default_lang], ENT_COMPAT, 'UTF-8') . '</option>';

		$countriesoptions .= "\n";
	}

	tpl_set_var('countryoptions', $countriesoptions);

	// Cachetypen
	$cachetype_options = $search_all_cachetypes;

	$rs = sql('SELECT `id`, `&1` FROM `cache_type` ORDER BY `&1`', $default_lang);
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);

		if ($record['id'] == $options['cachetype'])
			$cachetype_options .= '<option value="' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '" selected="selected">' . htmlspecialchars($record[$default_lang], ENT_COMPAT, 'UTF-8') . '</option>';
		else
			$cachetype_options .= '<option value="' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record[$default_lang], ENT_COMPAT, 'UTF-8') . '</option>';

		$cachetype_options .= "\n";
	}

	tpl_set_var('cachetype_options', $cachetype_options);

	// cache-attributes
	$attributes_jsarray = '';
	$attributes_img = '';
	$attributesCat2_img = '';
	$rs = sql("SELECT `id`, `text_long`, `icon_large`, `icon_no`, `icon_undef`, `category` FROM `cache_attrib` WHERE `language`='&1' ORDER BY `id`", $default_lang);
	while ($record = sql_fetch_array($rs))
	{
		// icon specified
		$line = $cache_attrib_jsarray_line;
		$line = mb_ereg_replace('{id}', $record['id'], $line);

		if (array_search($record['id'], $options['cache_attribs']) === false)
		{
			if (array_search($record['id'], $options['cache_attribs_not']) === false)
				$line = mb_ereg_replace('{state}', 0, $line);
			else
				$line = mb_ereg_replace('{state}', 2, $line);
		}
		else
			$line = mb_ereg_replace('{state}', 1, $line);

		$line = mb_ereg_replace('{text_long}', addslashes($record['text_long']), $line);
		$line = mb_ereg_replace('{icon}', $record['icon_large'], $line);
		$line = mb_ereg_replace('{icon_no}', $record['icon_no'], $line);
		$line = mb_ereg_replace('{icon_undef}', $record['icon_undef'], $line);
		$line = mb_ereg_replace('{category}', $record['category'], $line);
		if ($attributes_jsarray != '') $attributes_jsarray .= ",\n";
		$attributes_jsarray .= $line;

		$line = $cache_attrib_img_line;
		$line = mb_ereg_replace('{id}', $record['id'], $line);
		$line = mb_ereg_replace('{text_long}', $record['text_long'], $line);
		if (array_search($record['id'], $options['cache_attribs']) === false)
		{
			if (array_search($record['id'], $options['cache_attribs_not']) === false)
				$line = mb_ereg_replace('{icon}', $record['icon_undef'], $line);
			else
				$line = mb_ereg_replace('{icon}', $record['icon_no'], $line);
		}
		else
			$line = mb_ereg_replace('{icon}', $record['icon_large'], $line);

		if ($record['category'] != 1)
			$attributesCat2_img .= $line;
		else
			$attributes_img .= $line;
	}
	tpl_set_var('cache_attrib_list', $attributes_img);
	tpl_set_var('cache_attribCat2_list', $attributesCat2_img);
	tpl_set_var('attributes_jsarray', $attributes_jsarray);
	tpl_set_var('hidopt_attribs', implode(';', $options['cache_attribs']));
	tpl_set_var('hidopt_attribs_not', implode(';', $options['cache_attribs_not']));

	tpl_set_var('fulltext', '');
	tpl_set_var('ft_name_checked', 'checked="checked"');
	tpl_set_var('ft_desc_checked', '');
	tpl_set_var('ft_logs_checked', '');
	tpl_set_var('ft_pictures_checked', '');

	// fulltext options
	if ($options['searchtype'] == 'byfulltext')
	{
		if (!isset($options['fulltext'])) $options['fulltext'] = '';
		tpl_set_var('fulltext', htmlspecialchars($options['fulltext'], ENT_COMPAT, 'UTF-8'));

		if (isset($options['ft_name']) && $options['ft_name']==1)
			tpl_set_var('ft_name_checked', 'checked="checked"');
		else
			tpl_set_var('ft_name_checked', '');

		if (isset($options['ft_desc']) && $options['ft_desc']==1)
			tpl_set_var('ft_desc_checked', 'checked="checked"');
		else
			tpl_set_var('ft_desc_checked', '');

		if (isset($options['ft_logs']) && $options['ft_logs']==1)
			tpl_set_var('ft_logs_checked', 'checked="checked"');
		else
			tpl_set_var('ft_logs_checked', '');

		if (isset($options['ft_pictures']) && $options['ft_pictures']==1)
			tpl_set_var('ft_pictures_checked', 'checked="checked"');
		else
			tpl_set_var('ft_pictures_checked', '');
	}

	// errormeldungen
	tpl_set_var('ortserror', '');
	if (isset($options['error_plz']))
		tpl_set_var('ortserror', $error_plz);
	else if (isset($options['error_ort']))
		tpl_set_var('ortserror', $error_ort);
	else if (isset($options['error_locidnocoords']))
		tpl_set_var('ortserror', $error_locidnocoords);
	else if (isset($options['error_noort']))
		tpl_set_var('ortserror', $error_noort);

	tpl_set_var('fulltexterror', isset($options['error_nofulltext']) ? $error_nofulltext : '');

	tpl_BuildTemplate();
	exit;
}

function outputUniidSelectionForm($uniSql, $urlparams)
{
	global $tplname, $locline, $stylepath, $bgcolor1, $bgcolor2, $gns_countries;
	global $secondlocationname;

	require_once($stylepath . '/selectlocid.inc.php');

	unset($urlparams['queryid']);
	unset($urlparams['locid']);
	$urlparams['searchto'] = 'search' . $urlparams['searchtype'];
	unset($urlparams['searchtype']);

	$tplname = 'selectlocid';

	// urlparams zusammenbauen
	$urlparamString = '';
	foreach ($urlparams AS $name => $param)
	{
		// workaround for attribs
		if (is_array($param))
		{
			$pnew = '';
			foreach ($param AS $p)
				if ($urlparamString != '')
					$pnew .= ';' . $p;
				else
					$pnew .= $p;

			$param = $pnew;
		}

		if ($urlparamString != '')
			$urlparamString .= '&' . $name . '=' . urlencode($param);
		else
			$urlparamString = $name . '=' . urlencode($param);
	}
	$urlparamString .= '';

	sql('CREATE TEMPORARY TABLE `uniids` ENGINE=MEMORY ' . $uniSql);
	sql('ALTER TABLE `uniids` ADD PRIMARY KEY (`uni_id`)');

	// locidsite
	$locidsite = isset($_REQUEST['locidsite']) ? $_REQUEST['locidsite'] : 0;
	if (!is_numeric($locidsite)) $locidsite = 0;

	$rsCount = sql('SELECT COUNT(*) `count` FROM `uniids`');
	$rCount = sql_fetch_array($rsCount);
	mysql_free_result($rsCount);
	
	tpl_set_var('resultscount', $rCount['count']);
	
	// seitennummern erstellen
	$maxsite = ceil($rCount['count'] / 20) - 1; 
	$pages = '';
	
	if ($locidsite > 0)
		$pages .= '<a href="search.php?' . $urlparamString . '&locidsite=0">&lt;&lt;</a> <a href="search.php?' . $urlparamString . '&locidsite=' . ($locidsite - 1) . '">&lt;</a> ';
	else
		$pages .= '&lt;&lt; &lt; ';
	
	$frompage = $locidsite - 3;
	if ($frompage < 1) $frompage = 1;

	$topage = $frompage + 8;
	if ($topage > $maxsite) $topage = $maxsite + 1;

	for ($i = $frompage; $i <= $topage; $i++)
	{
		if (($locidsite + 1) == $i)
		{
			$pages .= '<b>' . $i . '</b> ';
		}
		else
		{
			$pages .= '<a href="search.php?' . $urlparamString . '&locidsite=' . ($i - 1) . '">' . $i . '</a> ';
		}
	}
	
	if ($locidsite < $maxsite)
		$pages .= '<a href="search.php?' . $urlparamString . '&locidsite=' . ($locidsite + 1) . '">&gt;</a> <a href="search.php?' . $urlparamString . '&locidsite=' . $maxsite . '">&gt;&gt;</a> ';
	else
		$pages .= '&gt; &gt;&gt; ';

	tpl_set_var('pages', $pages);
	
	$rs = sql('SELECT `gns_locations`.`rc` `rc`, `gns_locations`.`cc1` `cc1`, `gns_locations`.`admtxt1` `admtxt1`, `gns_locations`.`admtxt2` `admtxt2`, `gns_locations`.`admtxt3` `admtxt3`, `gns_locations`.`admtxt4` `admtxt4`, `gns_locations`.`uni` `uni_id`, `gns_locations`.`lon` `lon`, `gns_locations`.`lat` `lat`, `gns_locations`.`full_name` `full_name`, `uniids`.`olduni` `olduni` FROM `gns_locations`, `uniids` WHERE `uniids`.`uni_id`=`gns_locations`.`uni` ORDER BY `gns_locations`.`full_name` ASC LIMIT ' . ($locidsite * 20) . ', 20');

	$nr = $locidsite * 20 + 1;
	$locations = '';
	while ($r = sql_fetch_array($rs))
	{
		$thislocation = $locline;
		
		// locationsdings zusammenbauen
		$locString = '';
		if ($r['admtxt1'] != '')
		{
			if ($locString != '') $locString .= ' &gt; ';
			$locString .= htmlspecialchars($r['admtxt1'], ENT_COMPAT, 'UTF-8');
		}
		if ($r['admtxt2'] != '')
		{
			if ($locString != '') $locString .= ' &gt; ';
			$locString .= htmlspecialchars($r['admtxt2'], ENT_COMPAT, 'UTF-8');
		}
/*		if ($r['admtxt3'] != '')
		{
			if ($locString != '') $locString .= ' &gt; ';
			$locString .= htmlspecialchars($r['admtxt3'], ENT_COMPAT, 'UTF-8');
		}
*/		if ($r['admtxt4'] != '')
		{
			if ($locString != '') $locString .= ' &gt; ';
			$locString .= htmlspecialchars($r['admtxt4'], ENT_COMPAT, 'UTF-8');
		}
		
		$thislocation = mb_ereg_replace('{parentlocations}', $locString, $thislocation);

		// koordinaten ermitteln
		$coordString = help_latToDegreeStr($r['lat']) . ' ' . help_lonToDegreeStr($r['lon']);
		$thislocation = mb_ereg_replace('{coords}', htmlspecialchars($coordString, ENT_COMPAT, 'UTF-8'), $thislocation);

		if ($r['olduni'] != 0)
		{
			// der alte name wurde durch den native-wert ersetzt
			$thissecloc = $secondlocationname;
			
			$r['olduni'] = $r['olduni'] + 0;
			$rsSecLoc = sql('SELECT full_name FROM gns_locations WHERE uni=' . $r['olduni']);
			$rSecLoc = sql_fetch_assoc($rsSecLoc);
			$thissecloc = mb_ereg_replace('{secondlocationname}', htmlspecialchars($rSecLoc['full_name'], ENT_COMPAT, 'UTF-8'), $thissecloc);
			mysql_free_result($rsSecLoc);
			
			$thislocation = mb_ereg_replace('{secondlocationname}', $thissecloc, $thislocation);
		}
		else
			$thislocation = mb_ereg_replace('{secondlocationname}', '', $thislocation);

		$thislocation = mb_ereg_replace('{locationname}', htmlspecialchars($r['full_name'], ENT_COMPAT, 'UTF-8'), $thislocation);
		$thislocation = mb_ereg_replace('{urlparams}', $urlparamString . '&locid={locid}', $thislocation);
		$thislocation = mb_ereg_replace('{locid}', urlencode($r['uni_id']), $thislocation);
		$thislocation = mb_ereg_replace('{nr}', $nr, $thislocation);
		
		if ($nr % 2)
			$thislocation = mb_ereg_replace('{bgcolor}', $bgcolor1, $thislocation);
		else
			$thislocation = mb_ereg_replace('{bgcolor}', $bgcolor2, $thislocation);
		
		$nr++;
		$locations .= $thislocation . "\n";
	}
	mysql_free_result($rs);

	tpl_set_var('locations', $locations);
	
	tpl_BuildTemplate();
	exit;
}

function outputLocidSelectionForm($locSql, $urlparams)
{
	global $tplname, $locline, $stylepath, $bgcolor1, $bgcolor2;

	require_once($stylepath . '/selectlocid.inc.php');

	unset($urlparams['queryid']);
	unset($urlparams['locid']);
	$urlparams['searchto'] = 'search' . $urlparams['searchtype'];
	unset($urlparams['searchtype']);

	$tplname = 'selectlocid';

	// urlparams zusammenbauen
	$urlparamString = '';
	foreach ($urlparams AS $name => $param)
	{
		// workaround for attribs
		if (is_array($param))
		{
			$pnew = '';
			foreach ($param AS $p)
				if ($urlparamString != '')
					$pnew .= ';' . $p;
				else
					$pnew .= $p;

			$param = $pnew;
		}

		if ($urlparamString != '')
			$urlparamString .= '&' . $name . '=' . urlencode($param);
		else
			$urlparamString = $name . '=' . urlencode($param);
	}
	$urlparamString .= '&locid={locid}';

	sql('CREATE TEMPORARY TABLE `locids` ENGINE=MEMORY ' . $locSql);
	sql('ALTER TABLE `locids` ADD PRIMARY KEY (`loc_id`)');

	$rs = sql('SELECT `geodb_textdata`.`loc_id` `loc_id`, `geodb_textdata`.`text_val` `text_val` FROM `geodb_textdata`, `locids` WHERE `locids`.`loc_id`=`geodb_textdata`.`loc_id` AND `geodb_textdata`.`text_type`=500100000 ORDER BY `text_val`');

	$nr = 1;
	$locations = '';
	while ($r = sql_fetch_array($rs))
	{
		$thislocation = $locline;
		
		// locationsdings zusammenbauen
		$locString = '';
		$land = landFromLocid($r['loc_id']);
		if ($land != '') $locString .= htmlspecialchars($land, ENT_COMPAT, 'UTF-8');
		
		$rb = regierungsbezirkFromLocid($r['loc_id']);
		if ($rb != '') $locString .= ' &gt; ' . htmlspecialchars($rb, ENT_COMPAT, 'UTF-8');

		$lk = landkreisFromLocid($r['loc_id']);
		if ($lk != '') $locString .= ' &gt; ' . htmlspecialchars($lk, ENT_COMPAT, 'UTF-8');

		$thislocation = mb_ereg_replace('{parentlocations}', $locString, $thislocation);

		// koordinaten ermitteln
		$r['loc_id'] = $r['loc_id'] + 0;
		$rsCoords = sql('SELECT `lon`, `lat` FROM `geodb_coordinates` WHERE loc_id=' . $r['loc_id'] . ' LIMIT 1');
		if ($rCoords = sql_fetch_array($rsCoords))
			$coordString = help_latToDegreeStr($rCoords['lat']) . ' ' . help_lonToDegreeStr($rCoords['lon']);
		else
			$coordString = '[keine Koordinaten vorhanden]';

		$thislocation = mb_ereg_replace('{coords}', htmlspecialchars($coordString, ENT_COMPAT, 'UTF-8'), $thislocation);
		$thislocation = mb_ereg_replace('{locationname}', htmlspecialchars($r['text_val'], ENT_COMPAT, 'UTF-8'), $thislocation);
		$thislocation = mb_ereg_replace('{urlparams}', $urlparamString, $thislocation);
		$thislocation = mb_ereg_replace('{locid}', urlencode($r['loc_id']), $thislocation);
		$thislocation = mb_ereg_replace('{nr}', $nr, $thislocation);
		$thislocation = mb_ereg_replace('{secondlocationname}', '', $thislocation);
		
		if ($nr % 2)
			$thislocation = mb_ereg_replace('{bgcolor}', $bgcolor1, $thislocation);
		else
			$thislocation = mb_ereg_replace('{bgcolor}', $bgcolor2, $thislocation);
		
		$nr++;
		$locations .= $thislocation . "\n";
	}
	tpl_set_var('locations', $locations);
	
	tpl_set_var('resultscount', mysql_num_rows($rs));
	tpl_set_var('pages', '&lt;&lt; &lt; 1 &gt; &gt;&gt;');
	
	tpl_BuildTemplate();
	exit;
}
?>
