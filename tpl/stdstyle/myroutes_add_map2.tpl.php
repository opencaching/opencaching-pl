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
<script src="lib/js/gmap.js" type="text/javascript"></script>


<style type="text/css">

a, span, div, body {
	text-decoration: none;
}
ul {
	margin:0px;
}
img {
	border:none;
}
body,html {
	margin:0px;
	width: 100%;
	height: 100%;
	font-family: verdana;
}
input, select {
	border: 1px solid navy;
}

.topRowCell {
	border: 1px solid navy;
}
.topRowDiv {
	background: #dddddd;
	height:100px;
	margin:5px;
	padding: 3px;
	font: normal 12px courier new;
	text-align:left;
	color: navy;
}

#top1 {
	font: bold 14px courier new;
	text-align:center;

}
.Row2Cell {
	padding-left: 5px;
	padding-right: 5px;
}


#outerMapDiv {
	border: 2px solid navy;
	padding:3px;
}
#mapDiv {
/*
	width:100%;
	height:580px;
*/
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
	background: #dddddd;
}

#directions_info, #customMaps_info {
	height: 585px;
	overflow:auto;
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
	border: 2px solid #ECB052;
	background: #F6D84C;

}
.button:hover, .buttonB:hover, .button3:hover {
	background: #EBB94D;
	color: red;
	border: 2px solid red;
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
	border: 1px solid navy;
	background: #888888;
	color: #ffffff;
}

.routeSummaryDiv {
	border: 1px solid navy;
	cursor: pointer;
	background: #cccccc;
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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{add_new_route}}</div>
<div class="searchdiv">

<form action="myroutes_add_map2.php" method="request" enctype="multipart/form-data" name="myroute_form" dir="ltr" onsubmit="return checkForm();">
<input type="hidden" name="fromaddr" value=""/>
<input type="hidden" name="toaddr" value="" />
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
<td valign="top" align="left" colspan="2">
	<button type="submit" name="submitform" value="submit"  style="font-size:12px;width:160px"><b>{{save_route}}</b></button>
		<br /><br /></td>
	</tr>
</table><br/>
</form>
<form action="#" name="myram">

<table cellspacing="0" cellpadding="0" id="outerTable">
<colgroup>
	<col width="200">
	<col id="mapCol">
	<col width="260">
</colgroup>



	<tr>
	
	
			<td valign="top" rowspan="2">
<!-- Begin Info container -->
			<div id="infoContainer">
				<div id="directions_info"></div>
			</div>
<!-- End Info container -->
		</td>

		<td valign="top" id="mapCell">
			<div id="outerMapDiv">
				<div id="mapDiv"></div>
			</div>
		</td>
		<td valign="top" rowspan="2" align="center">
			<div id="buttonContainer">
				<div class="buttonB" onclick="rmOverlays()">Clear Overlays</div>
				<div class="buttonB" onclick="doUnload(1)">Map reset</div>
			</div>
<!-- Begin controls container -->
			<div  id="controlsContainer">
				<div id="directions_controls">
					<table cellspacing="0" cellpadding="0" id="directionsFormTable">
						<tr>
							<td>
								From:
							</td>
							<td>
								<input id="driveFrom" value="Toru�"><br>
							</td>
						</tr>
						<tr>
							<td>
								To: 
							</td>
							<td>
								<input id="driveTo" value="Bydgoszcz"><br>
							</td>
						</tr>
						<tr>
							<td>
								Via: 
							</td>
							<td>
								<textarea id="driveVia" rows="2" cols="22"></textarea><br>
							</td>
						</tr>
						<tr>
							<td>
								Lang: 
							</td>
							<td>
								<select id="locale" name="locale">
									<option value="pl" selected>Polski</option>
									<option value="en">English</option>

								</select><br>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="button" value="GO" onclick="getDirections()">
							</td>
						</tr>
					</table>
				</div>
			</div>
<!-- End controls container -->
		</td>
		
	</tr>
</table>
<script>
	window.onload = load;
	window.onunload = unload;
	window.onresize = resizePage;

</script>
<div id="loadingMessage" style="display:none;">Loading ...</div>












</div>
