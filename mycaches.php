<?php

global $lang, $rootpath, $usr, $dateFormat;

if (!isset($rootpath))
    $rootpath = '';

//include template handling
require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/cache_icon.inc.php');
require_once($stylepath . '/lib/icons.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        //get user record
        $user_id = $usr['userid'];
        tpl_set_var('userid', $user_id);
        if (isset($_REQUEST['status'])) {
            $stat_cache = $_REQUEST['status'];
        } else {
            $stat_cache = 1;
        }
        //get the news
        $tplname = 'mycaches';
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
            return filterevilchars($str);
        }

        function filterevilchars($str)
        {
            return str_replace('[\\x00-\\x09|\\x0A-\\x0E-\\x1F]', '', $str);
        }

        if (checkField('cache_status', $lang))
            $lang_db = $lang;
        else
            $lang_db = "en";

        $rs_stat = sqlValue("SELECT cache_status.$lang_db  FROM cache_status WHERE `cache_status`.`id` = '$stat_cache'", 0);
        tpl_set_var('cache_stat', $rs_stat);
        $ran = sqlValue("SELECT count(cache_id) FROM caches WHERE `caches`.`status` = '1' AND `caches`.`user_id`=$user_id", 0);
        tpl_set_var('activeN', $ran);
        $run = sqlValue("SELECT count(cache_id) FROM caches WHERE `caches`.`status` = '2' AND `caches`.`user_id`=$user_id", 0);
        tpl_set_var('unavailableN', $run);
        $rarn = sqlValue("SELECT count(cache_id) FROM caches WHERE `caches`.`status` = '3' AND `caches`.`user_id`=$user_id", 0);
        tpl_set_var('archivedN', $rarn);
        $rnpn = sqlValue("SELECT count(cache_id) FROM caches WHERE `caches`.`status` = '5' AND `caches`.`user_id`=$user_id", 0);
        tpl_set_var('notpublishedN', $rnpn);
        $rapn = sqlValue("SELECT count(cache_id) FROM caches WHERE `caches`.`status` = '4' AND `caches`.`user_id`=$user_id", 0);
        tpl_set_var('approvalN', $rapn);
        $rbln = sqlValue("SELECT count(cache_id) FROM caches WHERE `caches`.`status` = '6' AND `caches`.`user_id`=$user_id", 0);
        tpl_set_var('blockedN', $rbln);

        $LOGS_PER_PAGE = 50;
        $PAGES_LISTED = 10;
        $total_logs = sqlValue("SELECT count(cache_id) FROM caches WHERE `caches`.`status` = '$stat_cache' AND `caches`.`user_id`=$user_id", 0);
        $pages = "";
        $total_pages = ceil($total_logs / $LOGS_PER_PAGE);
        if (!isset($_GET['start']) || intval($_GET['start']) < 0 || intval($_GET['start']) > $total_logs)
            $start = 0;
        else
            $start = intval($_GET['start']);
        // obsluga sortowania kolumn
        if (!isset($_GET['col']) || intval($_GET['col']) < 1 || intval($_GET['col']) > 7)
            $sort_col = 1;
        else
            $sort_col = intval($_GET['col']);
        if (!isset($_GET['sort']) || intval($_GET['sort']) < 0 || intval($_GET['sort']) > 1)
            $sort_sort = 2;
        else
            $sort_sort = intval($_GET['sort']);
        if ($sort_sort == 1) {
            $sort_txt = 'ASC';
            $sort_neg = 2;
        } else {
            $sort_txt = 'DESC';
            $sort_neg = 1;
        };
        $my_cache_sort = "&start=$start&status=$stat_cache&sort=$sort_neg";
        tpl_set_var('my_cache_sort', $my_cache_sort);
        switch ($sort_col) {
            case 1:
                $sort_warunek = 'date_hidden';
                break;
            case 2:
                $sort_warunek = 'NAME';
                break;
            case 3:
                $sort_warunek = 'FOUNDS';
                break;
            case 4:
                $sort_warunek = 'TOPRATINGS';
                break;
            case 5:
                if ($stat_cache == 2)
                    $sort_warunek = 'dni_od_zmiany';
                else
                    $sort_warunek = 'ilosc_dni';
                break;
            case 6:
                $sort_warunek = 'gkcount';
                break;
            case 7:
                $sort_warunek = 'VISITS';
                break;
            default:
                $sort_warunek = 'date_hidden';
                break;
        };
        $startat = max(0, floor((($start / $LOGS_PER_PAGE) + 1) / $PAGES_LISTED) * $PAGES_LISTED);
        if (($start / $LOGS_PER_PAGE) + 1 >= $PAGES_LISTED)
            $pages .= '<a href="mycaches.php?status=' . $stat_cache . '&amp;start=' . max(0, ($startat - $PAGES_LISTED - 1) * $LOGS_PER_PAGE) . '&col=' . $sort_col . '&sort=' . $sort_sort . '">{first_img}</a> ';
        else
            $pages .= "{first_img_inactive}";
        for ($i = max(1, $startat); $i < $startat + $PAGES_LISTED; $i++) {
            $page_number = ($i - 1) * $LOGS_PER_PAGE;
            if ($page_number == $start)
                $pages .= '<b> [ ';
            $pages .= '<a href="mycaches.php?status=' . $stat_cache . '&amp;start=' . $page_number . '&col=' . $sort_col . '&sort=' . $sort_sort . '">' . $i . '</a> ';
            if ($page_number == $start)
                $pages .= ' ] </b>';
        }
        if ($total_pages > $PAGES_LISTED)
            $pages .= '<a href="mycaches.php?status=' . $stat_cache . '&amp;start=' . (($i - 1) * $LOGS_PER_PAGE) . '&col=' . $sort_col . '&sort=' . $sort_sort . '">{last_img}</a> ';
        else
            $pages .= '{last_img_inactive}';

        $caches_query = "
            SELECT
                `caches`.`cache_id`,
                `caches`.`name`,
                `date_hidden`,
                `status`,cache_type.icon_small AS cache_icon_small,
                `cache_status`.`id` AS `cache_status_id`,
                `caches`.`founds` AS `founds`,
                `caches`.`topratings` AS `topratings`,
                datediff(now(),`caches`.`last_found` ) as `ilosc_dni`,
                datediff(now(),`caches`.`last_modified` ) as `dni_od_zmiany`,
                COUNT(`gk_item`.`id`) AS `gkcount`,
                COALESCE(`cv`.`count`,0) AS `visits`
            FROM `caches`
                LEFT JOIN `gk_item_waypoint` ON `gk_item_waypoint`.`wp` = `caches`.`wp_oc`
                LEFT JOIN `gk_item`
                    ON `gk_item`.`id` = `gk_item_waypoint`.`id`
                        AND `gk_item`.`stateid`<>1
                        AND `gk_item`.`stateid`<>4
                        AND `gk_item`.`typeid`<>2
                        AND `gk_item`.`stateid` <>5
                LEFT JOIN (
                    SELECT
                        `count`,
                        `user_id_ip`,
                        `cache_id`
                    FROM `cache_visits`
                    WHERE `user_id_ip`=0
                    ) `cv`
                    ON `caches`.`cache_id` = `cv`.`cache_id`
                INNER JOIN `cache_type` ON (`caches`.`type` = `cache_type`.`id`),
                `cache_status`
            WHERE
                `user_id`=:user_id
                AND `cache_status`.`id`=`caches`.`status`
                AND `caches`.`status` = :stat_cache
            GROUP BY `caches`.`cache_id`
            ORDER BY `$sort_warunek` $sort_txt
            LIMIT " . intval($start) . ", " . intval($LOGS_PER_PAGE);
        //$params['v1']['value'] = (string) $lang_db;;
        //$params['v1']['data_type'] = 'string';
        $params['user_id']['value'] = (integer) $user_id;
        ;
        $params['user_id']['data_type'] = 'integer';
        $params['stat_cache']['value'] = (integer) $stat_cache;
        ;
        $params['stat_cache']['data_type'] = 'integer';

        if (!isset($dbc)) {
            $dbc = new dataBase();
        };
        $dbc->paramQuery($caches_query, $params);
        unset($params);
        $log_record_all = $dbc->dbResultFetchAll();

        $log_record_count = count($log_record_all);
        $file_content = '';
        //while ($log_record=sql_fetch_assoc($rs))
        //prepare second queryt
        $logs_query = "
            SELECT
                cache_logs.id,
                cache_logs.type AS log_type,
                cache_logs.text AS log_text,
                DATE_FORMAT(cache_logs.date,'%Y-%m-%d') AS log_date,
                caches.user_id AS cache_owner,
                cache_logs.encrypt encrypt,
                cache_logs.user_id AS luser_id,
                user.username AS user_name,
                user.user_id AS user_id,
                log_types.icon_small AS icon_small,
                datediff(now(),`cache_logs`.`date_created`) as `ilosc_dni`
            FROM
                cache_logs
                JOIN caches USING (cache_id)
                JOIN user ON (cache_logs.user_id=user.user_id)
                JOIN log_types ON (cache_logs.type = log_types.id)
            WHERE
                cache_logs.deleted=0 AND
                `cache_logs`.`cache_id`=:v1
            ORDER BY `cache_logs`.`date_created` DESC
            LIMIT 5";
        $edit_geocache_tr = tr('mc_edit_geocache');
        for ($zz = 0; $zz < $log_record_count; $zz++) {
            $log_record = $log_record_all[$zz];
            $tabelka = '';
            $tabelka .= '<td style="width: 90px;">' . htmlspecialchars(date($dateFormat, strtotime($log_record['date_hidden'])), ENT_COMPAT, 'UTF-8') . '</td>';
            $tabelka .= '<td ><a href="editcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="tpl/stdstyle/images/free_icons/pencil.png" alt="' . $edit_geocache_tr . '" title="' . $edit_geocache_tr . '"/></a></td>';
            $tabelka .= '<td >&nbsp;<img src="tpl/stdstyle/images/' . $log_record['cache_icon_small'] . '" border="0" alt=""/></td>';
            $tabelka .= '<td><b><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
            $tabelka .= '<td align="right">&nbsp;' . intval($log_record['founds']) . '&nbsp;</td>';
            $tabelka .= '<td  align="right">&nbsp;' . intval($log_record['topratings']) . '&nbsp;</td>';
            $tabelka .= '<td  align="right">&nbsp;' . intval($log_record['gkcount']) . '&nbsp;</td>';
            $tabelka .= '<td  align="right">&nbsp;' . intval($log_record['visits']) . '&nbsp;</td>';
            $tabelka .= '<td>&nbsp;';
            if ($stat_cache == 2)
                $dni = $log_record['dni_od_zmiany'];
            else
                $dni = $log_record['ilosc_dni'];
            if ($dni == NULL)
                $tabelka .= tr('not_found');
            elseif ($dni == 0)
                $tabelka .= tr('today');
            elseif ($dni == 1)
                $tabelka .= tr('yesterday');
            elseif ($dni > 180)
                $tabelka .= '<b>' . intval($dni) . ' ' . tr('days_ago') . '</b>';
            elseif ($dni > 1)
                $tabelka .= intval($dni) . ' ' . tr('days_ago');
            $tabelka .= '&nbsp;</td>';

            /* $rs_logs = sql("SELECT cache_logs.id,  cache_logs.type AS log_type, cache_logs.text AS log_text, DATE_FORMAT(cache_logs.date,'%Y-%m-%d') AS log_date,
              caches.user_id AS cache_owner, cache_logs.encrypt encrypt, cache_logs.user_id AS luser_id, user.username AS user_name,
              user.user_id AS user_id, log_types.icon_small AS icon_small, datediff(now(),`cache_logs`.`date_created`) as `ilosc_dni`
              FROM cache_logs JOIN caches USING (cache_id) JOIN user ON (cache_logs.user_id=user.user_id) JOIN log_types ON (cache_logs.type = log_types.id)
              WHERE cache_logs.deleted=0 AND `cache_logs`.`cache_id`='&1' ORDER BY `cache_logs`.`date_created` DESC LIMIT 5", $log_record['cache_id']); */

            $params['v1']['value'] = (integer) $log_record['cache_id'];
            ;
            $params['v1']['data_type'] = 'integer';
            $dbc->paramQuery($logs_query, $params);

            $tabelka .= '<td align=left>';
            $warning = 0;
            $dnf = 0;
            $sprawdzaj = 0;
            $log_entries_all = $dbc->dbResultFetchAll();
            $log_entries_count = count($log_entries_all);

            //while ($logs=sql_fetch_assoc($rs_logs))
            for ($yy = 0; $yy < $log_entries_count; $yy++) {
                $logs = $log_entries_all [$yy];
                $tabelka .= '<a class="links" href="viewlogs.php?logid=' . htmlspecialchars($logs['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\'';
                $tabelka .= '<b>' . $logs['user_name'] . '</b>&nbsp;(' . htmlspecialchars(date($dateFormat, strtotime($logs['log_date'])), ENT_COMPAT, 'UTF-8') . '):';

                if ($logs['encrypt'] == 1 && $logs['cache_owner'] != $usr['userid'] && $logs['luser_id'] != $usr['userid']) {
                    $tabelka .= "<img src=\'/tpl/stdstyle/images/free_icons/lock.png\' alt=\`\` /><br/>";
                }
                if ($logs['encrypt'] == 1 && ($logs['cache_owner'] == $usr['userid'] || $logs['luser_id'] == $usr['userid'])) {
                    $tabelka .= "<img src=\'/tpl/stdstyle/images/free_icons/lock_open.png\' alt=\`\` /><br/>";
                }
                $data = cleanup_text(str_replace("\r\n", " ", $logs['log_text']));
                $data = str_replace("\n", " ", $data);
                if ($logs['encrypt'] == 1 && $logs['cache_owner'] != $usr['userid'] && $logs['luser_id'] != $usr['userid']) {
                    //crypt the log ROT13, but keep HTML-Tags and Entities
                    $data = str_rot13_html($data);
                } else {
                    $tabelka .= "<br/>";
                }
                $tabelka .= $data;
                // sprawdz ile dni minelo od wpisania logu
                if ($logs['ilosc_dni'] < 3)
                    $oznacz = 'style="border: 1px green solid;"';
                else
                    $oznacz = '';
                $tabelka .= '\',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"><img src="tpl/stdstyle/images/' . $logs['icon_small'] . '" border="0" ' . $oznacz . ' alt=""/></a></b>';
                if ($stat_cache == 1) { //obsluga DNF i serwisu tylko dla skrzynek aktywnych
                    if ($sprawdzaj < 2) { // sprawdzaj logi
                        if ($logs['log_type'] == 10)
                            $sprawdzaj = 2;                                // skrzynka gotowa do szukania wiec nie trzeba juz nic sprawdzac
                        if (($logs['log_type'] == 3) && ($logs['cache_owner'] == $logs['luser_id']))
                            $sprawdzaj = 2;  //if comment by cache owner dont check
                        if ($sprawdzaj < 1) {
                            if ($logs['log_type'] == 2)
                                $dnf++;           // jesli DNF zwieksz licznik
                            if ($logs['log_type'] == 5)
                                $warning = 1;       // zgloszono potrzebe serwisu
                            if ($logs['log_type'] == 1)
                                $sprawdzaj = 1;     // skrzynka znaleziona wiec nie trzeba szukac DNF
                        } else {
                            if ($logs['log_type'] == 5)
                                $warning = 1;       // zgloszono potrzebe serwisu
                        };
                    };
                };
            }
            $pokaz_problem = '';
            if ($stat_cache == 1) {
                if ($dnf > 1)
                    $pokaz_problem = 'bgcolor=red';
                elseif ($dnf == 1)
                    $pokaz_problem = 'bgcolor=yellow';
                elseif ($warning > 0)
                    $pokaz_problem = 'bgcolor=#EEEEEE';
            } elseif ($stat_cache == 2) {
                if ($log_record['dni_od_zmiany'] > 155)
                    $pokaz_problem = 'bgcolor=red';
                elseif ($log_record['dni_od_zmiany'] > 124)
                    $pokaz_problem = 'bgcolor=yellow';
            };
            $file_content .= "<tr " . $pokaz_problem . ">" . $tabelka . "</td></tr>\n";
        }
        unset($dbc);
        $pages = mb_ereg_replace('{last_img}', $last_img, $pages);
        $pages = mb_ereg_replace('{first_img}', $first_img, $pages);

        $pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
        $pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);

        tpl_set_var('file_content', $file_content);
        tpl_set_var('pages', $pages);

        if ($stat_cache == 2)
            tpl_set_var('col5_header', tr('last_modified2_label'));
        else
            tpl_set_var('col5_header', tr('last_found'));
    }
}
//make the template and send it out
tpl_BuildTemplate();
?>
