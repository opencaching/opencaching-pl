<?php
/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*   
	*  UTF-8 ¹œæ
	***************************************************************************/
?>
<script type="text/javascript">
<!--
function insertSmiley(parSmiley) {
  var myText = document.checksolution.logtext;
  myText.focus();
  /* fuer IE */
  if(typeof document.selection != 'undefined') {
    var range = document.selection.createRange();
    var selText = range.text;
    range.text = parSmiley + selText;
  }
  /* fuer Firefox/Mozilla-Browser */
  else if(typeof myText.selectionStart != 'undefined')
  {
    var start = myText.selectionStart;
    var end = myText.selectionEnd;
    var selText = myText.value.substring(start, end);
    myText.value = myText.value.substr(0, start) + parSmiley + selText + myText.value.substr(end);
    /* Cursorposition hinter Smiley setzen */
    myText.selectionStart = start + parSmiley.length;
    myText.selectionEnd = start + parSmiley.length;
  }
  /* fuer die anderen Browser */
  else
  {
    alert(navigator.appName + ': Setting smilies is not supported');
  }
}

function toogleLayer( whichLayer, val )
{
	var elem, vis;

	if( document. getElementById )
		elem = document.getElementById(whichLayer);
	else if( document.all )
		elem = document.all[whichLayer];
	else if( document.layers )
		elem = document.layers[whichLayer];
	vis = elem.style;
	
	if(val != '')
	{
	if (document.checksolution.logtype.value == "1" || document.checksolution.logtype.value == "7") 
		vis.display = 'block';
	else
		vis.display = 'none';
	}
	else
		vis.display = val;
}
//-->
</script>
<form action="checksolution.php" method="post" enctype="application/x-www-form-urlencoded" name="checksolution" dir="ltr">
<input type="hidden" name="logid" value="{logid}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />
<table class="content">
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="" align="middle" /> <b>{{check_solution_log}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
</table>
<table class="content" style="font-size: 12px; line-height: 1.6em;">
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td>{solution_status}</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
</table>
<table class="content" style="font-size: 12px; line-height: 1.6em;">
	<tr>
		<td colspan="2"><br /><img src="tpl/stdstyle/images/free_icons/page_edit.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{comments_log}}:</td>
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
		<td>
			<span id="scriptwarning" class="errormsg">Javascript is turned off by your browser. You may only enter plain text. To switch and use the HTML code editor you have to enable Javascript.</span>
		</td>
	</tr>
	<tr>
		<td>
			<textarea name="logtext" id="logtext" cols="68" rows="25" >{logtext}</textarea>
    </td>
	</tr>
	<tr>
		<td colspan="2">
			{smilies}
		</td>
	</tr>
	<tr>
		<td class="header-small" colspan="2">
			<input type="submit" name="cancelform" value="{{cancel}}" style="width:120px"/>&nbsp;&nbsp;
			<input type="submit" name="submitform" value="{{submit}}" style="width:120px"/>
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
		if (document.getElementById("logtext").value == '')
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

	function toggleEditor(id) {
		if (!tinyMCE.getInstanceById(id))
			tinyMCE.execCommand('mceAddControl', false, id);
		else
			tinyMCE.execCommand('mceRemoveControl', false, id);
	}


	function SwitchToTextDesc(oldMode)
	{
		document.getElementById("descMode").value = 1;

		if(use_tinymce)
			toggleEditor("logtext");
		use_tinymce = 0;
		// convert HTML to text
		var desc = document.getElementById("logtext").value;

		desc = html_entity_decode(desc, ['ENT_NOQUOTES']);

		document.getElementById("logtext").value = desc;
	}

	function SwitchToHtmlDesc(oldMode)
	{
		document.getElementById("descMode").value = 2;

		if(use_tinymce)
			toggleEditor("logtext");
		use_tinymce = 0;

		// convert text to HTML
		var desc = document.getElementById("logtext").value;

		if(oldMode != 3)
			desc = htmlspecialchars(desc, ['ENT_NOQUOTES']);

		document.getElementById("logtext").value = desc;
	}

	function SwitchToHtmlEditDesc(oldMode)
	{
		document.getElementById("descMode").value = 3;
		use_tinymce = 1;

		if(oldMode == 2) {
			var desc = document.getElementById("logtext").value;
			desc = html_entity_decode(desc, ['ENT_NOQUOTES']);
			document.getElementById("logtext").value = desc;
		}

		toggleEditor("logtext");
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

		if(oldMode == descMode)
	{
			// convert text to HTML
			var desc = document.getElementById("logtext").value;

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

			document.getElementById("logtext").value = desc;
		}


		switch (mode)
		{
			case 1:
				SwitchToTextDesc(oldMode);
				break;
			case 2:
				SwitchToHtmlDesc(oldMode);
				break;
			case 3:

				SwitchToHtmlEditDesc(oldMode);
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
