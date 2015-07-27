<?php
/***************************************************************************
															-------------------
		begin                : July 23 2006
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/
global $usr;
?>

    <script type="text/javascript">

    //<![CDATA[
function load() {
	if (GBrowserIsCompatible()) {
		var map = new GMap2(document.getElementById("map"));
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.addControl(new GOverviewMapControl());
		map.setCenter(new GLatLng(49.845068,15.441284), 7);
		
		var baseIcon = new GIcon();
		//baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
		baseIcon.iconSize = new GSize(20, 34);
		//baseIcon.shadowSize = new GSize(37, 34);
		baseIcon.iconAnchor = new GPoint(10, 34);
		baseIcon.infoWindowAnchor = new GPoint(9, 2);
		//baseIcon.infoShadowAnchor = new GPoint(18, 25);
		
		// Creates a marker at the given point with the given label
		function createMarker(point, marker) {
			var cache_id = marker.getAttribute("id");
  			var cache_name = marker.getAttribute("name");
  			var cache_type = marker.getAttribute("type");
  			var cache_date = marker.getAttribute("date");
  			
			var icon = new GIcon(baseIcon);
			
			icon.image = "http://www.google.com/mapfiles/marker" + cache_type + ".png";
			
			var marker = new GMarker(point, icon);
			GEvent.addListener(marker, "click", function() {
				marker.openInfoWindowHtml("<a href=\"/viewcache.php?cacheid=" + cache_id + "\" target=\"_blank\">" + cache_name + "</a><br/>" + cache_date + "");
			});
			return marker;
		}

		var request = GXmlHttp.create();
		request.open("GET", "xml/found_caches.php?uid=<?php echo $usr['userid']; ?>", true);
		request.onreadystatechange = function()
		{
			if (request.readyState == 4)
			{
				var xmlDoc = request.responseXML;
				var markers = xmlDoc.documentElement.getElementsByTagName("marker");
				for (var i = 0; i < markers.length; i++)
				{
					//var point = new GPoint(parseFloat(markers[i].getAttribute("lng")), parseFloat(markers[i].getAttribute("lat")));
					var point = new GLatLng(markers[i].getAttribute("lat"), markers[i].getAttribute("lng"));
					var marker = createMarker(point, markers[i]);
					map.addOverlay(marker);
				}
			}
		}
		request.send(null);
	}
}

    //]]>
		    </script>

<div id="map" style="width: 800px; height: 600px"></div>
