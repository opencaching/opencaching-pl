<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Where Am I?</title>	
	
    <script src="http://maps.google.com/maps?file=api&v=3&key=ABQIAAAA4DS0L5IhPNkkzhAejJ1YghQmw8g3SyoYQoey3nQkQjZ-xBIKWxQBStwSQ5otzHFYPFzfrBNiNotrGQ"
      type="text/javascript"></script>

    <script type="text/javascript">

    var map = null;
    var geocoder = null;

    function initialize() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map_canvas"));
        map.setCenter(new GLatLng(36.4419, 138.1419), 1);
        map.setUIToDefault();
        geocoder = new GClientGeocoder();
      }
    }

function toWGS84(lat,lng)
{
	var lat = lat, lng = lng;
	var latD = 'N', lngD = 'E';

	if(lat < 0) {
		lat = -lat;
		latD = 'S';
	}
	if(lng < 0) {
		lng = -lng;
		lngD = 'W';
	}

	var latstr, lngstr;

		var degs1 = lat | 0;
		var degs2 = lng | 0;
		var minutes1 = ((lat - degs1)*60);
		var minutes2 = ((lng - degs2)*60);
		latstr = degs1 + "° " + minutes1.toFixed(3) + "'";
		lngstr = degs2 + "° " + minutes2.toFixed(3) + "'";

	return "<br><b>" + latD + " " + latstr + " " + lngD + " " + lngstr + "</b>";
}

    function showAddress(address) {
      if (geocoder) {
        geocoder.getLatLng(
          address,
          function(point) {
            if (!point) {
              alert(address + " not found");
            } else {
              map.setCenter(point, 15);
              var marker = new GMarker(point, {draggable: true});
              map.addOverlay(marker);
              GEvent.addListener(marker, "dragend", function() {
               var latlng=toWGS84(marker.getLatLng().lat().toFixed(5),marker.getLatLng().lng().toFixed(5));
                marker.openInfoWindowHtml(latlng);
              });
              GEvent.addListener(marker, "click", function() {
               var latlng=toWGS84(marker.getLatLng().lat().toFixed(5),marker.getLatLng().lng().toFixed(5));
                marker.openInfoWindowHtml(latlng);
              });
	      GEvent.trigger(marker, "click");
            }
          }
        );
      }
    }
    </script>
	
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=610,height=610,left = 495,top = 145');");
}
// End -->
</script>
  </head>
  
  <!-- Use this to make a small popup window that can be easily closed. -->
  <!-- <A HREF="javascript:popUp('geocoder.php')">Where am I?</A> -->
  <!-- geocoder.php is this page -->

  <body onload="initialize()" onunload="GUnload()">
    <form action="#" onsubmit="showAddress(this.address.value); return false">
      <br>
        Enter your address then drag the marker to tweak the location.
        <br/>
        The latitude/longitude will appear after each geocode/drag.
     <br />      
        <input type="text" style="width:350px" name="address" value="Warszawa, Polska" />

        <input type="submit" value="Go!" />
       <br>
      <div id="map_canvas" style="width: 600px; height: 400px"></div>
    </form>

  </body>
</html>


