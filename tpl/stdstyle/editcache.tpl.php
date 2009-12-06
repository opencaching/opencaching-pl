<?php
/***************************************************************************
											./tpl/stdstyle/editcache.tpl.php
															-------------------
		begin                : Mon July 6 2004
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

	 edit a cache listing

	 template replacement(s):

			cacheid
			show_all_countries
			name
			typeoptions
			sizeoptions
			selLatN
			selLatS
			selLonE
			selLonW
			lat_h
			lat_min
			lon_h
			lon_min
			lon_message
			lat_message
			countryoptions
			show_all_countries_submit
			difficultyoptions
			terrainoptions
			cache_descs
			date_day
			date_month
			date_year
			date_message
			reset
			submit
			cacheid_urlencode
			statusoptions
			search_time
			way_length
			styleoptions

 ****************************************************************************/
?>
<script type="text/javascript">
<!--
var maAttributes = new Array({jsattributes_array});

function _chkVirtual () 
{
  if (document.editcache_form.type.value == "4" || document.editcache_form.type.value == "5" || document.editcache_form.type.value == "6" || ({other_nobox} && document.editcache_form.type.value == "1") ) 
	{
		if( document.editcache_form.size.options[document.editcache_form.size.options.length - 1].value != "7" )
		{
			document.editcache_form.size.options[document.editcache_form.size.options.length] = new Option('Bez pojemnika', '7');
		}
		
		if( !({other_nobox} && document.editcache_form.type.value == "1"))
		{
			document.editcache_form.size.value = "7";
			document.editcache_form.size.disabled = true;
		}
		else
			document.editcache_form.size.disabled = false;
  }
  else
  {
		if( document.editcache_form.size.options[document.editcache_form.size.options.length - 1].value == "7" )
			document.editcache_form.size.options[document.editcache_form.size.options.length - 1 ] = null;
		document.editcache_form.size.disabled = false;
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
<form action="editcache.php" method="post" enctype="application/x-www-form-urlencoded" name="editcache_form" dir="ltr">
<input type="hidden" name="cacheid" value="{cacheid}"/>
<input type="hidden" id="cache_attribs" name="cache_attribs" value="{cache_attribs}" />
<input type="hidden" name="show_all_countries" value="{show_all_countries}"/>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" border="0" width="32" height="32" alt="" title="" align="middle"/>&nbsp;{edit_cache} &#8211; {name}</div>
	{general_message}
	<div class="buffer"></div>
	<div class="content2-container line-box">
			<p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/basic2.png" width="32" height="32" align="middle" border="0" alt=""/>&nbsp;{basic_information}</p>
		</div>

	<div class="buffer"></div>
	<table class="table" border="0">
	<colgroup>
		<col width="180"/>
		<col/>
	</colgroup>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{status_label}:</td>
		<td class="content-title-noshade">
			<select name="status" class="input200" {disablestatusoption}>
				{statusoptions}
			</select>{status_message}
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{name_label}:</td>
		<td class="content-title-noshade"><input type="text" name="name" value="{name}" maxlength="60" class="input400">{name_message}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{cache_type}:</td>
		<td>
			<select name="type" class="input200" onChange="return _chkVirtual()">
				{typeoptions}
			</select>
		</td>
	</tr>
	<tr>
		<td class="content-title-noshade">{cache_size}:</td>
		<td class="content-title-noshade">
			<select name="size" class="input200" onChange="return _chkVirtual()">
				{sizeoptions}
			</select>{size_message}
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td valign="top" class="content-title-noshade">{coordinates}:</td>
		<td class="content-title-noshade">
			<select name="latNS" class="input40">
				<option value="N"{selLatN}>N</option>
				<option value="S"{selLatS}>S</option>
			</select>
			&nbsp;<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />
			&deg;&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input50" />&nbsp;'&nbsp;
			{lat_message}
			&nbsp;&nbsp;
			<select name="lonEW" class="input40">
				<option value="E"{selLonE}>E</option>
				<option value="W"{selLonW}>W</option>
			</select>
			&nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />
			&deg;&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input50" />&nbsp;'&nbsp;
			{in_wgs84_system} {lon_message}
		</td>
	</tr>
		<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr>
		<td><p class="content-title-noshade">{country_label}:</p></td>
		<td>
			<select name="country" class="input200">
				{countryoptions}
			</select>
			{show_all_countries_submit}
		</td>
	</tr>
	<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr><td><p class="content-title-noshade">{difficulty_level}:</p></td>
		<td>
			{task_difficulty}:
			<select name="difficulty" class="input50">
				{difficultyoptions}
			</select>&nbsp;&nbsp;
			{terrain_difficulty}:
			<select name="terrain" class="input50">
				{terrainoptions}
			</select>
		</td>
	</tr>
		<tr>
		<td>&nbsp;</td>
		<td><div class="notice" style="width:500px;height:44px;">{difficulty_problem} <a href="rating-c.php" target="_BLANK">{rating_system}</a>.</div>
		</td>
	</tr>
	<tr><td><p class="content-title-noshade">{additional_information} ({optional}):</p></td>
	    <td>
				{time}:
				<input type="text" name="search_time" maxlength="10" value="{search_time}" class="input30" /> h
				&nbsp;&nbsp;
				{length}:
				<input type="text" name="way_length" maxlength="10" value="{way_length}" class="input30" /> km &nbsp; {effort_message}
			</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><div class="notice" style="width:500px;height:44px">{time_distance_hint}</div><div class="buffer"></div></td>
	</tr>
	<tr>
		<td><p class="content-title-noshade">{waypoint} ({optional}):</p></td>
		<td>geocaching.com: <input type="text" name="wp_gc" value="{wp_gc}" maxlength="7" class="input50"/>
			gpsgames.org: <input type="text" name="wp_nc" value="{wp_nc}" maxlength="6" class="input50"/>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><div class="notice" style="width:500px;height:44px;">{waypoint_other_portal_info}</div><div class="buffer"></div></td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="content2-container line-box"><p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/attributes.png" width="32" height="32" align="middle" border="0" alt=""/>&nbsp;{cache_attributes}</p></div>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">{cache_attrib_list}</td>
	</tr>
	<tr>
		<td colspan="2"><div class="notice" style="width:500px;min-height:24px;height:auto;"> {additional_attributes_hint} <a href="cache-atr.php" target="_BLANK">{additional_attributes}</a>. {attributes_desc_hint}</div></td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			<div class="content2-container line-box"><img src="tpl/stdstyle/images/blue/describe.png" width="32" height="32" align="middle" border="0" alt=""/>&nbsp;{description}</p></div>
			<p class="content-title-noshade"><img src="tpl/stdstyle/images/action/16x16-adddesc.png" width="16" height="16" align="middle" border="0" align="Dodaj nowy opis" title="Dodaj nowy opis"/>&nbsp;<a href="newdesc.php?cacheid={cacheid_urlencode}"/>{add_new_desc}</a></p>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	{cache_descs}
	{gc_com_refs_start}
	<tr><td colspan="2"><img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Hinweis" title="Hinweis"/><span style="color:red">.</span>
	</td></tr>
	{gc_com_refs_end}
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			<div class="content2-container line-box"><p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/picture.png" width="32" height="32" align="middle" border="0" alt=""/>&nbsp;&nbsp;{pictures_label}</p></div>
			<p class="content-title-noshade"><img src="tpl/stdstyle/images/action/16x16-addimage.png" width="16" height="16" align="middle" border="0" alt=""/>&nbsp;<a href="newpic.php?objectid={cacheid_urlencode}&type=2">{add_new_pict}</a></p>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	{pictures}
	<tr>
		<td colspan="2">
			<div class="content2-container line-box"><p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/crypt.png" width="32" height="32" align="middle" border="0"/>{other}</p></div>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{date_hidden_label}:</td>
		<td>
			<input class="input20" type="text" name="hidden_day" maxlength="2" value="{date_day}"/>.
			<input class="input20" type="text" name="hidden_month" maxlength="2" value="{date_month}"/>.
			<input class="input40" type="text" name="hidden_year" maxlength="4" value="{date_year}"/>&nbsp;
			{date_message}
		</td>
	</tr>
	<tr><td colspan="2"><div class="notice buffer" style="width:500px;height:24px;">{event_hidden_hint}</div></td></tr>
	{activation_form}
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td><p class="content-title-noshade">{log_password}:</p></td>
		<td><input class="input100" type="text" name="log_pw" value="{log_pw}" maxlength="20"/> ({no_password_label})</td>
	</tr>
	<tr><td colspan="2"><div class="notice buffer" style="width:500px;height:24px;">{please_read}</div></td></tr>
	<tr><td colspan="2"><div class="errormsg"><br>{creating_cache}<br><br></div></td></tr>

	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			<input type="reset" name="reset" value="{reset}" class="formbuttons"/>&nbsp;&nbsp;
			<input type="submit" name="submit" value="{submit}" class="formbuttons"/>
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">
<!--
_chkVirtual();
//-->
</script>