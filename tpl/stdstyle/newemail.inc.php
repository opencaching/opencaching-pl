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
 
 $change_email = 'Zmień adres e-mail';
 $get_code = 'Generuj kod';

 $email_changed = '<div class="notice" style="width:500px;height:24px;">Adres e-mail został zmieniony.</div>';
 $email_send = '<div class="notice" style="width:500px;height:24px;">Kod został wysłany na nowy adres e-mail.</div>';
 $email_subject = 'Kod do zmiany adresu e-mail na OCPL';
 
 $error_email_not_ok = '<div class="errormsg">Adres e-mail jest nieprawidłowy.</div>';
 $error_email_exists = '<div class="errormsg">Podany adres e-mail już istnieje w bazie OC PL.</div>';
 $error_no_new_email = '<div class="errormsg">Podany adres e-mail nie istnieje w bazie OC PL.</div>';
 $error_wrong_code = '<div class="errormsg">Kod wprowadzony nie jest poprawny.</div>';
 $error_code_timed_out = '<div class="errormsg">Kod stracił swoją ważność.</div>';
?>
