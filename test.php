<?php
function calcLatLong($long, $lat, $distance, $bearing) {
 $EARTH_RADIUS_EQUATOR = 6378140.0;
 $RADIAN = 180 / pi();
 $b = $bearing / $RADIAN;
 $long = $long / $RADIAN;
 $lat = $lat / $RADIAN;
 $f = 1/298.257;
 $e = 0.08181922;
	
 $R = $EARTH_RADIUS_EQUATOR * (1 - $e * $e) / pow( (1 - $e*$e * pow(sin($lat),2)), 1.5);	
 $psi = $distance/$R;
 $phi = pi()/2 - $lat;
 $arccos = cos($psi) * cos($phi) + sin($psi) * sin($phi) * cos($b);
 $latA = (pi()/2 - acos($arccos)) * $RADIAN;

 $arcsin = sin($b) * sin($psi) / sin($phi);
 $longA = ($long - asin($arcsin)) * $RADIAN;
 return array('longitude' => $longA, 'latitude' => $latA);
}

print_r(calcLatLong(17, 52, 129289, 30.34));

?>
