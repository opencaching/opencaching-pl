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
	   countrylist          list of countries
	   latNsel
	   latSsel
	   lonEsel
	   lonWsel
	   lat_h
	   lat_min
	   lon_h
	   lon_min
	   pmr_sel
	   registered_since
	   username_message
	   lat_message
	   lon_message
	   reset
	   change_data
	   allcountriesbutton
	   show_all_countries

 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" border="0" width="32" height="32" alt="{change_account_data}" title="{change_account_data}" align="middle">&nbsp;{change_account_data}</div>
<div class="notice">
{gray_field_is_hidden}
</div>
<div class="buffer"></div>
<p class="content-title-noshade-size2">{data_in_profile}:</p>
<div class="buffer"></div>
<table class="table">
<form name="change" action="myprofile.php?action=change" method="post" enctype="application/x-www-form-urlencoded"  style="display: inline;">
<input type="hidden" name="show_all_countries" value="{show_all_countries}">
	<colgroup>
		<col width="150">
		<col>
	</colgroup>
	<tr>
		<td class="content-title-noshade">{username_label}:</td>
		<td class="txt-grey07">
      <?
      if ($usr['admin']) {
      ?>
			<input type="text" name="username" maxlength="60" value="{username}" class="input200"/>
			{username_message}
      <?
      } else {
      ?>
      <input type="hidden" name="username" maxlength="60" value="{username}"/>
      {username}
      <?
      }
      ?>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade txt-grey07">{email_address}:</font></td>
		<td class="txt-grey07">{email}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{country_label}:</td>
		<td>
			<select name="country" class="input200">
				{countrylist}
			</select>
			{allcountriesbutton}
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade txt-grey07" valign="top">{coordinates}:</td>
		<td class="txt-grey07" valign="top">
			<select name="latNS" class="input40">
				<option value="N"{latNsel}>N</option>
				<option value="S"{latSsel}>S</option>
			</select>
			&nbsp;<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />
			°&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input50" />&nbsp;'&nbsp;
			{lat_message}
			<br/>
			<select name="lonEW" class="input40">
				<option value="E"{lonEsel}>E</option>
				<option value="W"{lonWsel}>W</option>
			</select>
			&nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />
			°&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input50" />&nbsp;'&nbsp;
			{lon_message}
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade txt-grey07" valign="top">{notification}:</td>
		<td class="txt-grey07" valign="top">
			{notify_new_caches_radius}&nbsp;
			<input type="text" name="notify_radius" maxlength="3" value="{notify_radius}" class="input30" />
			&nbsp;km {from_home_coords}.
			&nbsp;
			<div class="errormsg">{notify_message}</div><br/>
			<div class="notice" style="width:500px;height:44px;">{radius_hint}</div>
		</td>
	</tr>
	<tr>
		<td class="content-title-noshade txt-grey07" valign="top">{bulletin}:</td>
		<td class="txt-grey07" valign="middle">
			<input type="checkbox" name="bulletin" id="bulletin" value="1" {is_checked} class="checkbox" />
			<label for="bulletin">{get_bulletin}</label>&nbsp;
			&nbsp;<br />
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade" valign="top">{my_description}:</td>
		<td valign="top">
			<textarea name="description" cols="50" rows="5">{description}</textarea>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade txt-grey07" valign="top">{other}:</td>
		<td class="txt-grey07" valign="top">
			<input type="checkbox" name="using_permanent_login" value="1"{permanent_login_sel} id="l_using_permanent_login" class="checkbox" />
			<label for="l_using_permanent_login">{no_auto_logout}</label><br/>
			<div class="notice" style="width:500px;height:44px;">{no_auto_logout_warning}</div>
		</td>
	</tr>
	<tr>
		<td valign="top">&nbsp;</td>
		<td valign="top">
			<span class="txt-grey07"><input type="checkbox" name="no_htmledit" value="1"{no_htmledit_sel} id="l_no_htmledit" class="checkbox" /> <label for="l_no_htmledit">{hide_html_editor}</label></span><br/>
			<input type="checkbox" name="using_pmr" value="1"{pmr_sel} id="l_using_pmr" class="checkbox" /> <label for="l_using_pmr">{pmr_message}</label>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade txt-grey07" valign="top">{ozi_path_label}:</td>
		<td class="txt-grey07" valign="top"><input type="text" size="46" name="ozi_path" value="{ozi_path}"><br/>
		<div class="notice" style="width:500px;height:44px;">{ozi_path_info}</div>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{registered_since_label}:</td>
		<td>{registered_since}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			<input type="reset" name="reset" value="{reset}" style="width:120px"/>&nbsp;&nbsp;
			<input type="submit" name="submit" value="{change}" style="width:120px"/>
		</td>
	</tr>
</form>
</table>