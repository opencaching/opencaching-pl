<?php
/***************************************************************************
											./tpl/stdstyle/newcache.tpl.php
															-------------------
		begin                : June 24 2004
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

	 submit a new cache

	 replacements:

	     name
	     typeoptions
	     sizeoptions
	     show_all_countries_submit
	     show_all_langs_submit
	     latNsel
	     latSsel
	     lat_h
	     lat_min
	     lonEsel
	     lonWsel
	     lon_h
	     lon_min
	     countryoptions
	     langoptions
	     short_desc
	     desc
	     desc_html
	     desc_message
	     hints
	     hidden_since
	     toschecked
	     reset
	     submit_value
	     hidden_since_message
	     tos_message
	     show_all_countries
	     show_all_langs
	     difficulty_options
	     terrain_options
             effort_message
             search_time
             way_length
             type_message
             size_message
             diff_message

 ****************************************************************************/
?>
<script type="text/javascript">
<!--
var maAttributes = new Array({jsattributes_array});


function _chkVirtual () 
{
  if (document.newcacheform.type.value == "4" || document.newcacheform.type.value == "5" || document.newcacheform.type.value == "6" ) 
	{
		if( document.newcacheform.size.options[document.newcacheform.size.options.length - 1].value != "7" && document.newcacheform.size.options[document.newcacheform.size.options.length - 2].value != "7")
		{
			document.newcacheform.size.options[document.newcacheform.size.options.length] = new Option('Bez pojemnika', '7');
		}
		document.newcacheform.size.value = "7";
		document.newcacheform.size.disabled = true;
  }
  else
  {
		if( document.newcacheform.size.options[document.newcacheform.size.options.length - 1].value == "7" )
			document.newcacheform.size.options[document.newcacheform.size.options.length - 1 ] = null;
		if( document.newcacheform.size.options[document.newcacheform.size.options.length - 2].value == "7" )
			document.newcacheform.size.options[document.newcacheform.size.options.length - 2 ] = null;
		document.newcacheform.size.disabled = false;
  }
  return false;
}

function rebuildCacheAttr()
{
	var i = 0;
	var sAttr = '';
	
	for (i = 0; i < maAttributes.length; i++)
	{
		if (maAttributes[i][1] == 1)
		{
			if (sAttr != '') sAttr += ';';
			sAttr = sAttr + maAttributes[i][0];

			document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][3];
		}
		else
			document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][2];

		document.getElementById('cache_attribs').value = sAttr;
		
	}
}

