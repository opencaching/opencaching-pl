
<script type="text/javascript">
function initialize() {

if (GBrowserIsCompatible()) {

var icon = new GIcon();
 icon.image = "templates/icons/yellow.png";
 icon.shadow = "templates/icons/shadow.png";
 icon.iconSize = new GSize(12, 20);
 icon.shadowSize = new GSize(22, 20);
 icon.iconAnchor = new GPoint(6, 20);
 icon.infoWindowAnchor = new GPoint(5, 1);

var icon2 = new GIcon();
 icon2.image = "templates/icons/red.png";
 icon2.shadow = "templates/icons/shadow.png";
 icon2.iconSize = new GSize(12, 20);
 icon2.shadowSize = new GSize(22, 20);
 icon2.iconAnchor = new GPoint(6, 20);
 icon2.infoWindowAnchor = new GPoint(5, 1);

var icon3 = new GIcon();
 icon3.image = "templates/icons/green.png";
 icon3.shadow = "templates/icons/shadow.png";
 icon3.iconSize = new GSize(12, 20);
 icon3.shadowSize = new GSize(22, 20);
 icon3.iconAnchor = new GPoint(6, 20);
 icon3.infoWindowAnchor = new GPoint(5, 1);

 
var map0 = new GMap2(document.getElementById("map0"));
map0.addControl(new GSmallMapControl());
map0.addControl(new GMapTypeControl());

map0.setCenter(new GLatLng(), 6);
obszar = new GLatLngBounds(); 
{route}
 
   var nowyZoom = map0.getBoundsZoomLevel(obszar);  
   var nowyPunkt = obszar.getCenter();  
   map0.setCenter(nowyPunkt,nowyZoom-1);  
      }
    }


</script>
</head>
<div id="map0" style="width: 500px; height: 500px"></div>

