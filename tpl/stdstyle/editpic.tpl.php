<?php
/***************************************************************************
												./tpl/stdstyle/editpic.tpl.php
															-------------------
		begin                : Sat 16 October 2005
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
                                      				                                
	 edit properties of a picture
	
 ****************************************************************************/
?>
<script type="text/javascript">
<!--
function checkForm()
	{
		if(document.editpic_form.title.value == "")
		{
			alert("Proszę nadać nazwę obrazkowi!");
			return false;
		}

		if(document.editpic_form.file.value == "")
		{
			/*alert("Proszę podać źródło obrazka!");
			return false;*/
		}

		return true;
	}//-->
</script>

<form action="editpic.php" method="post" enctype="multipart/form-data" name="editpic_form" dir="ltr" onsubmit="return checkForm();">
<input type="hidden" name="uuid" value="{uuid}" />
<table class="content">
	<colgroup>
		<col width="100">
		<col>
	</colgroup>
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/picture.png" border="0" width="32" height="32" alt="" title="edit picture" align="middle"> <b>{pictypedesc} </b><a href="/viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>
	<tr><td class="spacer" colspan="2"><br></td></tr>

	<tr>
		<td valign="top">Tytuł:</td>
		<td><input class="input200" name="title" type="text" value="{title}" size="43" /> {errnotitledesc}</td>
	</tr>

	<tr>
		<td valign="top">Nazwa pliku:</td>
		<td><input class="input200" name="file" type="file" maxlength="{maxpicsize}" /></td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td align="right"><input class="checkbox" type="checkbox" name="spoiler" value="1" {spoilerchecked}></td>
		<td>Nie pokazuj miniatury (SPOILER) - użyj tej opcji jeśli wgrywasz zdjęcie na którym pokazane jest miejsce ukrycia skrzynki. 
		Dopiero jak ktoś kliknie na SPOILER pokaże mu się wgrany obrazek.</td>
	</tr>
	{begin_cacheonly}
	<tr>
		<td align="right"><input class="checkbox" type="checkbox" name="notdisplay" value="1" {notdisplaychecked}></td>
		<td>Tego obrazka nie pokazuj</td>
	</tr>
	{end_cacheonly}

	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td class="header-small" colspan="2">
			<input type="reset" name="reset" value="Powrót" style="width:120px"/>&nbsp;&nbsp;
			<input type="submit" name="submit" value="Wyslij" style="width:120px"/>
		</td>
	</tr>
  </table>
</form>
