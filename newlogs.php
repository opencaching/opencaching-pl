<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

/****************************************************************************
 new logs
 ****************************************************************************/

if (!isset($rootpath)) $rootpath = '';

//include template handling
require_once ($rootpath . 'lib/common.inc.php');
// this file use only database class. We don't need mysql_ connectors anymore.
db_disconnect();

//Preprocessing
if ($error == false) {
    //get the news
    $tplname = 'newlogs';
    require ($stylepath . '/newlogs.inc.php');
    $LOGS_PER_PAGE = 50;
    $PAGES_LISTED = 10;

    $rsQuery = "SELECT count(id) FROM cache_logs WHERE deleted=0";
    $db = new dataBase;
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
    $rsQuery = "SELECT `cache_logs`.`id` FROM `cache_logs` USE INDEX(date_created), `caches`
            WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`
            AND `cache_logs`.`deleted`=0
            AND `caches`.`status` IN (1, 2, 3)
            ORDER BY  `cache_logs`.`date_created` DESC
            LIMIT :variable1, :variable2 ";
    $db->paramQuery($rsQuery, array('variable1' => array ('value' => intval($start),'data_type'=> 'integer'),'variable2' => array ('value' => intval($LOGS_PER_PAGE),'data_type'=> 'integer'),));
    $log_ids = '';
    if ($db->rowCount() == 0){
        $log_ids = '0';
    }

    for ($i = 0; $i < $db->rowCount(); $i++) {
        $record = $db->dbResultFetch();
        if ($i > 0) {
            $log_ids .= ', ' . $record['id'];
        } else {
            $log_ids = $record['id'];
        }
    }

    $rsQuery = "SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
                  cache_logs.type AS log_type,
                  cache_logs.date AS log_date,
                 `cache_logs`.`encrypt` `encrypt`,
                  cache_logs.user_id AS luser_id,
                  cache_logs.text AS log_text,
                  cache_logs.text_html AS text_html,
                  caches.name AS cache_name,
                  caches.user_id AS cache_owner,
                  user.username AS user_name,
                  caches.user_id AS user_id,
                  user.user_id AS xuser_id,
                  caches.wp_oc AS wp_name,
                  caches.type AS cache_type,
                  cache_type.icon_small AS cache_icon_small,
                  log_types.icon_small AS icon_small,
                  IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,
                  COUNT(gk_item.id) AS geokret_in
                  FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id) INNER JOIN cache_type ON (caches.type = cache_type.id) LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
                  LEFT JOIN gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
                  LEFT JOIN gk_item ON gk_item.id = gk_item_waypoint.id AND
                  gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
                  WHERE cache_logs.deleted=0 AND cache_logs.id IN ($log_ids) AND cache_logs.cache_id=caches.cache_id AND caches.status<> 4 AND caches.status<> 5 AND caches.status<> 6
                  GROUP BY cache_logs.id
                  ORDER BY cache_logs.date_created DESC";

    $file_content = '';
    $tr_myn_click_to_view_cache = tr('myn_click_to_view_cache');
    $bgColor = '#eeeeee';
    $db->simpleQuery($rsQuery);
    for ($i = 0; $i < $db->rowCount(); $i++) {
        if ($bgColor == '#eeeeee') $bgColor = '#ffffff'; else $bgColor = '#eeeeee';
        $log_record = $db->dbResultFetch();
        $file_content .= '<tr bgcolor="' . $bgColor . '">';
        $file_content .= '<td style="width: 70px;">' . htmlspecialchars(date("d-m-Y", strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';
        if ($log_record['geokret_in'] != '0') {
            $file_content .= '<td width="22">&nbsp;<img src="images/gk.png" border="0" alt="" title="GeoKret" /></td>';
        } else {
            $file_content .= '<td width="22">&nbsp;</td>';
        }

        //$rating_picture
        if ($log_record['recommended'] == 1 && $log_record['log_type'] == 1) {
            $file_content .= '<td width="22"><img src="images/rating-star.png" border="0" alt="" title= ' . tr("recommendation") . ' /></td>';
        } else {
            $file_content .= '<td width="22">&nbsp;</td>';
        }

        if ($log_record['log_type'] == 12 && !$usr['admin']) {//hide COG entery
            $log_record['user_id'] = '0';
            $log_record['user_name'] = tr('cog_user_name');
        }

        $file_content .= '<td width="22"><img src="tpl/stdstyle/images/' . $log_record['icon_small'] . '" border="0" alt="" /></td>';
        $cacheicon = myninc::checkCacheStatusByUser($log_record, $usr['userid']);
        $file_content .= '<td width="22">&nbsp;<a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="' . $cacheicon . '" border="0" alt="' . $tr_myn_click_to_view_cache . '" title="' . $tr_myn_click_to_view_cache . '" /></a></td>';

        //$file_content .= '<td width="22"><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="tpl/stdstyle/images/' . $log_record['cache_icon_small'] . '" border="0" alt="" title="Kliknij aby zobaczyć skrzynke" /></a></td>';
        $file_content .= '<td><b><a class="links" href="viewlogs.php?logid=' . htmlspecialchars($log_record['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\'';
        $file_content .= '<b>' . $log_record['user_name'] . '</b>: &nbsp;';
        if ($log_record['encrypt'] == 1 && $log_record['cache_owner'] != $usr['userid'] && $log_record['luser_id'] != $usr['userid']) {
            $file_content .= "<img src=\'/tpl/stdstyle/images/free_icons/lock.png\' alt=\`\` /><br/>";
        }
        if ($log_record['encrypt'] == 1 && ($log_record['cache_owner'] == $usr['userid'] || $log_record['luser_id'] == $usr['userid'])) {
            $file_content .= "<img src=\'/tpl/stdstyle/images/free_icons/lock_open.png\' alt=\`\` /><br/>";
        }
        $data = common::cleanupText(str_replace("\r\n", " ", $log_record['log_text']));
        $data = str_replace("\n", " ", $data);
        if ($log_record['encrypt'] == 1 && $log_record['cache_owner'] != $usr['userid'] && $log_record['luser_id'] != $usr['userid']) {//crypt the log ROT13, but keep HTML-Tags and Entities
            $data = str_rot13_html($data);
        } else {
            $file_content .= "<br/>";
        }
        $file_content .= $data;
        $file_content .= '\', PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">' . htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
        $file_content .= '<td><b><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($log_record['luser_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
        $file_content .= "</tr>";
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

function cmp($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? 1 : -1;
}

//make the template and send it out
tpl_BuildTemplate();
?>
