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
function initialize() {

if (GBrowserIsCompatible()) {

var icon1 = new GIcon();
 icon1.image = "tpl/stdstyle/images/google_maps/guru.png";
 //icon1.shadow = "tpl/stdstyle/images/google_maps/shadow.png";
	icon1.iconSize = new GSize(20, 34);
	icon1.iconAnchor = new GPoint(9, 34);
	icon1.infoWindowAnchor=new GPoint(9, 0);




 
var map0 = new GMap2(document.getElementById("map0"));
map0.addControl(new GSmallMapControl());
map0.addControl(new GMapTypeControl());

map0.setCenter(new GLatLng({mapcenterLat},{mapcenterLon}), {mapzoom});
 

{points}
      }
    }

</script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/guru.png" class="icon32" alt="" />&nbsp;{{cacheguru}}</div>
<div class="searchdiv">
<span style="font-size: 13px;">
Na mapie możesz poszukać w Twojej okolicy czy jest jakiś geocacher który oferuje swoją pomoc innym jako Przewodnik geocachingu, który był aktywny przez ostatnie 90 dni.
<br/><br/>
Przewodnik Geocaching może:
<ul>
    <li> Pomóc dowiedzieć się więcej o geocachingu,</li>
    <li> Pomóc jak rejestrować / szukać skrzynek na serwisie OC PL,</li>
    <li> Pomoże w terenie zobaczyć o co w tym wszystkim chodzi, jak szukać, jak dobrze ukryć itd.</li>
</ul></br>
Możesz skontaktować sie z Przewodnikiem via Email i umówić się na spotkanie. <br/><br/>
<span>
</div>
<div class="searchdiv">
<center>
<div style="width:703px;border: 2px solid navy; padding:3px;">
    <div id="map0" style="width:700px;height:500px"></div>
	</div>	
</center>
</div>
<br/>


