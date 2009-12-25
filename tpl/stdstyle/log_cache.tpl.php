<?php
	require_once('./lib/common.inc.php');
/***************************************************************************
											./tpl/stdstyle/log_cache.tpl.php
															-------------------
		begin                : July 4 2004
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

	 log a cache visit

	 template replacements:

		cacheid
		logtypeoptions
		logdate
		logtext
		reset
		submit

 ****************************************************************************/
?>
<script type="text/javascript">
<!--
function disable()
{
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
	
	
	
	//if( vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
	//	vis.display=(elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
	//vis.display = (vis.display==''||vis.display=='block')?'none':'block';
}

//-->
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
			return;

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