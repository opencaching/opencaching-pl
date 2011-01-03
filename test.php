<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
  <head> 
    <title>Directions to Josh Gamble's</title> 
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;hl=pl&amp;key=ABQIAAAAKzfMHoyn1s1VSuNTwlFfzhTqTxhHAgqKNaAck663VX5jr8OSJBQrTiL58t4Rt3olsGRlxSuqVkU5Xg" type="text/javascript"></script>
    
  </head> 
  <body> 
    <div id="map" style="width: 400px; height: 400px"></div> 

    <script type="text/javascript"> 
      var gmarkers = []; 
      var htmls = []; 
      var to_htmls = []; 
      var from_htmls = []; 
      var i=0; 

      function tohere(i) { 
        gmarkers[i].openInfoWindowHtml(to_htmls[i]); 
      } 

      function fromhere(i) { 
        gmarkers[i].openInfoWindowHtml(from_htmls[i]); 
      } 

    // Check to see if this browser can run the Google API 
    if (GBrowserIsCompatible()) { 

      // A function to create the marker and set up the event window 
      function createMarker(point,name,html) { 
        var marker = new GMarker(point); 

        // The info window version with the "to here" form open 
        to_htmls[i] = html + '<br>Directions: <b>To here</b> - <a href="javascript<b></b>:fromhere(' + i + ')">From here</a>' + 
           '<br>Start address:<form action="http://maps.google.com/maps" method="get" target="_blank">' + 
           '<input type="text" SIZE=40 MAXLENGTH=40 name="saddr" id="saddr" value="" /><br>' + 
           '<INPUT value="Get Directions" TYPE="SUBMIT">' + 
           '<input type="hidden" name="daddr" value="' + 
           point.latDegrees + ',' + point.lngDegrees + "(" + name + ")" + '"/>'; 

        // The info window version with the "to here" form open 
        from_htmls[i] = html + '<br>Directions: <a href="javascript<b></b>:tohere(' + i + ')">To here</a> - <b>From here</b>' + 
           '<br>End address:<form action="http://maps.google.com/maps" method="get"" target="_blank">' + 
           '<input type="text" SIZE=40 MAXLENGTH=40 name="daddr" id="daddr" value="" /><br>' + 
           '<INPUT value="Get Directions" TYPE="SUBMIT">' + 
           '<input type="hidden" name="saddr" value="' + point.latDegrees + ',' + point.lngDegrees + "(" + name + ")" + '"/>'; 

        // The inactive version of the direction info 
        html = html + '<br>Directions: <a href="javascript<b></b>:tohere('+i+')">To here</a> - <a href="javascript<b></b>:fromhere('+i+')">From here</a>'; 

        GEvent.addListener(marker, "click", function() { 
          marker.openInfoWindowHtml(html); 
        }); 
        gmarkers[i] = marker; 
        htmls[i] = html; 
        i++; 
        return marker; 
      } 

      // Display the map, with some controls and set the initial location 
      var map = new GMap(document.getElementById("map")); 
      map.addControl(new GLargeMapControl()); 
      map.addControl(new GMapTypeControl()); 
      map.setCenter(new GLatLng(39.214034,-74.694285), 15); 

      // Set up a marker with an info window 
      var point = new GLatLng(39.214034,-74.694285); 
      var marker = createMarker(point,'2023 Cedar Ln.','Some stuff to display in the<br>First Info Window') 
      map.addOverlay(marker); 
    } 
    </script> 
  </body> 
  </html>
