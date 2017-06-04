<?php

use Utils\Database\XDb;
use lib\Objects\Coordinates\Coordinates;
//prepare the templates and include all neccessary

$tplname = 'coordinates';
require_once('./lib/common.inc.php');

require($stylepath . '/coordinates.inc.php');
require_once("./lib/tm_ll_lib.php");

$lat_float = 0;
if (isset($_REQUEST['lat']))
    $lat_float = (float) $_REQUEST['lat'];

$lon_float = 0;
if (isset($_REQUEST['lon']))
    $lon_float = (float) $_REQUEST['lon'];

$coords = Coordinates::FromCoordsFactory($lat_float, $lon_float);

tpl_set_var('coords_f1', $coords->getAsText(Coordinates::COORDINATES_FORMAT_DECIMAL));
tpl_set_var('coords_f2', $coords->getAsText(Coordinates::COORDINATES_FORMAT_DEG_MIN));
tpl_set_var('coords_f3', $coords->getAsText(Coordinates::COORDINATES_FORMAT_DEG_MIN_SEC));

/* $utm = cs2cs_utm($lat_float, $lon_float);

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
*/

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
if ($wp != '') {
    $rs = XDb::xSql(
        "SELECT `caches`.`name`, `user`.`username`
         FROM `caches` INNER JOIN `user` ON (`user`.`user_id`=`caches`.`user_id`)
         WHERE `caches`.`wp_oc`= ? ", $wp);

    if ($r = XDb::xFetchArray($rs)) {
        tpl_set_var('nocacheid_start', '');
        tpl_set_var('nocacheid_end', '');

        tpl_set_var('owner', htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cachename', htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('wp', htmlspecialchars($wp, ENT_COMPAT, 'UTF-8'));
    }
}

//make the template and send it out
tpl_BuildTemplate();

