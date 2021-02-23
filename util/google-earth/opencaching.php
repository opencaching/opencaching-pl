<?php

ob_start();

use src\Utils\Database\XDb;
use src\Models\OcConfig\OcConfig;
use src\Models\ApplicationContainer;

require_once(__DIR__.'/../../lib/common.inc.php');
require_once(__DIR__.'/../../lib/export.inc.php');

header('Content-Type: application/vnd.google-earth.kml; charset=utf8');
header('Content-Disposition: attachment; filename="opencaching.kml"');

$kml = '<?xml version="1.0" encoding="utf-8"?>
<kml xmlns="http://earth.google.com/kml/2.0">
    <Document>
        <Name>' . convert_string(OcConfig::getSiteName()) . '</Name>
        <LookAt>
            <longitude>{lon}</longitude>
            <latitude>{lat}</latitude>
            <range>{range}</range>
            <tilt>0</tilt>
            <heading>0</heading>
        </LookAt>
        <NetworkLink>
            <name>' . convert_string(OcConfig::getSiteName()) . '</name>
            <Link id="' . convert_string(OcConfig::getSiteName()) . '">
                <href>' . $absolute_server_URI . 'util/google-earth/caches.php</href>
                <viewRefreshTime>1</viewRefreshTime>
                <viewRefreshMode>onStop</viewRefreshMode>
            </Link>
        </NetworkLink>
    </Document>
</kml>
';
$countryCoords = OcConfig::getMapDefaultCenter();

$loggedUser = ApplicationContainer::GetAuthorizedUser();
if ($loggedUser) {
    // get the users home coords
    $rs_coords = XDb::xSql(
        "SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? ", $loggedUser->getUserId());

    $record_coords = XDb::xFetchArray($rs_coords);

    if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) ||
        (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {

        // invalid or missing home coordinates
        // use default country coordinates
        $lat = $countryCoords->getLatitude();
        $lon = $countryCoords->getLongitude();
        $range = '500000';
    } else {
        $lat = $record_coords['latitude'];
        $lon = $record_coords['longitude'];
        $range = '75000';
    }
    XDb::xFreeResults($rs_coords);
} else {
    // use default country coordinates
    $lat = $countryCoords->getLatitude();
    $lon = $countryCoords->getLongitude();
    $range = '500000';
}

$kml = str_replace('{lat}', $lat, $kml);
$kml = str_replace('{lon}', $lon, $kml);
$kml = str_replace('{range}', $range, $kml);

echo $kml;
ob_end_flush();

exit();
