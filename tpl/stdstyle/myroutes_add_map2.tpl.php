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
	function checkForm()
	{
			if(document.myroute_form.name.value == "")
		{
			alert("{{route_name_info}}");
			return false;
		}
				if(document.myroute_form.radius.value < 0.5 ||document.myroute_form.radius.value > 10 )
		{
			alert("{{radius_info}}");
			return false;
		}
		if(document.myroute_form.distance.value =="" )
		{
			alert("{{Trasa nie została wyznaczona}}");
			return false;
		}
		document.forms['myroute_form'].fromaddr.value=document.getElementById('driveFrom').value;
		document.forms['myroute_form'].toaddr.value=document.getElementById('driveTo').value;
		document.forms['myroute_form'].viaaddr.value=document.getElementById('driveVia').value;
		return true; 
	}
	//-->
</script>


<style type="text/css">


#outerMapDiv {
	border: 2px solid navy;
	padding:3px;

}
#mapDiv {
	width:500px;
	height:500px;

}
#mapCell {
/*	border: 2px solid navy; */
	padding:5px;

}

.statusDiv, #link, #mtContainer, #scale, #directions_info, #customMaps_info {
	padding: 3px;
	font: normal 12px courier new;
	text-align:left;
	color: navy;
}

.statusBar, #link, #mtContainer, #scale, #directions_info, #customMaps_info, #opacityContainer, #buttonContainer, #directionsFormTable  {
	border: 1px solid navy;
	margin-left:5px;
	margin-right:5px;
	margin-top:5px;
	background: #eff4f8;
/*	width: 200px;*/
}

#directions_info, #customMaps_info {

	overflow:auto; 
	height:436px;
	width:195px;	

}


#link, #scale {
	text-align:center;
}


/* -------------- Tabs --------------------*/
#tabsContainer {
	text-align: center;
	padding: 2px;
	margin-top: 2px;

}
#tabsTable td{
	text-align: center;
}

#customMapsTabContainer, #directionsTabContainer {
	padding-top: 2px;
}
#directionsTabContainer {

}


.functionsTab, .functionsTabSelected {
	font: bold 10px verdana;
	padding: 2px;
	cursor: pointer;
	border: 1px solid gray;
}
.functionsTab:hover {
	background: #EBB94D;
	color: red;
	border: 1px solid red;
}
.functionsTabSelected {
	background: #9743FF;
	color: #ffffff;

}

/* -------------- end Tabs ----------------*/



#directionsFormTable td{
	font: bold 10px verdana;
	padding: 2px;

}

.ddOption, .ddSelectedOption {
	background: #A6A8CC;
	color: navy;
	border: 2px solid navy;
	text-align:center;
	vertical-align:middle;
	font: bold 10px verdana;
	padding: 2px;
	cursor: pointer;
	margin: 3px;
	z-index:100;
}
.ddSelectedOption {
	border: 2px solid #008000;
	background: #80FF80;
}
.ddOption:hover {
	background: #EBB94D;
	border: 2px solid red;
}


#loadingMessage {
    position: absolute;
    width:  200px;
    text-align: center;
    padding: 10px;
    border: 5px solid #290B8B;
    background: #3F06FA;
	color: #eeeeee;
	font: bold 20px verdana;
    z-index: 1;
	left:0px;
	top:0px;
	opacity: 0.7;

}


.countyInfo, .countyInfoSel {
	font: normal 11px verdana;
	cursor:pointer;
	background: #A6A8CC;
	border: 2px solid navy;
	margin-bottom:5px;
	padding:3px;
}

.countyInfoSel {
	background:#F4E48C;
	border:2px solid #EF3E31;
}

#opacityContainer {
}
#opacityLabel {
	font: normal 12px verdana;
	text-align: center;
	margin: 2px;
}

#opacitySlider {
	border: none;
	background: url(sliderBG_800.jpg) repeat-X;
	cursor: pointer;
	height: 20px;
	text-align: left;
}

#sliderHandle {
	border: 2px solid black;
	width: 5px;
	height: 18px;
	position: relative;
}


/* -------------- Simplify ----------------- */
.button, .selectedButton, .buttonB, .button3 {
	background: #A6A8CC;
	color: navy;
	border: 2px solid navy;
	text-align:center;
	vertical-align:middle;
	font: normal 10px verdana;
	padding: 2px;
	cursor: pointer;
	margin: 3px;
}
.button3 {
	margin: 0px;
}

