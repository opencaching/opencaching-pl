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
<script language="javascript" type="text/javascript">

function sync_options(element)
{

	var recommendations = 0;
	if (document.optionsform.cache_rec[0].checked == true) {
		document.optionsform.cache_min_rec.disabled = 'disabled';
		recommendations = 0;
	}
	else if (document.optionsform.cache_rec[1].checked == true) {
		document.optionsform.cache_min_rec.disabled = false;
		recommendations = document.optionsform.cache_min_rec.value;
	}
	
		document.optionsform.cachesize_2.value = document.optionsform.cachesize_2.checked ? 1 : 0;
		document.optionsform.cachesize_3.value = document.optionsform.cachesize_3.checked ? 1 : 0;
		document.optionsform.cachesize_4.value = document.optionsform.cachesize_4.checked ? 1 : 0;
		document.optionsform.cachesize_5.value = document.optionsform.cachesize_5.checked ? 1 : 0;
		document.optionsform.cachesize_6.value = document.optionsform.cachesize_6.checked ? 1 : 0;
		document.optionsform.cachesize_7.value = document.optionsform.cachesize_7.checked ? 1 : 0;
		document.optionsform.cachevote_1.value = document.optionsform.cachevote_1.value;
		document.optionsform.cachevote_2.value = document.optionsform.cachevote_2.value;
		document.optionsform.cachenovote.value = document.optionsform.cachenovote.checked ? 1 : 0;
		document.optionsform.cachedifficulty_1.value = document.optionsform.cachedifficulty_1.value;
		document.optionsform.cachedifficulty_2.value = document.optionsform.cachedifficulty_2.value;
		document.optionsform.cacheterrain_1.value = document.optionsform.cacheterrain_1.value;
		document.optionsform.cacheterrain_2.value = document.optionsform.cacheterrain_2.value;
	document.optionsform.cacherating.value = recommendations;
	document.optionsform.cachenovote.value = document.optionsform.cachenovote.checked ? 1 : 0;
}
//-->
</script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{search_caches_along_route}}: {route_name}</div>
<form action="myroutes_search.php" method="post" enctype="multipart/form-data" name="optionsform" dir="ltr">
<input type="hidden" name="routeid" value="{routeid}"/>
<input type="hidden" name="distance" value="{distance}"/>

	<input type="hidden" name="f_inactive" value="1" />
	<input type="hidden" name="f_ignored" value="1" />
	<input type="hidden" name="f_userfound" value="1" />
	<input type="hidden" name="f_userowner" value="1" />

	<input type="hidden" name="cachetype" value="111111110" />
	<input type="hidden" name="cache_attribs" value="" />
	<input type="hidden" name="cache_attribs_not" value="" />

	<input type="hidden" name="cachesize_1" value="1" />
	<input type="hidden" name="cachesize_2" value="1" />
	<input type="hidden" name="cachesize_3" value="1" />
	<input type="hidden" name="cachesize_4" value="1" />
	<input type="hidden" name="cachesize_5" value="1" />
	<input type="hidden" name="cachesize_6" value="1" />
	<input type="hidden" name="cachesize_7" value="1" />
	
	<input type="hidden" name="cachevote_1" value="" />
	<input type="hidden" name="cachevote_2" value="" />
	<input type="hidden" name="cachenovote" value="1" />
	
	<input type="hidden" name="cachedifficulty_1" value="" />
	<input type="hidden" name="cachedifficulty_2" value="" />
	<input type="hidden" name="cacheterrain_1" value="" />
	<input type="hidden" name="cacheterrain_2" value="" />
	<input type="hidden" name="cacherating" value="0" />
	<input type="hidden" name="cachename" value="%"  />



<div class="searchdiv">

