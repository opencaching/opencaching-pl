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
function insertSmiley(parSmiley) {
  var myText = document.editlog.logtext;
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

function _chkFound () {
<?php 
	$sql = "SELECT count(cache_id) FROM cache_logs WHERE deleted=0 AND cache_id = (SELECT cache_id FROM cache_logs WHERE deleted=0 AND id = '".sql_escape(intval($_REQUEST['logid']))."') AND user_id = '".sql_escape($usr['userid'])."' AND type='1'";
	$founds = mysql_result(mysql_query($sql),0);
?>
  if (document.editlog.logtype.value == "1" || (<?php echo $founds;?>>0 && document.editlog.logtype.value == "3") || document.editlog.logtype.value == "7") {
    document.editlog.rating.disabled = false;
  }
  else
  {
    document.editlog.rating.disabled = true;
  }
  return false;
}

function toogleLayer( whichLayer, val )
{
	var elem, vis;
	_chkFound();
	if( document. getElementById )
		elem = document.getElementById(whichLayer);
	else if( document.all )
		elem = document.all[whichLayer];
	else if( document.layers )
		elem = document.layers[whichLayer];
	vis = elem.style;
	
	if(val != '')
	{
	if (document.editlog.logtype.value == "1" || document.editlog.logtype.value == "7") 
		vis.display = 'block';
	else
		vis.display = 'none';
	}
	else
		vis.display = val;
	
	
	
	//if( vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
	//	vis.display=(elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
	//vis.display = (vis.display==''||vis.display=='block')?'none':'block';
}
function showHide(id){
   el = document.getElementById(id);
   el.style.display = (el.style.display != 'block')? 'block' : 'none';
} 
//-->
</script>
<form action="editlog-test.php" method="post" enctype="application/x-www-form-urlencoded" name="editlog" dir="ltr">
<input type="hidden" name="logid" value="{logid}"/>
<input type="hidden" name="existDB" value="{existDB}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />
<table class="content">
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="edit log Cache" align="middle" /> <b>Edycja logu dla skrzynki <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
</table>
<table class="content" style="font-size: 12px; line-height: 1.6em;">
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td width="180px"><img src="tpl/stdstyle/images/free_icons/page_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{type_of_log}}:</strong></td>
		<td align="left">
			<!--<select name="logtype" onChange="return _chkFound()">-->
			<select onload="javascript:toogleLayer('ocena');" name="logtype" onchange="javascript:toogleLayer('ocena');">
				{logtypeoptions}
			</select>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td width="180px"><img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{date_logged}}:</td>
		<td align="left">
			<input class="input20" type="text" name="logday" maxlength="2" value="{logday}"/>.
			<input class="input20" type="text" name="logmonth" maxlength="2" value="{logmonth}"/>.
			<input class="input40" type="text" name="logyear" maxlength="4" value="{logyear}"/>
			  <img src="tpl/stdstyle/images/free_icons/clock.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{time}} :  <input class="input20" type="text" name="loghour" maxlength="2" value="{loghour}"/> HH (0-23)
			<input class="input20" type="text" name="logmin" maxlength="2" value="{logmin}"/> MM (0-60)
			<br />{date_message}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	{rating_message}
</table>

{coordinates_start}
<table class="content" style="font-size: 12px; line-height: 1.6em;">
	<tr><td class="spacer" colspan="2"></td>&nbsp;</tr>
	<tr>   
    <td width="800px" colspan="2" valign="top"><img src="tpl/stdstyle/images/free_icons/map.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong><input id="add_coord" type="checkbox" name="add_coord" value="1" {is_checked_coord} onclick="showHide('coord');" /><label for="add_coord">Dodaj współrzędne do logu</label></strong>
     </td>
	</tr>
	<tr><td colspan="2"><div class="notice" id="viewcache-attributesend" style="width:600px;">Możesz dodać lub usunąć współrzędne, które będą widzane w logu.</div>
	</td></tr>
</table>
<table width="95%" id="coord" class="content" style="font-size: 12px; line-height: 1.6em;display: {display};">
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td width="180px" valign="top"><b>{{coordinates}}:</b></td>
		<td width="600px">
		<fieldset style="border: 1px solid black; width: 30%; height: 32%; background-color: #FAFBDF;">
			<legend>&nbsp; <strong>WGS-84</strong> &nbsp;</legend>&nbsp;&nbsp;&nbsp;
			<select name="latNS" class="input40">
				<option value="N"{selLatN}>N</option>
				<option value="S"{selLatS}>S</option>
			</select>
			&nbsp;<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />
			&deg;&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input50" />&nbsp;'&nbsp;
			{lat_message}<br />
			&nbsp;&nbsp;&nbsp;
			<select name="lonEW" class="input40">
				<option value="E"{selLonE}>E</option>
				<option value="W"{selLonW}>W</option>
			</select>
			&nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />
			&deg;&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input50" />&nbsp;'&nbsp;
			{lon_message}
			</fieldset>
		</td>
	</tr>
</table>
{coordinates_end}
<table class="content" style="font-size: 12px; line-height: 1.6em;">
	<tr><td class="spacer" colspan="2"></td>&nbsp;</tr>
	<tr>   
    <td width="800px" colspan="2"><img src="tpl/stdstyle/images/free_icons/lock.png" class="icon16" alt="" title="" align="bottom" />&nbsp;<strong><input id="encrypt" type="checkbox" name="encrypt" value="1" {is_checked} /><label for="encrypt">Szyfowanie wpisu do logu</label></strong>
     
    <div class="notice" id="viewcache-attributesend" style="width:600px;"> Możesz zaszyfrować wpis do logu (ROT13 encrypt). Jest to przydatne, gdy dziennik zawiera spoilery lub takie informacje, które mogą zdradzić miejsca, w którym jest ukryta skrzynka. Tekst poza nawiasami kwadratowymi [treść] nie będzie szyfrowany.</div></td>
	</tr>
	<tr>
		<td colspan="2"><br /><img src="tpl/stdstyle/images/free_icons/page_edit.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{comments_log}}:</strong></td>
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
			<span id="scriptwarning" class="errormsg">Javascript jest włączona przez twoją przeglądarke.Możesz tylko wprowadzić zwykły tekst. Aby wprawdzić kod HTML i użyć edytor musisz włączyć obsługe Javascript.</span>
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
	<tr><td class="spacer" colspan="2"></td></tr>

		{log_pw_field}

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<button type="reset" name="reset" value="Reset" style="font-size:12px;width:140px;"/><b>Reset</b></button>&nbsp;&nbsp;
			<button type="submit" name="submitform" id="submitform" value="{{submit_log_entry}}" style="font-size:12px;width:140px;"/><b>{{submit_log_entry}}</b></button>
		</td>
	</tr>
</table>
</form>
<br/><br/>
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

	//_chkFound();

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
