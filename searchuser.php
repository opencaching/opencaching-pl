<?php
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
		} else
		{
			$tplname = 'searchuser';
			$options['username'] = isset($_POST['username']) ? $_POST['username'] : '';
			if(!isset($options['username'])) 
			{
				$options['username']= '';
			}
			if ($options['username'] != '') 
			{
				//$query = "SELECT user_id, username FROM user WHERE username LIKE '%" . sql_escape($options['username']) . "%' ORDER BY username ASC";;
				//$rs = sql($query);
				
				$query = "SELECT user_id, username, date_created FROM user WHERE username LIKE :username ORDER BY username ASC";;
				$params = array(
						"username" =>
						array(
								"value" => '%'.sql_escape($options['username']).'%',
								"data_type" =>"string"
					),						
				) ;
				
				$dbc = new dataBase();
				$dbc->paramQuery($query, $params);

				$bgcolor1 = '#eeeeee';
				$bgcolor2 = '#ffffff';
				$line = '<tr bgcolor={bgcolor}><td><a href=viewprofile.php?userid={user_id}>{username}</a></td><td>&nbsp;</td><td nowrap style="text-align:center;">{date_created}</td><td nowrap style="text-align:center;"></td></tr>';
				$lines = "";
				
				$ilosc = $dbc->rowCount();
				//if (mysql_num_rows($rs) != 0)
				if ( $ilosc != 0) 
				{
					if ( $ilosc == 1)
					{
						$record = $dbc->dbResultFetch();
						tpl_redirect("viewprofile.php?userid=".$record['user_id']);						
					}
					else
					{			
						//$ilosc=mysql_num_rows($rs);
						$linia="Znaleziono $ilosc kont(a)</BR><UL>";
						//while ($record = sql_fetch_array($rs))
						$i = 0;
						while( $record = $dbc->dbResultFetch() )	
						{		
							$tmp_line = $line;
							$tmp_line = mb_ereg_replace('{bgcolor}',($i % 2 == 0) ? $bgcolor1 : $bgcolor2, $tmp_line);
							$tmp_line = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $tmp_line);
							$tmp_line = mb_ereg_replace('{user_id}', htmlspecialchars($record['user_id'], ENT_COMPAT, 'UTF-8'), $tmp_line);
							$tmp_line = mb_ereg_replace('{date_created}', htmlspecialchars(fixPlMonth(strftime($dateformat, strtotime($record['date_created']))), ENT_COMPAT, 'UTF-8'), $tmp_line);
							
							//$linia.= "<LI><A HREF='viewprofile.php?userid=" . htmlspecialchars($record['user_id'], ENT_COMPAT, 'UTF-8') . "'>" . htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8') . "</A></LI>";
							
							$lines .= $tmp_line . "\n";
							$i++;
						};
						
						tpl_set_var('lines', $lines);
						tpl_set_var('username', '');
						tpl_set_var('not_found', '');
						//tpl_set_var('not_found', $linia);
					}
				} else { // Nie znaleziono użytkownika
					tpl_set_var('username', $options['username']);
					tpl_set_var('not_found', '<b>Nie znaleziono użytkownika: '. $options['username'] .'</b><br/><br/>');
					tpl_set_var('lines', '');
				}
				//mysql_free_result($rs);
				unset( $dbc );
			} else {
				tpl_set_var('username', '');
				tpl_set_var('not_found', '');
				tpl_set_var('lines', '');
			}
		}
	}
	tpl_BuildTemplate();
?>
