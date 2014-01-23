<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/



    //prepare the templates and include all neccessary
function convert($str)
{
    $str = mb_ereg_replace('ę', 'e', $str);
    $str = mb_ereg_replace('ó', 'o', $str);
    $str = mb_ereg_replace('ą', 'a', $str);
    $str = mb_ereg_replace('ś', 's', $str);
    $str = mb_ereg_replace('ł', 'l', $str);
    $str = mb_ereg_replace('ż', 'z', $str);
    $str = mb_ereg_replace('ź', 'z', $str);
    $str = mb_ereg_replace('ć', 'c', $str);
    $str = mb_ereg_replace('ń', 'n', $str);
    $str = mb_ereg_replace('Ę', 'E', $str);
    $str = mb_ereg_replace('Ó', 'O', $str);
    $str = mb_ereg_replace('Ą', 'A', $str);
    $str = mb_ereg_replace('Ś', 'S', $str);
    $str = mb_ereg_replace('Ł', 'L', $str);
    $str = mb_ereg_replace('Ż', 'Z', $str);
    $str = mb_ereg_replace('Ź', 'Z', $str);
    $str = mb_ereg_replace('Ć', 'C', $str);
    $str = mb_ereg_replace('Ń', 'N', $str);


    return $str;
}

    $tplname = 'garmin';
    require_once('./lib/common.inc.php');
    require($stylepath . '/garmin.inc.php');


    $lat = isset($_REQUEST['lat']) ? $_REQUEST['lat'] : '';
    $long = isset($_REQUEST['long']) ? $_REQUEST['long'] : '';
    $wp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';

    $str=convert($name);

    tpl_set_var('lat', $lat);
    tpl_set_var('long', $long);
    tpl_set_var('wp_oc',$wp);
    tpl_set_var('cachename',$str);



    //make the template and send it out
    tpl_BuildTemplate();


?>
