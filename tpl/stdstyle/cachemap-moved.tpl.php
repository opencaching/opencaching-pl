
<script type="text/javascript">
function initialize() {

if (GBrowserIsCompatible()) {

var icon1 = new GIcon();
 icon1.image = "tpl/stdstyle/images/google_maps/yellow.png";
 icon1.shadow = "tpl/stdstyle/images/google_maps/shadow.png";
 icon1.iconSize = new GSize(12, 20);
 icon1.shadowSize = new GSize(22, 20);
 icon1.iconAnchor = new GPoint(6, 20);
 icon1.infoWindowAnchor = new GPoint(5, 1);

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

{latlongl}
// map0.setCenter(new GLatLng(0,0),0);
// var bounds = new GLatLngBounds();
//bounds.extend(latlngl);
//map0.setZoom(map0.getBoundsZoomLevel(bounds));
// map0.setCenter(bounds.getCenter());



{route}
{points}

      }
    }


</script>
</head>
<div class="content2-pagetitle">&nbsp;<img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="" title=""/>&nbsp;&nbsp;{{route_cache}} <font color="black">{cachename}</font></div>
<div class="content2-container">
<br/><p class="content-title-noshade-size1">
<img src="tpl/stdstyle/images/google_maps/red.png" alt="a" width="12" height="20" title="begin" /> = {{start_point}}
<img src="tpl/stdstyle/images/google_maps/yellow.png" alt="b" width="12" height="20" title="point" /> = {{trp_points}}
<img src="tpl/stdstyle/images/google_maps/green.png" alt="c" width="12" height="20" title="end"/> = {{recently_seen}}
&nbsp;&nbsp;[<a class="links" href="viewcache-test.php?cacheid={cacheid}">{{back_to_cache}}</a>]</p><br/><br/>
<div id="map0" style="width: 780px; height: 500px"></div>
</div>
