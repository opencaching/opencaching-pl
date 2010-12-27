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

			$route_rs = sql("SELECT `route_id` ,`description` `desc`, `name`,`radius`  FROM `routes`  WHERE `user_id`=&1  ORDER BY `route_id` DESC",$user_id);
				if (mysql_num_rows($route_rs) != 0)
				{	

//				$routes = '<div class="listitems"><div style="width:60px;"><span style="font-size:12px;font-weight:bold">&nbsp;&nbsp;'.tr('route_name').'</span></div>';
//				$routes .= '<div style="width:250px;"><span style="font-size:12px;font-weight:bold">&nbsp;&nbsp;'.tr('route_desc').'</span></div>';
//				$routes .= '<div style="width:22px;"><span style="font-size:12px;font-weight:bold">&nbsp;&nbsp;'.tr('radius').'</span></div></div>';

				$routes = '<div class="listitems"><table width=100% cellpadding="6" border="0" style="font-size: 12px; line-height: 1.6em; color: #000000; ">';
						$routes .= '<tr><th width="80"><b>'.tr('route_name').'</b></th><th width="450"><b>'.tr('route_desc').'</b></th><th width="22"><b>'.tr('radius').'</b></th><th width="22"><b>'.tr('caches').'</b></th><th width="22"><b>'.tr('edit').'</b></th><th width="22"><b>'.tr('delete').'</b></th></tr></table></div>';
						for ($i = 0; $i < mysql_num_rows($route_rs); $i++)
							{
							
							$routes_record = sql_fetch_array($route_rs);

				$desc = $routes_record['desc'];
				if ($desc != ''){
				require_once($rootpath . 'lib/class.inputfilter.php');
				$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
				$desc = $myFilter->process($desc);
					
				}
//							$routes .= '<div class="listitems"><table width=100% cellpadding="6" border="0" style="font-size: 12px;font-weight: normal; line-height: 1.6em; color: #000000;">';
//							$routes .= '<tr><th width="80" align="left" valign="middle">'.$routes_record['name']. '</th><th width="235" align="left" valign="middle">'.nl2br($desc).'</th><th width="22" align="center" valign="middle"><center>'.$routes_record['radius']. ' km</center></th><th width="22" align="center" valign="middle"><center><a class="links" href="myroutes_search.php?routeid='.$routes_record['route_id'].'"><img src="tpl/stdstyle/images/action/16x16-search.png" alt="" title="Search caches along route" /></a></center></th><th width="22" align="center" valign="middle"><center><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'"><img src="images/actions/edit-16.png" alt="" title="Edit route" /></a></center></th><th width="22" align="center" valign="middle"><center><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'&delete" onclick="return confirm(\'Czy chcesz usunąć tę trase?\');"><img src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title="Usuń" /></a></center></th></tr></table></div>';
							$routes .= '<div class="listitems">';
							$routes .= '<div style="width:80px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">'.$routes_record['name']. '</div><div style="float:left;position:float;border-left: 2px solid lightblue;">&nbsp;</div><div style="width:450px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">'.nl2br($desc).'</div><div style="width:80px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">'.$routes_record['radius']. ' km</div><div style="width:22px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;"><a class="links" href="myroutes_search.php?routeid='.$routes_record['route_id'].'"><img src="tpl/stdstyle/images/action/16x16-search.png" alt="" title="Search caches along route" /></a></div><div style="width:22px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;"><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'"><img src="images/actions/edit-16.png" alt="" title="Edit route" /></a></center></div><div style="width:22px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;"><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'&delete" onclick="return confirm(\'Czy chcesz usunąć tę trase?\');"><img src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title="Usuń" /></a></div></div>';

							}
							$routes .= '<br/>';


						tpl_set_var('content', $routes);
						mysql_free_result($route_rs);
						
				} else { tpl_set_var('content', "<div class=\"listitems\"><br/><center><span style=\"font-size:140%;font-weight:bold \">&nbsp;&nbsp;".tr('no_routes')."</span><br/><br/></center></div>");}	
			
			
			
			
			
		
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
