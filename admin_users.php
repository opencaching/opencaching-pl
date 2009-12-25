<?php
//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');

	if( $usr['admin'] )
	{

		if (isset($_REQUEST['userid']))
		{
			$user_id = $_REQUEST['userid'];
			tpl_set_var('userid',$user_id);		
		}
		
		if( $_GET['stat_ban'] == 1 && $usr['admin'] )
		{
			$sql = "UPDATE user SET stat_ban = 1 - stat_ban WHERE user_id = ".intval($user_id);
			mysql_query($sql);
		}
		if($_GET['hide_flag'] == 1 && $usr['admin'] )
		{
			$sql = "UPDATE user SET hide_flag = 1 - hide_flag WHERE user_id = ".intval($user_id);
			mysql_query($sql);
		}		
		if($_GET['is_active_flag'] == 1 && $usr['admin'] )
		{
			$sql = "UPDATE user SET is_active_flag = 1 - is_active_flag WHERE user_id = ".intval($user_id);
			mysql_query($sql);
		}	
			
			if( $usr['userid']==$super_admin_id )
			{
				tpl_set_var('remove_all_logs', '<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<a href="removelog.php?userid='.$user_id.'"><font color="#ff0000">'.tr('Usuń wszystkie logi tego użytkownika').'</font></a>&nbsp;<img src="'.$stylepath.'/images/blue/atten-red.png" align="top" alt="" /></p>');
			}
			else
				tpl_set_var('remove_all_logs', '');

			$rsuser =sql("SELECT hidden_count, founds_count, log_notes_count, notfounds_count, 
								username, date_created,description, email,is_active_flag,
								stat_ban,activation_code,hide_flag,countries.pl country
								FROM `user` LEFT JOIN countries ON (user.country=countries.short) WHERE user_id=&1 ",$user_id);

			$record = sql_fetch_array($rsuser);
			if ( !$record['activation_code']) {
						tpl_set_var('activation_codes',tr('account_is_actived'));			
					} else {
					tpl_set_var('activation_codes',$record['activation_code']);
							}
			tpl_set_var('username',$record['username']);
			tpl_set_var('country', htmlspecialchars($record['country'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('registered', strftime($dateformat, strtotime($record['date_created'])));
			tpl_set_var('email',strip_tags($record['email']));
			tpl_set_var('description',nl2br($record['description']));		


			if( $record['is_active_flag'] )
					tpl_set_var('is_active_flags', '&nbsp;<a href="admin_users.php?userid='.$user_id.'&amp;is_active_flag=1"><font color="#ff0000">'.tr('lock').' '.tr('user_account').'</font></a>&nbsp;<img src="'.$stylepath.'/images/blue/atten-red.png" align="top" alt="" />');
				else
					tpl_set_var('is_active_flags', '&nbsp;<a href="admin_users.php?userid='.$user_id.'&amp;is_active_flag=1"><font color="#228b22">'.tr('unlock').' '.tr('user_account').'</font></a>&nbsp;<img src="'.$stylepath.'/images/blue/atten-green.png" align="bottom" alt="" />');


			if( !$record['stat_ban'] )
					tpl_set_var('stat_ban', '&nbsp;<a href="admin_users.php?userid='.$user_id.'&amp;stat_ban=1"><font color="#ff0000">'.tr('lock').' '.tr('user_stats').'</font></a>&nbsp;<img src="'.$stylepath.'/images/blue/atten-red.png" align="top" alt="" />');
				else
					tpl_set_var('stat_ban', '&nbsp;<a href="admin_users.php?userid='.$user_id.'&amp;stat_ban=1"><font color="#228b22">'.tr('unlock').' '.tr('user_stats').'</font></a>&nbsp;<img src="'.$stylepath.'/images/blue/atten-green.png" align="top" alt="" />');
	
//			if( $usr['admin'] && $block_new_user_caches ) {
//				$rs = sql("SELECT `user_id` as data FROM `user` WHERE `date_created` < CURDATE() + INTERVAL -1 MONTH AND `user_id` =  ". sql_escape($user_id)."");
//				$data = mysql_num_rows($rs);
//			
//				$rs = sql("SELECT COUNT(`cache_logs`.`id`) as ilosc FROM `cache_logs`, `caches` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`type` = 1 AND `caches`.`cache_id` = `cache_logs`.`cache_id` AND `caches`.`type` NOT IN(4,5) AND `cache_logs`.`user_id` = ". sql_escape($user_id)."");
//				$record = sql_fetch_array($rs);
//				$ilosc = $record['ilosc'];

				// Umożliwienie zakładania skrzynek dla nowych użytkowników			
//				if (($data == 0) || ($ilosc < 5)) {
					
//					$rs = sql("SELECT `hide_flag` as hide_flag FROM `user` WHERE `user_id` =  ". sql_escape($user_id)."");
//					$record = sql_fetch_array($rs);
					$hide_flag = $record['hide_flag'];
					
					if ($hide_flag == 1) {
						tpl_set_var('hide_flag', '<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<a href="admin_users.php?userid='.$user_id.'&amp;hide_flag=1"><font color="#228b22">'.tr('Dodaj możliwość zakładania skrzynek dla użytkownika').'</font></a>&nbsp;<img src="'.$stylepath.'/images/blue/atten-green.png" align="top" alt="" /></p>');
					} else {
						tpl_set_var('hide_flag', '<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<a href="admin_users.php?userid='.$user_id.'&amp;hide_flag=1"><font color="#ff0000">'.tr('Usuń możliwość zakładania skrzynek dla użytkownika').'</font></a>&nbsp;<img src="'.$stylepath.'/images/blue/atten-red.png" align="top" alt="" /></p>');
					}
	
//				} else {
//					tpl_set_var('hide_flag', '');
//				}
//	
//			} else {
//				tpl_set_var('hide_flag', '');
//			}
					
					
					
			$tplname = 'admin_users';
			tpl_BuildTemplate();					
	}
?>
