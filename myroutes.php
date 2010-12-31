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

			$route_rs = sql("SELECT `route_id` ,`description` `desc`, `name`,`radius`,`length`  FROM `routes`  WHERE `user_id`=&1  ORDER BY `route_id` DESC",$user_id);
				if (mysql_num_rows($route_rs) != 0)
				{	

							$routes .= '<div class="headitems">';
							$routes .= '<div style="width:80px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">'.tr('route_name'). '</div><div class="ver">&nbsp;</div><div style="width:295px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">&nbsp;'.tr('route_desc').'</div><div class="ver">&nbsp;</div><div style="width:60px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">&nbsp;'.tr('radius').'</div><div class="ver">&nbsp;</div><div style="width:60px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">&nbsp;'.tr('length').'</div><div class="ver">&nbsp;</div><div style="width:70px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">&nbsp;'.tr('cache').'</div><div class="ver">&nbsp;</div><div style="width:50px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">'.tr('edit').'</div><div class="ver">&nbsp;</div><div style="width:20px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">&nbsp;'.tr('delete').'</div></div>';


						for ($i = 0; $i < mysql_num_rows($route_rs); $i++)
							{
							
							$routes_record = sql_fetch_array($route_rs);

				$desc = $routes_record['desc'];
				if ($desc != ''){
				require_once($rootpath . 'lib/class.inputfilter.php');
				$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
				$desc = $myFilter->process($desc);
					
				}
							$routes .= '<div class="listitems">';
							$routes .= '<div style="margin-left:5px;width:75px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">'.$routes_record['name']. '</div><div class="ver35">&nbsp;</div><div style="width:295px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;">'.nl2br($desc).'</div><div class="ver35">&nbsp;</div><div style="width:60px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;text-align:center;">'.$routes_record['radius']. ' km</div><div class="ver35">&nbsp;</div><div style="width:60px;float:left;font-size:12px;font-weight:bold;line-height:1.6em;text-align:center;">'.round($routes_record['length'],0). ' km</div><div class="ver35">&nbsp;</div><div style="width:70px;float:left;text-align:center;"><a class="links" href="myroutes_search.php?routeid='.$routes_record['route_id'].'"><img src="tpl/stdstyle/images/action/16x16-search.png" alt="" title="Search caches along route" /></a></div><div class="ver35">&nbsp;</div><div style="width:50px;float:left;text-align:center;"><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'"><img src="images/actions/edit-16.png" alt="" title="Edit route" /></a></div><div class="ver35">&nbsp;</div><div style="width:20px;float:left;text-align:center;"><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'&delete" onclick="return confirm(\'Czy chcesz usunąć tę trase?\');"><img src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title="Usuń" /></a></div></div>';

							}
							$routes .= '';


						tpl_set_var('content', $routes);
						mysql_free_result($route_rs);
						
				} else { tpl_set_var('content', "<div class=\"listitems\"><br/><center><span style=\"font-size:140%;font-weight:bold \">&nbsp;&nbsp;".tr('no_routes')."</span><br/><br/></center></div>");}	
			
			
			
			
			
		
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
