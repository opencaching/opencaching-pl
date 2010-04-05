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

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="" />&nbsp;{{waypoints_cache}} &#8211; {name}</div>

<table cellSpacing="0" cellPadding="0" width="100%">
	<tr>
<form name="Form1" method="post" action="waypoints.php" onsubmit="javascript:return WebForm_OnSubmit();" id="Form1">
<div>
<input type="hidden" name="__EVENTTARGET" id="__EVENTTARGET" value="" />
<input type="hidden" name="__EVENTARGUMENT" id="__EVENTARGUMENT" value="" />
<input type="hidden" name="__LASTFOCUS" id="__LASTFOCUS" value="" />
<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="" />
</div>

<script type="text/javascript">
//<![CDATA[
var theForm = document.forms['Form1'];
if (!theForm) {
    theForm = document.Form1;
}
function __doPostBack(eventTarget, eventArgument) {
    if (!theForm.onsubmit || (theForm.onsubmit() != false)) {
        theForm.__EVENTTARGET.value = eventTarget;
        theForm.__EVENTARGUMENT.value = eventArgument;
        theForm.submit();
    }
}
//]]>
</script>


<script type="text/javascript">
//<![CDATA[
function WebForm_OnSubmit() {
if (typeof(ValidatorOnSubmit) == "function" && ValidatorOnSubmit() == false) return false;
return true;
}
//]]>
</script>

			<td vAlign="top" align="left">
				<span >
					<span>{{Waypoint_collection}}</span>

				</span>
				<p></p>
				<p>
					{{for_geocache}}: {name} </font></strike></a> (Traditional Cache)
				</p>
				<P>


<table>

	<tr>
<td style="HEIGHT: 20px" vAlign="top" align="left" colSpan="2"><font size="+1"><strong>{{add_new_waypoint}}&nbsp;</strong></font>&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td vAlign="top" align="left">{{type}}</td>

		<td vAlign="top" align="left"><select name="WaypointEdit$WptTypeList" onchange="javascript:setTimeout('__doPostBack(\'WaypointEdit$WptTypeList\',\'\')', 0)" id="WaypointEdit_WptTypeList">
	<option selected="selected" value="">-- {{choose_waypoint_type}} --</option>
	<option value="1">{{final_location}}</option>
	<option value="2">{{parking_area}}</option>
	<option value="3">{{reference_point}}</option>
	<option value="4">{{stages_of_multicache}}</option>


</select><span id="WaypointEdit_ValidateWaypointType" style="color:Red;visibility:hidden;">*</span></td>
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
		<td><input id="WaypointEdit_ViewMethod_0" type="radio" name="WaypointEdit$ViewMethod" value="1" checked="checked" /><label for="WaypointEdit_ViewMethod_0">{{Show all information for this waypoint, including coordinates}}</label></td>
	</tr><tr>
		<td><input id="WaypointEdit_ViewMethod_1" type="radio" name="WaypointEdit$ViewMethod" value="2" /><label for="WaypointEdit_ViewMethod_1">{{Show the details of this waypoint but hide the coordinates}}</label></td>

	</tr><tr>
		<td><input id="WaypointEdit_ViewMethod_2" type="radio" name="WaypointEdit$ViewMethod" value="3" /><label for="WaypointEdit_ViewMethod_2">{{Hide this waypoint from view except by the owner or administrator}}</label></td>
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
<P></P>
</P>
				<P></P>

				<P>&nbsp;</P>
			</td>
		
<script type="text/javascript">
//<![CDATA[
var Page_Validators =  new Array(document.getElementById("WaypointEdit_ValidateWaypointType"), document.getElementById("WaypointEdit_RegularExpressionValidator1"), document.getElementById("WaypointEdit_ValidatePrefixAlpha"), document.getElementById("WaypointEdit_Coordinates__requiredLatDeg"), document.getElementById("WaypointEdit_Coordinates__validatorLatDegs"), document.getElementById("WaypointEdit_Coordinates__requiredLatMins"), document.getElementById("WaypointEdit_Coordinates__validatorLatMins"), document.getElementById("WaypointEdit_Coordinates__requiredLongDeg"), document.getElementById("WaypointEdit_Coordinates__validatorLongDegs"), document.getElementById("WaypointEdit_Coordinates__requiredLongMins"), document.getElementById("WaypointEdit_Coordinates__validatorLongMins"));
//]]>
</script>

