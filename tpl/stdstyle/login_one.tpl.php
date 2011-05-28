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
<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login_form" dir="ltr" style="display: inline;">
<input type="hidden" name="target" value="{target}" />
<input type="hidden" name="action" value="login" />
<table class="table">
	<colgroup>
		<col width="150" />
		<col />
	</colgroup>
	<tr>
		<td class="content-title-noshade">{{username_label}}:</td>
		<td><input name="email" maxlength="80" type="text" value="{username}" class="input150" /></td>
		<td>
	<select name="gcnode" class="input40">
					<option value="2" selected="selected">OpenCaching.PL</option>
					<option value="3">OpenCaching.DE</option>
					<option value="4">OpenCaching.ES</option>
					<option value="5">OpenCaching.IT</option>
					<option value="6">OpenCaching.CZ</option>
					<option value="7">OpenCaching.SE</option>
					<option value="8">OpenCaching.UK</option>
					<option value="9">OpenCaching.US</option>
					<option value="10">Geocaching.com.au</option>
				</select>
		
		/td>
	</tr>
	<tr>
		<td class="content-title-noshade">{{password}}:</td>
		<td><input name="password" maxlength="60" type="password" value="" class="input150" /></td>
	</tr>
</table>
<button type="submit" name="LogMeIn" value="Login" style="font-size:14px;width:130px"><b>Login</b></button>

</form>
<p class="content-title-noshade">{{not_registered}}<br />
{{forgotten_your_password}}</p>
