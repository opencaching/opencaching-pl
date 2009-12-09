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
	
$message_user_not_found = "Nie ma takiego użytkownika!";
$message_sent = "E-Mail został wysłany!";

$errnosubject = '<span class="errormsg">Brak tematu!</span>';
$errnotext = '<span class="errormsg">Brak tesktu wprowadzonego!</span>';

$mailsubject = "[opencaching.pl] Email od '{from_username}': {subject}";

$mailtext_email = "Witaj {to_username},\n\n";
$mailtext_email .= "'{from_username}' o adresie Email {from_email} kontaktuje sie z toba poprzez www.opencaching.pl\n";
$mailtext_email .= "Profil uzytkownika: http://www.opencaching.pl/viewprofile.php?userid={from_userid}\n";
$mailtext_email .= "Aby odpowiedziec na ten E-mail uzyj funkcji odpowiedzi w swoim programie pocztowym.\n";
$mailtext_email .= "----------------------\n\n";
$mailtext_email .= "{text}\n";

$mailtext_anonymous = "Witaj {to_username},\n\n";
$mailtext_anonymous .= "'{from_username}' kontaktuje sie z toba poprzez www.opencaching.pl\n";
$mailtext_anonymous .= "Profil uzytkownika: http://www.opencaching.pl/viewprofile.php?userid={from_userid}\n";
$mailtext_anonymous .= "Aby odpowiedziec na ten email uzyj funkcji EMAIL-DO-UZYTKOWNIKA w profilu tego uzytkownika.\n";
$mailtext_anonymous .= "----------------------\n";
$mailtext_anonymous .= "{text}\n";

?>
