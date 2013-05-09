<?

error_reporting(E_ALL);
echo "test pobierania xml<br><br>";
$url1 = 'http://geokrety.org/export2.php?wpt=OP1234';
$url2 = 'http://www.w3schools.com/xml/note.xml';

$result1 = simplexml_load_file($url1);
$result2 = simplexml_load_file($url2);

echo 'xml z http://geokrety.org/export2.php?wpt=OP1234';
var_dump($result1);
echo '<br><br>xml z http://www.w3schools.com/xml/note.xml';
var_dump($result2);




exit;

$wpts = loadWaypointFromGpx(simplexml_load_file("serduszko.gpx"));
echo "<pre>";
print_r($wpts);


function loadWaypointFromGpx($wpts)
{


	$coords_lon = (float) $wpts->wpt->attributes()->lon;
	$coords_lat = (float) $wpts->wpt->attributes()->lat;

	if ($coords_lon < 0)
	{
		$coords_lonEW = 'W';
		$coords_lon = -$coords_lon;
	}
	else
	{
		$coords_lonEW = 'E';
	}

	if ($coords_lat < 0)
	{
		$coords_latNS = 'S';
		$coords_lat = -$coords_lat;
	}
	else
	{
		$coords_latNS = 'N';
	}

	$coords_lat_h = floor($coords_lat);
	$coords_lon_h = floor($coords_lon);

	$coords_lat_min = sprintf("%02.3f", round(($coords_lat - $coords_lat_h) * 60, 3));
	$coords_lon_min = sprintf("%02.3f", round(($coords_lon - $coords_lon_h) * 60, 3));


$result = array (
					'name' => (string)$wpts->wpt->name,
					'coords_latNS' => $coords_latNS,
					'coords_lonEW' => $coords_lonEW, 
					'coords_lat_h' => $coords_lat_h,
					'coords_lon_h' => $coords_lon_h, 
					'coords_lat_min' => $coords_lat_min, 
					'coords_lon_min' => $coords_lon_min, 
					'desc' => '',
);

//insert waypoint description in result array
if (isset($wpts->wpt->cmt) && $wpts->wpt->cmt != '') {
	$result['desc'] .= $wpts->wpt->desc;
}
if (isset($wpts->wpt->cmt) && $wpts->wpt->cmt != '') {
	$result['desc'] .= $wpts->wpt->cmt;
}

//$result = print_r($result, true);
return $result;
}