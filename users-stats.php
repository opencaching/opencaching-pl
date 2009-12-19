<?php
//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
		global $stat_menu;
	
	//Preprocessing
	if ($error == false)
	{
		// check for old-style parameters
		if (isset($_REQUEST['userid']))
		{
			$user_id = $_REQUEST['userid'];
		
		}
				$tplname = 'users-stats';
				$stat_menu = array(
					'title' => tr('Statystyka'),
					'menustring' => tr('Statystyka'),
					'siteid' => 'statlisting',
					'navicolor' => '#E8DDE4',
					'visible' => false,
					'filename' => 'users-stats.php?userid='.$user_id,
					'submenu' => array(
									array(
							'title' => tr('Statystyka ogólna'),
							'menustring' => tr('Statystka ogólna'),
							'visible' => true,
							'filename' => 'users-stats.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'stat_general'
						),
						array(
							'title' => tr('Skrzynki założone'),
							'menustring' => tr('Skrzynki założone'),
							'visible' => true,
							'filename' => 'ustatg1?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'stat_create'
						),
						array(
							'title' => tr('Skrzynki znalezione'),
							'menustring' => tr('Skrzynki znalezione'),
							'visible' => true,
							'filename' => 'ustatg2?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'stat_find'
						)
					)
				);

	$content="";
	// calculate diif days between date of register on OC  to current date
	  $rdd=sql("select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=&1 ",$user_id);
	  $ddays = mysql_fetch_array($rdd);
	  mysql_free_result($rdd);
	  // calculate days caching
	 // sql ("SELECT COUNT(*) FROM cache_logs WHERE type=1 AND user_id=&1 GROUP BY GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)",$user_id);


			$content .= '<table style="border-collapse: collapse" border="0" width="750"><tr><td width="150" bgcolor="#C6E2FF">Statystyka ogolna</b></td> <td width="200"><b><a href="ustatsg1.php?userid=' . $user_id . '">Wykresy skrzynek zalozonych</a></b></td> <td width="200"><b><a href="ustatsg2.php?userid=' . $user_id . '"> Wykresy skrzynek znalezionych</a></b></td><td width="300" ></td> </tr></table><br /><br />';
			$content .='<br><br><table style="border-collapse: collapse" border="0" width="500"><tr><td colspan="4" bgcolor="#C6E2FF"><b>Ogólna statystyka liczbowa</b></td></tr></table><br /><br />';	

	$rsGeneralStat =sql("SELECT hidden_count, founds_count, log_notes_count, username FROM `user` WHERE user_id=&1 ",$user_id);
	if ($rsGeneralStat !== false){
			$user_record = sql_fetch_array($rsGeneralStat);

			tpl_set_var('username',$user_record['username']);

			$content .= '<table style="border-collapse: collapse" border="1" width="500"><tr><td colspan="4" bgcolor="#C6E2FF"><b>Statystyka skrzynek założonych</b></td></tr><tr><td> Total created caches</td> <td>' . $user_record['hidden_count'] . '</td> <td> Create Rate </td> <td> .... </td></tr><tr><td> Avg cache/day </td> <td> ....</td> <td>First Cache created</td><td>.... </td></tr><tr><td> Most cache/day </td> <td>....</td> <td>Latest Cache created</td><td>....</td></tr></table><br /><br />';	

		}

		$content .= '<br><br><table style="border-collapse: collapse" border="1" width="500"><tr><td colspan="4" bgcolor="#C6E2FF"><b>Statystka skrzynek znalezionych</b></td></tr><tr><td> Total Found it Caches</td> <td>' . $user_record['founds_count'] . '</td> <td> Find Rate </td> <td> &nbsp;.... </td></tr><tr><td> Avg cache/day </td> <td> &nbsp;....</td> <td>  First Found it Cache  </td> <td>&nbsp;....</td></tr><tr><td> Most cache/day</td> <td> &nbsp;....</td> <td> Latest Found it Cache</td><td>&nbsp;....</td></tr></table><br /><br />';	


			$content .='<br><br><table style="border-collapse: collapse" border="0" width="500"><tr><td colspan="4" bgcolor="#C6E2FF"><b>Odwiedzone województwa Polski podczas poszukiwan (w opracowaniu) </b></td></tr><tr><td width="500"><center><img src=images/PLmapa250.jpg alt="" /></center></td</tr></table>';
			mysql_free_result($rsGeneralStat);
			tpl_set_var('content',$content);
	$tplname = 'users-stats';
}
	tpl_BuildTemplate();
?>
