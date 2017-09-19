<?php

use Utils\Database\OcDb;
use lib\Objects\GeoCache\GeoCacheLog;

global $dateFormat;
if (!isset($rootpath))
    $rootpath = '';

//include template handling
require_once ($rootpath . 'lib/common.inc.php');
require_once($stylepath . '/lib/icons.inc.php');

//Preprocessing
if ($error == false) {
    //get the news
    $tplname = 'newlogs';
    require ($stylepath . '/newlogs.inc.php');
    $LOGS_PER_PAGE = 50;
    $PAGES_LISTED = 10;

    $rsQuery = "SELECT count(id) FROM cache_logs WHERE deleted=0";
    $db = OcDb::instance();
    $total_logs = $db->simpleQueryValue($rsQuery, 0);

    $pages = "";
    $total_pages = ceil($total_logs / $LOGS_PER_PAGE);

    if (!isset($_GET['start']) || intval($_GET['start']) < 0 || intval($_GET['start']) > $total_logs) {
        $start = 0;
    } else {
        $start = intval($_GET['start']);
    }
    $startat = max(0, floor((($start / $LOGS_PER_PAGE) + 1) / $PAGES_LISTED) * $PAGES_LISTED);

    if ($start > 0) {
        $pages .= '<a href="newlogs.php?start=' . max(0, ($startat - $PAGES_LISTED - 1) * $LOGS_PER_PAGE) . '">{first_img}</a> ';
        $pages .= '<a href="newlogs.php?start=' . max(0, $start - $LOGS_PER_PAGE) . '">{prev_img}</a> ';
    } else
        $pages .= "{first_img_inactive} {prev_img_inactive} ";
    for ($i = max(1, $startat); $i < $startat + $PAGES_LISTED; $i++) {
        $page_number = ($i - 1) * $LOGS_PER_PAGE;
        if ($page_number == $start) {
            $pages .= "<b>$i</b> ";
        } else {
            $pages .= "<a href='newlogs.php?start=$page_number'>$i</a> ";
        }
    }
    if ($total_pages > $PAGES_LISTED) {
        $pages .= '<a href="newlogs.php?start=' . ($start + $LOGS_PER_PAGE) . '">{next_img}</a> ';
        $pages .= '<a href="newlogs.php?start=' . (($i - 1) * $LOGS_PER_PAGE) . '">{last_img}</a> ';
    } else {
        $pages .= ' {next_img_inactive} {last_img_inactive}';
    }

    $rsQuery = "SELECT `cache_logs`.`id` FROM `cache_logs`, `caches`
                    WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`
                    AND `cache_logs`.`deleted`=0
                    AND `caches`.`status` IN (1, 2, 3)
                    ORDER BY  `cache_logs`.`date_created` DESC
                    LIMIT :variable1, :variable2 ";
    $s = $db->paramQuery($rsQuery,
            array('variable1' => array('value' => intval($start), 'data_type' => 'integer'),
                  'variable2' => array('value' => intval($LOGS_PER_PAGE), 'data_type' => 'integer'),)
         );

    $log_ids = array();
    while( $record = $db->dbResultFetch($s) ){
        $log_ids[] = $record['id'];
    }

    $file_content = '';
    $tr_myn_click_to_view_cache = tr('myn_click_to_view_cache');
    $bgColor = '#eeeeee';

    //powertrail vel geopath variables
    $pt_cache_intro_tr = tr('pt_cache');
    $pt_icon_title_tr = tr('pt139');

    if( !empty($log_ids) ){
        $s = $db->simpleQuery(
            "SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
                    cache_logs.type AS log_type, cache_logs.date AS log_date,
                    cache_logs.user_id AS luser_id, cache_logs.text AS log_text,
                    cache_logs.text_html AS text_html, caches.name AS cache_name,
                    caches.user_id AS cache_owner, user.username AS user_name,
                    caches.user_id AS user_id, user.user_id AS xuser_id,
                    caches.wp_oc AS wp_name, caches.type AS cache_type,
                    cache_type.icon_small AS cache_icon_small, log_types.icon_small AS icon_small,
                    log_types.pl as pl,
                    IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,
                    COUNT(gk_item.id) AS geokret_in, `PowerTrail`.`id` AS PT_ID,
                    `PowerTrail`.`name` AS PT_name, `PowerTrail`.`type` As PT_type,
                    `PowerTrail`.`image` AS PT_image
            FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))
                INNER JOIN user ON (cache_logs.user_id = user.user_id)
                INNER JOIN log_types ON (cache_logs.type = log_types.id)
                INNER JOIN cache_type ON (caches.type = cache_type.id)
                LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id`
                    AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
                LEFT JOIN gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
                LEFT JOIN gk_item ON gk_item.id = gk_item_waypoint.id
                    AND gk_item.stateid<>1 AND gk_item.stateid<>4
                    AND gk_item.typeid<>2 AND gk_item.stateid !=5
                LEFT JOIN `powerTrail_caches` ON cache_logs.cache_id = `powerTrail_caches`.`cacheId`
                LEFT JOIN `PowerTrail` ON `PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId`  AND `PowerTrail`.`status` = 1
            WHERE cache_logs.deleted=0
                AND cache_logs.id IN (" .implode(',',$log_ids). ")
                AND cache_logs.cache_id=caches.cache_id
                AND caches.status<> 4 AND caches.status<> 5
                AND caches.status<> 6
            GROUP BY cache_logs.id
            ORDER BY cache_logs.date_created DESC");

        while ( $log_record = $db->dbResultFetch($s) ) {
            if ($bgColor == '#eeeeee')
                $bgColor = '#ffffff';
            else
                $bgColor = '#eeeeee';

            $file_content .= '<tr style="background-color:' . $bgColor . '">';
            $file_content .= '<td style="width: 70px;">' . htmlspecialchars(date($dateFormat, strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';
            if ($log_record['geokret_in'] != '0') {
                $file_content .= '<td style="width: 22px;">&nbsp;<img src="images/gk.png" alt="" title="GeoKret"></td>';
            } else {
                $file_content .= '<td style="width: 22px;">&nbsp;</td>';
            }

            //$rating_picture
            if ($log_record['recommended'] == 1 && $log_record['log_type'] == 1) {
                $file_content .= '<td style="width: 22px;"><img src="images/rating-star.png" alt="" title= ' . tr("recommendation") . '></td>';
            } else {
                $file_content .= '<td style="width: 22px;">&nbsp;</td>';
            }

            if ($log_record['log_type'] == 12 && !$usr['admin']) {//hide COG entry
                $log_record['user_id'] = '0';
                $log_record['user_name'] = tr('cog_user_name');
            }

            // PowerTrail vel GeoPath icon
            if (isset($log_record['PT_ID'])) {
                $PT_icon = icon_geopath_small($log_record['PT_ID'], $log_record['PT_image'], $log_record['PT_name'], $log_record['PT_type'], $pt_cache_intro_tr, $pt_icon_title_tr);
            } else {
                $PT_icon = '<img src="images/rating-star-empty.png" class="icon16" alt="" title="">';
            };
            $file_content .= '<td style="width: 22px;">' . $PT_icon . '</td>';

            $file_content .= '<td style="width: 22px;"><img src="tpl/stdstyle/images/' . $log_record['icon_small'] . '" alt="" title=" ' . tr('logType'.$log_record['log_type']) . ' "></td>';
            $cacheicon = myninc::checkCacheStatusByUser($log_record, $usr['userid']);
            $file_content .= '<td style="width: 22px;">&nbsp;<a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="' . $cacheicon . '" alt="' . $tr_myn_click_to_view_cache . '" title="' . $tr_myn_click_to_view_cache . '"></a></td>';

            //$file_content .= '<td style="width: 22px;"><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="tpl/stdstyle/images/' . $log_record['cache_icon_small'] . '" border="0" alt="" title="Kliknij aby zobaczyć skrzynke" /></a></td>';
            $file_content .= '<td><b><a class="links" href="viewlogs.php?logid=' . htmlspecialchars($log_record['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\'';
            $file_content .= '<b>' . $log_record['user_name'] . '</b>:<br>';
            $file_content .= GeoCacheLog::cleanLogTextForToolTip( $log_record['log_text'] );
            $file_content .= '\', PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">' . htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
            $file_content .= '<td><b><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($log_record['luser_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
            $file_content .= "</tr>";
        }
    }

    $pages = mb_ereg_replace('{prev_img}', $prev_img, $pages);
    $pages = mb_ereg_replace('{next_img}', $next_img, $pages);
    $pages = mb_ereg_replace('{last_img}', $last_img, $pages);
    $pages = mb_ereg_replace('{first_img}', $first_img, $pages);
    $pages = mb_ereg_replace('{prev_img_inactive}', $prev_img_inactive, $pages);
    $pages = mb_ereg_replace('{next_img_inactive}', $next_img_inactive, $pages);
    $pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
    $pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);

    tpl_set_var('file_content', $file_content);
    tpl_set_var('pages', $pages);
}

function cmp($a, $b)
{
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? 1 : -1;
}

//make the template and send it out
tpl_BuildTemplate();

