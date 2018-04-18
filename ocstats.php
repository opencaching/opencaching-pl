<?php

use Utils\Database\XDb;

require_once('./lib/common.inc.php');
global $dynbasepath;

// Parameter
$jpeg_qualitaet = 80;
$fontfile = "./lib/fonts/arial.ttf";

# get userid and style from URL
$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : 0;

// nie licz spotkan, skrzynek jeszcze nieaktywnych, zarchiwizowanych i wstrzymanych
$hidden = XDb::xMultiVariableQueryValue(
    "SELECT COUNT(*) FROM caches
    WHERE user_id= :1 AND status <> 2 AND status <> 3 AND status <> 4
        AND status <> 5 AND status <> 6 AND type <> 6",
    0, $userid);

$found = XDb::xMultiVariableQueryValue(
    "SELECT COUNT(*) founds_count FROM cache_logs
    WHERE user_id= :1 AND type=1 AND deleted=0", 0, $userid);

# get detailed info from DB
$rs = XDb::xSql(
    "SELECT `username`, `statpic_logo`, `statpic_text` FROM `user`
    WHERE `user_id`= ? LIMIT 1", $userid);

if ($record = XDb::xFetchArray($rs)) {

    $username = $record['username'];
    $logo = isset($record['statpic_logo']) ? $record['statpic_logo'] : 0;
    $logotext = isset($record['statpic_text']) ? $record['statpic_text'] : 'Opencaching';
} else {
    $userid = 0;
    $username = "<User not known>";
    $logo = 0;
    $logotext = 'Opencaching';
}

XDb::xFreeResults($rs);

$userid = $userid + 0;

if (!file_exists($dynbasepath . 'images/statpics/statpic' . $userid . '.jpg')) {
    // picture does not exist => create new
    $rs = XDb::xSql(
        "SELECT `tplpath`, `maxtextwidth` FROM `statpics` WHERE `id`= ? LIMIT 1", $logo);

    if ($record = XDb::xFetchArray($rs)) {

        $tplpath = $record['tplpath'];
        $maxtextwidth = $record['maxtextwidth'];
    } else {
        $tplpath = 'images/ocstats1.gif';
        $maxtextwidth = 60;
        $logo = 1;
    }
    XDb::xFreeResults($rs);

    $im = ImageCreateFromGIF($tplpath);
    $clrWhite = ImageColorAllocate($im, 255, 255, 255);
    $clrBorder = ImageColorAllocate($im, 70, 70, 70);
    $clrBlack = ImageColorAllocate($im, 0, 0, 0);
    $clrBlue = ImageColorAllocate($im, 0, 0, 255);

    switch ($logo) {
        case 4:
        case 5:
            // write text
            $fontsize = 10;
            $text = $username;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 8 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 8 : $maxtextwidth, 15, $clrBlack, $fontfile, $text);
            $fontsize = 8;
            $text = tr('statpic_found') . $found . ' / ' . tr('statpic_hidden') . $hidden;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 8 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 8 : $maxtextwidth, 32, $clrBlack, $fontfile, $text);
            break;
        case 2:
            // write text
            $fontsize = 10;
            $text = $username;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 8 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 8 : $maxtextwidth, 15, $clrBlack, $fontfile, $text);
            $fontsize = 7;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $logotext);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 5 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 5 : $maxtextwidth, 29, $clrBlack, $fontfile, $logotext);
            $fontsize = 8;
            $text = tr('statpic_found') . $found . ' / ' . tr('statpic_hidden') . $hidden;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 8 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 8 : $maxtextwidth, 45, $clrBlack, $fontfile, $text);
            break;
        case 6:
        case 7:
            // write text
            $fontsize = 10;
            $text = $username;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 5 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 5 : $maxtextwidth, 15, $clrBlack, $fontfile, $text);
            $fontsize = 7.5;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $logotext);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 5 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 5 : $maxtextwidth, 32, $clrBlack, $fontfile, $logotext);
            break;
        case 1:
        default:
            // write text
            $fontsize = 10;
            $text = $username;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 5 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 5 : $maxtextwidth, 15, $clrBlack, $fontfile, $text);
            $fontsize = 8;
            $text = tr('statpic_found') . $found . ' / ' . tr('statpic_hidden') . $hidden;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 8 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 8 : $maxtextwidth, 29, $clrBlack, $fontfile, $text);
            $fontsize = 8;
            $textsize = imagettfbbox($fontsize, 0, $fontfile, $logotext);
            ImageTTFText($im, $fontsize, 0, (imagesx($im) - ($textsize[2] - $textsize[0]) - 5 > $maxtextwidth) ? imagesx($im) - ($textsize[2] - $textsize[0]) - 5 : $maxtextwidth, 45, $clrBlack, $fontfile, $logotext);
    }

    // draw border
    ImageRectangle($im, 0, 0, imagesx($im) - 1, imagesy($im) - 1, $clrBorder);
    // write output
    Imagejpeg($im, $dynbasepath . 'images/statpics/statpic' . $userid . '.jpg', $jpeg_qualitaet);
    ImageDestroy($im);
}

// redirect to the saved picture
tpl_redirect('images/statpics/statpic' . $userid . '.jpg');

