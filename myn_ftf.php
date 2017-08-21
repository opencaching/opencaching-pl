<?php

use Utils\Database\XDb;
use Utils\Gis\Gis;
use lib\Objects\ApplicationContainer;
use lib\Objects\ChunkModels\PaginationModel;

const ITEMS_PER_PAGE = 50;

global $rootpath, $usr;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
require_once($rootpath . 'lib/calculation.inc.php');
require_once($stylepath . '/lib/icons.inc.php');

if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
} else {
    $applicationContainer = ApplicationContainer::Instance();

    tpl_set_tplname('myn_ftf');

    //get user record
    $user_id = $usr['userid'];
    tpl_set_var('userid', $user_id);

    $latitude = $applicationContainer->getLoggedUser()->getHomeCoordinates()->getLatitude();
    $longitude = $applicationContainer->getLoggedUser()->getHomeCoordinates()->getLongitude();
    if ($longitude == NULL || $latitude == NULL || ($longitude == 0 && $latitude == 0)) {
        tpl_set_var('info', '<div class="notice">' . tr("myn_info") . '</div>');
    } else {
        tpl_set_var('info', '');
    }

    if ($latitude == NULL || $latitude == 0) {
        $latitude = $applicationContainer->getOcConfig()->getMainPageMapCenterLat();
    }
    if ($longitude == NULL || $longitude == 0) {
        $longitude = $applicationContainer->getOcConfig()->getMainPageMapCenterLon();
    }

    $distance = $applicationContainer->getLoggedUser()->getNotifyRadius();
    if ($distance == 0)
        $distance = 35;

    //all target caches are between lat - max_lat_diff and lat + max_lat_diff
    $max_lat_diff = Gis::distanceInDegreesLat($distance);

    //all target caches are between lon - max_lon_diff and lon + max_lon_diff
    //TODO: check!!!
    $max_lon_diff = Gis::distanceInDegreesLon($distance, $latitude);

    XDb::xSql('DROP TEMPORARY TABLE IF EXISTS local_caches' . XDb::xEscape($user_id) );

    XDb::xSql(
        'CREATE TEMPORARY TABLE local_caches' . XDb::xEscape($user_id) . ' ENGINE=MEMORY
            SELECT
                (' . getSqlDistanceFormula($longitude, $latitude, $distance, 1 /*$multiplier[$distance_unit]*/) . ') AS `distance`,
                `cache_id`, `type`, `name`, `date_hidden`, `user_id`
            FROM `caches`
            WHERE `cache_id` NOT IN (
                SELECT `cache_ignore`.`cache_id` FROM `cache_ignore`
                WHERE `cache_ignore`.`user_id`= ?
                )
                AND caches.status = 1
                AND caches.founds = 0
                AND `caches`.`type`!=6
                AND `longitude` > ' . ($longitude - $max_lon_diff) . '
                AND `longitude` < ' . ($longitude + $max_lon_diff) . '
                AND `latitude` > ' . ($latitude - $max_lat_diff) . '
                AND `latitude` < ' . ($latitude + $max_lat_diff) . '
            HAVING `distance` < ' . $distance,
            $user_id);

    XDb::xSql(
        'ALTER TABLE local_caches' . XDb::xEscape($user_id) . ' ADD PRIMARY KEY ( `cache_id` ),
        ADD INDEX(`cache_id`),
        ADD INDEX(`type`),
        ADD INDEX(`user_id`),
        ADD INDEX(`date_hidden`)'
    );

    $count = XDb::xSimpleQueryValue(
        'SELECT COUNT(*) `count` FROM local_caches' . $user_id . ' `caches`', 0);

    $paginationModel = new PaginationModel(ITEMS_PER_PAGE);
    $paginationModel->setRecordsCount($count);
    list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
    $view->setVar('paginationModel', $paginationModel);

    $file_content = '';
    $rs = XDb::xSql(
        'SELECT `user`.`user_id` `userid`,
            `user`.`username` `username`,
            `caches`.`cache_id` `cacheid`,
            `caches`.`name` `cachename`,
            `caches`.`date_hidden` `date`,
            `caches`.`type` `cache_type`,
            `caches`.`distance` `distance`,
            `PowerTrail`.`id` AS PT_ID,
            `PowerTrail`.`name` AS PT_name,
            `PowerTrail`.`type` As PT_type,
            `PowerTrail`.`image` AS PT_image
        FROM local_caches' . $user_id . ' `caches` INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`)
            LEFT JOIN `powerTrail_caches` ON `caches`.`cache_id` = `powerTrail_caches`.`cacheId`
            LEFT JOIN `PowerTrail` ON (`PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId`
                AND `PowerTrail`.`status` = 1), `cache_type`
        WHERE `caches`.`type`=`cache_type`.`id`
        ORDER BY `date` DESC, `caches`.`cache_id` DESC
        LIMIT ' . $offset . ', ' . $limit);

    //powertrail vel geopath variables
    $pt_cache_intro_tr = tr('pt_cache');
    $pt_icon_title_tr = tr('pt139');

    while ($r = XDb::xFetchArray($rs)) {
        $file_content .= '<tr>';
        $file_content .= '<td style="white-space: nowrap">' . date($applicationContainer->getOcConfig()->getDateFormat(), strtotime($r['date'])) . '</td>';
        $cacheicon = myninc::checkCacheStatusByUser($r, $usr['userid']);

// PowerTrail vel GeoPath icon
        if (isset($r['PT_ID'])) {
            $PT_icon = icon_geopath_small($r['PT_ID'], $r['PT_image'], $r['PT_name'], $r['PT_type'], $pt_cache_intro_tr, $pt_icon_title_tr);
        } else {
            $PT_icon = '<img src="images/rating-star-empty.png" class="icon16" alt="">';
        };
        $file_content .= '<td>' . $PT_icon . '</td>';
        $file_content .= '<td><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($r['cacheid'], ENT_COMPAT, 'UTF-8') . '"><img src="' . $cacheicon . '" alt="' . tr('myn_click_to_view_cache') . '" title="' . tr('myn_click_to_view_cache') . '"></a></td>';
        $file_content .= '<td><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($r['cacheid'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8') . '</a> (' . number_format($r['distance'], 1, ',', '') . ' km)</td>';
        $file_content .= '<td><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($r['userid'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8') . '</a></td>';
        $file_content .= "</tr>";
    }
    XDb::xFreeResults($rs);
    tpl_set_var('file_content', $file_content);
}

//make the template and send it out
tpl_BuildTemplate();
