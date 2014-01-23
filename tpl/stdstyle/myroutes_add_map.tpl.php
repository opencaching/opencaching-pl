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
        document.forms['myroute_form'].fromaddr.value=document.myram.from.value;
        document.forms['myroute_form'].toaddr.value=document.myram.to.value;

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
<style type="text/css">
table.directions th {
background-color:#EEEEEE;
}
img {
color: #000000;
}
</style>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{add_new_route}}</div>
<div class="searchdiv">

<form action="myroutes_add_map.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr" onsubmit="return checkForm();">
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
<form action="#" name="myram" onsubmit="setDirections(this.from.value, this.to.value); return false">
<table class="content">
<tr>
<td align="right"><span style="font-weight:bold;">Punkt startowy:&nbsp;</span></td>
<td><input type="text" size="25" id="fromAddress" name="from" value="Warszawa"/></td>
<td align="right"><span style="font-weight:bold;">&nbsp;&nbsp;Punkt końcowy:&nbsp;</span></td>
<td align="right"><input type="text" size="25" id="toAddress" name="to" value="Torun" /></td>
<td align="right">&nbsp;&nbsp;<button name="submit" type="submit" value="Get Directions">Wyznacz trasę</button></td>
</tr>
</table>

</form>

<br/>
<table class="directions">
<tr><th>Elementy trasy</th><th>Mapa</th></tr>

<tr>
<td valign="top"><div id="directions" style="width: 275px"></div></td>
<td valign="top"><br/><div id="map_canvas" style="width: 400px; height: 400px"></div></td>

</tr>
</table>
</div>
