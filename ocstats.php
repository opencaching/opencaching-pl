<?php

// Unicode Reminder ??
setlocale(LC_TIME, 'pl_PL.UTF-8');
//setlocale(LC_TIME, 'pl_PL.ISO-8859-2');
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
global $dynbasepath;

// Parameter
$jpeg_qualitaet = 80;
//  $fontfile = "./util/fonts/verdana.ttf";
$fontfile = "./lib/fonts/arial.ttf";

# get userid and style from URL
$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : 0;

# get detailed info from DB
$rs = sql("SELECT `username`, `statpic_logo`, `statpic_text` FROM `user` WHERE `user_id`='&1'", $userid);

// nie licz spotkan, skrzynek jeszcze nieaktywnych, zarchiwizowanych i wstrzymanych
$sql = "SELECT COUNT(*) FROM caches WHERE user_id='$userid' AND status <> 2 AND status <> 3 AND status <> 4 AND status <> 5 AND status <> 6 AND type <> 6";
if ($odp = mysql_query($sql))
    $hidden = mysql_result($odp, 0);
else
    $hidden = 0;

$sql = "SELECT COUNT(*) founds_count
                    FROM cache_logs
                    WHERE user_id=$userid AND type=1 AND deleted=0";
if ($odp = mysql_query($sql))
    $found = mysql_result($odp, 0);
else
    $found = 0;


if (mysql_num_rows($rs) == 1) {
    $record = sql_fetch_array($rs);
    $username = $record['username'];
    $logo = isset($record['statpic_logo']) ? $record['statpic_logo'] : 0;
    $logotext = isset($record['statpic_text']) ? $record['statpic_text'] : 'Opencaching';
} else {
    $userid = 0;
    $username = "<User not known>";
    $logo = 0;
    $logotext = 'Opencaching';
}

mysql_free_result($rs);

$userid = $userid + 0;

if (!file_exists($dynbasepath . 'images/statpics/statpic' . $userid . '.jpg')) {
    // Bild existiert nicht => neu erstellen
    $rs = sql("SELECT `tplpath`, `maxtextwidth` FROM `statpics` WHERE `id`='&1'", $logo);

    if (mysql_num_rows($rs) == 1) {
        $record = sql_fetch_array($rs);
        $tplpath = $record['tplpath'];
        $maxtextwidth = $record['maxtextwidth'];
    } else {
        $tplpath = 'images/ocstats1.gif';
        $maxtextwidth = 60;
        $logo = 1;
    }
    mysql_free_result($rs);

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
            $text = tr(statpic_found) . $found . ' / ' . tr(statpic_hidden) . $hidden;
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
            $text = tr(statpic_found) . $found . ' / ' . tr(statpic_hidden) . $hidden;
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
            $text = tr(statpic_found) . $found . ' / ' . tr(statpic_hidden) . $hidden;
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

// Redirect auf das gespeicherte Bild
tpl_redirect('images/statpics/statpic' . $userid . '.jpg');
?>
