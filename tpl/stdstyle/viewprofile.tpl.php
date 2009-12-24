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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="{title_text}" title="{title_text}" />&nbsp;{{user_profile}}: {username} </div>
<div class="content-title-noshade">
<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px;" width="97%">
<tr>
<td rowspan="2" width="64"><img src="tpl/stdstyle/images/blue/profile1.png"  alt="" title="Profile" align="middle"/></td>
<td>{{registered_since_label}} {registered}<br /><br />{{country_label}}: {country}</td>
<td rowspan="2">
<img src="tpl/stdstyle/images/blue/email.png" class="icon32" alt="Email" title="Email" align="middle"/>&nbsp;<a href="mailto.php?userid={userid}">{{email_user}}</a><br />
<img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="Mapa" title="Map" align="middle"/>&nbsp;<a href="cachemap3.php?userid={userid}">{{show_user_map}}</a>
</td>
</tr>
<tr>
<td>{{description_user}}:<br />{description_start}{description}{description_end}</td>
</tr>
<tr>
<td colspan="3"><hr></hr></td>
</tr></table><br />
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