function toggleAttr(id)
{
	var i = 0;
	
	for (i = 0; i < maAttributes.length; i++)
	{
		if (maAttributes[i][0] == id)
		{
			if (maAttributes[i][1] == 0)
				maAttributes[i][1] = 1;
			else
				maAttributes[i][1] = 0;

			rebuildCacheAttr();
			break;
		}
	}
}
//-->
</script>
<form action="newcache.php" method="post" enctype="application/x-www-form-urlencoded" name="newcacheform" dir="ltr">
<input type="hidden" name="show_all_countries" value="{show_all_countries}"/>
<input type="hidden" name="show_all_langs" value="{show_all_langs}"/>
<input type="hidden" name="version2" value="1"/>
<input type="hidden" id="cache_attribs" name="cache_attribs" value="{cache_attribs}" />
<input id="descMode" type="hidden" name="descMode" value="1" />
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="{{new_cache}}" align="middle"/>&nbsp;{{new_cache}}</div>
	{general_message}
	<div class="buffer"></div>
	<div class="content2-container bg-blue02" >
			<p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/basic2.png" class="icon32" alt=""/>&nbsp;{{basic_information}}</p>
		</div>

	<div class="buffer"></div>
	<div class="notice">
		{{first_cache}}.
	</div>
	{approvement_note}
	<div class="buffer"></div>
	<table class="table" border="0">
	<colgroup>
		<col width="180"/>
		<col/>
	</colgroup>
	<tr>
		<td><p class="content-title-noshade">{{name_label}}:</p></td>
		<td><input type="text" name="name" value="{name}" maxlength="60" class="input400"/>{name_message}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td><p class="content-title-noshade">{{cache_type}}:</p></td>
		<td>
			<select name="type" class="input200" onchange="return _chkVirtual()">
				{typeoptions}
			</select>{type_message}
		</td>
	</tr>
	<tr>
		<td><p class="content-title-noshade">{{cache_size}}:</p></td>
		<td>
			<select name="size" class="input200" onchange="return _chkVirtual()" {is_disabled_size}>
				{sizeoptions}
			</select>{size_message}
		</td>
	</tr>
	<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr>
		<td valign="top"><p class="content-title-noshade">{{coordinates}}:</p></td>
		<td class="content-title-noshade">
		<fieldset style="border: 1px solid black; width: 65%; height: 32%; background-color: #FFFFCC;">
			<legend>&nbsp; <strong>WGS-84</strong> &nbsp;</legend>&nbsp;&nbsp;&nbsp;
			<select name="latNS" class="input40">
				<option value="N"{latNsel}>N</option>
				<option value="S"{latSsel}>S</option>
			</select>
			&nbsp;<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />
			&deg;&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input50" />&nbsp;'&nbsp;
			{lat_message}<br />
			&nbsp;&nbsp;&nbsp;
			<select name="lonEW" class="input40">
				<option value="E"{lonEsel}>E</option>
				<option value="W"{lonWsel}>W</option>
			</select>
			&nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />
			&deg;&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input50" />&nbsp;'&nbsp;
			 {lon_message}</fieldset>
		</td>
	</tr>
	<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr>
		<td><p class="content-title-noshade">{{country_label}}:</p></td>
		<td>
			<select name="country" class="input200">
				{countryoptions}
			</select>
			{show_all_countries_submit}
		</td>
	</tr>
	<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr><td><p class="content-title-noshade">{{difficulty_level}}:</p></td>
		<td>
			{{task_difficulty}}:
			<select name="difficulty" class="input60">
				{difficulty_options}
			</select>&nbsp;&nbsp;
			{{terrain_difficulty}}:
			<select name="terrain" class="input60">
				{terrain_options}
			</select>{diff_message}
		</td>
	</tr>
		<tr>
		<td>&nbsp;</td>
		<td><div class="notice" style="width:500px;height:44px;">{{difficulty_problem}} <a href="rating-c.php" target="_BLANK">{{rating_system}}</a>.</div>
		</td>
	</tr>
	<tr><td><p class="content-title-noshade">{{additional_information}} ({{optional}}):</p></td>
	    <td>
				{{time}}:
				<input type="text" name="search_time" maxlength="10" value="{search_time}" class="input30" /> h
				&nbsp;&nbsp;
				{{length}}:
				<input type="text" name="way_length" maxlength="10" value="{way_length}" class="input30" /> km &nbsp; {effort_message}
			</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><div class="notice" style="width:500px;height:44px">{{time_distance_hint}}</div><div class="buffer"></div></td>
	</tr>
	<tr>
		<td><p class="content-title-noshade">{{waypoint}} ({{optional}}):</p></td>
		<td>geocaching.com: <input type="text" name="wp_gc" value="{wp_gc}" maxlength="7" class="input50"/>
			gpsgames.org: <input type="text" name="wp_nc" value="{wp_nc}" maxlength="6" class="input50"/>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><div class="notice" style="width:500px;height:44px;">{{waypoint_other_portal_info}}</div><div class="buffer"></div></td>
	</tr>
	<tr>
		<td colspan="2"><div class="content2-container bg-blue02">
			<p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/attributes.png" class="icon32" alt=""/>&nbsp;{{cache_attributes}}</p>
		</div>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">{cache_attrib_list}</td>
	</tr>
	<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr>
		<td colspan="2"><div class="notice" style="width:500px;min-height:24px;height:auto;">{{attributes_edit_hint}} {{attributes_desc_hint}}</div></td></tr>
	<tr>
		<td colspan="2"><div class="content2-container bg-blue02"> 
			<p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt=""/>&nbsp;{{descriptions}}</p>
			</div>
			</td>
	</tr>
	<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr>
		<td><p class="content-title-noshade">{{language}}:</p></td>
		<td>
			<select name="desc_lang" class="input200">
				{langoptions}
			</select>
			{show_all_langs_submit}
		</td>
	</tr>
	<tr><td colspan="2"><div class="notice" style="width:500px;height:44px;">{{other_languages_desc}}</div></td></tr>
	<tr>
		<td><p class="content-title-noshade">{{short_desc_label}}:</p></td>
		<td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="input400"/></td>
	</tr>
	<tr>
		<td colspan="2">
			<br />
				<p class="content-title-noshade">{{full_description}}:</p>
			<br/>
			{desc_message}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="menuBar" style="height:20px;">
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
			<span id="scriptwarning" class="errormsg">{{no_javascript}}</span>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea id="desc" name="desc" rows="20" cols="20" class="cachedesc">{desc}</textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="notice" style="width:500px;min-height:24px;height:auto;"><b><i>{{mandatory_field}}.</i></b> {{full_desc_long_text}} {{description_hint}} {{html_usage}} <a href="articles.php?page=htmltags" target="_BLANK">{{available_html}}</a></div> 
		</td>
	</tr>
	<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr>
		<td colspan="2"><div class="notice" style="width:500px;min-height:24px;height:auto;">{{additional_enc_info}}</div></td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea name="hints" rows="5" cols="20" class="hint mceNoEditor">{hints}</textarea><br /><br />
		</td>
	</tr>
	<tr>
		<td colspan="2"><div class="content2-container bg-blue02">
		
			<p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt=""/>
				{{other}}
			</p>
			</div>
		</td>
	</tr>
	<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr><td colspan="2"><div class="notice" style="width:500px;height:24px;">{{add_photo_newcache}}</div></td></tr>
	<tr>
		<td><p class="content-title-noshade">{{date_hidden_label}}:</p></td>
		<td>
			<input class="input20" type="text" name="hidden_day" maxlength="2" value="{hidden_day}"/>.
			<input class="input20" type="text" name="hidden_month" maxlength="2" value="{hidden_month}"/>.
			<input class="input40" type="text" name="hidden_year" maxlength="4" value="{hidden_year}"/>
			{hidden_since_message}
		</td>
	</tr>
	<tr><td colspan="2"><div class="notice buffer" style="width:500px;height:24px;">{{event_hidden_hint}}</div></td></tr>
	{hide_publish_start}
	<tr>
		<td><p class="content-title-noshade">{{submit_new_cache}}:</p></td>
		<td>
			<input type="radio" class="radio" name="publish" id="publish_now" value="now" {publish_now_checked}/>&nbsp;<label for="publish_now">{{publish_now}}</label><br />
			<input type="radio" class="radio" name="publish" id="publish_later" value="later" {publish_later_checked}/>&nbsp;<label for="publish_later">{{publish_date}}:</label>
			<input class="input20" type="text" name="activate_day" maxlength="2" value="{activate_day}"/>.
			<input class="input20" type="text" name="activate_month" maxlength="2" value="{activate_month}"/>.
			<input class="input40" type="text" name="activate_year" maxlength="4" value="{activate_year}"/>&nbsp;
			<select name="activate_hour" class="input40">
				{activation_hours}
			</select>&nbsp;{{hour}}&nbsp;{activate_on_message}<br />
			<input type="radio" class="radio" name="publish" id="publish_notnow" value="notnow" {publish_notnow_checked}/>&nbsp;<label for="publish_notnow">{{dont_publish_yet}}</label>
			<div class="buffer"></div>
		</td>
	</tr>
	{hide_publish_end}
	<tr>
		<td><p class="content-title-noshade">{{log_password}}:</p></td>
		<td><input class="input100" type="text" name="log_pw" value="{log_pw}" maxlength="20"/> ({{no_password_label}})</td>
	</tr>
	<tr><td colspan="2"><div class="notice buffer" style="width:500px;height:24px;">{{please_read}}</div></td></tr>
	<tr><td colspan="2"><div class="errormsg"><br />{{creating_cache}}<br /><br /></div></td></tr>
	<tr>
		<td colspan="2">
		<input type="reset" name="reset" value="{{reset}}" class="formbuttons" style="width:120px"/>&nbsp;&nbsp;
		<input type="submit" name="submitform" value="{submit}" class="formbuttons" style="width:130px"/>
		<br /><br /></td>
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
		toggleEditor("desc");
		use_tinymce = 1;
/*		if (document.getElementById("desc").value == '')
			descMode = 1;
		else
			descMode = 2;*/
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
			toggleEditor("desc");
		use_tinymce = 0;
		// convert HTML to text
		var desc = document.getElementById("desc").value;

		desc = html_entity_decode(desc, ['ENT_NOQUOTES']);

		document.getElementById("desc").value = desc;
	}

	function SwitchToHtmlDesc(oldMode)
	{
		document.getElementById("descMode").value = 2;

		if(use_tinymce)
			toggleEditor("desc");
		use_tinymce = 0;

		// convert text to HTML
		var desc = document.getElementById("desc").value;

		if(oldMode != 3)
			desc = htmlspecialchars(desc, ['ENT_NOQUOTES']);

		document.getElementById("desc").value = desc;
	}

	function SwitchToHtmlEditDesc(oldMode)
	{
		document.getElementById("descMode").value = 3;
		use_tinymce = 1;

		if(oldMode == 2) {
			var desc = document.getElementById("desc").value;
			desc = html_entity_decode(desc, ['ENT_NOQUOTES']);
			document.getElementById("desc").value = desc;
		}

		toggleEditor("desc");
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