<p class="content-title-noshade-size3">Opcje wyszukiwania</p>
<div class="searchdiv">
	<table class="table">
		<tr>

			<td>Hide following caches:</td>
			<td colspan="2">
				<input type="checkbox" name="f_userowner" value="1" id="l_userowner" class="checkbox" onclick="javascript:sync_options(this)"  /> <label for="l_userowner">My owned</label>&nbsp;&nbsp;
				<input type="checkbox" name="f_userfound" value="1" id="l_userfound" class="checkbox" onclick="javascript:sync_options(this)"  /> <label for="l_userfound">My finds</label>&nbsp;&nbsp;
				<input type="checkbox" name="f_ignored" value="1" id="l_ignored" class="checkbox" onclick="javascript:sync_options(this)"  checked="checked" > <label for="l_ignored">My ignored</label>&nbsp;&nbsp;
				<input type="checkbox" name="f_inactive" value="1" id="l_inactive" class="checkbox" onclick="javascript:sync_options(this)"  checked="checked" > <label for="l_inactive">Inactive</label>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td valign="top">Cachetype:</td>
			<td>

				<table class="table">
					<tr>
						<td><input type="checkbox" id="cachetype2" name="cachetype2" value="2" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype2">Traditional Cache</label></td>
						<td><input type="checkbox" id="cachetype3" name="cachetype3" value="3" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype3">Multi cache</label></td>
						<td><input type="checkbox" id="cachetype5" name="cachetype5" value="5" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype5">Webcam Cache</label></td>
						<td><input type="checkbox" id="cachetype6" name="cachetype6" value="6" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype6">Event Cache</label></td>

					</tr>
					<tr>
						<td><input type="checkbox" id="cachetype7" name="cachetype7" value="7" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype7">Quiz cache</label></td>
						<td><input type="checkbox" id="cachetype8" name="cachetype8" value="8" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype8">Math/Physics Cache</label></td>
						<td><input type="checkbox" id="cachetype9" name="cachetype9" value="9" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype9">Moving Cache</label></td>
						<td><input type="checkbox" id="cachetype10" name="cachetype10" value="10" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype10">Drive-in Cache</label></td>

					</tr>
					<tr>
						<td><input type="checkbox" id="cachetype4" name="cachetype4" value="4" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype4">Virtual Cache</label></td>
						<td><input type="checkbox" id="cachetype1" name="cachetype1" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype1">unknown cache type</label></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>

				</table>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td valign="top">Cachesize</td>

			<td>
				<table class="table">
					<tr>
						<td>				<input type="checkbox" name="cachesize_2" value="1" id="l_cachesize_2" class="checkbox" onclick="javascript:sync_options(this)" checked="checked" /><label for="l_cachesize_2">Mikro</label>

