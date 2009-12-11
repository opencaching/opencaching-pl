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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" alt="" title="{{your_account}}" class="icon32"/>&nbsp;{{register_new}}</div>

<form name="register" action="register.php" method="post" enctype="application/x-www-form-urlencoded" style="display: inline;" id="register">
<input type="hidden" name="allcountries" value="{show_all_countries}" />

	<div>
			<img src="tpl/stdstyle/images/misc/hint.gif" alt="{{warning}}" title="{{warning}"/>
			{{register_msg1}}
	</div>
	<div>
		<label for="username">{{username_label}}</label>
		<input type="text" name="username" id="username" maxlength="60" value="{username}" class="input200" />* {username_message}
	</div>
	<div>
		<label for="country">{{country_label}}</label>

			<select name="country" id="country" class="input200" >
				{countries_list}
			</select>&nbsp;{all_countries_submit}
	</div>
	<div>
		<label for="email">{{email_address}}:</label>
		<input type="text" name="email" maxlength="80" id="email" value="{email}" class="input200" />*&nbsp;{email_message}
	</div>
	<div><label for="password1">{{password}}:</label>
		<input type="password" name="password1" maxlength="80" id="password1" value="" class="input200" />*&nbsp;{password_message}
	</div>
	<div><label for="password2">{{password_confirm}}</label>
		<input type="password" name="password2" maxlength="80" id="password2" value="" class="input200" />*
	</div>
	<div>
			{{register_msg2}}
	</div>
	<div>
			<img src="tpl/stdstyle/images/misc/hint.gif" alt="{{warning}" title="{{warning}}"/>
			{{register_msg3}}
	</div>

	<div>
		    <input type="checkbox" name="TOS" value="ON" style="border:0;" />{{register_msg4}}
			{tos_message}
	</div>

	<div>
			<input type="reset" name="reset" value="Reset" class="formbuttons"/>&nbsp;&nbsp;
			<input type="submit" name="submit" value="{{registration}}" class="formbuttons"/>
	</div>
</form>
