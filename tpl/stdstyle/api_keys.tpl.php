<?php

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder ąść


 ****************************************************************************/
?>

<table class="content" border="0">
	<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/decrypt.png" class="icon32" alt="" title="API-Key" align="middle" /><font size="4">&nbsp;&nbsp;<b>Zarządzanie kluczem do OC PL API REST</b></font></td></tr>
	<tr><td class="spacer"></td></tr>
</table>
<br/>
<div class="searchdiv" style="background-color: #FFF9E3;">
<br/>
<span style="font-weight:bold;font-size: 13px; line-height:1.7em; text-align: justify;">	
&nbsp;&nbsp; Aktualny klucz do API REST: </span> &nbsp;&nbsp;<span style="font-weight:bold;color: blue;font-size: 16px; line-height:1.7em; text-align: justify;">
{api_key}
</span>
<br/><br/>
</div>
<br/><br/>
<form action="api_keys.php" method="post" enctype="application/x-www-form-urlencoded" name="apikey" dir="ltr">
<input type="hidden" name="idkey" value="{idkey}" />
<input type="hidden" name="userid" value="{userid}" />
<input type="hidden" name="confirm" value="confirm" />
<center>
<button type ="submit" value="delete" name="delete" style="font-size:13px;"><b>Usun aktualny klucz</button>&nbsp;&nbsp;
<button type ="submit" value="new_key" name="new_key" style="font-size:13px;"><b>Wygeneruj nowy klucz</button>

</form>
