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

#outerMapDiv {
	border: 2px solid navy;
	padding:3px;
}

#mapDiv {

	width:300px;
	height:400px;

}
#mapCell {
	border: 2px solid navy; 
	padding:5px;

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

<table width="750" cellspacing="0" cellpadding="0">

	<tr>
	
	
			<td valign="top" rowspan="2">
<!-- Begin Info container -->
			<div id="infoContainer">
			<div id="directions_info"></div>
			</div>
<!-- End Info container -->
		</td>

		<td width="200" valign="top" id="mapCell">
			<div id="outerMapDiv">
				<div id="mapDiv"></div>
			</div>
		</td>
		<td width="200" valign="top" rowspan="2" align="center">
			<div id="buttonContainer">
				<div class="buttonB" onclick="rmOverlays()">Clear Overlays</div>
				<div class="buttonB" onclick="doUnload(1)">Map reset</div>
			</div>
<!-- Begin controls container -->

					<table cellspacing="0" cellpadding="0" >
						<tr>
							<td>
								From:
							</td>
							<td>
								<input  value="Toruń"><br>
							</td>
						</tr>
						<tr>
							<td>
								To: 
							</td>
							<td>
								<input value="Bydgoszcz"><br>
							</td>
						</tr>
						<tr>
							<td>
								Via: 
							</td>
							<td>
								<textarea  rows="2" cols="22"></textarea><br>
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
