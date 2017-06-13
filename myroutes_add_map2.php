<?php

use Utils\Database\XDb;
//prepare the templates and include all neccessary
$rootpath = "./";
global $googlemap_key;

require_once ('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'myroutes_add_map2';
        tpl_set_var('cachemap_header', '<script src="https://maps.googleapis.com/maps/api/js?libraries=geometry&amp;key=' . $googlemap_key . '&amp;language=' . $lang . '" type="text/javascript"></script>');

        // set map center
        tpl_set_var('map_lat',$main_page_map_center_lat);
        tpl_set_var('map_lon',$main_page_map_center_lon);
        tpl_set_var('map_zoom',$default_country_zoom);

        $user_id = $usr['userid'];
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        tpl_set_var('name', htmlspecialchars($name, ENT_COMPAT, 'UTF-8'));

        $desc = isset($_POST['desc']) ? $_POST['desc'] : '';
        tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'));

        $radius = isset($_POST['radius']) ? $_POST['radius'] : '0';
        tpl_set_var('radius', $radius);

        if (isset($_POST['back'])) {
            tpl_redirect('myroutes.php');
            exit;
        }

        if (isset($_POST['submitform'])) {
            $length = isset($_POST['distance']) ? $_POST['distance'] : '0';
            $route_points = isset($_POST['route_points']) ? explode(" ", $_POST['route_points']) : array();

            // insert route name
            XDb::xSql(
                "INSERT INTO `routes` (
                    `route_id`, `user_id`, `name`, `description`, `radius`, `length`)
                VALUES ('', ?, ?, ?, ?, ?)",
                $user_id, $name, $desc, $radius, $length);

            // get route_id
            $route_id = XDb::xMultiVariableQueryValue(
                "SELECT route_id FROM `routes`
                WHERE name= :1 AND description= :2 AND user_id= :3",
                0, $name, $desc, $user_id);

            $point_num = 0;
            foreach ($route_points as $route_point) {
                $point_num++;
                $latlng = explode(",", $route_point);
                XDb::xSql(
                    "INSERT into route_points (route_id,point_nr,lat,lon)
                    VALUES (?, ?, ?, ?)",
                    $route_id, $point_num, $latlng[0], $latlng[1]);
            }

            tpl_redirect('myroutes.php');
            exit;
        } //end submit
    }
}
tpl_set_var('bodyMod', '');
//make the template and send it out
tpl_BuildTemplate();

