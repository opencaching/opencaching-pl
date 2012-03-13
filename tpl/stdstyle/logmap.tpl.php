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
 icon1.image = "tpl/stdstyle/images/google_maps/green.png";
	icon1.iconSize = new GSize(12, 20);
	icon1.iconAnchor = new GPoint(9, 20);
	icon1.infoWindowAnchor=new GPoint(9, 0);
var icon2 = new GIcon();
 icon2.image = "tpl/stdstyle/images/google_maps/red.png";
	icon2.iconSize = new GSize(12, 20);
	icon2.iconAnchor = new GPoint(9, 20);
	icon2.infoWindowAnchor=new GPoint(9, 0);
var icon3 = new GIcon();
 icon3.image = "tpl/stdstyle/images/google_maps/yellow.png";
	icon3.iconSize = new GSize(12, 20);
	icon3.iconAnchor = new GPoint(9, 20);
	icon3.infoWindowAnchor=new GPoint(9, 0);
var icon4 = new GIcon();
 icon4.image = "tpl/stdstyle/images/google_maps/yellow.png";
	icon4.iconSize = new GSize(12, 20);
	icon4.iconAnchor = new GPoint(9, 20);
	icon4.infoWindowAnchor=new GPoint(9, 0);
var icon5 = new GIcon();
 icon5.image = "tpl/stdstyle/images/google_maps/yellow.png";
	icon5.iconSize = new GSize(12, 20);
	icon5.iconAnchor = new GPoint(9, 20);
	icon5.infoWindowAnchor=new GPoint(9, 0);

 
var map0 = new GMap2(document.getElementById("map0"));
map0.addControl(new GSmallMapControl());
map0.addControl(new GMapTypeControl());

map0.setCenter(new GLatLng({mapcenterLat},{mapcenterLon}), {mapzoom});
 

{points}
      }
    }

</script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="" />&nbsp;Mapa 100 najnowszych logów</div>
<br/>
<div class="searchdiv">
<center>
<div style="width:703px;border: 2px solid navy; padding:3px;">
    <div id="map0" style="width:700px;height:500px"></div>
	</div>	
</center>
</div>

<br/>


