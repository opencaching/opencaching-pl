
<script type="text/javascript">
function initialize() {

if (GBrowserIsCompatible()) {

var icon = new GIcon();
 icon.image = "tpl/stdstyle/images/google_maps/yellow.png";
 icon.shadow = "tpl/stdstyle/images/google_maps/shadow.png";
 icon.iconSize = new GSize(12, 20);
 icon.shadowSize = new GSize(22, 20);
 icon.iconAnchor = new GPoint(6, 20);
 icon.infoWindowAnchor = new GPoint(5, 1);

var icon2 = new GIcon();
 icon2.image = "tpl/stdstyle/images/google_maps/red.png";
 icon2.shadow = "tpl/stdstyle/images/google_maps/shadow.png";
 icon2.iconSize = new GSize(12, 20);
 icon2.shadowSize = new GSize(22, 20);
 icon2.iconAnchor = new GPoint(6, 20);
 icon2.infoWindowAnchor = new GPoint(5, 1);

var icon3 = new GIcon();
 icon3.image = "tpl/stdstyle/images/google_maps/green.png";
 icon3.shadow = "tpl/stdstyle/images/google_maps/shadow.png";
 icon3.iconSize = new GSize(12, 20);
 icon3.shadowSize = new GSize(22, 20);
 icon3.iconAnchor = new GPoint(6, 20);
 icon3.infoWindowAnchor = new GPoint(5, 1);

 
var map0 = new GMap2(document.getElementById("map0"));
map0.addControl(new GSmallMapControl());
map0.addControl(new GMapTypeControl());

map0.setCenter(new GLatLng({mapcenterLat},{mapcenterLon}), 6);
obszar = new GLatLngBounds(); 
{route}
{points}
 
//   var nowyZoom = map0.getBoundsZoomLevel(obszar);  
//   var nowyPunkt = obszar.getCenter();  
//   map0.setCenter(nowyPunkt,nowyZoom-1);  
      }
    }


</script>
</head>
<div id="map0" style="width: 780px; height: 500px"></div>

