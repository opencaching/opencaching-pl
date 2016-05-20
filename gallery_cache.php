<?php

use Utils\Database\XDb;
use Utils\Database\OcDb;
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //set here the template to process
    $tplname = 'gallery_cache';

    require_once('./lib/caches.inc.php');
    require($stylepath . '/lib/icons.inc.php');
    require($stylepath . '/viewcache.inc.php');
    require($stylepath . '/gallery_cache.inc.php');
    require($stylepath . '/smilies.inc.php');
    global $usr;


    $cache_id = 0;
    if (isset($_REQUEST['cacheid'])) {
        $cache_id = $_REQUEST['cacheid'];
    }

    global $hide_coords;
    if ($usr == false && $hide_coords) {
        $disable_spoiler_view = true; //hide any kind of spoiler if usr not logged in
    } else {
        $disable_spoiler_view = false;
    };


    if ($cache_id != 0) {
        //get cache record
        $rs = XDb::xSql(
            "SELECT `user_id`, `name`, `founds`, `notfounds`, `notes`, `status`, `type`
            FROM `caches` WHERE `caches`.`cache_id`= ? ", $cache_id);

        if (!$cache_record = XDb::xFetchArray($rs)) {
            $cache_id = 0;
        } else {

            // check if the cache is published, if not only the owner is allowed to view the log
            if (($cache_record['status'] == 4 || $cache_record['status'] == 5 || $cache_record['status'] == 6 ) && ($cache_record['user_id'] != $usr['userid'] && !$usr['admin'])) {
                $cache_id = 0;
            }
        }
        XDb::xFreeResults($rs);
    } else {

        //get cache record
        $rs = XDb::xSql(
            "SELECT `cache_logs`.`cache_id`,`caches`.`user_id`, `caches`.`name`, `caches`.`founds`,
                    `caches`.`notfounds`, `caches`.`notes`, `caches`.`status`, `caches`.`type`
            FROM `caches`,`cache_logs`
            WHERE `cache_logs`.`id`= ?
                AND `cache_logs`.`deleted` = 0
                AND `caches`.`cache_id`=`cache_logs`.`cache_id` ",
            $logid);

        if (! $cache_record = XDb::xFetchArray($rs)) {
            $cache_id = 0;
        } else {

            // check if the cache is published, if not only the owner is allowed to view the log
            if (($cache_record['status'] == 4 || $cache_record['status'] == 5 || $cache_record['status'] == 6 ) && ($cache_record['user_id'] != $usr['userid'] && !$usr['admin'])) {
                $cache_id = 0;
            } else {
                $cache_id = $cache_record['cache_id'];
            }
        }
        XDb::xFreeResults($rs);
    }


    if ($cache_id != 0) {
        //ok, cache is here, let's process
        $owner_id = $cache_record['user_id'];

        //cache data
        tpl_set_var('cachename', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cacheid', $cache_id);

        $pictureslog = '';

        // replace smilies in log-text with images
        // pictures

        $cachepicturelines = '';
        $append_atag = '';

        $dbc = OcDb::instance();

        $thatquery = "SELECT `pictures`.`url`, `pictures`.`title`, `pictures`.`uuid`, `pictures`.`user_id`,`pictures`.`object_id`, `pictures`.`spoiler` FROM `pictures` WHERE `pictures`.`object_id`=:v1 AND `pictures`.`object_type`=2 ORDER BY `pictures`.`seq`, `pictures`.`date_created` ASC";
        //// requires: ALTER TABLE `pictures` ADD `seq` SMALLINT UNSIGNED NOT NULL DEFAULT '1';
        $params['v1']['value'] = (integer) $cache_id;
        ;
        $params['v1']['data_type'] = 'integer';
        $s = $dbc->paramQuery($thatquery, $params);
        unset($params); //clear to avoid overlaping on next paramQuery (if any))
        $rscpictures_count = $dbc->rowCount($s);

        if ($rscpictures_count != 0) {
            tpl_set_var('cache_images_start', '');
            tpl_set_var('cache_images_end', '');
        } else {
            tpl_set_var('cache_images_start', '<!--');
            tpl_set_var('cache_images_end', '-->');
        }
        $rscpictures_all = $dbc->dbResultFetchAll($s);
        unset($dbc);

        for ($j = 0; $j < $rscpictures_count; $j++) {
            $pic_crecord = $rscpictures_all[$j];
            $thisline = $cachepicture;

            if ($disable_spoiler_view && intval($pic_crecord['spoiler']) == 1) {  // if hide spoiler (due to user not logged in) option is on prevent viewing pic link and show alert
                $thisline = mb_ereg_replace('{log_picture_onclick}', "alert('" . $spoiler_disable_msg . "'); return false;", $thisline);
                $thisline = mb_ereg_replace('{link}', 'index.php', $thisline);
                $thisline = mb_ereg_replace('{longdesc}', 'index.php', $thisline);
            } else {
                $thisline = mb_ereg_replace('{log_picture_onclick}', "enlarge(this)", $thisline);
                $thisline = mb_ereg_replace('{link}', $pic_crecord['url'], $thisline);
                $thisline = mb_ereg_replace('{longdesc}', str_replace("uploads", "uploads", $pic_crecord['url']), $thisline);
            };

            $thisline = mb_ereg_replace('{imgsrc}', 'thumbs.php?uuid=' . urlencode($pic_crecord['uuid']), $thisline);
            if ($pic_crecord['title'] == "") {
                $title = "link";
            } else {
                $title = htmlspecialchars($pic_crecord['title'], ENT_COMPAT, 'UTF-8');
            }
            $thisline = mb_ereg_replace('{title}', $title, $thisline);

            $cachepicturelines .= $thisline;
        }
        $tmplog = $cachepicturelines;
        $clogs = "$tmplog\n";

        tpl_set_var('cachepictures', $clogs);

        $logpicturelines = '';
        $append_atag = '';
        $rspictures = XDb::xSql(
            "SELECT `pictures`.`url`, `pictures`.`title`, `pictures`.`uuid`, `pictures`.`user_id`,
                    `pictures`.`object_id`, `pictures`.`spoiler`
            FROM `pictures`,`cache_logs`
            WHERE `pictures`.`object_id`=`cache_logs`.`id` AND `cache_logs`.`deleted` = 0
                AND `pictures`.`object_type`=1
                AND `cache_logs`.`cache_id`= ?
            ORDER BY `pictures`.`date_created` DESC",
            $cache_id);

        if(! $pic_record = XDb::xFetchArray($rspictures) ){
            //no records
            tpl_set_var('logs_images_start', '<!--');
            tpl_set_var('logs_images_end', '-->');
        }else{
            //there are records
            tpl_set_var('logs_images_start', '');
            tpl_set_var('logs_images_end', '');

            do {

                $thisline = $logpicture;
                if ($disable_spoiler_view && intval($pic_record['spoiler']) == 1) {  // if hide spoiler (due to user not logged in) option is on prevent viewing pic link and show alert
                    $thisline = mb_ereg_replace('{log_picture_onclick}', "alert('" . $spoiler_disable_msg . "'); return false;", $thisline);
                    $thisline = mb_ereg_replace('{link}', 'index.php', $thisline);
                    $thisline = mb_ereg_replace('{longdesc}', 'index.php', $thisline);
                } else {
                    $thisline = mb_ereg_replace('{log_picture_onclick}', "enlarge(this)", $thisline);
                    $thisline = mb_ereg_replace('{link}', $pic_record['url'], $thisline);
                    $thisline = mb_ereg_replace('{longdesc}', str_replace("uploads", "uploads", $pic_record['url']), $thisline);
                };

                $thisline = mb_ereg_replace('{imgsrc}', 'thumbs.php?uuid=' . urlencode($pic_record['uuid']), $thisline);
                if ($pic_record['title'] == "") {
                    $title = "link";
                } else {
                    $title = htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8');
                }
                $thisline = mb_ereg_replace('{title}', "<a class=links href=viewlogs.php?logid=" . $pic_record['object_id'] . ">" . $title . "</a>", $thisline);

                $logpicturelines .= $thisline;
            }while( $pic_record = XDb::xFetchArray($rspictures) );

        }
        XDb::xFreeResults($rspictures);

        $tmplog = $logpicturelines;

        $logs = "$tmplog\n";

        tpl_set_var('logpictures', $logs);
    } else {
        //display search page
        // redirection
        tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id));
        exit;
    }
}

//make the template and send it out
tpl_BuildTemplate();
