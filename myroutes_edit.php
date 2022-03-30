<?php

use src\Models\ApplicationContainer;
use src\Models\OcConfig\OcConfig;
use src\Utils\Database\XDb;
use src\Utils\I18n\I18n;

require_once __DIR__ . '/lib/common.inc.php';

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (! $loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);

    exit;
}

$googleMapKey = OcConfig::instance()->getGoogleMapKey();

if (isset($_REQUEST['routeid'])) {
    $route_id = $_REQUEST['routeid'];
} else {
    exit('Error: No routeID');
}
$remove = 0;

if (isset($_REQUEST['delete'])) {
    $remove = 1;
}

if (isset($_POST['delete'])) {
    $route_id = $_POST['routeid'];
    $remove = 1;
}

if (isset($_POST['routeid'])) {
    $route_id = $_POST['routeid'];
}

$view = tpl_getView();
$view->setTemplate('myroutes_edit');
$user_id = $loggedUser->getUserId();

if (isset($_POST['back'])) {
    tpl_redirect('myroutes.php');

    exit;
}

$route_rs = XDb::xSql(
    'SELECT `user_id`,`name`, `description`, `radius`
            FROM `routes`
            WHERE `route_id`= ? AND `user_id`= ?',
    $route_id,
    $user_id
);

$record = XDb::xFetchArray($route_rs);
tpl_set_var('routes_name', $record['name']);

$rname = $_POST['name'] ?? '';
$rdesc = $_POST['desc'] ?? '';
$rradius = $_POST['radius'] ?? '';

$view->addLocalJs(
    'https://maps.googleapis.com/maps/api/js?libraries=geometry&amp;key=' . $googleMapKey
    . '&amp;language=' . I18n::getCurrentLang()
);

if ($record['user_id'] == $loggedUser->getUserId()) {
    if ($remove == 1) {
        //remove
        XDb::xSql('DELETE FROM `routes` WHERE `route_id`= ? AND `user_id`= ? ', $route_id, $user_id);
        XDb::xSql('DELETE FROM `route_points` WHERE `route_id`= ? ', $route_id);
        tpl_redirect('myroutes.php');

        exit;
    }
}

// start submit
if (isset($_POST['submit']) && $remove == 0) {
    XDb::xSql(
        'UPDATE `routes` SET `name`= ? ,`description`= ?,`radius`= ?
                WHERE `route_id`= ? ',
        $rname,
        $rdesc,
        $rradius,
        $route_id
    );

    if ($_FILES['file']['tmp_name'] != '') {
        XDb::xSql('DELETE FROM `route_points` WHERE `route_id`= ? ', $route_id);

        $upload_filename = $_FILES['file']['tmp_name'];
        // Read file KML with route
        exec('/usr/bin/gpsbabel -i kml -f ' . $upload_filename . ' -x interpolate,distance=0.25k -o kml -F ' . $upload_filename);
        $xml = simplexml_load_file($upload_filename);

        // get length route
        foreach ($xml->Document->Folder as $f) {
            foreach ($f->Folder as $folder) {
                $dis = $folder->description;
                $dis1 = explode(' ', trim($dis));
                $len = (float) $dis1[27];
                XDb::xSql(
                    'UPDATE `routes` SET `length`= ?
                                WHERE `route_id`= ? ',
                    $len,
                    $route_id
                );
            }
        }

        $coords = [];

        foreach ($xml->Document->Folder as $xmlElement) {
            foreach ($xmlElement->Folder as $folder) {
                foreach ($folder->Placemark->LineString->coordinates as $coordinates) {
                    if ($coordinates) {
                        $coords_raw = explode(' ', trim($coordinates));

                        foreach ($coords_raw as $coords_raw_part) {
                            if ($coords_raw_part) {
                                $coords_raw_parts = explode(',', $coords_raw_part);
                                $coords[] = $coords_raw_parts[0];
                                $coords[] = $coords_raw_parts[1];
                            }
                        }
                    }
                }
            }
        }
        // end of read
        //we get the point data in to an array called $points:
        $points = [];

        for ($i = 0; $i < count($coords) - 1; $i = $i + 2) {
            $points[] = ['lon' => $coords[$i], 'lat' => $coords[$i + 1]];

            if (($coords[$i] + 0 == 0) or ($coords[$i + 1] + 0 == 0)) {
                break;
            }
        }
        // add it to the route_points database:
        $point_num = 0;

        foreach ($points as $point) {
            $point_num++;
            $result = XDb::xSql(
                'INSERT into route_points (route_id, point_nr, lat, lon)
                                VALUES ( ?, ?, ?, ?)',
                $route_id,
                $route_id,
                $point['lat'],
                $point['lon']
            );
        }
    } //end update points

    tpl_redirect('myroutes.php');

    exit;
} //end submit
tpl_set_var('name', htmlspecialchars($record['name'], ENT_COMPAT));
tpl_set_var('desc', htmlspecialchars($record['description'], ENT_COMPAT));
tpl_set_var('radius', $record['radius']);
tpl_set_var('routeid', $route_id);

//make the template and send it out
$view->buildView();
