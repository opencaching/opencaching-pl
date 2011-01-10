<?php
/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*   
	*  UTF-8 ąść
GEvent.addListener(map,'moveend',load() { CheckZoom(); } );

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
var map;
function searchArea(){

	}
	function CheckZoom() {
			var new_zoom=map.getZoom();
			var a=parseFloat(new_zoom);
			var b=parseFloat(old_zoom);
			if (a!=b) {
				showSearchArea();
			}
			old_zoom=new_zoom;
		}


   function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
	map.addMapType(G_PHYSICAL_MAP);
        map.addControl(new GLargeMapControl());
        map.addControl(new GMapTypeControl());
        map.addControl(new GScaleControl());

        var encodedPolyline = new GPolyline.fromEncoded({

          weight: 5,
          points: "<?= $polyline->points ?>",
          levels: "<?= $polyline->levels ?>",
          zoomFactor: <?= $polyline->zoomFactor ?>,
          numLevels: <?= $polyline->numLevels ?>
        });

    var bounds = encodedPolyline.getBounds();
    map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds)); 

        map.addOverlay(encodedPolyline);
//GEvent.addListener(map,'moveend',function CheckZoom();  );
	

			var searchRadius = document.myroute_form.radius.value;
			var p1=map.fromContainerPixelToLatLng(new GPoint(300,300));
			var p2=map.fromContainerPixelToLatLng(new GPoint(300,301));
			var lngdist = p1.distanceFrom(p2);
			var p3=map.fromContainerPixelToLatLng(new GPoint(301,300));
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

	map.addOverlay(searchArea);

      }
    }
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
		return true;
	}
	//-->
</script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{edit_route}}: <span style="color: black;font-size:13px;">{routes_name}</span></div>
	
<form action="myroutes_edit.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr" onsubmit="return checkForm();">
<input type="hidden" name="routeid" value="{routeid}"/>
<input type="hidden" name="MAX_FILE_SIZE" value="51200" />
<div class="searchdiv">
<table class="content">
	<tr>
<td valign='top' width='25%'><span style="font-weight:bold;">{{route_name}}:</span></td>
<td width='75%'><input type='text' name='name' size='50' value='{name}'></td>
</tr>
<tr>
<td valign='top' width='25%'><span style="font-weight:bold;"><span style="font-weight:bold;">{{route_desc}}:</span></td>
<td width='75%'><textarea name='desc' cols='80' rows='3' >{desc}</textarea></td>
</tr>
<tr>
<td valign='top' width='25%'><span style="font-weight:bold;">{{route_radius}} (km):</span></td>
<td width='75%'><input type='text' name='radius' size='5' value='{radius}'>&nbsp;&nbsp;<span class="notice">{{radius_info}}</span></td>
</tr>
	<tr>
		<td valign="top"><span style="font-weight:bold;">{{file_name}} KML:</span></td>
		<td><input class="input200" name="file" type="file" /></td>
	</tr>
<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td valign="top" align="left" colspan="2">
			<button type="submit" name="back" value="back" style="font-size:12px;width:160px"><b>{{cancel}}</b></button>&nbsp;&nbsp;
			<button type="submit" name="submit" value="submit" style="font-size:12px;width:160px"><b>{{save}}</b></button>
		<br /><br /></td>
	</tr>

  </table>
</form>
</div>
<br/>
<div class="searchdiv">
<center>
<div style="width:503px;border: 2px solid navy; padding:3px;">
    <div id="map" style="width:500px;height:500px"></div>
	</div>
</center>
</div>
