<?php

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************
  display all watches of this user
 * ************************************************************************** */

//prepare the templates and include all neccessary
require_once ('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'myroutes_add_map2';
        tpl_set_var('cachemap_header', '<script src="//maps.googleapis.com/maps/api/js?libraries=geometry&amp;sensor=false&amp;language=' . $lang . '" type="text/javascript"></script>');

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
            sql("INSERT INTO `routes` (`route_id`, `user_id`, `name`, `description`, `radius`, `length`) VALUES ('', '&1', '&2', '&3', '&4', '&5')", $user_id, $name, $desc, $radius, $length);

            // get route_id
            $route_id = sqlValue("SELECT route_id FROM `routes` WHERE name='$name' AND description='$desc' AND user_id=$user_id", 0);

            $point_num = 0;
            foreach ($route_points as $route_point) {
                $point_num++;
                $latlng = explode(",", $route_point);
                sql("INSERT into route_points (route_id,point_nr,lat,lon) VALUES (&1, &2, &3, &4)", $route_id, $point_num, $latlng[0], $latlng[1]);
            }

            tpl_redirect('myroutes.php');
            exit;
        } //end submit
    }
}
tpl_set_var('bodyMod', '');
//make the template and send it out
tpl_BuildTemplate();
?>
