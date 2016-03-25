<?php

use Utils\Database\XDb;
global $lang, $rootpath, $dateFormat;

if (!isset($rootpath))
    $rootpath = '';

//include template handling
require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/cache_icon.inc.php');
//  require_once($stylepath . '/lib/icons.inc.php');
//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {

        if (isset($_REQUEST['userid'])) {
            $user_id = $_REQUEST['userid'];
            tpl_set_var('userid', $user_id);
        }else{
            //no param userid - display data for currently logged user
            $user_id = $usr['userid'];
            tpl_set_var('userid', $user_id);
        }

        //get the news
        $tplname = 'my_logs';
        tpl_set_var('latest_logs_cache', 'Najnowsze logi wprowadzone');
        require($stylepath . '/newlogs.inc.php');

        function cleanup_text($str)
        {
            $from[] = '<p>&nbsp;</p>';
            $to[] = '';
            $str = strip_tags($str, "<li>");
            $from[] = '&nbsp;';
            $to[] = ' ';
            $from[] = '<p>';
            $to[] = '';
            $from[] = '\n';
            $to[] = '';
            $from[] = '\r';
            $to[] = '';
            $from[] = '</p>';
            $to[] = "";
            $from[] = '<br>';
            $to[] = "";
            $from[] = '<br />';
            $to[] = "";
            $from[] = '<br/>';
            $to[] = "";

            $from[] = '<li>';
            $to[] = " - ";
            $from[] = '</li>';
            $to[] = "";

            $from[] = '&oacute;';
            $to[] = 'o';
            $from[] = '&quot;';
            $to[] = '"';
            $from[] = '&[^;]*;';
            $to[] = '';

            $from[] = '&';
            $to[] = '';
            $from[] = '\'';
            $to[] = '';
            $from[] = '"';
            $to[] = '';
            $from[] = '<';
            $to[] = '';
            $from[] = '>';
            $to[] = '';
            $from[] = '(';
            $to[] = ' -';
            $from[] = ')';
            $to[] = '- ';
            $from[] = ']]>';
            $to[] = ']] >';
            $from[] = '';
            $to[] = '';

            for ($i = 0; $i < count($from); $i++)
                $str = str_replace($from[$i], $to[$i], $str);

            return str_replace('[\\x00-\\x09|\\x0A-\\x0E-\\x1F]', '', $str); //filterevilchars
        }

        $username = XDb::xMultiVariableQueryValue(
            "SELECT username FROM user WHERE user_id= :1 LIMIT 1", '', $user_id);
        tpl_set_var('username', $username);

        $LOGS_PER_PAGE = 50;
        $PAGES_LISTED = 10;

        $total_logs = XDb::xMultiVariableQueryValue(
            "SELECT count(id) FROM cache_logs, caches WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`
                AND `cache_logs`.`deleted`=0
                AND `caches`.`status` != 4
                AND `caches`.`status` != 5
                AND `caches`.`status` != 6
                AND `cache_logs`.`user_id`= :1 ", 0, $user_id);

        $pages = "";
        $total_pages = ceil($total_logs / $LOGS_PER_PAGE);

        if (!isset($_GET['start']) || intval($_GET['start']) < 0 || intval($_GET['start']) > $total_logs)
            $start = 0;
        else
            $start = intval($_GET['start']);

        $startat = max(0, floor((($start / $LOGS_PER_PAGE) + 1) / $PAGES_LISTED) * $PAGES_LISTED);

        if (($start / $LOGS_PER_PAGE) + 1 >= $PAGES_LISTED)
            $pages .= '<a href="my_logs.php?userid=' . $user_id . '&amp;start=' . max(0, ($startat - $PAGES_LISTED - 1) * $LOGS_PER_PAGE) . '">{first_img}</a> ';
        else
            $pages .= "{first_img_inactive}";
        for ($i = max(1, $startat); $i < min($startat + $PAGES_LISTED, $total_pages + 1); $i++) {
            $page_number = ($i - 1) * $LOGS_PER_PAGE;
            if ($page_number == $start)
                $pages .= '<b>';
            $pages .= '<a href="my_logs.php?userid=' . $user_id . '&amp;start=' . $page_number . '">' . $i . '</a> ';
            if ($page_number == $start)
                $pages .= '</b>';
        }
        if ($total_pages >= $startat + $PAGES_LISTED)
            $pages .= '<a href="my_logs.php?userid=' . $user_id . '&amp;start=' . (($i - 1) * $LOGS_PER_PAGE) . '">{last_img}</a> ';
        else
            $pages .= '{last_img_inactive}';


        $rs = XDb::xSql(
            "SELECT `cache_logs`.`id`
            FROM `cache_logs`, `caches`
            WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`
                AND `cache_logs`.`deleted`=0
                AND `caches`.`status` != 4
                AND `caches`.`status` != 5
                AND `caches`.`status` != 6
                AND `cache_logs`.`user_id`= ?
            ORDER BY  `cache_logs`.`date_created` DESC
            LIMIT " . intval($start) . ", " . intval($LOGS_PER_PAGE), $user_id );

        $log_ids = array();
        while( $record = XDb::xFetchArray($rs)){
            $log_ids[] = $record['id'];
        }
        XDb::xFreeResults($rs);

        $rs = XDb::xSql(
            "SELECT cache_logs.id, cache_logs.cache_id AS cache_id, cache_logs.type AS log_type, cache_logs.date AS log_date,
                    cache_logs.text AS log_text, `cache_logs`.`encrypt` AS `encrypt`, caches.user_id AS cache_owner,
                    caches.name AS cache_name, user.username AS user_name, cache_logs.user_id AS luser_id,
                    caches.wp_oc AS wp_name, caches.type AS cache_type, cache_type.icon_small AS cache_icon_small,
                    log_types.icon_small AS icon_small,
                    IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,COUNT(gk_item.id) AS geokret_in
            FROM ((cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)))
                INNER JOIN user ON (cache_logs.user_id = user.user_id)
                INNER JOIN log_types ON (cache_logs.type = log_types.id)
                INNER JOIN cache_type ON (caches.type = cache_type.id)
                LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id`
                    AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
                LEFT JOIN gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
                LEFT JOIN gk_item ON gk_item.id = gk_item_waypoint.id
                    AND gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
            WHERE cache_logs.deleted=0 AND cache_logs.id IN ( ".implode(',', $log_ids)." ) AND `cache_logs`.`user_id`= ?
            GROUP BY cache_logs.id ORDER BY cache_logs.date_created DESC",
            $user_id);

        $file_content = '';
        while( $log_record = XDb::xFetchArray($rs)){
            if ( !(($log_record['log_type'] == 12) && (!$usr['admin'])) ) { //ten warunek ukryje logi typu "komentarz COG" przed zwykłymi userami, natomiast adminom wyświetli wszystkie logi
                $file_content .= '<tr>';
                $file_content .= '<td style="width: 70px;">' . htmlspecialchars(date($dateFormat, strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';

                if ($log_record['geokret_in'] != '0') {
                    $file_content .= '<td width="26">&nbsp;<img src="images/gk.png" border="0" alt="" title="GeoKret" /></td>';
                } else {
                    $file_content .='<td width="26">&nbsp;</td>';
                }

                //$rating_picture
                if ($log_record['recommended'] == 1 && $log_record['log_type'] == 1) {
                    $file_content .= '<td width="22"><img src="images/rating-star.png" border="0" alt=""/></td>';
                } else {
                    $file_content .= '<td width="22">&nbsp;</td>';
                }
                $file_content .= '<td width="22"><img src="tpl/stdstyle/images/' . $log_record['icon_small'] . '" border="0" alt="" /></td>';
                $file_content .= '<td width="22"><a class="links" href="viewcache.php?cacheid=' . $log_record['cache_id'] . '"><img src="tpl/stdstyle/images/' . $log_record['cache_icon_small'] . '" border="0" alt=""/></a></td>';
                $file_content .= '<td><b><a class="links" href="viewlogs.php?logid=' . htmlspecialchars($log_record['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\'';
                $file_content .= '<b>' . $log_record['user_name'] . '</b>:&nbsp;';
                if ($log_record['encrypt'] == 1 && $log_record['cache_owner'] != $usr['userid'] && $log_record['luser_id'] != $usr['userid']) {
                    $file_content .= "<img src=\'/tpl/stdstyle/images/free_icons/lock.png\' alt=\`\` /><br/>";
                }
                if ($log_record['encrypt'] == 1 && ($log_record['cache_owner'] == $usr['userid'] || $log_record['luser_id'] == $usr['userid'])) {
                    $file_content .= "<img src=\'/tpl/stdstyle/images/free_icons/lock_open.png\' alt=\`\` /><br/>";
                }
                $data = cleanup_text(str_replace("\r\n", " ", $log_record['log_text']));
                $data = str_replace("\n", " ", $data);
                if ($log_record['encrypt'] == 1 && $log_record['cache_owner'] != $usr['userid'] && $log_record['luser_id'] != $usr['userid']) {//crypt the log ROT13, but keep HTML-Tags and Entities
                    $data = str_rot13_html($data);
                } else {
                    $file_content .= "<br/>";
                }
                $file_content .=$data;
                $file_content .= '\', PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">' . htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
                $file_content .= '<td>&nbsp;</td>';
                $file_content .= "</tr>";
            } // koniec ifa ukrywajacego log o id 12 (komentarz COG)
        }

        $pages = mb_ereg_replace('{last_img}', $last_img, $pages);
        $pages = mb_ereg_replace('{first_img}', $first_img, $pages);

        $pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
        $pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);

        tpl_set_var('file_content', $file_content);
        tpl_set_var('pages', $pages);
    }
}
//make the template and send it out
tpl_BuildTemplate();
