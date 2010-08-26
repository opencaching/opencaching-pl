<?php

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	*  UTF8 remaider śąłó
	
<script>
    window.onload = function chkMoved()
    {
         	var el;
	el='coord_table';
	if (document.logform.logtype.value == "4") 		
	document.getElementById(el).style.display='block';
    }
</script>
	***************************************************************************/


require_once('./lib/common.inc.php');

?>
<script type="text/javascript">
<!--
function onSubmitHandler()
{

	
	if(document.getElementById('logtext').value.length == 0) {
		
	}	
	var length;
	if(tinyMCE && tinyMCE.get('logtext')) {
		length = tinyMCE.get('logtext').getContent().length;
	}
	else  {
		length = document.getElementById('logtext').value.length;
	}
	if(length == 0) {
		if(!confirm("{{empty_entry_confirm}}"))
			return false;
	}

	//document.getElementById(obj).disabled = true;
	//document.logform.submitform.disabled = true;	
	setTimeout('document.logform.submitform.disabled=true',1);
	
	return true;
}
function insertSmiley(parSmiley) {
  var myText = document.logform.logtext;
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
	$sql = "SELECT count(cache_id) FROM cache_logs WHERE `deleted`=0 AND cache_id = '".sql_escape($_REQUEST['cacheid'])."' AND user_id = '".sql_escape($usr['userid'])."' AND type='1'";
	$founds = mysql_result(mysql_query($sql),0);
?>

  if (document.logform.logtype.value == "1" || (<?php echo $founds;?>>0 && document.logform.logtype.value == "3") || document.logform.logtype.value == "7") {
  
    document.logform.rating.disabled = false;
  }
  else
  {
    document.logform.rating.disabled = true;
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
	if (document.logform.logtype.value == "1" || document.logform.logtype.value == "7") 
		vis.display = 'block';
	else
		vis.display = 'none';
	}
	else
		vis.display = val;

chkMoved();



	//if( vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
	//	vis.display=(elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
	//vis.display = (vis.display==''||vis.display=='block')?'none':'block';
}

function chkMoved()
    {

			var mode = document.logform.logtype.value;
			var iconarray = new Array();
				iconarray['1'] = '16x16-found.png';
				iconarray['2'] = '16x16-dnf.png';
				iconarray['3'] = '16x16-note.png';
				iconarray['4'] = '16x16-moved.png';
				iconarray['5'] = '16x16-need-maintenance.png';
				iconarray['6'] = '16x16-need-maintenance.png';
				iconarray['7'] = '16x16-go.png';
				iconarray['8'] = '16x16-wattend.png';
				iconarray['9'] = '16x16-trash.png';
				iconarray['10'] = '16x16-published.png';
				iconarray['11'] = '16x16-temporary.png';
				iconarray['12'] = '16x16-octeam.png';
			var image_log = "/tpl/stdstyle/images/log/" + iconarray[mode];
			//document.write(image_log);
			document.logform.actionicon.src = image_log;



        var el;
	el='coord_table';
	if (document.logform.logtype.value == "4") 		
	{document.getElementById(el).style.display='block';
    } else {document.getElementById(el).style.display='none';}

}
function showHide(id){
   el = document.getElementById(id);
   el.style.display = (el.style.display != 'block')? 'block' : 'none';
} 
//-->
</script>

<form action="log-test.php" method="post" enctype="application/x-www-form-urlencoded" name="logform" dir="ltr" onsubmit="onSubmitHandler()">
<input type="hidden" name="cacheid" value="{cacheid}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />
<table class="content">
	<tr>
		<td class="content2-pagetitle" colspan="2">
			<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="Cache" align="middle" />
			<b>{{post_new_log}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b>
		</td>
	</tr>
</table>

<table class="content" style="font-size: 12px; line-height: 1.6em;">
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan="2"><div class="notice" style="width:500px;height:44px">{{send_report_info}}&nbsp;<img src="/tpl/stdstyle/images/blue/arrow.png" alt="" title=""/>&nbsp; <a class="links" href="reportcache.php?cacheid={cacheid}">{{send_report}}</a></div></td>
	</tr>
	<tr>
		<td width="180px"><img src="tpl/stdstyle/images/free_icons/page_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{type_of_log}}:</strong></td>
		<td>
			<select id="logtypeid" name="logtype" onLoad="javascript:toogleLayer('ocena');" onChange="javascript:toogleLayer('ocena');">
				{logtypeoptions}
			</select>&nbsp;&nbsp;<img name='actionicon' src='' align="top" alt="">
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td width="180px"><img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{date_logged}}:</strong></td>
		<td>
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
	<tr><td class="spacer" colspan="2"></td></tr>
