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
 
	$getcode = 'Generate Code';
	$changepw = 'Change Password';
	$emailnotexist = '<div class="errormsg">This address is not correct for that user</div>';
	$newpw_subject = '<div class="notice" style="width:500px;height:24px;">A special code to change your password</div>';
	$emailsend = '<div class="notice" style="width:500px;height:44px;">Message with the code has been sent to your email address.</div>';
	$pw_not_ok = '<div class="errormsg">Password contains illegal characters.</div>';
	$pw_no_match = '<div class="errormsg">Repeated password does not agree with the first.</div>';
	$pw_changed = '<div class="notice" style="width:500px;height:44px;">Password has been changed. Please <a href="login.php">sign in</a> using the new password.</div>';
	$code_timed_out = '<div class="errormsg">Code no longer valid</div>';
	$code_not_ok = '<div class="errormsg">Entered code is not valid</div>'; 
?>