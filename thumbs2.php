<?php

/* * *************************************************************************
  ./thumbs.php
  -------------------
  begin                : September 13 2005
  copyright            : (C) 2005 The OpenCaching Group
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

  create a thumb of an image ... use caching of the minimized picture

 * ************************************************************************** */

require_once('./lib/common.inc.php');

$uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : '';
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : 0;

// TODO: zmerge'ować z thumbs.php (jedyna różnica to wielkość generowanej miniaturki, i katalog zapisu)
/* TODO: zmiana obrazka powinna odświeżać obydwie miniaturki, teraz tak nie jest - 
  odświeżana jest tylko ta, o którą pierwszą zapyta przeglądarka */
// TODO: uuid (renderowany w HTMLu zawsze) zdradza adres obrazka

if ($error == false) {
    require_once($stylepath . '/thumbs.inc.php');

    $rs = sql("SELECT `local`, `spoiler`, `url`, `thumb_last_generated`, `last_modified`, `unknown_format`, `uuid`, `thumb_url` FROM `pictures` WHERE `uuid`='&1'", $uuid);
    if (mysql_num_rows($rs) == 1) {
        $r = sql_fetch_array($rs);
        mysql_free_result($rs);

        if ($r['local'] == 0)
            if ($debug == 1)
                die('Debug: line ' . __LINE__);
            else
                tpl_redirect_absolute($imgurl_extern);

        if (($r['spoiler'] == 1) && ($_REQUEST['showspoiler'] != '1'))
            if ($debug == 1)
                die('Debug: line ' . __LINE__);
            else
                tpl_redirect_absolute($imgurl_spoiler);

        $imgurl = $r['url'];
        $urlparts = mb_split('/', $imgurl);
        if (!file_exists($picdir . '/' . $urlparts[count($urlparts) - 1]))
            if ($debug == 1)
                die('Debug: line ' . __LINE__);
            else
                tpl_redirect_absolute($imgurl_intern);

        // thumb neu erstellen?
        $bGenerate = false;
        if (strtotime($r['thumb_last_generated']) < strtotime($r['last_modified']))
            $bGenerate = true;

        if (!file_exists($thumbdir . '2/' . mb_substr($urlparts[count($urlparts) - 1], 0, 1) . '/' . mb_substr($urlparts[count($urlparts) - 1], 1, 1) . '/' . $urlparts[count($urlparts) - 1]))
            $bGenerate = true;

        if ($bGenerate) {
            // Bild erstellen

            if ($r['unknown_format'] == 1)
                if ($debug == 1)
                    die('Debug: line ' . __LINE__);
                else
                    tpl_redirect_absolute($imgurl_format);

            // ok, mal kucken ob das Dateiformat unterstützt wird
            $filename = $urlparts[count($urlparts) - 1];
            $filenameparts = mb_split('\\.', $filename);
            $extension = mb_strtolower($filenameparts[count($filenameparts) - 1]);

            if (mb_strpos($picextensions, ';' . $extension . ';') === false) {
                sql("UPDATE `pictures` SET `unknown_format`=1 WHERE `uuid`='&1'", $r['uuid']);

                if ($debug == 1)
                    die('Debug: line ' . __LINE__);
                else
                    tpl_redirect_absolute($imgurl_format);
            }

            if ($extension == 'jpeg')
                $extension = 'jpg';
            switch ($extension) {
                case 'jpg':
                    $im = imagecreatefromjpeg($picdir . '/' . $filename);
                    break;

                case 'gif':
                    $im = imagecreatefromgif($picdir . '/' . $filename);
                    break;

                case 'png':
                    $im = imagecreatefrompng($picdir . '/' . $filename);
                    break;

                case 'bmp':
                    require($rootpath . 'lib/imagebmp.inc.php');
                    $im = imagecreatefrombmp($picdir . '/' . $filename);
                    break;
            }

            if ($im == '') {
                sql("UPDATE `pictures` SET `unknown_format`=1 WHERE `uuid`='&1'", $r['uuid']);

                if ($debug == 1)
                    die('Debug: line ' . __LINE__);
                else
                    tpl_redirect_absolute($imgurl_format);
            }

            $imheight = imagesy($im);
            $imwidth = imagesx($im);

            if (($imheight > $thumb2_max_height) || ($imwidth > $thumb2_max_width)) {
                if ($imheight > $imwidth) {
                    $thumbheight = $thumb2_max_height;
                    $thumbwidth = $imwidth * ($thumbheight / $imheight);
                } else {
                    $thumbwidth = $thumb2_max_width;
                    $thumbheight = $imheight * ($thumbwidth / $imwidth);
                }
            } else {
                $thumbwidth = $imwidth;
                $thumbheight = $imheight;
            }


            // Thumb erstellen und speichern
            $thumbimage = imagecreatetruecolor($thumbwidth, $thumbheight);
            imagecopyresampled($thumbimage, $im, 0, 0, 0, 0, $thumbwidth, $thumbheight, $imwidth, $imheight);

            // verzeichnis erstellen
            if (!file_exists($thumbdir . '2/' . mb_substr($filename, 0, 1)))
                mkdir($thumbdir . '2/' . mb_substr($filename, 0, 1));
            if (!file_exists($thumbdir . '2/' . mb_substr($filename, 0, 1) . '/' . mb_substr($filename, 1, 1)))
                mkdir($thumbdir . '2/' . mb_substr($filename, 0, 1) . '/' . mb_substr($filename, 1, 1));

            $savedir = $thumbdir . '2/' . mb_substr($filename, 0, 1) . '/' . mb_substr($filename, 1, 1);


            switch ($extension) {
                case 'jpg':
                    imagejpeg($thumbimage, $savedir . '/' . $filename);
                    break;

                case 'gif':
                    imagegif($thumbimage, $savedir . '/' . $filename);
                    break;

                case 'png':
                    imagepng($thumbimage, $savedir . '/' . $filename);
                    break;

                case 'bmp':
                    imagebmp($thumbimage, $savedir . '/' . $filename);
                    break;
            }

            sql("UPDATE `pictures` SET `thumb_last_generated`=NOW(), `thumb_url`='&1' WHERE `uuid`='&2'", $thumburl . '2/' . mb_substr($filename, 0, 1) . '/' . mb_substr($filename, 1, 1) . '/' . $filename, $r['uuid']);

            if ($debug == 1)
                die($thumburl . '2/' . $filename);
            else
                tpl_redirect_absolute($thumburl . '2/' . mb_substr($filename, 0, 1) . '/' . mb_substr($filename, 1, 1) . '/' . $filename);
        }
        else {
            if ($debug == 1)
                die($thumburl . '2/' . $filename);
            else
                tpl_redirect_absolute($r['thumb_url']);
        }
    }
    else {
        mysql_free_result($rs);

        if ($debug == 1)
            die('Debug: line ' . __LINE__);
        else
            tpl_redirect_absolute($imgurl_404);
    }
    exit;
}

tpl_BuildTemplate(false);
?>