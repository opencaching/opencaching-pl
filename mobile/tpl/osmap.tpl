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
		  .leaflet-control-layers-base label { text-align: left ;
		       }		
		  .leaflet-control-layers-overlays label { text-align: left ;
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

		var cacheLocation = new L.LatLng({$lat},{$lon}) ;

{literal} 

		var osmAttribution = 'Map data: (cc-by-sa) OpenStreetMap.org contributors' ;
		var osmOptions = {maxZoom: 18, attribution: osmAttribution};

		// OSM (OSMapa.pl)
		var OSMapaUrl = 'http://{s}.osm.trail.pl/{z}/{x}/{y}.png',
			osmapa = new L.TileLayer(OSMapaUrl, osmOptions);

		// OSM (Mapnik)
		var mapnikUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			mapnik = new L.TileLayer(mapnikUrl, osmOptions);

		// shadow overlay
		var shadowUrl = 'http://toolserver.org/%7Ecmarqu/hill/{z}/{x}/{y}.png',
			shadow = new L.TileLayer(shadowUrl, osmOptions);		
			
		// OC.pl overlay
		var ocplUrl = "http://opencaching.pl/lib/cgi-bin/mapper.fcgi?z={z}&x={x}&y={y}&sc=0&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_owncache=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&waypoints=false&be_ftf=false&h_de=false&h_pl=true&h_se=false&h_no=false&min_score=-3&max_score=3.000&h_noscore=true&mapid=0&",
			ocpl = new L.TileLayer(ocplUrl, osmOptions);
			
		// markers overlay
		var cacheMarker = new L.Marker(cacheLocation);
		var markers = new L.LayerGroup();
		markers.addLayer(cacheMarker) ;
		
		var map = new L.Map('map', {
				center: cacheLocation ,
				zoom: 14,
				layers: [osmapa, ocpl, markers]
		});	

		var baseMaps = {
			    "OSMapa.pl": osmapa,
			    "Mapnik": mapnik
			};

		var overlayMaps = {
		        "Relief": shadow,
				"Caches": ocpl,
			    "Cache location": markers
			};		

{/literal}			
		var layersControl = new L.Control.Layers(baseMaps, overlayMaps);


		map.addControl(layersControl);
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