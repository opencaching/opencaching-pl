<?php
/***************************************************************************
											./tpl/stdstyle/mailto.inc.php
															-------------------
		begin                : Oct 21 2005
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
	   
   Unicode Reminder メモ
	
 ****************************************************************************/
	
$message_user_not_found = "There is no such user!";
$message_sent = "E-Mail was sent!";

$errnosubject = '<span class="errormsg">No Subject!</span>';
$errnotext = '<span class="errormsg">No text entered!</span>';

$mailsubject = "[opencaching.org.uk] Email from '{from_username}': {subject}";

$mailtext_email = "Hello {to_username},\n\n";
$mailtext_email .= "'{from_username}' Email address {from_email} contacting you via opencaching.se\n";
$mailtext_email .= "User Profile: http://opencaching.se/viewprofile.php?userid={from_userid}\n";
$mailtext_email .= "To reply to this E-mail, please use 'reply' in your email program.\n";
$mailtext_email .= "----------------------\n\n";
$mailtext_email .= "{text}\n";

$mailtext_anonymous = "Hello {to_username},\n\n";
$mailtext_anonymous .= "'{from_username}' contacting you via opencaching.se\n";
$mailtext_anonymous .= "User Profile: http://opencaching.se/viewprofile.php?userid={from_userid}\n";
$mailtext_anonymous .= "To reply to this email, please use the functions EMAIL-TO-USER in the profile of this user.\n";
$mailtext_anonymous .= "----------------------\n";
$mailtext_anonymous .= "{text}\n";

?>