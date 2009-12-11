<?php
/***************************************************************************
											./tpl/stdstyle/log_cache.tpl.php
															-------------------
		begin                : July 4 2004
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
                                      				                                
	 log a cache visit
	 
	 template replacements:
		
		cacheid
		logtypeoptions
		logdate
		logtext
		reset
		submit
		
 ****************************************************************************/
?>
<form action="mailto.php" method="post" enctype="application/x-www-form-urlencoded" name="mailto_form" dir="ltr">
<input type="hidden" name="userid" value="{userid}"/>
<table class="table">
	<colgroup>
		<col width="200">
		<col>
	</colgroup>
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/email.png" border="0" width="32" height="32" alt="" title="Neuer Cache" align="middle"> <b>Wysyłanie wiadomości e-mail do <a href='viewprofile.php?userid={userid}'>{to_username}</a></b></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>

	{message_start}
	<tr><TD colspan="2"><b>{message}</b></TD></tr>
	{message_end}
	{formular_start}
	
	<tr>
		<td colspan="2">Tytuł: <input type="text" name="subject" value="{subject}" class="input400"> {errnosubject}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td colspan="2">Treść listu {errnotext}</td>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea class="logs" name="text" cols="68" rows="15">{{text}}</textarea>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td colspan="2"><label for="l_send_emailaddress">Mój adres email będzie wysłany w tym liście </label><input type="checkbox" name="send_emailaddress" value="1"{send_emailaddress_sel} id="l_send_emailaddress" class="checkbox" />
		</td>
	</tr>
	<tr>
		<td class="help" colspan="2">
			<img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Hinweis" title="Uwaga" align="middle"> 
			W wyniku wybrania tej opcji odbiorca będzie znał twój adres e-mail i będzie mógł odpowiedzieć ci bezposrednio.<br />
			<br />


		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td class="header-small" colspan="2">
			<input type="reset" name="reset" value="Powrót" class="formbuttons">&nbsp;&nbsp;
			<input type="submit" name="submit" value="Wyslij" class="formbuttons">
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	{formular_end}
</table>
</form>
