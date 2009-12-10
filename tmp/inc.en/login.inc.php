<?php
/***************************************************************************
												  ./tpl/stdstyle/newpw.inc.php
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
                                    				                                
	 set template specific language variables
	
 ****************************************************************************/
 
	$message_start = '<div class="warning" style="height:40px;">';
	$message_end = '</div>';
 	$logoutsuccess = ''.$language[$lang][logoutsuccess].'';
	$message_logout_before_login_title = 'Already notified';
	$message_logout_before_login = 'You are already logged on. If you want to log in as another user, you must first<a href="logout.php">logout</a>.</p>';
	$message_login_redirect = 'You are signed in correctly. Now you will be redirected to the destination page.';

	$error_loginnotok = 'Login failed.<br />If the problem continues, please contact us at the address admin @ opencaching.se</a>.';
	$error_toomuchlogins = 'Login failed<br />Too many login attempts. The last 25 attempts were using erroneous details';
	$error_invalidemail = 'Login failed.<br />The username or email is not valid.';
	$error_wrongauthinfo = 'Login failed.<br />The username or password is incorrect.';
	$error_usernotactive = ''.$language[$lang][error_usernotactive].'';

	$cookies_error = 'Your browser is not accepting cookies from opencaching.org.uk';

	$emptyform = 'You must be logged in to view that page.';
?>