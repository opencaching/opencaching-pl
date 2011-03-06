<script type="text/javascript">
  var lat=52.8917;
  var lng=19.6517;
		var mapa;
		var polilinie = {};
		var punktCentralny = new GLatLng(52.8917,19.6517)
		
		function load()
		{
			if(GBrowserIsCompatible())  
			{
				mapa = new GMap2(document.getElementById('mapka'));
				mapa.setCenter(punktCentralny,8,G_HYBRID_MAP);
				mapa.addControl(new GLargeMapControl());
        			mapa.addControl(new GMapTypeControl());   
				var poli = okrag(punktCentralny,180,'#99CCCC',4,0.8,'#9999CC',0.2,55);
				mapa.addOverlay(poli);
				var point = new GLatLng(lat, lng);
				var marker = new GMarker(point);
				mapa.addOverlay(marker);
			}
		}			
		
		function okrag(srodek,promien)
		{
			if(!srodek || !promien)
				return;

			// domyœlne wartoœci
			var wyp_kolor = '#0000ff';
			var wyp_alfa = 0.25;
			var obr_kolor = '#0000ff';
			var obr_grubosc = 1;
			var obr_alfa = 0.65;
			var dokladnosc = 24;
			
			switch(arguments.length)
			{
				case 8: dokladnosc = arguments[7];
				case 7: wyp_alfa = arguments[6];
				case 6: wyp_kolor = arguments[5];
				case 5: obr_alfa = arguments[4];
				case 4: obr_grubosc = arguments[3];
				case 3: obr_kolor = arguments[2];
			}
		
			var punkty=[];
			for(i=0;i<dokladnosc;i++)
			{
				var kat=360*i/dokladnosc;
				kat = Math.PI*kat/180;
				var srodekXY = mapa.fromLatLngToDivPixel(srodek);
				var nowyPunktXY = new GPoint(srodekXY.x+parseFloat(promien)*Math.cos(kat),srodekXY.y+parseFloat(promien)*Math.sin(kat));
				punkty.push(mapa.fromDivPixelToLatLng(nowyPunktXY));
			}
			
			punkty.push(punkty[0]); // powielamy jeszcze raz pierwszy punkt, aby zamkn¹æ okr¹g
			
			if(arguments.length>5)
				var poli = new GPolygon(punkty,obr_kolor,obr_grubosc,obr_alfa,wyp_kolor,wyp_alfa);
			else
				var poli = new GPolyline(punkty,obr_kolor,obr_grubosc,obr_alfa);
			return poli;
		}

	</script>

</script>


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

 var sw = new GLatLng({latlonmin});  
 var ne = new GLatLng({latlonmax});  
 var bounds = new GLatLngBounds(sw, ne);  
 map0.setCenter(bounds.getCenter(), map0.getBoundsZoomLevel(bounds));  

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
