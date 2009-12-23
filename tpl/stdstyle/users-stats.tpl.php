<?php
/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/
?>
<!-- 	CONTENT -->
<div class="content2-container">
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{title_text}" title="{title_text}" />&nbsp;{{statistics_users}}: {username} </div>
<div class="content-title-noshade">
<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px;" width="97%">
<tr><td ><img src="tpl/stdstyle/images/blue/profile1.png"  alt="" title="Profile" align="middle"/></td>
<td>{username} {{registered_since_label}} {registered}<br>{{description_user}}:<br>{description_start}{description}{description_end}</td>
<td>
	<img src="tpl/stdstyle/images/blue/email.png" class="icon32" alt="Email" title="Email" align="middle"/>&nbsp;<a href="mailto.php?userid={userid}">{{email_user}}</a><br/>
	<img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="Mapa" title="Map" align="middle"/>&nbsp;<a href="cachemap2.php?userid={userid}">{{show_user_map}}</a>
	{description_start}{description}{description_end}

</td>
</tr>
<tr>
<td colspan="3"><hr></hr></td>
</tr>
</table>
</div>

<div class="nav4">
<?


					// statlisting
					$statidx = mnu_MainMenuIndexFromPageId($menu, "statlisting");
					if( $menu[$statidx]['title'] != '' )
					{
						echo '<ul id="statmenu">';
						$menu[$statidx]['visible'] = false;
						echo '<li class="title" ';
						echo '>'.$menu[$statidx]["title"].'</li>';
						mnu_EchoSubMenu($menu[$statidx]['submenu'], $tplname, 1, false);
						echo '</ul>';
					}
					//end statlisting
?>
				</div>

{content}
</div>

