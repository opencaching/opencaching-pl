<?php
/**
 * This script allow to upload GPX file with new cache description
 */

$destination_path = $picdir . DIRECTORY_SEPARATOR;

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

function loadWaypointFromGpx($wpts)
{
    $arr = (array) $wpts;
    if (count($wpts->wpt) == 1) {
        $tmp = $arr['wpt'];
        unset($arr);
        $arr['wpt'][0] = $tmp;
    }

    foreach ($arr['wpt'] as $key => $waypoint) {
        $coordsLon = (float) $waypoint->attributes()->lon;
        $coordsLat = (float) $waypoint->attributes()->lat;
        if ($coordsLon < 0) {
            $coords_lonEW = 'W';
            $coordsLon = -$coordsLon;
        } else {
            $coords_lonEW = 'E';
        }

        if ($coordsLat < 0) {
            $coords_latNS = 'S';
            $coordsLat = -$coordsLat;
        } else {
            $coords_latNS = 'N';
        }

        $coords_lat_h = floor($coordsLat);
        $coords_lon_h = floor($coordsLon);

        $coords_lat_min = sprintf("%02.3f", round(($coordsLat - $coords_lat_h) * 60, 3));
        $coords_lon_min = sprintf("%02.3f", round(($coordsLon - $coords_lon_h) * 60, 3));

        $result[$key] = array(
            'name' => (string) $waypoint->name,
            'coords_latNS' => $coords_latNS,
            'coords_lonEW' => $coords_lonEW,
            'coords_lat_h' => $coords_lat_h,
            'coords_lon_h' => $coords_lon_h,
            'coords_lat_min' => $coords_lat_min,
            'coords_lon_min' => $coords_lon_min,
            'time' => (string) $waypoint->time,
            'altitude' => (float) $waypoint->ele,
            'desc' => '',
            'cmt' => '',
        );
        //insert waypoint description in result array
        if (isset($waypoint->cmt) && $wpts->wpt->cmt != '') {
            $result[$key]['desc'] .= (string) $wpts->wpt->desc;
        }
        if (isset($waypoint->cmt) && $wpts->wpt->cmt != '') {
            $result[$key]['cmt'] .= (string) $wpts->wpt->cmt;
        }
    }

    //var_dump($result);
    return $result;
}
?>

<script type="text/javascript">window.top.window.stopUpload(<?php echo "'" . $result . "'"; ?>);</script>
