<?php

use src\Models\ApplicationContainer;
use src\Models\Coordinates\Coordinates;
use src\Models\Coordinates\GeoCode;
use src\Models\Coordinates\NutsLocation;

require_once __DIR__ . '/lib/common.inc.php';

if (! ApplicationContainer::GetAuthorizedUser()) {
    echo 'Not authorized!';

    exit;
}

$lat_float = 0;
$latOk = false;
$lonOk = false;
$coords = null;

if (isset($_REQUEST['lat'])) {
    $lat_float = (float) $_REQUEST['lat'];
    $lat = $_REQUEST['lat'];
    $latOk = true;
}

$lon_float = 0;

if (isset($_REQUEST['lon'])) {
    $lon_float = (float) $_REQUEST['lon'];
    $lon = $_REQUEST['lon'];
    $lonOk = true;
}

if ($latOk && $lonOk) {
    $coords = Coordinates::FromCoordsFactory($lat, $lon);
}

if (! is_null($coords)) {
    tpl_set_var('coords_str', $coords->getAsText(Coordinates::COORDINATES_FORMAT_DEG_MIN));

    $nutsData = NutsLocation::fromCoordsFactory($coords);
    tpl_set_var('nutsDesc', $nutsData->getDescription(' > '));

    $googleGeocode = GeoCode::fromGoogleApi($coords);

    if ($googleGeocode) {
        tpl_set_var('googleDesc', $googleGeocode->getDescription(' > '));
    } else {
        tpl_set_var('googleDesc', '-');
    }

    $nominatimGeoCode = GeoCode::fromNominatimApi($coords);

    if ($nominatimGeoCode) {
        tpl_set_var('nominatimDesc', $nominatimGeoCode->getDescription(' > '));
    } else {
        tpl_set_var('nominatimDesc', '-');
    }
} else {
    tpl_set_var('coords_str', '');
    tpl_set_var('nutsDesc', '-');
    tpl_set_var('googleDesc', '-');
    tpl_set_var('mapQuestDesc', '-');
}

//make the template and send it out
tpl_set_tplname('region');
tpl_BuildTemplate();
