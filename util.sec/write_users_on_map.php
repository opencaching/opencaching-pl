<?php

global $lang, $rootpath;
$lang = 'en';

if (!isset($rootpath))
    $rootpath = '../';

//include template handling
require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/cache_icon.inc.php');

//$red2 = imagecolorallocate ($im2, 255,0,0);

$rs = sql(" SELECT latitude, longitude, date_created FROM user ORDER BY `date_created`");
$im = imagecreatefromjpeg("mapa.jpg");
$blue = imagecolorallocate($im, 0, 0, 255);
$green = imagecolorallocate($im, 0, 255, 0);
$l = 0;
$no_users = mysql_num_rows($rs);
for ($i = 0; $i < $no_users; $i++) {

    $record = sql_fetch_array($rs);
    $long = $record['longitude'];
    $lat = $record['latitude'];

    $pt = latlon_to_pix($lat, $long);
    imagefilledellipse($im, $pt["x"], $pt["y"], 2, 2, $blue);
    // Now mark the point on the map using a red 4 pixel rectangle
    if ($i % 50 == 0) {
        // Write the string at the top left
        imagefilledrectangle($im, 0, 0, 90, 14, $green);
        imagestring($im, 5, 0, 0, substr($record['date_created'], 0, 10), $blue);
        imagejpeg($im, "pics/mapa-new-" . $l . ".jpg", 80);
        $l++;
    }
}
imagedestroy($im);

function latlon_to_pix($lat, $lon)
{
    $lat = abs($lat);
    $lon = abs($lon);

    $x_min = 0;
    $x_max = 300;
    $y_min = 0;
    $y_max = 284;
    $lon_min = 24.3;
    $lon_max = 13.9;
    $lat_min = 48.9;
    $lat_max = 55;

    $x = $x_min + ($x_max - $x_min) *
            ( 1 - ($lon - $lon_min) / ($lon_max - $lon_min) );
    $y = $y_max - ($y_max - $y_min) *
            ( ($lat - $lat_min) / ($lat_max - $lat_min) );
//  return array(intval($x),intval($y));
    return array("x" => round($x), "y" => round($y));
}

?>
