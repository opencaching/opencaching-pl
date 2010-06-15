
<div id="map_canvas" style="width: 100%; height: 100%; position: absolute; top: 0p; bottom: 0px;">
</div>

<!--
http://rushbase.net:5580/~rush/ocpl/lib/cgi-bin/mapper.fcgi?userid=8595&z=11&x=1127&y=654&sc=false&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=false&h_arch=false&signes=false&be_ftf=false&h_de=false&h_pl=false&min_score=1&max_score=5&h_noscore=false&mapid=0

http://rushbase.net:5580/~rush/ocpl/lib/cgi-bin/mapper.fcgi?userid=8595&z=13&x=4520&y=2616&sc=0&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&be_ftf=false&h_de=true&h_pl=true&min_score=1&max_score=5&h_noscore=true&mapid=0

<input id="h_u" name="h_u" value="false" type="hidden"  />
<input id="h_t" name="h_t" value="false" type="hidden"  />
<input id="h_m" name="h_m" value="false" type="hidden"  />
<input id="h_v" name="h_v" value="false" type="hidden"  />
<input id="h_w" name="h_w" value="false" type="hidden"  />
<input id="h_e" name="h_e" value="false" type="hidden"  />
<input id="h_q" name="h_q" value="false" type="hidden"  />
<input id="h_o" name="h_o" value="false" type="hidden"  />
<input id="h_ignored" name="h_ignored" value="false" type="hidden"  />
<input id="h_own" name="h_own" value="false" type="hidden"  />
<input id="h_found" name="h_found" value="false" type="hidden"  />
<input id="h_noattempt" name="h_noattempt" value="false" type="hidden"  />
<input id="h_nogeokret" name="h_nogeokret" value="false" type="hidden"  />
<input id="h_avail" name="h_avail" value="false" type="hidden"  />
<input id="h_temp_unavail" name="h_temp_unavail" value="true" type="hidden"  />
<input id="h_arch" name="h_arch" value="true" type="hidden"  />
<input class="chbox" id="signes" name="signes" value="true" type="hidden" />
<input class="chbox" id="be_ftf" name="be_ftf" value="false" type="hidden" />
<input class="chbox" id="h_pl" name="h_pl" value="false" type="hidden" />
<input class="chbox" id="h_de" name="h_de" value="false" type="hidden" />
<input id="min_score" name="min_score" type="hidden" value="-3" />
<input id="max_score" name="max_score" type="hidden" value="3.0" />
<input class="chbox" id="h_noscore" name="h_noscore" value="false" type="hidden" />




-->



<input class="chbox" id="zoom" name="zoom" value="{zoom}" type="hidden" />

<script type="text/javascript">
function load() { 

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
var trasa = new GPolyline([new GLatLng(50.03053,19.96215),
new GLatLng(50.05840,19.92953),
new GLatLng(52.30897,20.82102),
new GLatLng(52.26767,20.89120),
new GLatLng(49.13768,22.60440),
new GLatLng(52.24407,21.00218),
], "#004080", 5); map0.addOverlay(trasa); var punkt = new GLatLng(50.03053,19.96215);  
var marker3 = new GMarker(punkt, icon);
      map0.addOverlay(marker3);
      GEvent.addListener(marker3, "click", function() {marker3.openInfoWindowHtml('Ukryty: 2010-05-13 12:00:00<br />w 50.03053/19.96215<br />');});
			obszar.extend(punkt);
var punkt = new GLatLng(50.05840,19.92953);  
var marker5 = new GMarker(punkt, icon);
      map0.addOverlay(marker5);
      GEvent.addListener(marker5, "click", function() {marker5.openInfoWindowHtml('Ukryty: 2010-05-09 14:30:00<br />w <a href="http://www.geocaching.com/seek/cache_details.aspx?wp=GC22VTD" target="_blank">GC22VTD</a><br />');});
			obszar.extend(punkt);
var punkt = new GLatLng(52.30897,20.82102);  
var marker7 = new GMarker(punkt, icon);
      map0.addOverlay(marker7);
      GEvent.addListener(marker7, "click", function() {marker7.openInfoWindowHtml('Ukryty: 2010-04-10 14:30:00<br />w <a href="http://www.opencaching.pl/searchplugin.php?userinput=OP098A" target="_blank">OP098A</a> <span class="bardzomale">Droga Łączniczek AK (Tradycyjna)<br />Polska</span><br />');});
			obszar.extend(punkt);
var punkt = new GLatLng(52.26767,20.89120);  
var marker9 = new GMarker(punkt, icon);
      map0.addOverlay(marker9);
      GEvent.addListener(marker9, "click", function() {marker9.openInfoWindowHtml('Ukryty: 2010-03-28 16:30:00<br />w <a href="http://www.opencaching.pl/searchplugin.php?userinput=OP1F21" target="_blank">OP1F21</a> <span class="bardzomale">Ścieżka zdrowia (Tradycyjna)<br />Polska</span><br />');});
			obszar.extend(punkt);
var punkt = new GLatLng(49.13768,22.60440);  
var marker12 = new GMarker(punkt, icon);
      map0.addOverlay(marker12);
      GEvent.addListener(marker12, "click", function() {marker12.openInfoWindowHtml('Ukryty: 2009-08-19 12:00:00<br />w <a href="http://www.opencaching.pl/searchplugin.php?userinput=OP05FB" target="_blank">OP05FB</a> <span class="bardzomale">Połonina Caryńska (Tradycyjna)<br />Polska</span><br />');});
			obszar.extend(punkt);
var punkt = new GLatLng(52.24407,21.00218);  
var marker14 = new GMarker(punkt, icon2);
      map0.addOverlay(marker14);
      GEvent.addListener(marker14, "click", function() {marker14.openInfoWindowHtml('Ukryty: 2009-07-25 12:00:00<br />w <a href="http://www.opencaching.pl/searchplugin.php?userinput=OP0A4E" target="_blank">OP0A4E</a> <span class="bardzomale">Wielka Synagoga Tłomackie (Tradycyjna)<br />Polska</span><br />');});
			obszar.extend(punkt);
 
   var nowyZoom = map0.getBoundsZoomLevel(obszar);  
   var nowyPunkt = obszar.getCenter();  
   map0.setCenter(nowyPunkt,nowyZoom-1);  
      }
    }
</script>
</script>
