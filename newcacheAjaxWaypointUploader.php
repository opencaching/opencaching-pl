<?php
// Edit upload location here
$destination_path = $picdir.DIRECTORY_SEPARATOR;

    $valid_formats = array("gpx");

    $name = $_FILES['myfile']['name'];
    $size = $_FILES['myfile']['size'];
    if (strlen($name)) {
        list($txt, $ext) = explode(".", $name);
        $ext = strtolower($ext);
        if (in_array($ext, $valid_formats)) {
            if ($size < (1024 * 1024 * 2)) { // Image size max 2 MB
                $actual_image_name = 'tempgpx.' . $ext;

                $result = 0;
                $target_path = $destination_path . $actual_image_name;

                $wpts = simplexml_load_file($_FILES['myfile']['tmp_name']);
                @unlink($_FILES['myfile']);

                $result = json_encode(loadWaypointFromGpx($wpts));
            }
        }
    }

function loadWaypointFromGpx($wpts) {

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

<script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo "'".$result."'"; ?>);</script>
