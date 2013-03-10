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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/guru.png" class="icon32" alt="" />&nbsp;{{cacheguides}}</div>
<div class="searchdiv">
<span style="font-size: 13px;">
{{guru_01}}
<br/><br/>
{{guru_02}}
<ul>
    <li> {{guru_03}}</li>
    <li> {{guru_04}}</li>
    <li> {{guru_05}}</li>
</ul></br>
{{guru_06}} <br/><br/>
{{guru_07}} <b><font color="blue">{nguides}</font></b> {{guru_08}} <br/>
<span>
</div>
<div class="searchdiv">
<center>
<div style="width:703px;border: 2px solid navy; padding:3px;">
    <div id="map0" style="width:700px;height:500px"></div>
	</div>	
</center>
<br/>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/rating-star.png" alt="rekomendacje" title="rekomendacje"><b>&nbsp{{guru_09}}</b><br/>
</div>
<div class="searchdiv">
<span class="content-title-noshade" style="width: 600px;margin: 10px;line-height: 1.6em;font-size: 12px;">{{guru_10}}
<ul><font color="black">
<li>{{guru_11}}</li>
</font></ul>
&nbsp;&nbsp;&nbsp;{{guru_12}} <a class="links" href="http://www.opencaching.pl/myprofile.php?action=change">{{guru_13}}</a>.
<br/><br/>
&nbsp;&nbsp;&nbsp;{{guru_14}}
</span></div>
<br/>


