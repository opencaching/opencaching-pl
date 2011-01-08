<?php
require_once('./lib/common.inc.php');
require_once('./lib/class.polylineEncoder.php');

			$rscp = sql("SELECT `lat` ,`lon`
					FROM `route_points`
					WHERE `route_id`='17'");
$p=array();
$points=array();
for ($i = 0; $i < mysql_num_rows($rscp); $i++)
{	
				$record = sql_fetch_array($rscp);
				$y=$record['lon'];
				$x=$record['lat'];		

  $p[0]=$x;
  $p[1]=$y;
  $points[$i]=$p;

}

$encoder = new PolylineEncoder();
$polyline = $encoder->encode($points);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Google Maps JavaScript API</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAKzfMHoyn1s1VSuNTwlFfzhTqTxhHAgqKNaAck663VX5jr8OSJBQrTiL58t4Rt3olsGRlxSuqVkU5Xg"
      type="text/javascript"></script>
    <script type="text/javascript">

    //<![CDATA[

    function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
        map.addControl(new GLargeMapControl());
        map.addControl(new GMapTypeControl());
        map.addControl(new GScaleControl());

        var encodedPolyline = new GPolyline.fromEncoded({

          weight: 5,
          points: "<?= $polyline->points ?>",
          levels: "<?= $polyline->levels ?>",
          zoomFactor: <?= $polyline->zoomFactor ?>,
          numLevels: <?= $polyline->numLevels ?>
        });
		

    var bounds = encodedPolyline.getBounds();
    map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds)); 
        map.addOverlay(encodedPolyline);
      }
    }

    //]]>
    </script>
  </head>
  <body onload="load()" onunload="GUnload()">
    <div id="map" style="width:500px;height:500px"></div>
  </body>
</html>