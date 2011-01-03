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
				if(document.myroute_form.radius.value < 0.5 ||document.myroute_form.radius.value > 5 )
		{
			alert("{{radius_info}}");
			return false;
		}
		if(document.myroute_form.file.value == "")
		{
			alert("{{file_name_info}}");
			return false;
		}

		return true; 
	}
	//-->
</script>


	
<script src=" http://maps.google.com/?file=api&v=2.x&key=ABQIAAAAKzfMHoyn1s1VSuNTwlFfzhTqTxhHAgqKNaAck663VX5jr8OSJBQrTiL58t4Rt3olsGRlxSuqVkU5Xg"
type="text/javascript"></script>

<script type="text/javascript">

var map;
var gdir;
var geocoder = null;
var addressMarker;
var locale = "pl_PL";

function initialize() {
if (GBrowserIsCompatible()) {
map = new GMap2(document.getElementById("map_canvas"));
gdir = new GDirections(map, document.getElementById("directions"));
GEvent.addListener(gdir, "load", onGDirectionsLoad);
GEvent.addListener(gdir, "error", handleErrors);

setDirections("Warszawa", "Torun", "pl_PL");
}
}

function setDirections(fromAddress, toAddress, locale) {
gdir.load("from: " + fromAddress + " to: " + toAddress,
{ "locale": locale });
}

function handleErrors(){
if (gdir.getStatus().code == G_GEO_UNKNOWN_ADDRESS)
alert("No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect.\nError code: " + gdir.getStatus().code);
else if (gdir.getStatus().code == G_GEO_SERVER_ERROR)
alert("A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.\n Error code: " + gdir.getStatus().code);

else if (gdir.getStatus().code == G_GEO_MISSING_QUERY)
alert("The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.\n Error code: " + gdir.getStatus().code);

// else if (gdir.getStatus().code == G_UNAVAILABLE_ADDRESS) <--- Doc bug... this is either not defined, or Doc is wrong
// alert("The geocode for the given address or the route for the given directions query cannot be returned due to legal or contractual reasons.\n Error code: " + gdir.getStatus().code);

else if (gdir.getStatus().code == G_GEO_BAD_KEY)
alert("The given key is either invalid or does not match the domain for which it was given. \n Error code: " + gdir.getStatus().code);

else if (gdir.getStatus().code == G_GEO_BAD_REQUEST)
alert("A directions request could not be successfully parsed.\n Error code: " + gdir.getStatus().code);

else alert("An unknown error occurred.");

}

function onGDirectionsLoad(){
// Use this function to access information about the latest load()
// results.

// e.g.
// document.getElementById("getStatus").innerHTML = gdir.getStatus().code;
// and yada yada yada...
}
</script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{add_new_route}}</div>

<form action="#" onsubmit="setDirections(this.from.value, this.to.value, this.locale.value); return false">

<table>

<tr><th align="right">From:&nbsp;</th>

<td><input type="text" size="25" id="fromAddress" name="from"
value="Warszawa"/></td>
<th align="right">&nbsp;&nbsp;To:&nbsp;</th>
<td align="right"><input type="text" size="25" id="toAddress" name="to"
value="Torun" /></td></tr>

<input name="submit" type="submit" value="Get Directions!" />

</td></tr>
</table>


</form>

<br/>
<table class="directions">
<tr><th>Formatted Directions</th><th>Map</th></tr>

<tr>
<td valign="top"><div id="directions" style="width: 275px"></div></td>
<td valign="top"><div id="map_canvas" style="width: 310px; height: 400px"></div></td>

</tr>
</table>
</div>
