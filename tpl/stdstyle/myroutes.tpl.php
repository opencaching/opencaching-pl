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
<script type="text/javascript">
<!--
	function checkForm()
	{

		if(document.newpic_form.file.value == "")
		{
			alert("Proszę podać nazwę pliku");
			return false;
		}

		return true;
	}
	//-->
</script>
<form action="myroutes.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr" onsubmit="return checkForm();">
<input type="hidden" name="MAX_FILE_SIZE" value="51200" />
<table class="content">
	<colgroup>
		<col width="100">
		<col>
	</colgroup>
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="" title="Myroute" align="middle" /> <b>{pictypedesc} &nbsp;<a href="/viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
	<tr><td class="spacer" colspan="2"><br /><font color="red"><b>{{myroute_intro}}</b></font></td></tr>

	<tr>
		<td valign="top">{{file_name}}:</td>
		<td><input class="input200" name="kmlfile" type="file" /> </td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td class="header-small" colspan="2">
		<button type="submit" name="submit" value="submit" style="font-size:12px;width:120px"><b>{submit}</b></button>
		</td>
	</tr>
  </table>
</form>
