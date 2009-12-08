<?php
/***************************************************************************
												./tpl/stdstyle/newpic.tpl.php
															-------------------
		begin                : August 17 2005
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
                                 				                                
	 add a picture for "my profile", caches, logs etc.
	 
	 replacements:
	 
	
 ****************************************************************************/
?>
<script type="text/javascript">
<!--
	function checkForm()
	{
		if(document.newpic_form.title.value == "")
		{
			alert("Proszę nadać nazwę plikowi!");
			return false;
		}

		if(document.newpic_form.file.value == "")
		{
			alert("Proszę podać źródło pliku!");
			return false;
		}

		return true;
	}
	//-->
</script>
<form action="newmp3.php" method="post" enctype="multipart/form-data" name="newmp3_form" dir="ltr" onsubmit="return checkForm();">
<input type="hidden" name="objectid" value="{objectid}" />
<input type="hidden" name="type" value="{type}" />
<table class="content">
	<colgroup>
		<col width="100">
		<col>
	</colgroup>
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/podcache-mp3.png" border="0" width="32" height="32" alt="" title="Cache" align="middle"> <b>{mp3typedesc}: </b><font size="2"><a href="/viewcache.php?cacheid={cacheid}">{cachename}</a></font></td></tr>

	<tr><td class="spacer" colspan="2"><br><br></td></tr>
	<tr>
		<td valign="top">Tytuł:</td>
		<td><input class="input200" name="title" type="text" value="{title}" size="43" /> {errnotitledesc}</td>
	</tr>

	<tr>
		<td valign="top">Nazwa pliku:</td>
		<td><input class="input200" name="file" type="file" maxlength="{maxpicsize}" /> {errnopicgivendesc}</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	{begin_cacheonly}
	<tr>
		<td align="right"><input class="checkbox" type="checkbox" name="notdisplay" value="1"{notdisplaychecked}/></td>
		<td>Tego pliku nie pokazuj</td>
	</tr>
	{end_cacheonly}

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="help" colspan="2"><img src="tpl/stdstyle/images/misc/16x16-info.png" border="0" alt="Uwaga" title="Uwaga"> Następujacy format pliku jest akceptowany: MP3. Maksymalna wielkość pliku dozwolona .... KB. Zalecane jakość MP3 22kHZ MONO.</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td class="header-small" colspan="2">
			<input type="submit" name="submit" value="{submit}" style="width:120px"/>
		</td>
	</tr>
  </table>
</form>
