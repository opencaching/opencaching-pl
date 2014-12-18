<?php

/* * *************************************************************************
  ./cachemaps.php
  -------------------
  begin                : June 17 2006
  copyright            : (C) 2006 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  create receive an image from a wms-mapserver for caches

 * ************************************************************************** */

require_once('./lib/common.inc.php');
if ($error == true)
    redirect_na();

$wp = isset($_REQUEST['wp']) ? mb_trim($_REQUEST['wp']) : '';
$rs = sql("SELECT `caches`.`cache_id` `cache_id`, `caches`.`wp_oc` `wp_oc`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, IF(ISNULL(`cache_maps`.`cache_id`) OR `caches`.`last_modified`>`cache_maps`.`last_refresh`, 1, 0) AS `refresh` FROM `caches` LEFT JOIN `cache_maps` ON `caches`.`cache_id`=`cache_maps`.`cache_id` WHERE `caches`.`wp_oc`='&1'", $wp);
$r = sql_fetch_assoc($rs);
mysql_free_result($rs);

if ($r !== false) {
    $d1 = mb_substr($r['wp_oc'], 2, 1);
    $d2 = mb_substr($r['wp_oc'], 3, 1);
    $file = $cachemap_dir . $d1 . '/' . $d2 . '/' . $r['wp_oc'] . '.jpg';

    if (($r['refresh'] == 1) || !is_file($file)) {
        $url = $cachemap_wms_url;
        $url = mb_ereg_replace('{min_lon}', $r['longitude'] - $cachemap_size_lon / 2, $url);
        $url = mb_ereg_replace('{max_lon}', $r['longitude'] + $cachemap_size_lon / 2, $url);
        $url = mb_ereg_replace('{min_lat}', $r['latitude'] - $cachemap_size_lat / 2, $url);
        $url = mb_ereg_replace('{max_lat}', $r['latitude'] + $cachemap_size_lat / 2, $url);
        $url = mb_ereg_replace('{wp_oc}', $r['wp_oc'], $url);

        if (!is_dir($cachemap_dir . $d1))
            mkdir($cachemap_dir . $d1);
        if (!is_dir($cachemap_dir . $d1 . '/' . $d2))
            mkdir($cachemap_dir . $d1 . '/' . $d2);

        if (@copy($url, $file)) {
            $im = imagecreatefromjpeg($file);
            if (!$im)
                redirect_na(); // bild ist kein lesbares jpg

            $white = imagecolorallocate($im, 255, 255, 255);
            $green = imagecolorallocate($im, 100, 255, 100);

            imageline($im, ($cachemap_pixel_x / 2) - 10, ($cachemap_pixel_y / 2) - 1, ($cachemap_pixel_x / 2) + 10, ($cachemap_pixel_y / 2) - 1, $green);
            imageline($im, ($cachemap_pixel_x / 2) - 1, ($cachemap_pixel_y / 2) - 10, ($cachemap_pixel_x / 2) - 1, ($cachemap_pixel_y / 2) + 10, $green);

            imageline($im, ($cachemap_pixel_x / 2), ($cachemap_pixel_y / 2) - 10, ($cachemap_pixel_x / 2), ($cachemap_pixel_y / 2) + 10, $white);
            imageline($im, ($cachemap_pixel_x / 2) - 10, ($cachemap_pixel_y / 2), ($cachemap_pixel_x / 2) + 10, ($cachemap_pixel_y / 2), $white);

            imagecolordeallocate($im, $white);
            imagecolordeallocate($im, $green);

            imagejpeg($im, $file);
            imagedestroy($im);

            sql("INSERT INTO `cache_maps` (`cache_id`, `last_refresh`) VALUES ('&1', NOW()) ON DUPLICATE KEY UPDATE `last_refresh`=NOW()", $r['cache_id']);
        } else
            redirect_na(); // download fehlgeschlagen
    }

    tpl_redirect($cachemap_url . $d1 . '/' . $d2 . '/' . $r['wp_oc'] . '.jpg');
} else
    redirect_na(); // wp existiert nicht

function redirect_na()
{
    global $cachemap_dir;
    tpl_redirect('images/cachemaps/na.gif');
}

?>