<script type="text/javascript">
//<![CDATA[
var WaypointEdit_ValidateWaypointType = document.all ? document.all["WaypointEdit_ValidateWaypointType"] : document.getElementById("WaypointEdit_ValidateWaypointType");
WaypointEdit_ValidateWaypointType.errormessage = "There is an error with your waypoint type selection";
WaypointEdit_ValidateWaypointType.evaluationfunction = "CustomValidatorEvaluateIsValid";
var WaypointEdit_RegularExpressionValidator1 = document.all ? document.all["WaypointEdit_RegularExpressionValidator1"] : document.getElementById("WaypointEdit_RegularExpressionValidator1");
WaypointEdit_RegularExpressionValidator1.controltovalidate = "WaypointEdit_WptCode";
WaypointEdit_RegularExpressionValidator1.errormessage = "Please use A-Z or 0-9 in your lookup code";
WaypointEdit_RegularExpressionValidator1.evaluationfunction = "RegularExpressionValidatorEvaluateIsValid";
WaypointEdit_RegularExpressionValidator1.validationexpression = "^[a-zA-Z0-9]+$";
var WaypointEdit_ValidatePrefixAlpha = document.all ? document.all["WaypointEdit_ValidatePrefixAlpha"] : document.getElementById("WaypointEdit_ValidatePrefixAlpha");
WaypointEdit_ValidatePrefixAlpha.controltovalidate = "WaypointEdit_PrefixCode";
WaypointEdit_ValidatePrefixAlpha.errormessage = "Please use A-Z or 0-9 in your Prefix Code";
WaypointEdit_ValidatePrefixAlpha.evaluationfunction = "RegularExpressionValidatorEvaluateIsValid";
WaypointEdit_ValidatePrefixAlpha.validationexpression = "^[a-zA-Z0-9]+$";
var WaypointEdit_Coordinates__requiredLatDeg = document.all ? document.all["WaypointEdit_Coordinates__requiredLatDeg"] : document.getElementById("WaypointEdit_Coordinates__requiredLatDeg");
WaypointEdit_Coordinates__requiredLatDeg.controltovalidate = "WaypointEdit_Coordinates__inputLatDegs";
WaypointEdit_Coordinates__requiredLatDeg.errormessage = "Missing latitude degrees";
WaypointEdit_Coordinates__requiredLatDeg.display = "Dynamic";
WaypointEdit_Coordinates__requiredLatDeg.evaluationfunction = "RequiredFieldValidatorEvaluateIsValid";
WaypointEdit_Coordinates__requiredLatDeg.initialvalue = "";
var WaypointEdit_Coordinates__validatorLatDegs = document.all ? document.all["WaypointEdit_Coordinates__validatorLatDegs"] : document.getElementById("WaypointEdit_Coordinates__validatorLatDegs");
WaypointEdit_Coordinates__validatorLatDegs.controltovalidate = "WaypointEdit_Coordinates__inputLatDegs";
WaypointEdit_Coordinates__validatorLatDegs.errormessage = "Invalid latitude degrees";
WaypointEdit_Coordinates__validatorLatDegs.display = "Dynamic";
WaypointEdit_Coordinates__validatorLatDegs.type = "Integer";
WaypointEdit_Coordinates__validatorLatDegs.evaluationfunction = "RangeValidatorEvaluateIsValid";
WaypointEdit_Coordinates__validatorLatDegs.maximumvalue = "180";
WaypointEdit_Coordinates__validatorLatDegs.minimumvalue = "0";
var WaypointEdit_Coordinates__requiredLatMins = document.all ? document.all["WaypointEdit_Coordinates__requiredLatMins"] : document.getElementById("WaypointEdit_Coordinates__requiredLatMins");
WaypointEdit_Coordinates__requiredLatMins.controltovalidate = "WaypointEdit_Coordinates__inputLatMins";
WaypointEdit_Coordinates__requiredLatMins.errormessage = "Missing latitude minutes";
WaypointEdit_Coordinates__requiredLatMins.display = "Dynamic";
WaypointEdit_Coordinates__requiredLatMins.evaluationfunction = "RequiredFieldValidatorEvaluateIsValid";
WaypointEdit_Coordinates__requiredLatMins.initialvalue = "";
var WaypointEdit_Coordinates__validatorLatMins = document.all ? document.all["WaypointEdit_Coordinates__validatorLatMins"] : document.getElementById("WaypointEdit_Coordinates__validatorLatMins");
WaypointEdit_Coordinates__validatorLatMins.controltovalidate = "WaypointEdit_Coordinates__inputLatMins";
WaypointEdit_Coordinates__validatorLatMins.errormessage = "Invalid latitude minutes";
WaypointEdit_Coordinates__validatorLatMins.display = "Dynamic";
WaypointEdit_Coordinates__validatorLatMins.type = "Double";
WaypointEdit_Coordinates__validatorLatMins.decimalchar = ".";
WaypointEdit_Coordinates__validatorLatMins.evaluationfunction = "RangeValidatorEvaluateIsValid";
WaypointEdit_Coordinates__validatorLatMins.maximumvalue = "60";
WaypointEdit_Coordinates__validatorLatMins.minimumvalue = "0";
var WaypointEdit_Coordinates__requiredLongDeg = document.all ? document.all["WaypointEdit_Coordinates__requiredLongDeg"] : document.getElementById("WaypointEdit_Coordinates__requiredLongDeg");
WaypointEdit_Coordinates__requiredLongDeg.controltovalidate = "WaypointEdit_Coordinates__inputLongDegs";
WaypointEdit_Coordinates__requiredLongDeg.errormessage = "Missing longitude degrees";
WaypointEdit_Coordinates__requiredLongDeg.display = "Dynamic";
WaypointEdit_Coordinates__requiredLongDeg.evaluationfunction = "RequiredFieldValidatorEvaluateIsValid";
WaypointEdit_Coordinates__requiredLongDeg.initialvalue = "";
var WaypointEdit_Coordinates__validatorLongDegs = document.all ? document.all["WaypointEdit_Coordinates__validatorLongDegs"] : document.getElementById("WaypointEdit_Coordinates__validatorLongDegs");
WaypointEdit_Coordinates__validatorLongDegs.controltovalidate = "WaypointEdit_Coordinates__inputLongDegs";
WaypointEdit_Coordinates__validatorLongDegs.errormessage = "Invalid longitude degrees";
WaypointEdit_Coordinates__validatorLongDegs.display = "Dynamic";
WaypointEdit_Coordinates__validatorLongDegs.type = "Integer";
WaypointEdit_Coordinates__validatorLongDegs.evaluationfunction = "RangeValidatorEvaluateIsValid";
WaypointEdit_Coordinates__validatorLongDegs.maximumvalue = "180";
WaypointEdit_Coordinates__validatorLongDegs.minimumvalue = "0";
var WaypointEdit_Coordinates__requiredLongMins = document.all ? document.all["WaypointEdit_Coordinates__requiredLongMins"] : document.getElementById("WaypointEdit_Coordinates__requiredLongMins");
WaypointEdit_Coordinates__requiredLongMins.controltovalidate = "WaypointEdit_Coordinates__inputLongMins";
WaypointEdit_Coordinates__requiredLongMins.errormessage = "Missing longitude minutes";
WaypointEdit_Coordinates__requiredLongMins.display = "Dynamic";
WaypointEdit_Coordinates__requiredLongMins.evaluationfunction = "RequiredFieldValidatorEvaluateIsValid";
WaypointEdit_Coordinates__requiredLongMins.initialvalue = "";
var WaypointEdit_Coordinates__validatorLongMins = document.all ? document.all["WaypointEdit_Coordinates__validatorLongMins"] : document.getElementById("WaypointEdit_Coordinates__validatorLongMins");
WaypointEdit_Coordinates__validatorLongMins.controltovalidate = "WaypointEdit_Coordinates__inputLongMins";
WaypointEdit_Coordinates__validatorLongMins.errormessage = "Invalid longitude minutes";
WaypointEdit_Coordinates__validatorLongMins.display = "Dynamic";
WaypointEdit_Coordinates__validatorLongMins.type = "Double";
WaypointEdit_Coordinates__validatorLongMins.decimalchar = ".";
WaypointEdit_Coordinates__validatorLongMins.evaluationfunction = "RangeValidatorEvaluateIsValid";
WaypointEdit_Coordinates__validatorLongMins.maximumvalue = "60";
WaypointEdit_Coordinates__validatorLongMins.minimumvalue = "0";
//]]>
</script>


<script type="text/javascript">
//<![CDATA[

var Page_ValidationActive = false;
if (typeof(ValidatorOnLoad) == "function") {
    ValidatorOnLoad();
}

function ValidatorOnSubmit() {
    if (Page_ValidationActive) {
        return ValidatorCommonOnSubmit();
    }
    else {
        return true;
    }
}
        //]]>
</script>
</form>
		&nbsp;

	</tr>
</table>

