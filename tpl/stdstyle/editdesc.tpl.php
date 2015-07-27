<?php
/***************************************************************************
											./tpl/stdstyle/editdesc.tpl.php
															-------------------
		begin                : July 7 2004
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
	      
   Unicode Reminder メモ
                                   				                                
	 edit a cache listing
	
	 template replacement(s):
			
			desclang
			desclang_name
			cachename
			reset
			submit
			short_desc
			desc_err
			desc
			hints
			
 ****************************************************************************/
?>
<form name="descform" action="editdesc.php" method="post" enctype="application/x-www-form-urlencoded" id="editcache_form" dir="ltr">
<input type="hidden" name="post" value="1"/>
<input type="hidden" name="descid" value="{descid}"/>
<input type="hidden" name="show_all_langs_value" value="{show_all_langs_value}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />
<table class="content">
	<colgroup>
		<col width="100">
		<col>
	</colgroup>
	<tr><td class="header" colspan="2"><img src="tpl/stdstyle/images/description/22x22-description.png" border="0" width="22" height="22" alt="" title="Nowy Cache" align="middle"> <b>Edycja opisu skrzynki <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td>Język:</td>
		<td>
			<select name="desclang">
				{desclangs}
			</select>{show_all_langs_submit}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td>Krótki opis:</td>
		<td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="input400"/></td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2" class="help"><img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwaga" title="Uwaga">
		Możesz dołożyć dodatkowe atrybuty do opisy skrzynki korzystając z tego formularza: <b><a href="cache-atr.php" target="_BLANK">Dodatkowe atrybuty skrzynki</a></b>.
		</td>
	</tr>

	<tr>
		<td colspan="2">Pełny opis:{desc_err}</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="menuBar">
				<span id="descText" class="buttonNormal" onclick="btnSelect(1)" onmouseover="btnMouseOver(1)" onmouseout="btnMouseOut(1)">Text</span>
				<span class="buttonSplitter">|</span>
				<span id="descHtml" class="buttonNormal" onclick="btnSelect(2)" onmouseover="btnMouseOver(2)" onmouseout="btnMouseOut(2)">&lt;html&gt;</span>
				<span class="buttonSplitter">|</span>
				<span id="descHtmlEdit" class="buttonNormal" onclick="btnSelect(3)" onmouseover="btnMouseOver(3)" onmouseout="btnMouseOut(3)">Editor</span>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<span id="scriptwarning" class="errormsg">Javascript jest włączona przez twoją przeglądarke.Możesz tylko wprowadzić zwykły tekst. Aby wprawdzić kod HTML i użyć edytor musisz włączyć obsługe Javascript.</span>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea id="desc" name="desc" cols="80" rows="25">{desc}</textarea>
    </td>	
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="help" colspan="2">
			<img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwagi" title="Uwagi"> Używaj tylko znaczników HTML do formatowania tekstu. Wykaz dozwolonych znaczników znajdziesz
			<a href="articles.php?page=htmltags">TUTAJ</a>.<br>
			<img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwaga" title="Uwaga">
			Proszę nie używać obrazków z geocaching.com.
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">Dodatkowe informacje które będą kodowane:</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea name="hints" class="mceNoEditor" cols="80" rows="15">{hints}</textarea>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<input type="reset" name="reset" value="{reset}" style="width:120px"/>&nbsp;&nbsp;
			<input type="submit" name="submitform" value="{submit}" style="width:120px"/>
		</td>
	</tr>
</table>
</form>
<script language="javascript" type="text/javascript">
<!--
	/*
		1 = Text
		2 = HTML
		3 = HTML-Editor
	*/
	var use_tinymce = 0;
	var descMode = {descMode};
	document.getElementById("scriptwarning").firstChild.nodeValue = "";
	
	// descMode auf 1 oder 2 stellen ... wenn Editor erfolgreich geladen wieder auf 3 Stellen
	if (descMode == 3)
	{
		if (document.getElementById("desc").value == '')
			descMode = 1;
		else
			descMode = 2;
	}

	document.getElementById("descMode").value = descMode;
	mnuSetElementsNormal();
	
	function postInit()
	{
		descMode = 3;
		use_tinymce = 1;
		document.getElementById("descMode").value = descMode;
		mnuSetElementsNormal();
	}

	function SwitchToTextDesc()
	{
		document.getElementById("descMode").value = 1;
	
		if (use_tinymce == 1)
			document.descform.submit();
	}

	function SwitchToHtmlDesc()
	{
		document.getElementById("descMode").value = 2;

		if (use_tinymce == 1)
			document.descform.submit();
	}

	function SwitchToHtmlEditDesc()
	{
		document.getElementById("descMode").value = 3;

		if (use_tinymce == 0)
			document.descform.submit();
	}

	function mnuSelectElement(e)
	{
		e.backgroundColor = '#D4D5D8';
		e.borderColor = '#6779AA';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuNormalElement(e)
	{
		e.backgroundColor = '#F0F0EE';
		e.borderColor = '#F0F0EE';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuHoverElement(e)
	{
		e.backgroundColor = '#B6BDD2';
		e.borderColor = '#0A246A';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuUnhoverElement(e)
	{
		mnuSetElementsNormal();
	}

	function mnuSetElementsNormal()
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (descMode)
		{
			case 1:
				mnuSelectElement(descText);
				mnuNormalElement(descHtml);
				mnuNormalElement(descHtmlEdit);

				break;
			case 2:
				mnuNormalElement(descText);
				mnuSelectElement(descHtml);
				mnuNormalElement(descHtmlEdit);

				break;
			case 3:
				mnuNormalElement(descText);
				mnuNormalElement(descHtml);
				mnuSelectElement(descHtmlEdit);

				break;
		}
	}

	function btnSelect(mode)
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		var oldMode = descMode;
		descMode = mode;
		mnuSetElementsNormal();

		if ((oldMode == 1) && (descMode != 1))
		{
			// convert text to HTML
			var desc = document.getElementById("desc").value;

			if ((desc.indexOf('&amp;') == -1) && 
			    (desc.indexOf('&quot;') == -1) &&
			    (desc.indexOf('&lt;') == -1) &&
			    (desc.indexOf('&gt;') == -1) &&
			    (desc.indexOf('<p>') == -1) &&
			    (desc.indexOf('<i>') == -1) &&
			    (desc.indexOf('<strong>') == -1) &&
			    (desc.indexOf('<br />') == -1))
			{
				desc = desc.replace(/&/g, "&amp;");
				desc = desc.replace(/"/g, "&quot;");
				desc = desc.replace(/</g, "&lt;");
				desc = desc.replace(/>/g, "&gt;");
				desc = desc.replace(/\r\n/g, "\<br />");
				desc = desc.replace(/\n/g, "<br />");
				desc = desc.replace(/<br \/>/g, "<br />\n");
			}

			document.getElementById("desc").value = desc;
		}

		switch (mode)
		{
			case 1:
				SwitchToTextDesc();
				break;
			case 2:
				SwitchToHtmlDesc();
				break;
			case 3:
				SwitchToHtmlEditDesc();
				break;
		}
	}
	
	function btnMouseOver(id)
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 1:
				mnuHoverElement(descText);
				break;
			case 2:
				mnuHoverElement(descHtml);
				break;
			case 3:
				mnuHoverElement(descHtmlEdit);
				break;
		}
	}
	
	function btnMouseOut(id)
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 1:
				mnuUnhoverElement(descText);
				break;
			case 2:
				mnuUnhoverElement(descHtml);
				break;
			case 3:
				mnuUnhoverElement(descHtmlEdit);
				break;
		}
	}
//-->
</script>