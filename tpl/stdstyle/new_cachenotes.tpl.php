<?php
/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*   
	*  UTF-8 ąść
	***************************************************************************/
?>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" />&nbsp;Dodaj notatkę dla skrzynki: {cache_name}</div>
	{general_message}
<form action="new_cachenotes.php" method="post" enctype="application/x-www-form-urlencoded" name="newnotes_form" dir="ltr">
<input type="hidden" name="cacheid" value="{cacheid}"/>

<table width="90%" class="table" border="0">
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td valign="top" class="content-title-noshade">Treść notatki:</td>
		<td class="content-title-noshade">
		<textarea name="desc" rows="10" cols="80">{desc}</textarea>{desc_message}</td>
	</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr><td valign="top" class="content-title-noshade">&nbsp;</td>
	<td valign="top"><input type="checkbox" name="newshtml" id="newshtml" value="1" style="border:0;" {newshtml} /> <label for="newshtml"><b>Treść notatki zawiera kod HTML</b></label></td></tr>


<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			<button type="submit" name="submitform" value="submit" style="font-size:14px;width:140px"><b>Dodaj notatkę</b></button>
		<br /><br /></td>
	</tr>

</table>
</form>
