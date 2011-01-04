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

<div id="loadingMessage" style="display:none;">Loading ...</div>





<script>
	window.onload = load;
	window.onunload = unload;
	window.onresize = resizePage;

</script>







</div>
