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
        if (isset($_REQUEST['routeid']))
            {
            $route_id = $_REQUEST['routeid'];
            }
            $remove = 0;
            if (isset($_REQUEST['delete']))
            {
            $route_id = $_REQUEST['routeid'];
            $remove = 1;
            }
            if (isset($_POST['delete']))
            {
            $route_id = $_POST['routeid'];
            $remove = 1;
            }
            if (isset($_POST['routeid'])){
            $route_id = $_POST['routeid'];}

            $tplname = 'myroutes_edit';
            $user_id = $usr['userid'];

                if (isset($_POST['back']))
                {
                            tpl_redirect('myroutes.php');
                            exit;
                }

            $route_rs = sql("SELECT `user_id`,`name`, `description`, `radius` FROM `routes` WHERE `route_id`='&1' AND `user_id`='&2'", $route_id,$user_id);
            $record = sql_fetch_array($route_rs);
            tpl_set_var('routes_name',$record['name']);

                $rname = isset($_POST['name']) ? $_POST['name'] : '';
                $rdesc = isset($_POST['desc']) ? $_POST['desc'] : '';
                $rradius = isset($_POST['radius']) ? $_POST['radius'] :'';

            tpl_set_var('cachemap_header', '<script src="//maps.googleapis.com/maps/api/js?libraries=geometry&amp;sensor=false&amp;language='.$lang.'" type="text/javascript"></script>');

            if ($record['user_id'] == $usr['userid'])
                {

                        if ($remove == 1)
                        {
                            //remove
                            sql("DELETE FROM `routes` WHERE `route_id`='&1' AND `user_id`='&2'", $route_id,$user_id);
                            sql("DELETE FROM `route_points` WHERE `route_id`='&1'", $route_id);
                            tpl_redirect('myroutes.php');
                            exit;
                        }
                }

                // start submit
                if (isset($_POST['submit']) && $remove == 0)
                {

                sql("UPDATE `routes` SET `name`='&1',`description`='&2',`radius`='&3' WHERE `route_id`='&4'",$rname,$rdesc,$rradius,$route_id);

                if ($_FILES['file']['tmp_name']!="") {

                sql("DELETE FROM `route_points` WHERE `route_id`='&1'", $route_id);

                $upload_filename=$_FILES['file']['tmp_name'];
// Read file KML with route
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
                } //end update points

                        tpl_redirect('myroutes.php');
                            exit;
                } //end submit
                tpl_set_var('name', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'));
                tpl_set_var('desc', htmlspecialchars($record['description'], ENT_COMPAT, 'UTF-8'));
                tpl_set_var('radius', $record['radius']);
                tpl_set_var('routeid', $route_id);
            }
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
