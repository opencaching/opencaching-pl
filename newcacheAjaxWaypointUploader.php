<?php
$error = "";
$msg = "";
$fileElementName = 'fileToUpload';
if ($_FILES[$fileElementName]['size'] > 20480) {
	$_FILES[$fileElementName]['error'] = 2;
}
if (!empty($_FILES[$fileElementName]['error'])) {
	switch($_FILES[$fileElementName]['error']) {
		case '1' :
			$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			break;
		case '2' :
			$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			break;
		case '3' :
			$error = 'The uploaded file was only partially uploaded';
			break;
		case '4' :
			$error = 'No file was uploaded.';
			break;

		case '6' :
			$error = 'Missing a temporary folder';
			break;
		case '7' :
			$error = 'Failed to write file to disk';
			break;
		case '8' :
			$error = 'File upload stopped by extension';
			break;
		case '999' :
		default :
			$error = 'No error code avaiable';
	}
} elseif (empty($_FILES['fileToUpload']['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none') {
	$error = 'No file was uploaded..';
} else {
	$wpts = simplexml_load_file($_FILES['fileToUpload']['tmp_name']);
	// force to remove uploaded file
	@unlink($_FILES['fileToUpload']);
	$msg2 = json_encode(loadWaypointFromGpx($wpts));
}
echo "{";
echo "error: '" . $error . "',\n";
echo "msg: '" . $msg2 . "'\n";
echo "}";

function loadWaypointFromGpx($wpts) {
	// $wpts = simplexml_load_file("Waypointy2.gpx");

	$coords_lon = (float)$wpts -> wpt -> attributes() -> lon;
	$coords_lat = (float)$wpts -> wpt -> attributes() -> lat;

	if ($coords_lon < 0) {
		$coords_lonEW = 'W';
		$coords_lon = -$coords_lon;
	} else {
		$coords_lonEW = 'E';
	}

	if ($coords_lat < 0) {
		$coords_latNS = 'S';
		$coords_lat = -$coords_lat;
	} else {
		$coords_latNS = 'N';
	}

	$coords_lat_h = floor($coords_lat);
	$coords_lon_h = floor($coords_lon);

	$coords_lat_min = sprintf("%02.3f", round(($coords_lat - $coords_lat_h) * 60, 3));
	$coords_lon_min = sprintf("%02.3f", round(($coords_lon - $coords_lon_h) * 60, 3));

	$result = array('name' => (string)$wpts -> wpt -> name, 'coords_latNS' => $coords_latNS, 'coords_lonEW' => $coords_lonEW, 'coords_lat_h' => $coords_lat_h, 'coords_lon_h' => $coords_lon_h, 'coords_lat_min' => $coords_lat_min, 'coords_lon_min' => $coords_lon_min, 'desc' => '', );

	//insert waypoint description in result array
	if (isset($wpts -> wpt -> cmt) && $wpts -> wpt -> cmt != '') {
		$result['desc'] .= $wpts -> wpt -> desc;
	}
	if (isset($wpts -> wpt -> cmt) && $wpts -> wpt -> cmt != '') {
		$result['desc'] .= $wpts -> wpt -> cmt;
	}

	//$result = print_r($result, true);
	return $result;
}
?>