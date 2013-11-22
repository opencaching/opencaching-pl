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

function subs_days (days_number) {
 //alert('ok');
 var d_day = document.getElementById('logday').value ;
 var d_mn = document.getElementById('logmonth').value-1;
 var d_yr = document.getElementById('logyear').value ;
 var d = new Date (d_yr, d_mn , d_day - days_number , 0, 0,0);

 
 //alert(d);
 if (isNaN(d)==false) 
    {
		var d_now = +new Date;
		if (d <=d_now ) 
 		{
 		 document.getElementById('logday').value = d.getDate();
 		 document.getElementById('logmonth').value = d.getMonth()+1;
		 document.getElementById('logyear').value = d.getFullYear();
		}	;
	};
};

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
	chkMoved();
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
function chkMoved()
    {
			var mode = document.editlog.logtype.value;
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
			document.editlog.actionicon.src = image_log;
//         var el;
//	el='coord_table';
//	if (document.editlog.logtype.value == "4") 		
//	{document.getElementById(el).style.display='block';
//    } else {document.getElementById(el).style.display='none';}
}

//-->
</script>
<form action="editlog.php" method="post" enctype="application/x-www-form-urlencoded" name="editlog" dir="ltr">
<input type="hidden" name="logid" value="{logid}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="{descMode}" />
<table class="content">
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="edit log Cache" align="middle" /> <b>{{edit_logentry}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
</table>
<div class="searchdiv">
<table class="content" style="font-size: 12px; line-height: 1.6em;">
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td width="180px"><img src="tpl/stdstyle/images/free_icons/page_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{type_of_log}}:</strong></td>
		<td align="left">
			<!--<select name="logtype" onChange="return _chkFound()">-->
			<select onload="javascript:toogleLayer('ocena');" name="logtype" onchange="javascript:toogleLayer('ocena');">
				{logtypeoptions}
			</select>&nbsp;&nbsp;<img name='actionicon' src='' align="top" alt="">
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td width="180px"><img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{date_logged}}:</td>
		<td align="left">
			<img src="tpl/stdstyle/images/free_icons/date_previous.png" alt ="{{lc_Day_before}}" title="{{lc_Day_before}}" onclick="subs_days(1);"/>
			<input class="input20" type="text" id="logday"  name="logday" maxlength="2" value="{logday}"/>.
			<input class="input20" type="text" id="logmonth" name="logmonth" maxlength="2" value="{logmonth}"/>.
			<input class="input40" type="text" id="logyear" name="logyear" maxlength="4" value="{logyear}"/>
			<img src="tpl/stdstyle/images/free_icons/date_next.png" alt ="{{lc_Day_after}}" title="{{lc_Day_after}}" onclick="subs_days(-1);"/>
			&nbsp;&nbsp;
			<img src="tpl/stdstyle/images/free_icons/clock.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{time}} :  <input class="input20" type="text" name="loghour" maxlength="2" value="{loghour}"/> HH (0-23)
			<input class="input20" type="text" name="logmin" maxlength="2" value="{logmin}"/> MM (0-60)
			<br />{date_message}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	{rating_message}
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
			<span id="scriptwarning" class="errormsg">Javascript jest włączona przez twoją przeglądarke.Możesz tylko wprowadzić zwykły tekst. Aby wprawdzić kod HTML i użyć edytor musisz włączyć obsługe Javascript.</span>
		</td>
	</tr>
	<tr>
		<td>
			<textarea name="logtext" id="logtext" cols="68" rows="25" >{logtext}</textarea>
    </td>
	</tr>
	<tr>
		<td id="smilies" colspan="2" style="display: {smiliesdisplay}">
			{smilies}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

		{log_pw_field}

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<input type="reset" name="reset" value="{{reset}}" style="width:120px"/>&nbsp;&nbsp;
			<input type="submit" name="submitform" value="{{submit}}" style="width:120px"/>
		</td>
	</tr>
</table>
</div>
</form>
<script language="javascript" type="text/javascript">
<!--
	/*
		1 = Text
		2 = HTML
		3 = HTML-Editor
	*/
	var use_tinymce = {use_tinymce};
	var descMode = {descMode};
	document.getElementById("scriptwarning").firstChild.nodeValue = "";

	function postInit()
	{
		mnuSetElementsNormal();

		// TinyMCE is on by default - toggle it off if not requested
		if (use_tinymce == 0)
			toggleEditor("logtext");
	}

	function toggleEditor(id) {
		if (!tinyMCE.getInstanceById(id)) {
			tinyMCE.execCommand('mceAddControl', false, id);
			document.getElementById("smilies").style.display = "none";
		} else {
			tinyMCE.execCommand('mceRemoveControl', false, id);
			document.getElementById("smilies").style.display = "";
		}
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
