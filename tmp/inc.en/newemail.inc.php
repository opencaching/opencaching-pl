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
 
 $change_email = 'Change E-mail Address';
 $get_code = 'Generate Code';

 $email_changed = '<div class="notice" style="width:500px;height:24px;">E-mail address has been changed.</div>';
 $email_send = '<div class="notice" style="width:500px;height:24px;">The code was sent to a new e-mail.</div>';
 $email_subject = 'Code to change your e-mail at OC.org.uk';
 
 $error_email_not_ok = '<div class="errormsg">E-mail address is not valid.</div>';
 $error_email_exists = '<div class="errormsg">The e-mail address already exists in the database</div>';
 $error_no_new_email = '<div class="errormsg">The e-mail does not exist in the database</div>';
 $error_wrong_code = '<div class="errormsg">The code entered is not valid.</div>';
 $error_code_timed_out = '<div class="errormsg">The code has lost its validity.</div>';
?>