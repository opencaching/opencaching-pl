<?php
/***************************************************************************
											./tpl/stdstyle/register.tpl.php
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
                                   				                                
	 register page
	
	 template replacement(s):
	    register                submit button caption
	    reset                   reset button caption
	    tos_message             terms of service message	
	    all_countries_submit    submission button to display all countries
	    countries_list          list of countries for <select> tag
	    email_message           email message
	    email                   entered email
	    username_message
	    username
	    show_all_countries      reminder to show all countries
	 
 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" border="0" align="middle" width="32" height="32" alt="" title="Twoje konto" align="middle">&nbsp;{register_new}</div>
<table>
<form name="register" action="register.php" method="post" enctype="application/x-www-form-urlencoded" style="display: inline;">
<input type="hidden" name="allcountries" value="{show_all_countries}" />
	<colgroup>
		<col width="150">
		<col>
	</colgroup>
	<tr>
		<td colspan="2" class="help">
			<img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwaga" title="Uwaga" align="middle">
			{register_msg1}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td width="145" valign="top">{user}:</td>
		<td valign="top"><input type="text" name="username" maxlength="60" value="{username}" class="input200" />* {username_message}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td valign="top">{country_label}:</td>
		<td valign="top">
			<select name="country" class="input200" >
				{countries_list}
			</select>&nbsp;{all_countries_submit}</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td width="145" valign="top">{email_address}:</td>
		<td valign="top"><input type="text" name="email" maxlength="80" value="{email}" class="input200" />*&nbsp;{email_message}
			</td>
	</tr>
	<tr>
		<td width="145" valign="top">{password}:</td>
		<td valign="top"><input type="password" name="password1" maxlength="80" value="" class="input200" />*&nbsp;{password_message}
		</td>
	</tr>
	<tr>
		<td width="145" valign="top">{password_confirm}:</td>
		<td valign="top"><input type="password" name="password2" maxlength="80" value="" class="input200" />*
		</td>
	</tr>
	<tr>
		<td width="145" valign="top">&nbsp;</td>
		<td valign="top">
			{register_msg2}</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td colspan="2" class="help">
			<img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwaga" title="Uwaga" align="middle">
			{register_msg3}</B>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td colspan="2">
		    <input type="checkbox" name="TOS" value="ON" style="border:0;" />{register_msg4}
			{tos_message}
		</td>
	</tr>

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td class="header-small" colspan="2">
			<input type="reset" name="reset" value="Reset" class="formbuttons"/>&nbsp;&nbsp;
			<input type="submit" name="submit" value="{registration}" class="formbuttons"/>
		</td>
	</tr>
</table>
</form>

