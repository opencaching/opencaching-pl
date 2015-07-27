<?php
/***************************************************************************
											./tpl/stdstyle/newemail.tpl.php
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

	 template replacement(s):
	 
	    message
	    email_message
	    code_message
	    new_email
	 
 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/profile/32x32-email.png" border="0" width="32" height="32" alt="E-Mail-Adres" title="E-Mail-Adres" align="middle">&nbsp;Zmiana adresu e-mail</div>
{message}
<div class="notice">
Aby zmienić adres e-mail najpierw musisz otrzymać kod, który zostanie wysłany na nowy adres e-mail.
</div>

<form action="newemail.php" method="post" enctype="application/x-www-form-urlencoded" name="forgot_pw_form" dir="ltr" style="display: inline;">
<table class="table">
	<colgroup>
		<col width="150px">
		<col>
	</colgroup>
	<tr>
		<td class="content-title-noshade">Nowy adres e-mail:</td>
		<td>
			<input name="newemail" maxlength="60" type="text" value="{new_email}" class="input200" /> {email_message}
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="submit" name="submit_getcode" value="{getcode}" class="formbuttons" />
		</td>
	</tr>
</table>
<div class="notice">Należy wprowadzić kod razem z nowym adresem e-mail. Kod jest ważny 3 dni i po tym okresie należy wygenerować nowy.</div>
<table class="table">
	<colgroup>
		<col width="150px">
		<col>
	</colgroup>
	<tr>
		<td class="content-title-noshade">Kod, który otrzymałeś via e-mail:</td>
		<td>
			<input name="code" maxlength="60" type="text" value="" class="input100" />{code_message}
		</td>
	</tr>
</table>
<div class="buffer"></div>
<input type="reset" name="reset" value="{reset}" class="formbuttons" />&nbsp;&nbsp;
<input type="submit" name="submit_changeemail" value="{change_email}" class="formbuttons" />
</form>