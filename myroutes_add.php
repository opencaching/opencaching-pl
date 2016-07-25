<?php

use Utils\Database\XDb;
//prepare the templates and include all neccessary
global $rootpath;
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {

        $tplname = 'myroutes_add';
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
        // start submit
        if (isset($_POST['submitform'])) {

            // insert route name
            XDb::xSql(
                "INSERT INTO `routes` ( `route_id`, `user_id`, `name`, `description`, `radius` )
                VALUES ('', ?, ?, ?, ?)",
                $user_id, $name, $desc, $radius);

            $upload_filename = $_FILES['file']['tmp_name'];

            // get route_id
            $route_id = XDb::xMultiVariableQueryValue(
                "SELECT route_id FROM `routes`
                WHERE name=:1 AND description=:2 AND user_id=:3",
                0, $name, $desc, $user_id);

            // Read file KML with route, load in the KML file through the my_routes page, and run that KML file through GPSBABEL which has a tool interpolate data points in the route.
            if (!$error) {
                exec("/usr/bin/gpsbabel -i kml -f " . $upload_filename . " -x interpolate,distance=0.25k -o kml -F " . $upload_filename . "");
                $xml = simplexml_load_file($upload_filename);

                // get length route
                foreach ($xml->Document->Folder as $f) {
                    foreach ($f->Folder as $folder) {
                        $dis = $folder->description;
                        $dis1 = explode(" ", trim($dis));
                        $len = (float) $dis1[27];
                        XDb::xSql(
                            "UPDATE `routes` SET `length`=? WHERE `route_id`=?", $len, $route_id);
                    }
                }


                foreach ($xml->Document->Folder as $xmlelement) {
                    foreach ($xmlelement->Folder as $folder) {
                        foreach ($folder->Placemark->LineString->coordinates as $coordinates) {
                            if ($coordinates) {
                                $coords_raw = explode(" ", trim($coordinates));
                                foreach ($coords_raw as $coords_raw_part) {
                                    if ($coords_raw_part) {
                                        $coords_raw_parts = explode(",", $coords_raw_part);
                                        $coords[] = $coords_raw_parts[0];
                                        $coords[] = $coords_raw_parts[1];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // end of read
//we get the point data in to an array called $points:

            if (!$error && isset($coords)) {

                for ($i = 0; $i < count($coords) - 1; $i = $i + 2) {
                    $points[] = array("lon" => $coords[$i], "lat" => $coords[$i + 1]);
                    if (($coords[$i] + 0 == 0) OR ( $coords[$i + 1] + 0 == 0)) {
                        $error .= "Invalid Co-ords found in import file.<br>\n";
                        break;
                    }
                }
            }
// add it to the route_points database:
            $point_num = 0;
            if(isset($points)){
                foreach ($points as $point) {
                    $point_num++;
                    $result = XDb::xSql(
                        "INSERT into route_points (route_id,point_nr,lat,lon)
                        VALUES (?,?,?,?)",
                        $route_id, $point_num, $point["lat"], $point["lon"]);
                }
            }
            tpl_redirect('myroutes.php');
            exit;
        } //end submit
    }
}

//make the template and send it out
tpl_BuildTemplate();
