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


/* 

  Uwaga ponizsz tresc tego pliku jest tylko forma przykladu bazujaca na GC.com a nie gotowym kodem do uzycia !!!

*/

?>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="" />&nbsp;{{waypoints_cache}} &#8211; {cache_name}</div>

<form action="waypoints.php" method="post" enctype="application/x-www-form-urlencoded" name="waypoints_form" dir="ltr">
<input type="hidden" name="cacheid" value="{cacheid}"/>
<input type="hidden" name="cacheid" value="{wpid}"/>

	
<table width="98%" class="table" border="0">
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
</table>
</form>
