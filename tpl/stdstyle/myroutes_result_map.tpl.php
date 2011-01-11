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
<?php
require_once('./lib/common.inc.php');
require_once('./lib/class.polylineEncoder.php');
$route_id=$_REQUEST['routeid'];

			$rscp = sql("SELECT `lat` ,`lon`
					FROM `route_points`
					WHERE `route_id`='$route_id'");
$p=array();
$points=array();
for ($i = 0; $i < mysql_num_rows($rscp); $i++)
{	
				$record = sql_fetch_array($rscp);
				$y=$record['lon'];
				$x=$record['lat'];		

  $p[0]=$x;
  $p[1]=$y;
  $points[$i]=$p;

}

$encoder = new PolylineEncoder();
$polyline = $encoder->encode($points);
?>
<script type="text/javascript">
<!--

var searchArea= null;
var old_zoom=6;
var map0=null;


   function load() {
      if (GBrowserIsCompatible()) {


 var icon3 = new GIcon();
 icon3.image = "tpl/stdstyle/images/google_maps/gmgreen.gif";
 icon3.shadow = null;
 icon3.iconSize = new GSize(12, 20);
 icon3.shadowSize = new GSize(22, 20);
 icon3.iconAnchor = new GPoint(6, 20);
 icon3.infoWindowAnchor = new GPoint(5, 1);
 
        var map0 = new GMap2(document.getElementById("map0"));
        map0.addControl(new GLargeMapControl());
        map0.addControl(new GMapTypeControl());
        map0.addControl(new GScaleControl());

        var encodedPolyline = new GPolyline.fromEncoded({
          weight: 5,
          points: "<?= $polyline->points ?>",
          levels: "<?= $polyline->levels ?>",
          zoomFactor: <?= $polyline->zoomFactor ?>,
          numLevels: <?= $polyline->numLevels ?>
        });

    var bounds = encodedPolyline.getBounds();
    map0.setCenter(bounds.getCenter(), map0.getBoundsZoomLevel(bounds)); 

   //display route
     map0.addOverlay(encodedPolyline);

//display search Area
			var searchArea= null;
			var searchRadius = document.myroute_form.distance.value;
			var p1=map0.fromContainerPixelToLatLng(new GPoint(300,300));
			var p2=map0.fromContainerPixelToLatLng(new GPoint(300,301));
			var lngdist = p1.distanceFrom(p2);
			var p3=map0.fromContainerPixelToLatLng(new GPoint(301,300));
			var latdist = p1.distanceFrom(p3);
			var pixelWidth = Math.ceil((searchRadius*1000/latdist)*2);

	searchArea =  new GPolyline.fromEncoded({
	 color:'#c0c0c0',
          weight: pixelWidth,
	  opacity: 0.40,
          points: "<?= $polyline->points ?>",
          levels: "<?= $polyline->levels ?>",
          zoomFactor: <?= $polyline->zoomFactor ?>,
          numLevels: <?= $polyline->numLevels ?>
        });

	map0.addOverlay(searchArea);

 var sw = new GLatLng({latlonmin});  
 var ne = new GLatLng({latlonmax});  
 var bounds = new GLatLngBounds(sw, ne);  
 map0.setCenter(bounds.getCenter(), map0.getBoundsZoomLevel(bounds));  
   // display caches
	   {points}


 GEvent.addListener(map0,'moveend',function(){

			var new_zoom=map0.getZoom(); 
			var a=parseFloat(new_zoom);
			var b=parseFloat(old_zoom);
			if (a!=b) {

			if (searchArea) {
				map0.removeOverlay(searchArea);
				searchArea = null;
			}
			var searchRadius = document.myroute_form.distance.value;
			var p1=map0.fromContainerPixelToLatLng(new GPoint(300,300));
			var p2=map0.fromContainerPixelToLatLng(new GPoint(300,301));
			var lngdist = p1.distanceFrom(p2);
			var p3=map0.fromContainerPixelToLatLng(new GPoint(301,300));
			var latdist = p1.distanceFrom(p3);
			var pixelWidth = Math.ceil((searchRadius*1000/latdist)*2);
	searchArea =  new GPolyline.fromEncoded({
	 color:'#c0c0c0',
          weight: pixelWidth,
	  opacity: 0.40,
          points: "<?= $polyline->points ?>",
          levels: "<?= $polyline->levels ?>",
          zoomFactor: <?= $polyline->zoomFactor ?>,
          numLevels: <?= $polyline->numLevels ?>
        });
	map0.addOverlay(searchArea);

			}
			old_zoom=new_zoom;
		});
	   
	}
    }

function check_logs(){
	if (document.myroute_form.cache_log[1].checked == true) {
		if (isNaN(document.myroute_form.nrlogs.value)) {
			alert("Minimalna ilość logów musi być cyfrą!");
			return false;
		} else if (document.myroute_form.nrlogs.value <= 0 || document.myroute_form.nrlogs.value > 999) {
			alert("Dozwolona wartość minimalnej ilości logów musi być z zakresu: 0 - 999");
			return false;
		}
	}
	return true;
}
function sync_options(element)
{
	var nlogs = 0;
	if (document.forms['myroute_form'].cache_log[0].checked == true) {
		document.forms['myroute_form'].nrlogs.disabled = 'disabled';
		nlogs = 0;
	}
	else if (document.forms['myroute_form'].cache_log[1].checked == true) {
		document.forms['myroute_form'].nrlogs.disabled = false;
		nlogs = document.forms['myroute_form'].nrlogs.value;
	}
		document.forms['myroute_form'].logs.value = nlogs;
}	
//-->
</script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{caches_along_route}} ({number_caches}): <span style="color: black;font-size:13px;">{routes_name} ({{radius}} {distance} km)</span></div>

<div class="searchdiv">
<center>
<div style="width:703px;border: 2px solid navy; padding:3px;">
    <div id="map0" style="width:700px;height:500px"></div>
	</div>
</center>
</div>
<br/>
<div class="searchdiv">
<form action="myroutes_search.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr">
<input type="hidden" name="routeid" value="{routeid}"/>
<input type="hidden" name="distance" value="{distance}"/>
<input type="hidden" name="logs" value=""/>
<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
	<tr>
			<td class="content-title-noshade" style="font-size:14px;">{{logs_cache_gpx}}:</td></tr>
<tr>
			<td class="content-title-noshade" style="font-size:12px;" colspan="2">
				<input type="radio" name="cache_log" value="0" tabindex="0" id="l_all_logs_caches" class="radio" onclick="javascript:sync_options(this)" {all_logs_caches} /> <label for="l_all_logs_caches">{{show_all_log_entries}}</label>&nbsp;
				<input type="radio" name="cache_log" value="1" tabindex="1" id="l_minl_caches" class="radio" onclick="javascript:sync_options(this)" {min_logs_caches} /> <label for="l_minl_caches">{{min_logs_cache}}</label>&nbsp;
				<input type="text" name="nrlogs" value="{nrlogs}" maxlength="3" class="input50" onchange="javascript:sync_options(this)" {min_logs_caches_disabled}/>
			</td>
		</tr>
	</table>
</div>
<br/>
			<button type="submit" name="back" value="back" style="font-size:12px;width:160px"><b>{{back}}</b></button>&nbsp;&nbsp;
{list_empty_start}
			<button type="submit" name="submit_gpx" value="submit_gpx" style="font-size:12px;width:160px"><b>{{save_gpx}}</b></button>
{list_empty_end}			
			<br/><br/><br/>
</form>
