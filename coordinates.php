<?php
/***************************************************************************
                                         ./coordinates.php
                                         -------------------
        begin                : June 24 2004
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

 ***************************************************************************/

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

     view cache coordinates in other (country dependent) coordinate systems

     used template(s): coordinates

     GET Parameter: lat, lon

 ****************************************************************************/

    //prepare the templates and include all neccessary

    $tplname = 'coordinates';
    require_once('./lib/common.inc.php');
//phpinfo();
//die();
    require($stylepath . '/coordinates.inc.php');
    //require_once("./lib/cs2cs.inc.php");
    require_once("./lib/tm_ll_lib.php");

    $lat_float = 0;
    if (isset($_REQUEST['lat']))
        $lat_float = (float) $_REQUEST['lat'];

    $lon_float = 0;
    if (isset($_REQUEST['lon']))
        $lon_float = (float) $_REQUEST['lon'];

    list($lon_dir, $lon_deg_int, $lon_min_int, $lon_sec_float, $lon_min_float) = help_lonToArray2($lon_float);
    list($lat_dir, $lat_deg_int, $lat_min_int, $lat_sec_float, $lat_min_float) = help_latToArray2($lat_float);

    tpl_set_var('lon_float', sprintf('%0.5f', abs($lon_float)));
    tpl_set_var('lon_dir', $lon_dir);
    tpl_set_var('lon_deg_int', $lon_deg_int);
    tpl_set_var('lon_min_int', $lon_min_int);
    tpl_set_var('lon_sec_float', $lon_sec_float);
    tpl_set_var('lon_min_float', $lon_min_float);

    tpl_set_var('lat_float', sprintf('%0.5f', abs($lat_float)));
    tpl_set_var('lat_dir', $lat_dir);
    tpl_set_var('lat_deg_int', $lat_deg_int);
    tpl_set_var('lat_min_int', $lat_min_int);
    tpl_set_var('lat_sec_float', $lat_sec_float);
    tpl_set_var('lat_min_float', $lat_min_float);

    /*$utm = cs2cs_utm($lat_float, $lon_float);

    tpl_set_var('utm_zone', $utm[0]);
    tpl_set_var('utm_letter', $utm[1]);
    tpl_set_var('utm_east', (int) $utm[2]);
    tpl_set_var('utm_north', (int) $utm[3]);

    $xy1992 = cs2cs_1992($lat_float, $lon_float);
    tpl_set_var('x1992', (int) $xy1992[0]);
    tpl_set_var('y1992', (int) $xy1992[1]);

    $gk = cs2cs_gk($lat_float, $lon_float);

    tpl_set_var('gk_rechts', (int) $gk[0]);
    tpl_set_var('gk_hoch', (int) $gk[1]);

    $qthlocator = help_latlongToQTH($lat_float, $lon_float);

    tpl_set_var('qthlocator', $qthlocator);*/

    $utm = ll2utm($lat_float, $lon_float);

    tpl_set_var('utm2_zone', $utm[0]);
    tpl_set_var('utm2_letter', $utm[1]);
    tpl_set_var('utm2_NS', $utm[2]);
    tpl_set_var('utm2_north', (int) $utm[3]);
    tpl_set_var('utm2_EW', $utm[4]);
    tpl_set_var('utm2_east', (int) $utm[5]);

    tpl_set_var('nocacheid_start', '<!--');
    tpl_set_var('nocacheid_end', '-->');
    tpl_set_var('owner', '');
    tpl_set_var('cachename', '');
    tpl_set_var('wp', '');

    // wp gesetzt?

    $wp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
    if ($wp != '')
    {
        $rs = sql("SELECT `caches`.`name`, `user`.`username` FROM `caches` INNER JOIN `user` ON (`user`.`user_id`=`caches`.`user_id`) WHERE `caches`.`wp_oc`='&1'", $wp);

        if ($r = sql_fetch_array($rs))
        {
            tpl_set_var('nocacheid_start', '');
            tpl_set_var('nocacheid_end', '');

            tpl_set_var('owner', htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('cachename', htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('wp', htmlspecialchars($wp, ENT_COMPAT, 'UTF-8'));
        }
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