</table>
<div class="content" id="ocena" style="display:{display};">
<table class="content" style="font-size: 12px; line-height: 1.6em;">
	<tr>
		<td width="180px"><img src="tpl/stdstyle/images/free_icons/star.png" class="icon16" alt="" title="" align="middle" />&nbsp;<b>{score_header}</b></td>
		<td width="*">{score}<br/></td>
	</tr>
</table>
</div>	
{coordinates_start}
<table width="95%" id="coord_table" class="content" style="font-size: 12px; line-height: 1.6em;display:none;">
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td width="180px" valign="top"><img src="tpl/stdstyle/images/free_icons/map.png" class="icon16" alt="" title="" align="middle" />&nbsp;<b>{{coordinates}}:</b></td>
		<td width="600px">
		<fieldset style="border: 1px solid black; width: 30%; height: 32%; background-color: #FAFBDF;">
			<legend>&nbsp; <strong>WGS-84</strong> &nbsp;</legend>&nbsp;&nbsp;&nbsp;
			<select name="latNS" class="input40">
				<option value="N"{selLatN}>N</option>
				<option value="S"{selLatS}>S</option>
			</select>
			&nbsp;<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />
			&deg;&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input50" />&nbsp;'&nbsp;
			<br />
			&nbsp;&nbsp;&nbsp;
			<select name="lonEW" class="input40">
				<option value="E"{selLonE}>E</option>
				<option value="W"{selLonW}>W</option>
			</select>
			&nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />
			&deg;&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input50" />&nbsp;'&nbsp;			
			</fieldset>{lon_message} {lat_message} {coord_empty_message}
		</td>
	</tr>
	<tr><td colspan="2"><div class="notice" id="viewcache-attributesend" style="width:600px;">{{moved_cache_info}}.</div>
	</td></tr>
</table>

{coordinates_end}
<table class="content" style="font-size: 12px; line-height: 1.6em;">
	<tr><td class="spacer" colspan="2"></td>&nbsp;</tr>
	<tr>   
    <td width="800px" colspan="2"><img src="tpl/stdstyle/images/free_icons/lock.png" class="icon16" alt="" title="" align="bottom" />&nbsp;<strong><input id="encrypt" type="checkbox" name="encrypt" value="1" {is_checked} /><label for="encrypt">{{encrypt_log_entry}}</label></strong>
     
    <div class="notice" id="viewcache-attributesend" style="width:650px;">{{encrypt_log_info}}.</div></td>
	</tr>

<tr>                                                                                                                                                                                                                               <td colspan="2">
	<div class="notice" style="width:500px;height:44px">{{empty_entry_notice}}</div>				<td>                                                                                                                                                                                                 
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
			<span id="scriptwarning" class="errormsg">Javascript jest wyłączona przez twoją przeglądarke.Możesz tylko wprowadzić zwykły tekst. Aby wprawdzić kod HTML i użyć edytor musisz włączyć obsługe Javascript.</span>
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
	{listed_start}
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2" width="600px"><strong><img src="tpl/stdstyle/images/free_icons/world_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{listed_other}}:&nbsp;{listed_on}</strong>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	{listed_end}
		<tr>
		<td colspan="2">
			{log_geokret}
		</td>
	</tr>
		<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<button type="reset" name="reset" value="Reset" style="font-size:12px;width:140px;"/><b>Reset</b></button>&nbsp;&nbsp;
			<button type="submit" name="submitform" id="submitform" value="{{submit_log_entry}}" style="font-size:12px;width:140px;"/><b>{{store}}</b></button>
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