.selectedButton {
	border: 2px solid #008000;
	background: #80FF80;
	color: #008000;
}
.buttonB {
	border: 2px solid rgb(219,230,241);
    background-color: #7fa2ca ;
	font-weight: bold;
	color: #FFFFFF;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
}
.button:hover, .buttonB:hover, .button3:hover {
	color: #000000;
	font-weight: bold;
	border: 2px solid #7fa2ca;
	background: #dde7f1;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
}

/* ------------------------------------------------- */

/* ------------ Driving directions ----------------- */
.stepRow td {
	border-top: 1px solid #bbbbbb;
	vertical-align: top;
	padding:2px;
	cursor: pointer;
}

.globalSummaryDiv {
	font-weight:bold;
	border: 1px solid navy;
	background: #9cbad6; 
	color: #ffffff;
}

.routeSummaryDiv {
	border: 1px solid navy;
	cursor: pointer;
	background: #fff url(/tpl/stdstyle/images/misc/bg-gradient-blue.png) repeat-x top left;
	/* background: #cccccc; */
}

#detailmap {
	width: 250px;
	height: 150px;
	border:1px solid gray;
}
.bubble {
	font: normal 12px verdana;
	width: 250px;
	height: 150px;
}

#POI_controls {
	font: normal 12px verdana;
	padding:2px;
	text-align: left;
}

#driveVia {
	border: 1px solid gray;
	font: normal 10px verdana;


}

</style>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{setup_new_route}}</div>
<div class="searchdiv">

<div class="searchdiv">
<table class="content">
<tr>
<td align="right"><span style="font-weight:bold;">Punkt startowy:&nbsp;</span></td>
<td><input type="text" size="25" id="driveFrom" name="from" value="Warszawa"/></td>
<td rowspan="2"><span style="font-weight:bold;">Via: </span></td>
<td rowspan="2"><textarea name="via" id="driveVia" rows="2" cols="22"></textarea></td>
<td rowspan="2" align="right">&nbsp;&nbsp;<button name="submit" type="submit" value="Go" onclick="getDirections()">{{setup_new_route}}</button></td>
</tr>
<tr>
<td align="right"><span style="font-weight:bold;">Punkt końcowy:&nbsp;</span></td>
<td align="right"><input type="text" size="25" id="driveTo" name="to" value="Torun" /></td>
</tr>
</table>
</div>

<br/>
<table cellspacing="0" cellpadding="0" id="outerTable">
	<tr>
			<td width="200" valign="top">
			<div id="buttonContainer">
			<div class="buttonB" onclick="rmOverlays()">Wyczyść trasę</div>
			<div class="buttonB" onclick="doUnload(1)">Reset mapy</div>
			</div>
			<div id="directions_info"></div>
		</td>
		<td valign="top">
			<div id="outerMapDiv">
			<div id="mapDiv"></div>		
		</td>		
	</tr>
</table>
<script>
	window.onload = load;
	window.onunload = unload;
	window.onresize = resizePage;
</script>
<div id="loadingMessage" style="display:none;">Wczytuję trasę ...</div>
<br/>

<div class="searchdiv">

<form action="myroutes_add_map2.php" method="request" enctype="multipart/form-data" name="myroute_form" dir="ltr" onsubmit="return checkForm();">
<input type="hidden" name="fromaddr" value=""/>
<input type="hidden" name="toaddr" value="" />
<input type="hidden" name="viaaddr" value="" />
<input type="hidden" name="distance" value="" />
<table class="content">
	<tr>
<td valign='top' width='25%'><span style="font-weight:bold;">{{route_name}}:</span></td>
<td width='75%'><input type='text' name='name' size='50' value=''></td>
</tr>
<tr>
<td valign='top' width='25%'><span style="font-weight:bold;">{{route_desc}}:</span></td>
<td width='75%'><textarea name='desc' cols='80' rows='3'></textarea></td>
</tr>
<tr>
<td valign='top' width='25%'><span style="font-weight:bold;">{{route_radius}} (km):</span></td>
<td width='75%'><input type='text' name='radius' size='5' value=''>&nbsp;&nbsp;<span class="notice">{{radius_info}}</span></td>
</tr>
<tr>
<td valign="top" align="left" colspan="2"><br /><br />
	<button type="submit" name="submitform" value="submit"  style="font-size:12px;width:160px"><b>{{save_route}}</b></button>
<br /></td>
	</tr>
</table><br/>
</form>
</div>
</div>
