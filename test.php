<?php
	require_once('./lib/common.inc.php');
	
function distance($lat1, $lon1, $lat2, $lon2, $unit) { 

  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
  if ($miles<0) $miles=0;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344); 
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

$distance=0;
	$route_id="15";
			$rsc = sql("SELECT `lat`,`lon`
					FROM `route_points` 
					WHERE `route_id`='&1'
			        ORDER BY point_nr", $route_id);
			if (mysql_num_rows($rsc) !=0)
			{	$record = sql_fetch_array($rsc);
				$firsty=$record['lon'];
				$firtsx=$record['lat'];
			for ($i = 1; $i < mysql_num_rows($rsc); $i++)
			{
				$record = sql_fetch_array($rsc);
				$secy=$record['lon'];
				$secx=$record['lat'];
				$distance1=calcDistance($firtsx,$firsty,$secx,$secy,1);
				$distance=$distance+$distance1;
				$firsty=$secy;
				$firtsx=$secx;
			}
echo round($distance,1);
			sql("UPDATE `routes` SET `length`='&1' WHERE `route_id`='&2'",$distance,$route_id);
			}

/*
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
*/
?>
