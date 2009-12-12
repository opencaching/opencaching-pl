<?php
/***************************************************************************
											./tpl/stdstyle/myprofile.tpl.php
															-------------------
		begin                : Mon June 14 2004
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

   Unicode Reminder ??

	 the users profile page

	 template replacement(s):

	   username
	   email
	   country
	   coords
	   user_options
	   registered_since

 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" border="0" width="32" height="32" alt="{{your_data}}" title="{{your_data}}" align="middle" />&nbsp;{{your_data}}</div>
<div class="notice">
{{gray_field_is_hidden}}
</div>
<div class="buffer"></div>
<p class="content-title-noshade-size2">{{data_in_profile}}:</p>
<div class="buffer"></div>
<table class="table">
	<colgroup>
		<col width="150" />
		<col/>
	</colgroup>
	<tr>
		<td class="content-title-noshade">{{username_label}}:</td>
		<td>{username}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{{email_address}}:</td>
		<td class="txt-grey07">{email}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{{country_label}}:</td>
		<td>{country}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade" valign="top">{{coordinates}}:</td>
		<td class="txt-grey07">{coords}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade" valign="top">{{notification}}:</td>
		<td class="txt-grey07">{notify}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade" valign="top">{{bulletin}}:</td>
		<td class="txt-grey07" valign="middle">{{bulletin_label}}</td>
	</tr>
	<tr>
		<td class="content-title-noshade" valign="top">{{my_description}}:</td>
		<td class="txt-grey07">{{description}}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade" valign="top">{{other}}:</td>
		<td class="txt-grey07" valign="top">{user_options}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade" valign="top">{{ozi_path_label}}:</td>
		<td class="txt-grey07" valign="top">{ozi_path}</td>
	</tr>
	<tr>
		<td class="buffer" colspan="2">
			<div class="notice" style="height:44px;">{{ozi_path_info}}</div>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{{registered_since_label}}:</td>
		<td>{registered_since}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade" style="vertical-align:top;">{{statpic_label}}:</td>
		<td><img src="statpics/{userid}.jpg" align="middle" alt="" /></td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade" style="vertical-align:top;">{{html_statpic_link}}:</td>
		<td class="txt-grey07">&lt;img src="{statlink}" alt="Opencaching PL - Statystyka dla {username_html}" title="Opencaching PL - Statystyka dla {username_html}" /></td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade" style="vertical-align:top;">{{bbcode_statpic}}:</td>
		<td class="txt-grey07">[url={profileurl}][img]{statlink}[/img][/url]</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
</table>
