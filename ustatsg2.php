<?php

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  UTF-8 ąść
 * ************************************************************************* */

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
global $stat_menu, $dynbasepath;
;
//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {

        // check for old-style parameters
        if (isset($_REQUEST['userid'])) {
            $user_id = $_REQUEST['userid'];
        }
        $tplname = 'ustat';
        if ($user_id != $usr['userid']) {
            // do not highlight My stats menu item if browsing other users stats
            $mnu_siteid = 'start';
        }


        $stat_menu = array(
            'title' => 'Statictics',
            'menustring' => 'Statistics',
            'siteid' => 'statlisting',
            'visible' => false,
            'filename' => 'ustatsg2.php?userid=' . $user_id,
            'submenu' => array(
                array(
                    'title' => tr('generla_stat'),
                    'menustring' => tr('general_stat'),
                    'visible' => true,
                    'filename' => 'viewprofile.php?userid=' . $user_id,
                    'newwindow' => false,
                    'siteid' => 'general_stat',
                    'icon' => 'images/actions/stat'
                ),
                array(
                    'title' => tr('graph_created'),
                    'menustring' => tr('graph_created'),
                    'visible' => true,
                    'filename' => 'ustatsg1.php?userid=' . $user_id,
                    'newwindow' => false,
                    'siteid' => 'createstat',
                    'icon' => 'images/actions/stat'
                )
            )
        );

        $content = "";

        $rsGeneralStat = sql("SELECT  hidden_count, founds_count, log_notes_count, notfounds_count, username FROM `user` WHERE user_id=&1 ", $user_id);

        $user_record = sql_fetch_array($rsGeneralStat);
        tpl_set_var('username', $user_record['username']);
        if ($user_record['founds_count'] == 0) {
            $content .= '<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;' . tr("graph_find") . '</p></div><br /><br /><p> <b>' . tr("there_is_no_caches_found") . '</b></p>';
        } else {

            // calculate diif days between date of register on OC  to current date
            $rdd = sql("select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=&1 ", $user_id);
            $ddays = mysql_fetch_array($rdd);
            mysql_free_result($rdd);
            // calculate days caching
            // sql ("SELECT COUNT(*) FROM cache_logs WHERE type=1 AND user_id=&1 GROUP BY GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)",$user_id);

            $rsGeneralStat = sql("SELECT YEAR(`date_created`) usertime,hidden_count, founds_count, log_notes_count, username FROM `user` WHERE user_id=&1 ", $user_id);
            if ($rsGeneralStat !== false) {
                $user_record = sql_fetch_array($rsGeneralStat);

                tpl_set_var('username', $user_record['username']);
            }
            $content .='<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;' . tr("graph_find") . '</p></div><br />';
            $content .= '<p><img src="graphs/PieGraphustat.php?userid=' . $user_id . '&amp;t=cf"  border="0" alt="" width="500" height="300" /></p>';

            $year = date("Y");
            $rsCachesFindMonth = sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` , MONTH(`date`) `month` FROM `cache_logs` WHERE type=1 AND cache_logs.deleted='0' AND user_id=&1 AND YEAR(`date`)=&2 GROUP BY MONTH(`date`) , YEAR(`date`) ORDER BY YEAR(`date`) ASC, MONTH(`date`) ASC", $user_id, $year);

            if ($rsCachesFindMonth !== false) {
                //          while ($rcfm = mysql_fetch_array($rsCachesFindYear)){

                $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=cfm' . $year . '"  border="0" alt="" width="500" height="200" /></p>';

                if ($user_record['usertime'] != $year) {
                    $yearr = $year - 1;
                    $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=cfm' . $yearr . '"  border="0" alt="" width="500" height="200" /></p>';
                }
//          }
            }
            mysql_free_result($rsGeneralStat);
            mysql_free_result($rsCachesFindMonth);


            $rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `cache_logs` WHERE type=1 AND user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC", $user_id);

            if ($rsCachesFindYear !== false) {

                $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=cfy"  border="0" alt="" width="500" height="200" /></p>';
            }




            mysql_free_result($rsCachesFindYear);
        }


        // Parameter
        $jpeg_qualitaet = 80;
        $fontfile = "./lib/fonts/arial.ttf";
        $tplpath = 'images/PLmapa.gif';
        $im = ImageCreateFromGIF($tplpath);
        $clrWhite = ImageColorAllocate($im, 255, 255, 255);
        $clrBorder = ImageColorAllocate($im, 70, 70, 70);
        $clrBlack = ImageColorAllocate($im, 0, 0, 0);
        $clrRed = ImageColorAllocate($im, 255, 0, 0);
        $clrBlue = ImageColorAllocate($im, 0, 0, 255);
        $fontsize = 18;

        $wojewodztwa = array(
            'PL11' => array(110, 138), // Lodzkie
            'PL12' => array(155, 108), // Mazowieckie
            'PL21' => array(135, 208), // Malopolskie
            'PL22' => array(103, 188), // Slaskie
            'PL31' => array(200, 150), // Lubelskie
            'PL32' => array(180, 200), // Podkarpackie
            'PL33' => array(146, 170), // Swietokrzyskie
            'PL34' => array(195, 75), // Podlaskie
            'PL41' => array(65, 115), // Wielkopolskie
            'PL42' => array(26, 55), // Zachodniopmorskie
            'PL43' => array(19, 100), // Lubuskie
            'PL51' => array(35, 149), // Dolnoslaskie
            'PL52' => array(78, 169), // Opolskie
            'PL61' => array(90, 85), // Kujawskie
            'PL62' => array(145, 50), // Warminskie
            'PL63' => array(85, 43)     // Pomorskie
        );
        $wyniki = sql("SELECT cache_location.code3 wojewodztwo,COUNT(*) ilosc FROM cache_logs,cache_location WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0' AND cache_location.code3 IN ('PL11','PL12','PL21','PL22','PL31','PL32','PL33','PL34','PL41','PL42','PL43','PL51','PL52','PL61','PL62','PL63') AND cache_logs.cache_id=cache_location.cache_id GROUP BY cache_location.code3", 0);
        while ($wynik = sql_fetch_assoc($wyniki)) {
            $text = $wynik[ilosc];
            if ($text != "0")
                ImageTTFText($im, 14, 0, $wojewodztwa[$wynik[wojewodztwo]][0], $wojewodztwa[$wynik[wojewodztwo]][1], $clrBlack, $fontfile, $text);
        };

        /*
          // Lodzkie
          $sqlpl11 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL11' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl11;
          if($text!="0") ImageTTFText($im, 14, 0,110,138, $clrBlack, $fontfile, $text);
          //Malopolskie
          $sqlpl21 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL21' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl21;
          if($text!="0") ImageTTFText($im, 14, 0,135,208, $clrBlack, $fontfile, $text);
          //Slaskie
          $sqlpl22 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL22' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl22;
          if($text!="0") ImageTTFText($im, 14, 0,103,188, $clrBlack, $fontfile, $text);
          // zachodniopomorskie
          $sqlpl42 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL42' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl42;
          if($text!="0") ImageTTFText($im, 14, 0,26,55, $clrBlack, $fontfile, $text);
          // Lubelskie
          $sqlpl31 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL31' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl31;
          if($text!="0") ImageTTFText($im, 14, 0,200,150, $clrBlack, $fontfile, $text);
          // Podkarpackie
          $sqlpl32 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL32' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl32;
          if($text!="0") ImageTTFText($im, 14, 0,180,200, $clrBlack, $fontfile, $text);
          // Swietokrzyskie
          $sqlpl33 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL33' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl33;
          if($text!="0") ImageTTFText($im, 14, 0,146,170, $clrBlack, $fontfile, $text);
          // Podlaskie
          $sqlpl34 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL34' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl34;
          if($text!="0") ImageTTFText($im, 14, 0,195,75, $clrBlack, $fontfile, $text);
          // Wielkopolskie
          $sqlpl41 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type=1 AND cache_logs.deleted='0'  AND cache_location.code3='PL41' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl41;
          if($text!="0") ImageTTFText($im, 14, 0,65,115, $clrBlack, $fontfile, $text);
          // Lubuskie
          $sqlpl43 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL43' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl43;
          if($text!="0") ImageTTFText($im, 14, 0,19,100, $clrBlack, $fontfile, $text);
          // Dolnoslaskie
          $sqlpl51 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL51' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl51;
          if($text!="0") ImageTTFText($im, 14, 0,35,149, $clrBlack, $fontfile, $text);
          // Opolskie
          $sqlpl52 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL52' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl52;
          if($text!="0") ImageTTFText($im, 14, 0,78,169, $clrBlack, $fontfile, $text);
          // Kujawskie
          $sqlpl61 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL61' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl61;
          if($text!="0") ImageTTFText($im, 14, 0,90,85, $clrBlack, $fontfile, $text);
          //Warminskie
          $sqlpl62 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0'  AND cache_location.code3='PL62' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl62;
          if($text!="0") ImageTTFText($im, 14, 0,145,50, $clrBlack, $fontfile, $text);
          //Pomorskie
          $sqlpl63 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0' AND cache_location.code3='PL63' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl63;
          if($text!="0") ImageTTFText($im, 14, 0,85,43, $clrBlack, $fontfile, $text);
          //Mazowieckie
          $sqlpl12 = sqlValue("SELECT COUNT(*) founds_count
          FROM cache_logs,cache_location
          WHERE cache_logs.user_id=$user_id AND cache_logs.type='1' AND cache_logs.deleted='0' AND cache_location.code3='PL12' AND cache_logs.cache_id=cache_location.cache_id",0);
          $text = $sqlpl12;
          if($text!="0") ImageTTFText($im, 14, 0,155,108, $clrBlack, $fontfile, $text);
         */
        // write output
        Imagejpeg($im, $dynbasepath . 'images/statpics/mapstat' . $user_id . '.jpg', $jpeg_qualitaet);
        ImageDestroy($im);
        // generate number for refresh image
        $rand = rand();
        $content .= '<p style="margin-left: 125px;"><img src=/images/statpics/mapstat' . $user_id . '.jpg?rand=' . $rand . ' border="0" alt="" width="250" height="235" /></p>';

        tpl_set_var('content', $content);
    }
}
tpl_BuildTemplate();
?>
