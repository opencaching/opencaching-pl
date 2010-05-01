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

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="" />&nbsp;Dodatkowy waypoint dla skrzynki: {cache_name}</div>
	{general_message}
<form action="editwp.php" method="post" enctype="application/x-www-form-urlencoded" name="waypoints_form" dir="ltr">
<input type="hidden" name="cacheid" value="{cacheid}"/>
<input type="hidden" name="wpid" value="{wpid}"/>

	
<table width="90%" class="table" border="0">
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td class="content-title-noshade">Typ waypointa:</td>
		<td>
			<select name="type" class="input200">
				{typeoptions}
			</select>
		</td>
	</tr>
	<tr><td>&nbsp;</td>
		<td><div class="notice" style="width:500px;min-height:24px;height:auto;"><a class="links" href="http://wiki.opencaching.pl/index.php/Dodatkowe_waypoints_w_skrzynce" target="_blank">Zobacz opis i rodzaje dodatkowych waypointów</a></div></td>
	</tr>

		<tr>
		<td class="content-title-noshade">Numer etapu:</td>
		<td>
		<input type="text" name="stage" maxlength="2" value="{stage}" class="input30" />
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><div class="notice" style="width:400px;height:44px;">Jeśli ten waypoint nie jest kolejnym etapem wymaganym do odnalezienia skrzynki typu multicache lub quiz wstaw wartość 0.</div>
		</td>
	</tr>
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
		<td valign="top" class="content-title-noshade">Opis waypointa:</td>
		<td class="content-title-noshade">
		<textarea name="desc" rows="5" cols="60">{desc}</textarea>{desc_message}</td>
	</td>
	</tr>
	<tr>
		<td valign="top" class="content-title-noshade">Status waypointa:</td>
	</tr>	
	<tr>
		<td vAlign="top" align="left" colSpan="2">
		<table border="0" style="width:600px;font-size: 12px; line-height: 1.6em;">
		<tr><td><input type="radio" name="status" value="1" {checked1} /><label for="status" style="font-size: 12px; line-height: 1.6em;">Pokaż wszystkie informacje waypointa włączając w to współrzędne</label>
		</td></tr>
		<tr><td>
		<input type="radio" name="status" value="2" {checked2} /><label for="status" style="font-size: 12px; line-height: 1.6em;">Pokaż wszystkie informacje waypointa za wyjątkiem współrzędnych</label>
		</td></tr>
		<tr><td>
		<input  type="radio" name="status" value="3" {checked3} /><label for="status" style="font-size: 12px; line-height: 1.6em;">Ukryj ten waypoint w wykazie waypointów skrzynki</label>
		</td></tr></td>
		</table>
<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
			<td valign="top" class="content-title-noshade">&nbsp;</td>
		<td>
			<button type="submit" name="back" value="back" style="font-size:12px;width:140px"><b>Anuluj</b></button>&nbsp;&nbsp;
			<button type="submit" name="delete" value="delete" onclick="return confirm('Czy usunąć ten waypoint?');" style="font-size:12px;width:140px"><b>Usuń waypoint</b></button> &nbsp;&nbsp;
			<button type="submit" name="submit" value="submit" style="font-size:12px;width:140px"><b>Zapisz waypoint</b></button>
		<br /><br /></td>
	</tr>

</table>
</form>
