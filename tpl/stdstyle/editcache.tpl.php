<?php
/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder ąś

 ****************************************************************************/
?>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript">
<!--
$(function() {
	chkcountry2();
}); 

var maAttributes = new Array({jsattributes_array});

function chkcountry2(){
	$('#region1').hide();
	$('#regionAjaxLoader').show();
	request = $.ajax({
    	url: "ajaxGetRegionsByCountryCode.php",
    	type: "post",
    	data:{countryCode: $('#country').val(), selectedRegion: '{cache_region}' },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	$('#region1').html(response);
    	console.log(response);
    });
    
    request.always(function () {
		$('#regionAjaxLoader').hide();
		$('#region1').fadeIn(1000);
    });
}

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
function extractregion()
{
		var latNS = document.forms['editcache_form'].latNS.value;
		var lat_h = document.forms['editcache_form'].lat_h.value;
		var lat_min = document.forms['editcache_form'].lat_min.value;
		var lat ;
		lat=(lat_h*1)+(lat_min/60);
		if (latNS=="S") lat=-lat;
		var lonEW = document.forms['editcache_form'].lonEW.value;
		var lon_h = document.forms['editcache_form'].lon_h.value;
		var lon_min = document.forms['editcache_form'].lon_min.value;
		var lon ;
		lon=(lon_h*1)+(lon_min/60);
	        if (lonEW=="W") lon=-lon;
		if (document.editcache_form.lat_h.value == "0" && document.editcache_form.lon_h.value == "0" ) {
		alert("Please input coordinates location of cache"); 
			} else {
	window.open('/region.php?lat=' + lat + '&lon=' + lon+ '&popup=y','Region','width=300,height=250');
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

<!--[if IE 6 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]--> 
<!--[if IE 7 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]--> 
<!--[if IE 8 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]--> 

<form action="editcache.php" method="post" enctype="application/x-www-form-urlencoded" name="editcache_form" dir="ltr">
<input type="hidden" name="cacheid" value="{cacheid}"/>
<input type="hidden" id="cache_attribs" name="cache_attribs" value="{cache_attribs}" />
<input type="hidden" name="show_all_countries" value="{show_all_countries}"/>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" />&nbsp;{{edit_cache}} &#8211; {name}</div>
	{general_message}
	<div class="buffer"></div>
	<div class="content2-container bg-blue02">
			<p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/basic2.png" class="icon32" alt=""/>&nbsp;{{basic_information}}</p>
		</div>
	<div class="buffer"></div>
	<table class="table" border="0">
	<colgroup>
		<col width="180"/>
		<col/>
	</colgroup>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{{status_label}}:</td>
		<td class="content-title-noshade">
			<select name="status" class="input200" {disablestatusoption}>
				{statusoptions}
			</select>{status_message}
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{{name_label}}:</td>
		<td class="content-title-noshade"><input type="text" name="name" value="{name}" maxlength="60" class="input400">{name_message}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">{{cache_type}}:</td>
		<td>
			<select name="type" class="input200" onChange="return _chkVirtual()">
				{typeoptions}
			</select>
		</td>
	</tr>
	<tr>
		<td class="content-title-noshade">{{cache_size}}:</td>
		<td class="content-title-noshade">
			<select name="size" class="input200" onChange="return _chkVirtual()">
				{sizeoptions}
			</select>{size_message}
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td valign="top" class="content-title-noshade">{{coordinates}}:</td>
		<td class="content-title-noshade">
		<fieldset style="border: 1px solid black; width: 65%; height: 32%; background-color: #FAFBDF;">
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
		<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr>
		<td><p class="content-title-noshade">{{country_label}}:</p></td>
		<td>
			<select name="country" id="country" class="input200" onChange="javascript:chkcountry2();">
				{countryoptions}
			</select>
			{show_all_countries_submit}
		</td>                                        
		
	</tr></table>
		
	<table id="regions" class="table">
	<colgroup>
		<col width="180"/>
		<col/>
	</colgroup>

		  <tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr>
		<td><p class="content-title-noshade">{{regiononly}}:</p></td>
		<td>
			<select name="region" id="region1" class="input200">
				
			</select>
			&nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/help.png" class="icon16" alt=""/>&nbsp;<button onclick="return extractregion()">{{region_from_coord}}</button>
			
		</td>
	</tr>
	</table>
	<table class="table">
		  	<colgroup>
		<col width="180"/>
		<col/>
	</colgroup>
	<tr><td colspan="2"><div class="buffer"></div></td></tr>
	<tr><td><p class="content-title-noshade">{{difficulty_level}}:</p></td>
		<td>
			{{task_difficulty}}:
			<select name="difficulty" class="input50">
				{difficultyoptions}
			</select>&nbsp;&nbsp;
			{{terrain_difficulty}}:
			<select name="terrain" class="input50">
				{terrainoptions}
			</select>
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
		<td>
			Geocaching.com: &nbsp;&nbsp;<input type="text" name="wp_gc" value="{wp_gc}" maxlength="7" size="7" />
			Navicache.com: <input type="text" name="wp_nc" value="{wp_nc}" maxlength="6" size="6"/><br/>
			OpenCaching.com: <input type="text" name="wp_tc" value="{wp_tc}" maxlength="7" size="7"/>
			&nbsp;GPSGames.org: <input type="text" name="wp_ge" value="{wp_ge}" maxlength="6" size="6"/>

		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><div class="notice" style="width:500px;height:44px;">{{waypoint_other_portal_info}}</div><div class="buffer"></div></td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="content2-container bg-blue02"><p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/attributes.png" class="icon32" alt=""/>&nbsp;{{cache_attributes}}</p></div>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">{cache_attrib_list}</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
	<td colspan="2"><div class="notice" style="width:500px;min-height:24px;height:auto;">{{attributes_edit_hint}} {{attributes_desc_hint}}</div>
	</td></tr>
	<tr>
		<td colspan="2">
			<div class="content2-container bg-blue02"><p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt=""/>&nbsp;{{descriptions}}</p></div>
			<p class="content-title-noshade"><img src="images/actions/list-add-20.png" align="middle" border="0" alt="" title="Dodaj nowy opis"/>&nbsp;<a href="newdesc.php?cacheid={cacheid_urlencode}">{{add_new_desc}}</a></p>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	{cache_descs}
	{gc_com_refs_start}
	<tr><td colspan="2"><img src="tpl/stdstyle/images/misc/16x16-info.gif" border="0" width="15" height="11" alt="" title=""/><span style="color:red">.</span>
	</td></tr>
	{gc_com_refs_end}
	<tr><td class="buffer" colspan="2"></td></tr>
{waypoints_start}
	<tr>
		<td colspan="2">
			<div class="content2-container bg-blue02"><p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt=""/>&nbsp;{{additional_waypoints}}</p></div>
			<p class="content-title-noshade"><img src="images/actions/list-add-20.png" align="middle" border="0" alt=""/>&nbsp;<a href="newwp.php?cacheid={cacheid}">{{add_new_waypoint}}</a></p>

			</td>
	</tr>

	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">{cache_wp_list}</td>
	</tr>
	<tr>
		<td colspan="2"><br /><div class="notice" style="width:500px;min-height:24px;height:auto;">{{waypoints_about_info}}</div></td>
	</tr>
	{waypoints_end}

	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			<div class="content2-container bg-blue02"><p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt=""/>&nbsp;&nbsp;{{pictures_label}}</p></div>
			<p class="content-title-noshade"><img src="images/actions/list-add-20.png" align="middle" border="0" alt=""/>&nbsp;<a href="newpic.php?objectid={cacheid_urlencode}&type=2&def_seq={def_seq}">{{add_new_pict}}</a></p>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	{pictures}
	<tr><td class="buffer" colspan="2"></td></tr>
<!-- Text container -->
{hidemp3_start}
	<tr>
		<td colspan="2">
			<div class="content2-container bg-blue02"><p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/podcache-mp3.png" class="icon32" alt=""/>&nbsp;&nbsp;{{mp3_label}}</p></div>
			<p class="content-title-noshade"><img src="images/actions/list-add-20.png" align="middle" border="0" alt=""/>&nbsp;<a href="newmp3.php?objectid={cacheid_urlencode}&type=2">{{add_new_mp3}}</a></p>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	{mp3files}

{hidemp3_end}
<!-- End Text Container -->
	<tr>
		<td colspan="2">
			<div class="content2-container bg-blue02"><p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/crypt.png" class="icon32"/>{{other}}</p></div>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
		<fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FFFFFF;">
		<legend>&nbsp; <strong>{{date_hidden_label}}</strong> &nbsp;</legend>
			<input class="input20" type="text" name="hidden_day" maxlength="2" value="{date_day}"/>-
			<input class="input20" type="text" name="hidden_month" maxlength="2" value="{date_month}"/>-
			<input class="input40" type="text" name="hidden_year" maxlength="4" value="{date_year}"/>&nbsp;
			{date_message}
			</fieldset>	
		</td>
	</tr>
	<tr><td colspan="2"><div class="notice buffer" style="width:500px;height:24px;">{{event_hidden_hint}}</div></td></tr>
	{activation_form}
	<tr><td class="spacer" colspan="2"></td></tr>
	{logpw_start}
	<tr>
		<td colspan="2"><br />	
		<fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FFFFFF;">
		<legend>&nbsp; <strong>{{log_password}}</strong> &nbsp;</legend><input class="input100" type="text" name="log_pw" id="log_pw" value="{log_pw}" maxlength="20"/> ({{no_password_label}})
		</fieldset>

		</td>
	</tr>
	<tr><td colspan="2"><div class="notice buffer" style="width:500px;height:24px;">{{please_read}}</div></td></tr>
	{logpw_end}
	<tr><td colspan="2"><div class="errormsg"><br />{{creating_cache}}<br /></div></td></tr>

	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">

			<button type="submit" name="submit" value="{submit}" style="font-size:14px;width:130px"><b>{{store}}</b></button>
		<br /><br /></td>
	</tr>
</table>
</form>
<script type="text/javascript">
<!--
_chkVirtual();
//-->
</script>
