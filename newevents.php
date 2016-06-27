<?php

use Utils\Database\XDb;
use lib\Objects\GeoCache\GeoCacheLog;
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
require_once('./lib/cache_icon.inc.php');

//Preprocessing
if ($error == false) {
    //get the news
    $tplname = 'newevents';
    require($stylepath . '/newcaches.inc.php');

    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
    $startat = $startat + 0;

    $perpage = 50;
    $startat -= $startat % $perpage;

    $content = '';
    $cache_location = '';
    $file_content = '';
    $rs = XDb::xSql(
        'SELECT `caches`.`cache_id` `cacheid`, `user`.`user_id` `userid`,
                `caches`.`country` `country`, `caches`.`name` `cachename`,
                `caches`.`wp_oc` `wp_name`, `user`.`username` `username`,
                `caches`.`date_created` `date_created`, `caches`.`date_hidden` `date_hidden`,
                `cache_type`.`icon_large` `icon_large`, cache_location.adm3 AS state
        FROM `caches`, `user`, `cache_type`,cache_location
        WHERE date_add(`caches`.`date_hidden`, INTERVAL 2 DAY) >= curdate()
            AND cache_location.cache_id=caches.cache_id
            AND `caches`.`user_id`=`user`.`user_id`
            AND `caches`.`type`=`cache_type`.`id`
            AND `caches`.`status` = 1  AND `caches`.`type`=6
        ORDER BY `caches`.`date_hidden` ASC,cache_location.adm3 COLLATE utf8_polish_ci ASC
        LIMIT ' . ($startat + 0) . ', ' . ($perpage + 0));

    while( $record = XDb::xFetchArray($rs) ){
        //group by country
        $newcaches[$record['state']][] = array(
            'name' => $record['cachename'],
            'wp_name' => $record['wp_name'],
            'userid' => $record['userid'],
            'username' => $record['username'],
            'cache_id' => $record['cacheid'],
            'state' => $record['state'],
            'date' => $record['date_hidden'],
            'icon_large' => $record['icon_large']
        );
    }

    if (isset($newcaches)) {
        foreach ($newcaches AS $statename => $state_record) {
            $cache_location = '<tr><td colspan="8" class="content-title-noshade-size1">' . htmlspecialchars($statename, ENT_COMPAT, 'UTF-8') . '</td></tr>';

            $content .= $cache_location;
            foreach ($state_record AS $cache_record) {
                $file_content = '';
                $file_content .= '<tr>';
                $file_content .= '<td style="width: 90px;">' . date('d-m-Y', strtotime($cache_record['date'])) . '</td>';
                $file_content .= '<td width="22">&nbsp;<img src="tpl/stdstyle/images/' . getSmallCacheIcon($cache_record['icon_large']) . '" border="0" alt=""/></td>';
                $file_content .= '<td><b><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($cache_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
                $file_content .= '<td width="32"><b><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($cache_record['userid'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($cache_record['username'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';

                $rs_log = XDb::xSql(
                    "SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
                            cache_logs.text AS log_text, cache_logs.type AS log_type,
                            cache_logs.date AS log_date, user.username AS user_name,
                            cache_logs.user_id AS luser_id, user.user_id AS user_id,
                            log_types.icon_small AS icon_small
                    FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))
                        INNER JOIN user ON (cache_logs.user_id = user.user_id)
                        INNER JOIN log_types ON (cache_logs.type = log_types.id)
                    WHERE cache_logs.deleted=0 AND cache_logs.cache_id= ?
                    GROUP BY cache_logs.id ORDER BY cache_logs.date_created DESC LIMIT 1",
                    $cache_record['cache_id']);

                if ( $r_log = XDb::xFetchArray($rs_log) ) {

                    $file_content .= '<td style="width: 80px;">' . htmlspecialchars(date("d-m-Y", strtotime($r_log['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';

                    $file_content .= '<td width="22"><b><a class="links" href="viewlogs.php?logid=' . htmlspecialchars($r_log['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\'';

                    $file_content .= '<b>' . $r_log['user_name'] . '</b>:&nbsp;';

                    $data = GeoCacheLog::cleanLogTextForToolTip( $r_log['log_text'] );
                    
                    $file_content .= $data;
                    $file_content .= '\',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"><img src="tpl/stdstyle/images/' . $r_log['icon_small'] . '" border="0" alt=""/></a></b></td>';
                    $file_content .= '<td>&nbsp;&nbsp;<b><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($r_log['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r_log['user_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
                } else {
                    $file_content .= '<td style="width: 80px;">&nbsp;</td><td width="22">&nbsp;</td><td>&nbsp;</td>';
                }
                $file_content .= "</tr>";
                $content .=$file_content;
                XDb::xFreeResults($rs_log);
            }
        }
    }

    XDb::xFreeResults($rs);
    tpl_set_var('file_content', $content);

    $count = XDb::xSimpleQueryValue(
        'SELECT COUNT(*) `count` FROM `caches` WHERE type=6 AND status=1', 0);

    $frompage = $startat / 100 - 3;
    if ($frompage < 1)
        $frompage = 1;

    $topage = $frompage + 8;
    if (($topage - 1) * $perpage > $count)
        $topage = ceil($count / $perpage);

    $thissite = $startat / 100 + 1;

    $pages = '';
    if ($startat > 0)
        $pages .= '<a href="newevents.php?startat=0">{first_img}</a> <a href="newevents.php?startat=' . ($startat - 100) . '">{prev_img}</a> ';
    else
        $pages .= '{first_img_inactive} {prev_img_inactive} ';

    for ($i = $frompage; $i <= $topage; $i++) {
        if ($i == $thissite)
            $pages .= $i . ' ';
        else
            $pages .= '<a href="newcaches.php?startat=' . ($i - 1) * $perpage . '">' . $i . '</a> ';
    }
    if ($thissite < $topage)
        $pages .= '<a href="newevents.php?startat=' . ($startat + $perpage) . '">{next_img}</a> <a href="newevents.php?startat=' . (ceil($count / 100) * 100 - 100) . '">{last_img}</a>';
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
