<?php

use Utils\Database\XDb;
use Utils\Gis\Gis;
use lib\Objects\ApplicationContainer;
use lib\Objects\GeoCache\GeoCacheLog;
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
    tpl_set_tplname('myn_newcaches');
    require($stylepath . '/newcaches.inc.php');
    $applicationContainer = ApplicationContainer::Instance();

    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
    $startat = $startat + 0;

    if( $startat < 0 ) $startat = 0;

    $perpage = 50;
    $startat -= $startat % $perpage;

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

    if ($latitude == NULL || $latitude == 0){
        $latitude = $applicationContainer->getOcConfig()->getMainPageMapCenterLat();
    }
    if ($longitude == NULL || $longitude == 0){
        $longitude = $applicationContainer->getOcConfig()->getMainPageMapCenterLon();
    }

    $distance = $applicationContainer->getLoggedUser()->getNotifyRadius();
    if ($distance == 0) {
        $distance = 35;
    }

    //all target caches are between lat - max_lat_diff and lat + max_lat_diff
    $max_lat_diff = Gis::distanceInDegreesLat($distance);

    //all target caches are between lon - max_lon_diff and lon + max_lon_diff
    $max_lon_diff = Gis::distanceInDegreesLon($distance, $latitude);

    XDb::xSql('DROP TEMPORARY TABLE IF EXISTS local_caches' . $user_id . '');
    XDb::xSql(
        'CREATE TEMPORARY TABLE local_caches' . $user_id . ' ENGINE=MEMORY
         SELECT (' . getSqlDistanceFormula($longitude, $latitude, $distance, 1) . ') AS `distance`,
                 `caches`.`cache_id` AS `cache_id`,
                 `caches`.`type` AS `type`, `caches`.`name` AS `name`,
                IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
                 `caches`.`user_id` `user_id`
         FROM `caches`
         WHERE `caches`.`cache_id` NOT IN (
                SELECT `cache_ignore`.`cache_id` FROM `cache_ignore`
                WHERE `cache_ignore`.`user_id`= ? )
            AND caches.status = 1
            AND `longitude` > ' . ($longitude - $max_lon_diff) . '
            AND `longitude` < ' . ($longitude + $max_lon_diff) . '
            AND `latitude` > ' . ($latitude - $max_lat_diff) . '
            AND `latitude` < ' . ($latitude + $max_lat_diff) . '
         HAVING `distance` < ' . $distance,
        $user_id);

    XDb::xSql(
        'ALTER TABLE local_caches' . $user_id . ' ADD PRIMARY KEY ( `cache_id` ),
        ADD INDEX(`cache_id`), ADD INDEX(`user_id`), ADD INDEX(`date`)');

    $count = XDb::xSimpleQueryValue(
        'SELECT COUNT(*) `count` FROM local_caches' . $user_id . ' `caches`', 0);

    $paginationModel = new PaginationModel(ITEMS_PER_PAGE);
    $paginationModel->setRecordsCount($count);
    list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
    $view->setVar('paginationModel', $paginationModel);

    $file_content = '';
    $rs = XDb::xSql(
        'SELECT `caches`.`cache_id` `cache_id`,
                `user`.`user_id` `user_id`,
                `caches`.`name` `cachename`,
                `caches`.`type` `cache_type`,
                `user`.`username` `username`,
                `caches`.`date` `date`,
                `caches`.`distance` `distance`,
                `PowerTrail`.`id` AS PT_ID, `PowerTrail`.`name` AS PT_name,
                `PowerTrail`.`type` As PT_type, `PowerTrail`.`image` AS PT_image
        FROM (local_caches' . $user_id . ' caches
            LEFT JOIN `powerTrail_caches` ON `caches`.`cache_id` = `powerTrail_caches`.`cacheId`
            LEFT JOIN `PowerTrail` ON `PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId`  AND `PowerTrail`.`status` = 1), `user`, `cache_type`
        WHERE `caches`.`date` <= NOW()
            AND `caches`.`user_id`=`user`.`user_id`
            AND `cache_type`.`id`=`caches`.`type`
        ORDER BY `caches`.`date` DESC, `caches`.`cache_id` DESC
        LIMIT ' . $offset . ', ' . $limit);

    $tr_myn_click_to_view_cache = tr('myn_click_to_view_cache');

    //powertrail vel geopath variables
    $pt_cache_intro_tr = tr('pt_cache');
    $pt_icon_title_tr = tr('pt139');
    while ($r = XDb::xFetchArray($rs)) {
        $file_content .= '<tr>';
        $file_content .= '<td style="white-space: nowrap">' . date($applicationContainer->getOcConfig()->getDateFormat(), strtotime($r['date'])) . '</td>';
        $cacheicon = myninc::checkCacheStatusByUser($r, $user_id);

        // PowerTrail vel GeoPath icon
        if (isset($r['PT_ID'])) {
            $PT_icon = icon_geopath_small($r['PT_ID'], $r['PT_image'], $r['PT_name'], $r['PT_type'], $pt_cache_intro_tr, $pt_icon_title_tr);
        } else {
            $PT_icon = '<img src="images/rating-star-empty.png" class="icon16" alt="">';
        };
        $file_content .= '<td>' . $PT_icon . '</td>';

        $file_content .= '<td><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($r['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="' . $cacheicon . '" alt="' . $tr_myn_click_to_view_cache . '" title="' . $tr_myn_click_to_view_cache . '"></a></td>';
        $file_content .= '<td><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($r['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8') . '</a> (' . number_format($r['distance'], 1, ',', '') . ' km)</td>';
        $file_content .= '<td><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($r['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8') . '</a></td>';

        $rs_log = XDb::xSql(
            "SELECT cache_logs.id AS id,
                    DATE_FORMAT(cache_logs.date,'%Y-%m-%d') AS log_date,
                    cache_logs.text AS log_text,
                    user.username AS user_name,
                    user.user_id AS user_id,
                    log_types.icon_small AS icon_small
            FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))
                INNER JOIN user ON (cache_logs.user_id = user.user_id)
                INNER JOIN log_types ON (cache_logs.type = log_types.id)
            WHERE cache_logs.deleted=0 AND cache_logs.cache_id= ?
            GROUP BY cache_logs.id
            ORDER BY cache_logs.date_created DESC LIMIT 1",
            $r['cache_id']);

        if ( $r_log = XDb::xFetchArray($rs_log) ) {
            $file_content .= '<td style="white-space: nowrap">'
                    . htmlspecialchars(date($applicationContainer->getOcConfig()->getDateFormat(), strtotime($r_log['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';
            $file_content .= '<td><a class="links" href="viewlogs.php?logid='
                    . htmlspecialchars($r_log['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\'';
            $file_content .= '<strong>' . $r_log['user_name'] . '</strong>:<br>';
            $file_content .= GeoCacheLog::cleanLogTextForToolTip( $r_log['log_text'] );
            $file_content .= '\',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"><img src="tpl/stdstyle/images/' . $r_log['icon_small'] . '" alt=""></a></td>';
            $file_content .= '<td><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($r_log['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r_log['user_name'], ENT_COMPAT, 'UTF-8') . '</a></td>';
            $file_content .= "</tr>";
            XDb::xFreeResults($rs_log);
        } else {
            $file_content .= '<td colspan="3"></td>';
        }
    }
    XDb::xFreeResults($rs);
    tpl_set_var('file_content', $file_content);
}

//make the template and send it out
tpl_BuildTemplate();
