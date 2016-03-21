<?php

/**
 *  opensprawdzacz.php
 *  ------------------------------------------------------------------------------------------------
 *  Puzzle Final Cache coordinate checker
 *  ------------------------------------------------------------------------------------------------
 *  @author: Andrzej 'Łza' Woźniak [wloczynutka@gmail.com]
 *
 *
 *
 *  ================================================================================================
 *  to do:
 *  1.) validation data from post (should be numeric, but this is not a must - if data is not numeric
 *      we just get result that puzzle solution is incorrect)
 *  2.) convert section 2 to OOP.
 *  3.) after succesfull check waypoint (.gpx) generate with final stage coords, downloadable for
 *      GPS devices.
 *  ================================================================================================
 */

use Utils\Database\XDb;

// variables required by opencaching.pl
global $lang, $rootpath, $usr;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
require_once 'pagination_class.php';
require_once 'opensprawdzacz_classes.php';

//Preprocessing
if ($error == false) {
    // czy user zalogowany ?
    if ($usr == false) {
        // nie zalogowany wiec przekierowanie na strone z logowaniem
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        // wskazanie pliku z kodem html ktory jest w tpl/stdstyle/
        $tplname = 'opensprawdzacz';

        $OpensprawdzaczSetup = New OpensprawdzaczSetup();
        tpl_set_var('os_script', $OpensprawdzaczSetup->scriptname);

        tpl_set_var("sekcja_5_start", '<!--');
        tpl_set_var("sekcja_5_stop", '-->');

        $Opensprawdzacz = New OpensprawdzaczCore();

        /*
         * if isset $_POST['stopnie_N'] means that user entered coords to check.
         * - Checking for bruteforce (and prevent if detected)
         * - Checking if coordinates are correct, then display result
         */
        if (isset($_POST['stopnie_N'])) {
            $Opensprawdzacz->BruteForceCheck($OpensprawdzaczSetup);
            $Opensprawdzacz->CoordsComparing($opt);
        }


        $Opensprawdzacz->DisplayAllOpensprawdzaczCaches($OpensprawdzaczSetup, $opt);


        // sekcja 2 (wyswietla dane kesza i formularz do wpisania współrzędnych)
        // todo: zobiektywizować tą sekcję, zapytania przerobić na PDO


        $rs = XDb::xSql("SELECT `caches`.`name`,
                `caches`.`cache_id`,
                `caches`.`type`,
                `caches`.`user_id`,
                `cache_type`.`icon_large`,
                `user`.`username`
                FROM   `caches`, `user`, `cache_type`
                WHERE  `caches`.`user_id` = `user`.`user_id`
                AND    `caches`.`type` = `cache_type`.`id`
                AND    `caches`.`wp_oc` = ? ", $Opensprawdzacz->cache_wp);



        // przekaznie wynikow w postaci zmiennych do pliku z kodem html
        tpl_set_var("sekcja_1_start", '<!--');
        tpl_set_var("sekcja_1_stop", '-->');
        tpl_set_var("sekcja_2_start", '');
        tpl_set_var("sekcja_2_stop", '');
        tpl_set_var("sekcja_3_start", '<!--');
        tpl_set_var("sekcja_3_stop", '-->');
        tpl_set_var("sekcja_4_start", '<!--');
        tpl_set_var("sekcja_4_stop", '-->');

        $czyjest = XDb::xNumRows($rs);
        if ($czyjest == 0) {
            tpl_set_var("ni_ma_takiego_kesza", tr(ni_ma_takiego_kesza));
            tpl_set_var("sekcja_2_start", '<!--');
            tpl_set_var("sekcja_2_stop", '-->');
            tpl_set_var("sekcja_5_start", '');
            tpl_set_var("sekcja_5_stop", '');
            $Opensprawdzacz->endzik();
        }

        $record = Xdb::xFetchArray($rs);
        $cache_id = $record['cache_id'];

        tpl_set_var("wp_oc", $Opensprawdzacz->cache_wp);
        tpl_set_var("ikonka_keszyny", '<img src="tpl/stdstyle/images/' . $record['icon_large'] . '" />');
        tpl_set_var("cacheid", $record['cache_id']);
        tpl_set_var("ofner", $record['username']);
        tpl_set_var("cachename", $record['name']);
        tpl_set_var("id_uzyszkodnika", $record['user_id']);

        Xdb::xFreeResults($rs);


        $wp_rs = XDb::xSql("SELECT `waypoints`.`wp_id`,
                `waypoints`.`type`,
                `waypoints`.`longitude`,
                `waypoints`.`latitude`,
                `waypoints`.`status`,
                `waypoints`.`type`,
                `waypoints`.`opensprawdzacz`
                FROM `waypoints`
                WHERE `cache_id`= ? AND `type` = 3 ", $cache_id);

        $wp_record = XDb::xFetchArray($wp_rs);
        if (($wp_record['type'] == 3) && ($wp_record['opensprawdzacz'] == 1)) {
            tpl_set_var("sekcja_formularz_opensprawdzacza_start", '');
            tpl_set_var("sekcja_formularz_opensprawdzacza_stop", '');
            tpl_set_var("okienka", '');
        } else {
            tpl_set_var("okienka", tr('os_nie_ma_w_os'));
            tpl_set_var("sekcja_formularz_opensprawdzacza_start", '<!--');
            tpl_set_var("sekcja_formularz_opensprawdzacza_stop", '-->');
        }
    }
    XDb::xFreeResults($wp_rs);
}

// budujemy kod html ktory zostaje wsylany do przegladraki
$Opensprawdzacz->endzik();
?>