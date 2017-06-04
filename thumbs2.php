<?php

use Utils\Database\XDb;
require_once('./lib/common.inc.php');

$uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : '';
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : 0;

global $picdir, $picurl;

// thumbs-dir/url
$thumbdir = $picdir . '/thumbs';
$thumburl = $picurl . '/thumbs';


// TODO: zmerge'ować z thumbs.php (jedyna różnica to wielkość generowanej miniaturki, i katalog zapisu)
/* TODO: zmiana obrazka powinna odświeżać obydwie miniaturki, teraz tak nie jest -
  odświeżana jest tylko ta, o którą pierwszą zapyta przeglądarka */
// TODO: uuid (renderowany w HTMLu zawsze) zdradza adres obrazka

if ($error == false) {
    require_once($stylepath . '/thumbs.inc.php');

    $rs = XDb::xSql(
        "SELECT `local`, `spoiler`, `url`, `thumb_last_generated`, `last_modified`, `unknown_format`, `uuid`, `thumb_url`
        FROM `pictures` WHERE `uuid`= ? LIMIT 1", $uuid);

    if ($r = XDb::xFetchArray($rs)) {

        if ($r['local'] == 0)
            if ($debug == 1)
                die('Debug: line ' . __LINE__);
            else
                tpl_redirect_absolute($imgurl_extern);

        if ( ($r['spoiler'] == 1) &&
             (!isset($_REQUEST['showspoiler']) ||
              $_REQUEST['showspoiler'] != '1'
             )
           )
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

            if (mb_strpos($config['limits']['image']['extension'], ';' . $extension . ';') === false) {
                XDb::xSql(
                    "UPDATE `pictures` SET `unknown_format`=1 WHERE `uuid`= ? ", $r['uuid']);

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
                XDb::xSql(
                    "UPDATE `pictures` SET `unknown_format`=1 WHERE `uuid`= ? ", $r['uuid']);

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

            XDb::xSql(
                "UPDATE `pictures` SET `thumb_last_generated`=NOW(), `thumb_url`=?
                WHERE `uuid`= ? LIMIT 1",
                $thumburl . '2/' . mb_substr($filename, 0, 1) . '/' . mb_substr($filename, 1, 1) . '/' . $filename, $r['uuid']);

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

    } else {

        if ($debug == 1)
            die('Debug: line ' . __LINE__);
        else
            tpl_redirect_absolute($imgurl_404);
    }
    exit;
}

tpl_BuildTemplate(false);

