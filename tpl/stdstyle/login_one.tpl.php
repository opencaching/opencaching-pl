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
	   
   Unicode Reminder ăĄă˘
                                      				                                
	 login page
	
 ****************************************************************************/
?>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="" title="Login" align="middle"/>&nbsp;Login</div>
	{message_start}
	{message}
	{message_end}
<div class="searchdiv">
<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login_form" dir="ltr" style="display: inline;">
<input type="hidden" name="target" value="{target}" />
<input type="hidden" name="action" value="login" />
<table class="table">
	<tr><td colspan="2">&nbsp;</td><td><b>My account is on the following website:</b></td></tr>
	<tr>
		<td class="content-title-noshade">{{username_label}}:</td>
		<td><input name="email" maxlength="80" type="text" value="{username}" class="input150" /></td>
		<td>
	<select name="gcnode" class="input150">
					<option value="2" selected="selected">OpenCaching.PL</option>
					<option value="1">OpenCaching.DE</option>
					<option value="1">OpenCaching.ES</option>
					<option value="1">OpenCaching.IT</option>
					<option value="3">OpenCaching.CZ</option>
					<option value="5">OpenCaching.NL</option>
					<option value="6">OpenCaching.UK</option>
					<option value="7">OpenCaching.SE/NO</option>
					<option value="9">OpenCaching.US</option>
					<option value="11">OpenCaching.JP</option>
					<option value="12">OpenCaching.org.RU</option>
					<option value="9901">Geocaching.com.au</option>
				</select>
		
		</td>
	</tr>
	<tr>
		<td class="content-title-noshade">{{password}}:</td>
		<td><input name="password" maxlength="60" type="password" value="" class="input150" /></td>
	</tr>
	<tr>
	<td colspan="1">&nbsp;</td>
	<td>
	<button type="submit" name="LogMeIn" value="Login" style="font-size:14px;width:120px"><b>Login</b></button>
	</td>
	</tr>
</table>


</form>
<br/>
<br/>
<p class="content-title-noshade">{{not_registered}}<br />
{{forgotten_your_password}}</p>
</div>
