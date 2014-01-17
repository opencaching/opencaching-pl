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
 	$logoutsuccess = $language[$lang]['logoutsuccess'];
	$message_logout_before_login_title = tr('login_message_01');
	$message_logout_before_login = tr('login_message_02');
	$message_login_redirect = tr('login_message_03');

	$error_loginnotok = tr('login_message_04');
	$error_toomuchlogins = tr('login_message_05');
	$error_invalidemail = tr('login_message_06');
	$error_wrongauthinfo = tr('login_message_07');
	$error_usernotactive = $language[$lang]['error_usernotactive'];

	$cookies_error = tr('login_message_08');

	$emptyform = tr('login_message_09');
?>
