{$pagename=$show_map}

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN"
"http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
  
<html xmlns="http://www.w3.org/1999/xhtml">

<head>	
	<meta name="description" content="Geocaching Opencaching Polska"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="pl" />
	<title>m.Opencaching.pl - {$pagename}</title>	
	<meta name="HandheldFriendly" content="true" />
	<meta name="Viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
	<link rel="stylesheet" type="text/css" href="../lib/style.css" />
    <link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.css" />	
<!--[if lte IE 8]>
    <link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.ie.css" />
<![endif]-->    
    <script src="http://code.leafletjs.com/leaflet-0.3.1/leaflet.js"></script>	
    
    {literal}       
       <style type="text/css">
          html, body, #content {
    			height: 100%;
				}
		  #map { height: 85%;
				}		
       </style>
	{/literal}	
</head>

<body style="max-width:600px; margin:auto; padding: 8px;">
  <div id="content">	
		<b>{$name} ({$smarty.get.wp})</b><hr/>

		<div id="map"></div>
		<hr/>
		<div class='menu'>
			<div class='button'>					
			<a href='./viewcache.php?wp={$wp}'>{$back}</a>					
			</div>
		</div>		
  </div>
  
	<script>
		var map = new L.Map('map');
{literal} 
		var OSMapaUrl = 'http://{s}.osm.trail.pl/{z}/{x}/{y}.png',
			OSMapaAttribution = 'Map data cc-by-sa OpenStreetMap contributors, Hosting: trail.pl',
			osmapa = new L.TileLayer(OSMapaUrl, {maxZoom: 18, attribution: OSMapaAttribution});
{/literal}	
		map.setView(new L.LatLng({$lat}, {$lon}), 13).addLayer(osmapa);


		var markerLocation = new L.LatLng({$lat},{$lon}),
			marker = new L.Marker(markerLocation);

		map.addLayer(marker);
		// marker.bindPopup("<b>{$smarty.get.wp}</b>").openPopup();


		var circleLocation = new L.LatLng(51.508, -0.11),
{literal} 		
			circleOptions = {color: '#f03', opacity: 0.7},
{/literal}				
			circle = new L.Circle(circleLocation, 500, circleOptions);

		circle.bindPopup("I am a circle.");
		// map.addLayer(circle);


		var p1 = new L.LatLng(51.509, -0.08),
			p2 = new L.LatLng(51.503, -0.06),
			p3 = new L.LatLng(51.51, -0.047),
			polygonPoints = [p1, p2, p3],
			polygon = new L.Polygon(polygonPoints);

		polygon.bindPopup("I am a polygon.");
		//map.addLayer(polygon);


		// map.on('click', onMapClick);

		var popup = new L.Popup();

		function onMapClick(e) {
			var latlngStr = '(' + e.latlng.lat.toFixed(3) + ', ' + e.latlng.lng.toFixed(3) + ')';

			popup.setLatLng(e.latlng);
			popup.setContent("You clicked the map at " + latlngStr);
			map.openPopup(popup);
		}
	</script>			
	
	
</body>

</html>