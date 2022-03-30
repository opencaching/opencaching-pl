<?php

use src\Models\ApplicationContainer;
use src\Models\OcConfig\OcConfig;
use src\Utils\Database\XDb;
use src\Utils\I18n\I18n;

require_once __DIR__ . '/lib/common.inc.php';

$googleMapKey = OcConfig::instance()->getGoogleMapKey();

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (! $loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);

    exit;
}

$view = tpl_getView();
$view->setTemplate('myroutes_add_map2')
    ->addLocalJs(
        'https://maps.googleapis.com/maps/api/js?libraries=geometry&amp;key=' . $googleMapKey
        . '&amp;language=' . I18n::getCurrentLang()
    );

// set map center
tpl_set_var('map_lat', OcConfig::getMapDefaultCenter()->getLatitude());
tpl_set_var('map_lon', OcConfig::getMapDefaultCenter()->getLongitude());
tpl_set_var('map_zoom', OcConfig::getStartPageMapZoom() + 1);

$user_id = $loggedUser->getUserId();
$name = $_POST['name'] ?? '';
tpl_set_var('name', htmlspecialchars($name, ENT_COMPAT));

$desc = $_POST['desc'] ?? '';
tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT));

$radius = $_POST['radius'] ?? '0';
tpl_set_var('radius', $radius);

if (isset($_POST['back'])) {
    tpl_redirect('myroutes.php');

    exit;
}

if (isset($_POST['submitform'])) {
    $length = $_POST['distance'] ?? '0';
    $route_points = isset($_POST['route_points']) ? explode(' ', $_POST['route_points']) : [];

    // insert route name
    XDb::xSql(
        'INSERT INTO `routes` (
                    `user_id`, `name`, `description`, `radius`, `length`)
                VALUES (?, ?, ?, ?, ?)',
        $user_id,
        $name,
        $desc,
        $radius,
        $length
    );

    // get route_id
    $route_id = XDb::xMultiVariableQueryValue(
        'SELECT route_id FROM `routes`
                WHERE name= :1 AND description= :2 AND user_id= :3',
        0,
        $name,
        $desc,
        $user_id
    );

    $point_num = 0;

    foreach ($route_points as $route_point) {
        $point_num++;
        $latlng = explode(',', $route_point);
        XDb::xSql(
            'INSERT into route_points (route_id,point_nr,lat,lon)
                    VALUES (?, ?, ?, ?)',
            $route_id,
            $point_num,
            $latlng[0],
            $latlng[1]
        );
    }

    tpl_redirect('myroutes.php');

    exit;
} //end submit

//make the template and send it out
tpl_BuildTemplate();