<input type="checkbox" name="cachesize_3" value="1" id="l_cachesize_3" class="checkbox" onclick="javascript:sync_options(this)" checked="checked" /><label for="l_cachesize_3">Mała</label>
<input type="checkbox" name="cachesize_4" value="1" id="l_cachesize_4" class="checkbox" onclick="javascript:sync_options(this)" checked="checked" /><label for="l_cachesize_4">Normalna</label>
<input type="checkbox" name="cachesize_5" value="1" id="l_cachesize_5" class="checkbox" onclick="javascript:sync_options(this)" checked="checked" /><label for="l_cachesize_5">Duża</label>
<input type="checkbox" name="cachesize_6" value="1" id="l_cachesize_6" class="checkbox" onclick="javascript:sync_options(this)" checked="checked" /><label for="l_cachesize_6">Bardzo duża</label>
<input type="checkbox" name="cachesize_7" value="1" id="l_cachesize_7" class="checkbox" onclick="javascript:sync_options(this)" checked="checked" /><label for="l_cachesize_7">Bez pojemnika</label>
</td>

						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>

			<td valign="top" class="content-title-noshade">Trudność zadań:</td>
			<td class="content-title-noshade">
				od <select name="cachedifficulty_1" class="input40" onchange="javascript:sync_options(this)">
					<option value="1" selected="selected">1</option>
					<option value="1.5">1.5</option>
					<option value="2">2</option>

					<option value="2.5">2.5</option>
					<option value="3">3</option>
					<option value="3.5">3.5</option>
					<option value="4">4</option>
					<option value="4.5">4.5</option>
					<option value="5">5</option>

				</select>
				do <select name="cachedifficulty_2" class="input40" onchange="javascript:sync_options(this)">
					<option value="1">1</option>
					<option value="1.5">1.5</option>
					<option value="2">2</option>
					<option value="2.5">2.5</option>

					<option value="3">3</option>
					<option value="3.5">3.5</option>
					<option value="4">4</option>
					<option value="4.5">4.5</option>
					<option value="5" selected="selected">5</option>
				</select>

			</td>
		</tr>
		<tr><td class="buffer" colspan="3"></td></tr>
		<tr>
			<td valign="top" class="content-title-noshade">Trudność terenu:</td>
			<td class="content-title-noshade">
				od <select name="cacheterrain_1" class="input40" onchange="javascript:sync_options(this)">
					<option value="1" selected="selected">1</option>

					<option value="1.5">1.5</option>
					<option value="2">2</option>
					<option value="2.5">2.5</option>
					<option value="3">3</option>
					<option value="3.5">3.5</option>
					<option value="4">4</option>

					<option value="4.5">4.5</option>
					<option value="5">5</option>
				</select>
				do <select name="cacheterrain_2" class="input40" onchange="javascript:sync_options(this)">
					<option value="1">1</option>
					<option value="1.5">1.5</option>

					<option value="2">2</option>
					<option value="2.5">2.5</option>
					<option value="3">3</option>
					<option value="3.5">3.5</option>
					<option value="4">4</option>
					<option value="4.5">4.5</option>

					<option value="5" selected="selected">5</option>
				</select>
			</td>
		</tr>

	</table>
</div>

<div class="searchdiv">
	<table class="table">

			<td valign="top" class="content-title-noshade">Ocena:</td>
			<td class="content-title-noshade">

				od <select name="cachevote_1" onchange="javascript:sync_options(this)">
	                <option value="-3">słaba</option>
	                <option value="0.5">poniżej przeciętnej</option>
	                <option value="1.2">normalna</option>
	                <option value="2">dobra</option>
	                <option value="2.5">znakomita</option>

				</select>
				do <select name="cachevote_2" onchange="javascript:sync_options(this)">
	                <option value="0.499">słaba</option>
	                <option value="1.199">poniżej przeciętnej</option>
	                <option value="1.999">normalna</option>
	                <option value="2.499">dobra</option>

	                <option value="3.000" selected="selected">znakomita</option>
				</select>
				<input type="checkbox" name="cachenovote" value="1" id="l_cachenovote" class="checkbox" onclick="javascript:sync_options(this)" checked="checked"/><label for="l_cachenovote">Uwzględnij skrzynki bez oceny</label>
			</td>
		</tr>
				<tr><td class="buffer" colspan="3"></td></tr>
		<tr>
			<td class="content-title-noshade">Rekomendacje:</td>

			<td class="content-title-noshade" colspan="2">
				<input type="radio" name="cache_rec" value="0" tabindex="0" id="l_all_caches" class="radio" onclick="javascript:sync_options(this)"  checked="checked" /> <label for="l_all_caches">wszystkie skrzynki</label>&nbsp;
				<input type="radio" name="cache_rec" value="1" tabindex="1" id="l_recommended_caches" class="radio" onclick="javascript:sync_options(this)"  /> <label for="l_recommended_caches">minimalna ilość rekomendacji: </label>&nbsp;
				<input type="text" name="cache_min_rec" value="0" maxlength="3" class="input50" onchange="javascript:sync_options(this)"  disabled="disabled" />
			</td>
		</tr>

	</table>
</div>








</div>
<br/>
			<button type="submit" name="back_list" value="back_list" style="font-size:12px;width:160px"><b>{{back}}</b></button>&nbsp;&nbsp;
			<button type="submit" name="submit" value="submit" style="font-size:12px;width:160px"><b>{{search}}</b></button>
</form>
			<br/><br/><br/>


