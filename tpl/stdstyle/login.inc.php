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
	$message_logout_before_login_title = 'Już powiadomiony';
	$message_logout_before_login = 'Już jesteś zalogowany. Jeśli chcesz zalogować jako inny użytkownik, musisz się najpierw <a href="logout.php">wylogować</a>.</p>';
	$message_login_redirect = 'Zalogowałeś się poprawnie. Teraz będziesz przekierowany na stronę docelową.';

	$error_loginnotok = 'Logowanie nie powiodło się.<br />Jeśli problem powtarza się prosimy o kontakt na adres ocpl @ opencaching.pl</a>.';
	$error_toomuchlogins = 'Logowanie nie powiodło się.<br />Zbyt dużo prób logowania. Próbowałeś przez ostatnią godzinę 25 razy zalogować się do opencaching.pl podając błędne dane.';
	$error_invalidemail = 'Logowanie nie powiodło się.<br />Wprowadzona nazwa użytkownika lub email są nieprawidłowe.';
	$error_wrongauthinfo = 'Logowanie nie powiodło się.<br />Wprowadzona nazwa użytkownika lub hasło są nieprawidłowe.';
	$error_usernotactive = ''.$language[$lang][error_usernotactive].'';

	$cookies_error = 'Twoja przeglądarka nie zapisała ciasteczek dla opencaching.pl.';

	$emptyform = 'Strona, którą zamierzasz otworzyć, wymaga zalogowania.';
?>
