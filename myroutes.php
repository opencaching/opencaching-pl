<?php
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

	 display all watches of this user

 ****************************************************************************/

	//prepare the templates and include all neccessary
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
			$tplname = 'myroutes';
			$user_id = $usr['userid'];

			$route_rs = sql("SELECT `route_id` ,`description` `desc`, `name`  FROM `routes`  WHERE `user_id`=&1  ORDER BY `route_id` DESC",$user_id);
				if (mysql_num_rows($route_rs) != 0)
				{	

				
						$routes = '<table id="gradient" cellpadding="5" width="97%" border="1" style="border-collapse: collapse; font-size: 11px; line-height: 1.6em; color: #000000; ">';
						$routes .= '<tr><th width="60"><b>'.tr('routes_name').'</th><th><b>'.tr('route_desc').'</b></th><th width="22"><b>'.tr('caches').'</b></th><th width="22"><b>'.tr('edit').'</b></th><th width="22"><b>'.tr('delete').'</b></th></tr>';
						for ($i = 0; $i < mysql_num_rows($route_rs); $i++)
							{
							
							$routes_record = sql_fetch_array($route_rs);

				$desc = $routes_record['desc'];
				if ($desc != ''){
				require_once($rootpath . 'lib/class.inputfilter.php');
				$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
				$desc = $myFilter->process($desc);
					
				}

							$routes .= '<tr><td align="center" valign="middle"><center></center>'.$routes_record['name']. '</td><td>'.nl2br($desc).'</td><td align="center" valign="middle"><center><a class="links" href="myroutes_search.php?routeid='.$routes_record['route_id'].'"><img src="tpl/stdstyle/images/action/16x16-search.png" alt="" title="Search caches along route" /></a></center></td><td align="center" valign="middle"><center><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'"><img src="images/actions/edit-16.png" alt="" title="Edit route" /></a></center></td><td align="center" valign="middle"><center><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'&delete" onclick="return confirm(\'Czy chcesz usunąć tę trase?\');"><img src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title="Usuń" /></a></center></td></tr>';
							}
							$routes .= '</table><br /><br />';


						tpl_set_var('content', $routes);
						mysql_free_result($route_rs);
						
				} else { tpl_set_var('content', "<div class=\"searchdiv\"><br/><span style=\"font-size:130%;font-weight:bold \">&nbsp;&nbsp;".tr('no_routes')."</span><br/><br/></div>");}	
			
			
			
			
			
		
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
