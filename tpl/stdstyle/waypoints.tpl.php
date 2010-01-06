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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="" />&nbsp;{{waypoints_cache}} &#8211; {name}</div>

<table>

	<tr>
		<td style="HEIGHT: 20px" vAlign="top" align="left" colSpan="2"><font size="+1"><strong>{{add_new_waypoint}}&nbsp;
				</strong></font>&nbsp;
			<a href="./wptlist.aspx"></a>
			&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td vAlign="top" align="left">{{type}}</td>

		<td vAlign="top" align="left"><select name="WaypointEdit$WptTypeList" onchange="javascript:setTimeout('__doPostBack(\'WaypointEdit$WptTypeList\',\'\')', 0)" id="WaypointEdit_WptTypeList">
	<option selected="selected" value="">-- {{choose_waypoint_type}} --</option>
	<option value="220">{{final_ocation}}</option>
	<option value="217">{{parking_area}}</option>
	<option value="218">{{question_to_answer}}</option>
	<option value="452">{{reference_point}}</option>
	<option value="219">{{stages_of_multicache}}</option>
	<option value="221">{{trailhead}}</option>

</select><span id="WaypointEdit_ValidateWaypointType" style="color:Red;visibility:hidden;">*</span></td>
	</tr>
	<tr>
		<td vAlign="top" align="left">{{name}}</td>
		<td vAlign="top" align="left"><input name="WaypointEdit$Name" type="text" maxlength="50" size="40" id="WaypointEdit_Name" />&nbsp;&nbsp;</td>

	</tr>
	<tr>
		<td vAlign="top" align="left" colSpan="2">&nbsp;</td>
	</tr>
	<tr>
		<td vAlign="top" align="left" colSpan="2">{{coordinates}}
			&nbsp;</td>
	</tr>
	<tr>
		<td vAlign="top" align="left" colSpan="2"><table cellpadding="2" border="0" title="">
	<tr>
		<td colspan="4" align="left"></td>

	</tr><tr>
		<td colspan="4" align="left"><select name="WaypointEdit$Coordinates" onchange="__doPostBack('WaypointEdit$Coordinates','ProcessFormatChange')">
			<option value="2">
				Degrees, minutes, seconds (DMS)
			</option><option value="1" selected="True">
				Degrees and minutes (MinDec)
			</option><option value="0">
				Decimal Degrees (DegDec)
			</option>
		</select></td>

	</tr><tr>
		<td><select name="WaypointEdit$Coordinates:_selectNorthSouth" id="WaypointEdit_Coordinates:_selectNorthSouth" title="North or South of equator">
			<option selected="selected" value="1">N</option>
			<option value="-1">S</option>

		</select></td><td><input name="WaypointEdit$Coordinates$_inputLatDegs" type="text" value="00" maxlength="3" size="3" id="WaypointEdit_Coordinates__inputLatDegs" title="Latitude Degrees" />&nbsp;<span id="WaypointEdit_Coordinates__requiredLatDeg" style="color:Red;display:none;">*</span><span id="WaypointEdit_Coordinates__validatorLatDegs" style="color:Red;display:none;">*</span>&nbsp;&deg;&nbsp;</td><td><input name="WaypointEdit$Coordinates$_inputLatMins" type="text" value="00.000" maxlength="10" size="6" id="WaypointEdit_Coordinates__inputLatMins" title="Latitude Minutes" />&nbsp;<span id="WaypointEdit_Coordinates__requiredLatMins" style="color:Red;display:none;">*</span><span id="WaypointEdit_Coordinates__validatorLatMins" style="color:Red;display:none;">*</span>&nbsp;&#39;&nbsp;&nbsp;</td><td>&nbsp;</td>

	</tr><tr>
		<td><select name="WaypointEdit$Coordinates:_selectEastWest" id="WaypointEdit_Coordinates:_selectEastWest" title="East or West of prime meridian">
			<option selected="selected" value="-1">W</option>
			<option value="1">E</option>

		</select></td><td><input name="WaypointEdit$Coordinates$_inputLongDegs" type="text" value="000" maxlength="3" size="3" id="WaypointEdit_Coordinates__inputLongDegs" title="Longitude Degrees" />&nbsp;<span id="WaypointEdit_Coordinates__requiredLongDeg" style="color:Red;display:none;">*</span><span id="WaypointEdit_Coordinates__validatorLongDegs" style="color:Red;display:none;">*</span>&nbsp;&deg;&nbsp;</td><td><input name="WaypointEdit$Coordinates$_inputLongMins" type="text" value="00.000" maxlength="10" size="6" id="WaypointEdit_Coordinates__inputLongMins" title="Longitude Minutes" />&nbsp;<span id="WaypointEdit_Coordinates__requiredLongMins" style="color:Red;display:none;">*</span><span id="WaypointEdit_Coordinates__validatorLongMins" style="color:Red;display:none;">*</span>&nbsp;&#39;&nbsp;&nbsp;</td><td>&nbsp;</td>

	</tr><tr>
		<td colspan="4" align="left"></td>
	</tr>
</table><input name="WaypointEdit$Coordinates:_currentLatLongFormat" type="hidden" value="1" /></td>
	</tr>
	<tr>
		<td vAlign="top" align="left" colSpan="2">{{description}} 
			&nbsp;</td>
	</tr>
	<tr>

		<td vAlign="top" align="left" colSpan="2"><textarea name="WaypointEdit$ShortDescription" rows="5" cols="60" id="WaypointEdit_ShortDescription"></textarea></td>
	</tr>
	<tr>
		<td vAlign="top" align="left" colSpan="2"><table id="WaypointEdit_ViewMethod" border="0" style="width:600px;">
	<tr>
		<td><input id="WaypointEdit_ViewMethod_0" type="radio" name="WaypointEdit$ViewMethod" value="0" checked="checked" /><label for="WaypointEdit_ViewMethod_0">{{Show all information for this waypoint, including coordinates}}</label></td>
	</tr><tr>
		<td><input id="WaypointEdit_ViewMethod_1" type="radio" name="WaypointEdit$ViewMethod" value="1" /><label for="WaypointEdit_ViewMethod_1">{{Show the details of this waypoint but hide the coordinates}}</label></td>

	</tr><tr>
		<td><input id="WaypointEdit_ViewMethod_2" type="radio" name="WaypointEdit$ViewMethod" value="2" /><label for="WaypointEdit_ViewMethod_2">{{Hide this waypoint from view except by the owner or administrator}}</label></td>
	</tr>
</table></td>
	</tr>
	<tr>
		<td vAlign="top" align="left" colSpan="2"></td>
	</tr>
	<tr>

		<td vAlign="top" align="left" colSpan="2">&nbsp;</td>
	</tr>
	<tr>
		<td vAlign="top" align="left" colSpan="2"><input type="submit" name="WaypointEdit$SubmitIt" value="Create Waypoint" onclick="javascript:WebForm_DoPostBackWithOptions(new WebForm_PostBackOptions(&quot;WaypointEdit$SubmitIt&quot;, &quot;&quot;, true, &quot;&quot;, &quot;&quot;, false, false))" id="WaypointEdit_SubmitIt" />&nbsp;
			<input type="submit" name="WaypointEdit$ArchiveIt" value="Remove Waypoint" onclick="javascript:WebForm_DoPostBackWithOptions(new WebForm_PostBackOptions(&quot;WaypointEdit$ArchiveIt&quot;, &quot;&quot;, true, &quot;&quot;, &quot;&quot;, false, false))" id="WaypointEdit_ArchiveIt" /></td>
	</tr>
</table>

