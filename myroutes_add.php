<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

     display all watches of this user

 ****************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {

            $tplname = 'myroutes_add';
            $user_id = $usr['userid'];


                $name = isset($_POST['name']) ? $_POST['name'] : '';
                tpl_set_var('name', htmlspecialchars($name, ENT_COMPAT, 'UTF-8'));

                $desc = isset($_POST['desc']) ? $_POST['desc'] : '';
                tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'));

                $radius = isset($_POST['radius']) ? $_POST['radius'] : '0';
                tpl_set_var('radius', $radius);

                if (isset($_POST['back']))
                {
                            tpl_redirect('myroutes.php');
                            exit;
                }
                // start submit
                if (isset($_POST['submitform']))
                {
// insert route name

                        sql("INSERT INTO `routes` (
                                                    `route_id`,
                                                    `user_id`,
                                                    `name`,
                                                    `description`,
                                                    `radius`
                                                ) VALUES (
                                                    '', '&1', '&2', '&3', '&4')",
                                                $user_id,
                                                $name,
                                                $desc,
                                                $radius);

$upload_filename=$_FILES['file']['tmp_name'];

// get route_id
$route_id=sqlValue("SELECT route_id FROM `routes` WHERE name='$name' AND description='$desc' AND user_id=$user_id",0);

// Read file KML with route, load in the KML file through the my_routes page, and run that KML file through GPSBABEL which has a tool interpolate data points in the route.
if ( !$error ) {
exec("/usr/local/bin/gpsbabel -i kml -f ".$upload_filename." -x interpolate,distance=0.25k -o kml -F ".$upload_filename."");
$xml = simplexml_load_file($upload_filename);

    // get length route
foreach ($xml->Document->Folder as $f){
foreach ($f->Folder as $folder){
$dis=$folder->description;
$dis1=explode(" ",trim($dis));
$len=(float)$dis1[27];
    sql("UPDATE `routes` SET `length`='&1' WHERE `route_id`='&2'",$len,$route_id);
    }}


    foreach ( $xml->Document->Folder as $xmlelement ) {
    foreach ( $xmlelement->Folder as $folder ) {
    foreach ( $folder->Placemark->LineString->coordinates as $coordinates ) {
        if ( $coordinates ) {
        $coords_raw = explode(" ",trim($coordinates));
        foreach ( $coords_raw as $coords_raw_part ) {
        if ( $coords_raw_part ) {
        $coords_raw_parts = explode(",",$coords_raw_part);
        $coords[] = $coords_raw_parts[0];
        $coords[] = $coords_raw_parts[1];
        }}}}}}}
        // end of read
//we get the point data in to an array called $points:

if (!$error){
        for( $i=0; $i<count($coords)-1; $i=$i+2 ) {
        $points[] = array("lon"=>$coords[$i],"lat"=>$coords[$i+1]);
        if ( ($coords[$i]+0==0) OR ($coords[$i+1]+0==0) ) {
        $error .= "Invalid Co-ords found in import file.<br>\n";
        break;
            }
        }
    }
// add it to the route_points database:
        $point_num = 0;
        foreach ($points as $point) {
        $point_num++;
        $query = "INSERT into route_points (route_id,point_nr,lat,lon)"."VALUES ($route_id,$point_num,".addslashes($point["lat"]).",".addslashes($point["lon"]).");";
        $result=sql($query);
        }

                        tpl_redirect('myroutes.php');
                            exit;

                } //end submit

            }
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
