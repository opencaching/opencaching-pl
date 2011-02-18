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

map0.setCenter(new GLatLng({mapcenterLat},{mapcenterLon}), 8);
 

{points}
      }
    }

</script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{cacheguru}}</div>
Geocachers who are new to geocaching, future geocachers who are interested but don't know how to go about getting started or people who are interested in talking about geocaching can be easily confused by the amount of information out there and it's not always easy to get a simple understanding of geocaching.

Here you can search for your closest Geocaching Guide / Guru who has been active within the last 90 days and make contact with them to help you understand more of the fun of geocaching.

A Geocaching Guide / Guru is willing to:

    * Help you find out more about geocaching.
    * Help you understand how to go about finding and/or hiding caches as well as logging caches at Geocaching.com and Geocaching Australia.
    * Get out in the field and let you see what's it's all about by letting you 'follow the arrow', 'do the circle dance' and 'get down and dirty'.

Contacting a Geocaching Guide / Guru is a more than just an email or phone conversation. It's also about getting out there and getting all physical. 
<div class="searchdiv">
<center>
<div style="width:703px;border: 2px solid navy; padding:3px;">
    <div id="map0" style="width:700px;height:500px"></div>
	</div>	
</center>
</div>
<br/>


