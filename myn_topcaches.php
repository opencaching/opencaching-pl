<?php

use Utils\Database\XDb;
use Utils\Gis\Gis;
use lib\Objects\ApplicationContainer;
use lib\Objects\GeoCache\GeoCacheLog;

const ITEMS_PER_PAGE = 50;

global $rootpath, $usr;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
require_once('./lib/calculation.inc.php');
require_once($stylepath . '/lib/icons.inc.php');

if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
} else {
    $applicationContainer = ApplicationContainer::Instance();
    tpl_set_tplname('myn_topcaches');
    require($stylepath . '/newcaches.inc.php');

    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
    $startat = $startat + 0;

    if ($startat < 0) $startat = 0;

    $perpage = 50;
    $startat -= $startat % $perpage;

    //get user record
    $user_id = $usr['userid'];
    tpl_set_var('userid', $user_id);

    $latitude = $applicationContainer->getLoggedUser()->getHomeCoordinates()->getLatitude();
    $longitude = $applicationContainer->getLoggedUser()->getHomeCoordinates()->getLongitude();

    if (($longitude == NULL && $latitude == NULL) || ($longitude == 0 && $latitude == 0)) {
        tpl_set_var('info', '<div class="notice">' . tr("myn_info") . '</div>');
    } else {
        tpl_set_var('info', '');
    }

    if ($latitude == NULL || $latitude == 0){
        $latitude = $applicationContainer->getOcConfig()->getMainPageMapCenterLat();
    }
    if ($longitude == NULL || $longitude == 0){
        $longitude = $applicationContainer->getOcConfig()->getMainPageMapCenterLon();
    }

    $distance = $applicationContainer->getLoggedUser()->getNotifyRadius();
    if ($distance == 0)
        $distance = 35;
    $distance_unit = 'km';
    $radius = $distance;

    $lat = $latitude;
    $lon = $longitude;


    //all target caches are between lat - max_lat_diff and lat + max_lat_diff
    $max_lat_diff = Gis::distanceInDegreesLat($distance);

    //all target caches are between lon - max_lon_diff and lon + max_lon_diff
    $max_lon_diff = Gis::distanceInDegreesLon($distance, $lat);

    XDb::xSql('DROP TEMPORARY TABLE IF EXISTS local_caches' . $user_id . '');
    XDb::xSql('CREATE TEMPORARY TABLE local_caches' . $user_id . ' ENGINE=MEMORY
                                        SELECT
                                            (' . getSqlDistanceFormula($lon, $lat, $distance) . ') AS `distance`,
                                            `caches`.`cache_id` AS `cache_id`,
                                            `caches`.`type` AS `type`,
                                            `caches`.`name` AS `name`,
                                            `caches`.`date_hidden` `date_hidden`,
                                            `caches`.`status` `status`,
                                            `caches`.`user_id` `user_id`
                                        FROM `caches`
                                        WHERE `caches`.`cache_id` NOT IN (SELECT `cache_ignore`.`cache_id` FROM `cache_ignore` WHERE `cache_ignore`.`user_id`=\'' . $user_id . '\')
                                            AND caches.status NOT IN (4, 5, 6)
                                            AND `caches`.`type`!=6
                                            AND `caches`.`status`=1
                                            AND `longitude` > ' . ($lon - $max_lon_diff) . '
                                            AND `longitude` < ' . ($lon + $max_lon_diff) . '
                                            AND `latitude` > ' . ($lat - $max_lat_diff) . '
                                            AND `latitude` < ' . ($lat + $max_lat_diff) . '
                                        HAVING `distance` < ' . $distance);
    XDb::xSql('ALTER TABLE local_caches' . $user_id . ' ADD PRIMARY KEY ( `cache_id` ),
               ADD INDEX(`cache_id`), ADD INDEX(`type`), ADD INDEX(`name`), ADD INDEX(`user_id`), ADD INDEX(`date_hidden`)');

    $file_content = '';
    $rs = XDb::xSql(
        'SELECT `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`user_id` `user_id`,
                `caches`.`name` `cachename`,
                `caches`.`date_hidden` `date`,
                `caches`.`type` `cache_type`,
                `cache_type`.`icon_large` `icon_large`,
                count(`cache_rating`.`cache_id`) `toprate`,
                `PowerTrail`.`id` AS PT_ID,
                `PowerTrail`.`name` AS PT_name,
                `PowerTrail`.`type` As PT_type,
                `PowerTrail`.`image` AS PT_image
        FROM local_caches' . $user_id . ' `caches`
            INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`)
            LEFT JOIN `cache_rating` ON (`caches`.`cache_id`=`cache_rating`.`cache_id`)
            LEFT JOIN `powerTrail_caches` ON `caches`.`cache_id` = `powerTrail_caches`.`cacheId`
            LEFT JOIN `PowerTrail` ON (`PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId`  AND `PowerTrail`.`status` = 1), `cache_type`
        WHERE `cache_rating`.`cache_id`=`caches`.`cache_id`
              AND `caches`.`type`=`cache_type`.`id`
              GROUP BY `caches`.`cache_id`
            ORDER BY `toprate` DESC, `caches`.`name` ASC LIMIT  ' . ($startat + 0) . ', ' . ($perpage + 0));

    $tr_myn_click_to_view_cache = tr('myn_click_to_view_cache');
    //powertrail vel geopath variables
    $pt_cache_intro_tr = tr('pt_cache');
    $pt_icon_title_tr = tr('pt139');

    while ($r = XDb::xFetchArray($rs)) {
        $file_content .= '<tr>';
        $file_content .= '<td style="white-space: nowrap">' . date($applicationContainer->getOcConfig()->getDateFormat(), strtotime($r['date'])) . '</td>';
        $file_content .= '<td>' . $r['toprate'] . '</td>';
        $cacheicon = myninc::checkCacheStatusByUser($r, $usr['userid']);

        // PowerTrail vel GeoPath icon
        if (isset($r['PT_ID'])) {
            $PT_icon = icon_geopath_small($r['PT_ID'], $r['PT_image'], $r['PT_name'], $r['PT_type'], $pt_cache_intro_tr, $pt_icon_title_tr);
        } else {
            $PT_icon = '<img src="images/rating-star-empty.png" class="icon16" alt="">';
        };
        $file_content .= '<td>' . $PT_icon . '</td>';
        $file_content .= '<td><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($r['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="' . $cacheicon . '" border="0" alt="' . $tr_myn_click_to_view_cache . '" title="' . $tr_myn_click_to_view_cache . '" /></a></td>';
        $file_content .= '<td><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($r['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8') . '</a></td>';
        $file_content .= '<td><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($r['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8') . '</a></td>';

        $rs_log = XDb::xSql(
            "SELECT cache_logs.id AS id, cache_logs.cache_id AS cache_id,
                    cache_logs.type AS log_type,
                    DATE_FORMAT(cache_logs.date,'%Y-%m-%d') AS log_date,
                    cache_logs.text AS log_text,
                    caches.user_id AS cache_owner,
                    cache_logs.user_id AS luser_id,
                    user.username AS user_name,
                    user.user_id AS user_id,
                    log_types.icon_small AS icon_small
            FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))
                INNER JOIN user ON (cache_logs.user_id = user.user_id)
                INNER JOIN log_types ON (cache_logs.type = log_types.id)
            WHERE cache_logs.deleted=0 AND cache_logs.cache_id= ?
            GROUP BY cache_logs.id
            ORDER BY cache_logs.date_created DESC LIMIT 1", $r['cache_id']);

        if($r_log = XDb::xFetchArray($rs_log)){
            $file_content .= '<td style="white-space: nowrap">' . htmlspecialchars(date($applicationContainer->getOcConfig()->getDateFormat(), strtotime($r_log['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';
            $file_content .= '<td><a class="links" href="viewlogs.php?logid=' . htmlspecialchars($r_log['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\'';
            $file_content .= '<b>' . $r_log['user_name'] . '</b>:<br>';
            $file_content .= GeoCacheLog::cleanLogTextForToolTip( $r_log['log_text'] );
            $file_content .= '\',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"><img src="tpl/stdstyle/images/' . $r_log['icon_small'] . '" alt=""></a></td>';
            $file_content .= '<td><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($r_log['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r_log['user_name'], ENT_COMPAT, 'UTF-8') . '</a></td>';
        }
        $file_content .= "</tr>";
        XDb::xFreeResults($rs_log);
    }
    XDb::xFreeResults($rs);
    tpl_set_var('file_content', $file_content);

    $count = XDb::xSimpleQueryValue('SELECT COUNT(*) `count` FROM (local_caches' . $user_id . ' caches)', 0);

    $frompage = $startat / 100 - 3;
    if ($frompage < 1)
        $frompage = 1;

    $topage = $frompage + 8;
    if (($topage - 1) * $perpage > $count)
        $topage = ceil($count / $perpage);

    $thissite = $startat / 100 + 1;

    $pages = '';
    if ($startat > 0)
        $pages .= '<a href="myn_topcaches.php?startat=0">{first_img}</a> <a href="myn_topcaches.php?startat=' . ($startat - 100) . '">{prev_img}</a> ';
    else
        $pages .= '{first_img_inactive} {prev_img_inactive} ';

    for ($i = $frompage; $i <= $topage; $i++) {
        if ($i == $thissite)
            $pages .= $i . ' ';
        else
            $pages .= '<a href="myn_topcaches.php?startat=' . ($i - 1) * $perpage . '">' . $i . '</a> ';
    }
    if ($thissite < $topage)
        $pages .= '<a href="myn_topcaches.php?startat=' . ($startat + $perpage) . '">{next_img}</a> <a href="myn_topcaches.php?startat=' . (ceil($count / 100) * 100 - 100) . '">{last_img}</a>';
    else
        $pages .= '{next_img_inactive} {last_img_inactive}';

    $pages = mb_ereg_replace('{prev_img}', $prev_img, $pages);
    $pages = mb_ereg_replace('{next_img}', $next_img, $pages);
    $pages = mb_ereg_replace('{last_img}', $last_img, $pages);
    $pages = mb_ereg_replace('{first_img}', $first_img, $pages);

    $pages = mb_ereg_replace('{prev_img_inactive}', $prev_img_inactive, $pages);
    $pages = mb_ereg_replace('{next_img_inactive}', $next_img_inactive, $pages);
    $pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
    $pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);

    tpl_set_var('pages', $pages);
}

//make the template and send it out
tpl_BuildTemplate();
