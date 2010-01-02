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
 
	$getcode = 'Generuj kod';
	$changepw = 'Zmień hasło';
	$emailnotexist = '<div class="errormsg">Podany adres nie jest poprawny dla tego użytkownika.</div>';
	$newpw_subject = 'Specjalny kod do zmiany hasła';
	$emailsend = '<div class="notice" style="width:500px;height:44px;">Wiadomość z kodem została wysłana na podany adres e-mail.</div>';
	$pw_not_ok = '<div class="errormsg">Hasło zawiera niedozwolone znaki.</div>';
	$pw_no_match = '<div class="errormsg">Powtórzone hasło nie zgadza się z pierwszym.</div>';
	$pw_changed = '<div class="notice" style="width:500px;height:44px;">Hasło zostało zmienione. Proszę <a href="login.php">zalogować się</a> używając nowego hasła.</div>';
	$code_timed_out = '<div class="errormsg">Kod starcił swoją ważność.</div>';
	$code_not_ok = '<div class="errormsg">Wprowadzony kod jest niepoprawny.</div>'; 
?>
