<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=opencaching"></script>
<style type="text/css"> 
#mapContainer { 	 
  height: 600px; 	 
  width: 600px;
}
 </style>
<div id="mapContainer"></div> 
<script type="text/javascript"> 
// Create a map object
 var map = new YMap(document.getElementById('mapContainer')); 
// Display the map centered on given address 
//map.drawZoomAndCenter("94089", 5);
// Overlay data from GeoRSS file
map.addOverlay(new YGeoRSS('http://www.opencaching.pl/xml/ocxmlmap.php?modifiedsince=20060101000000&caches=1'));
// Add a pan control 
map.addPanControl(); 
// Add a slider zoom control 
map.addZoomLong(); 
// Set map type to either of: YAHOO_MAP_SAT YAHOO_MAP_HYB YAHOO_MAP_REG
//map.setMapType(YAHOO_MAP_REG);

//Get valid map types, returns array [YAHOO_MAP_REG, YAHOO_MAP_SAT, YAHOO_MAP_HYB]
//var myMapTypes = map.getMapTypes(); 
</script>
<br /><br />
<b>Legenda:</b>
<table>
<tr><td><img src="http://opencaching.pl/tpl/stdstyle/images/cache/16x16-traditional.png">  Tradycyjna</td><td><img src="http://opencaching.pl/tpl/stdstyle/images/cache/16x16-multi.png">  Multicache</td><td><img src="http://opencaching.pl/tpl/stdstyle/images/cache/16x16-quiz.png">  Quiz</td><td><img src="http://opencaching.pl/tpl/stdstyle/images/cache/16x16-moving.png">  Mobilna</td></tr>
<tr><td><img src="http://opencaching.pl/tpl/stdstyle/images/cache/16x16-virtual.png">  Wirtualna</td><td><img src="http://opencaching.pl/tpl/stdstyle/images/cache/16x16-webcam.png">  Webcam</td><td><img src="http://opencaching.pl/tpl/stdstyle/images/cache/16x16-event.png">  Wydarzenie</td><td><img src="http://opencaching.pl/tpl/stdstyle/images/cache/16x16-unknown.png">  Nieznany typ</td></tr>
</table>
