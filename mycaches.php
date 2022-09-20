<?php

use src\Models\ApplicationContainer;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLogCommons;
use src\Utils\Database\OcDb;
use src\Utils\Text\Formatter;

//include template handling
require_once __DIR__ . '/lib/common.inc.php';

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (! $loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('/login.php?target=' . $target);
}

$view = tpl_getView();

require __DIR__ . '/src/Views/newlogs.inc.php';

$cachesStatus = $_REQUEST['status'] ?? GeoCacheCommons::STATUS_READY;

if (! in_array($cachesStatus, GeoCacheCommons::CacheStatusArray())) {
    $cachesStatus = GeoCacheCommons::STATUS_READY;
}
$view->setVar('cacheStatus', $cachesStatus)
    ->setVar('cacheStatusTr', GeoCacheCommons::CacheStatusTranslationKey($cachesStatus));

$dbc = OcDb::instance();
$query = 'SELECT `status`, COUNT(*) AS `items` FROM `caches` WHERE `user_id` = :1 GROUP BY `status`';
$stmt = $dbc->multiVariableQuery($query, $loggedUser->getUserId());
$caches = $stmt->fetchAll();

$cachesNo = [];

foreach (GeoCacheCommons::CacheStatusArray() as $status) {
    $cachesNo[$status] = 0;
}

foreach ($caches as $item) {
    $cachesNo[$item['status']] = $item['items'];
}
$view->setVar('cachesNo', $cachesNo);

$LOGS_PER_PAGE = 50;
$PAGES_LISTED = 10;

$query = 'SELECT count(cache_id) FROM caches WHERE `caches`.`status` = :1 AND `caches`.`user_id`= :2 ';
$total_logs = $dbc->multiVariableQueryValue($query, 0, $cachesStatus, $loggedUser->getUserId());
$pages = '';
$total_pages = ceil($total_logs / $LOGS_PER_PAGE);

if (! isset($_GET['start']) || intval($_GET['start']) < 0 || intval($_GET['start']) > $total_logs) {
    $start = 0;
} else {
    $start = intval($_GET['start']);
}

// obsługa sortowania kolumn
if (! isset($_GET['col']) || intval($_GET['col']) < 1 || intval($_GET['col']) > 9) {
    $sort_col = 1;
} else {
    $sort_col = intval($_GET['col']);
}

if (! isset($_GET['sort']) || intval($_GET['sort']) < 0 || intval($_GET['sort']) > 1) {
    $sort_sort = 2;
} else {
    $sort_sort = intval($_GET['sort']);
}

if ($sort_sort == 1) {
    $sort_txt = 'ASC';
    $sort_neg = 2;
} else {
    $sort_txt = 'DESC';
    $sort_neg = 1;
}

$view->setVar('myCacheSort', "&start={$start}&status={$cachesStatus}&sort={$sort_neg}");

switch ($sort_col) {
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
        if ($cachesStatus == GeoCacheCommons::STATUS_UNAVAILABLE) {
            $sort_warunek = 'dni_od_zmiany';
        } else {
            $sort_warunek = 'ilosc_dni';
        }
        break;
    case 6:
        $sort_warunek = 'gkcount';
        break;
    case 7:
        $sort_warunek = 'VISITS';
        break;
    case 8:
        $sort_warunek = 'watchers';
        break;
    case 9:
        $sort_warunek = 'notfounds';
        break;
    default:
        $sort_warunek = 'date_hidden';
        break;
}

$startAt = max(0, floor((($start / $LOGS_PER_PAGE) + 1) / $PAGES_LISTED) * $PAGES_LISTED);

if (($start / $LOGS_PER_PAGE) + 1 >= $PAGES_LISTED) {
    $pages .= '<a href="/mycaches.php?status=' . $cachesStatus . '&amp;start=' . max(
        0,
        ($startAt - $PAGES_LISTED - 1) * $LOGS_PER_PAGE
    ) . '&col=' . $sort_col . '&sort=' . $sort_sort . '">{first_img}</a> ';
} else {
    $pages .= '{first_img_inactive}';
}

for ($i = max(1, $startAt); $i < min($startAt + $PAGES_LISTED, $total_pages + 1); $i++) {
    $page_number = ($i - 1) * $LOGS_PER_PAGE;

    if ($page_number == $start) {
        $pages .= '<b> [ ';
    }
    $pages .= '<a href="/mycaches.php?status=' . $cachesStatus . '&amp;start=' . $page_number . '&col=' . $sort_col . '&sort=' . $sort_sort . '">' . $i . '</a> ';

    if ($page_number == $start) {
        $pages .= ' ] </b>';
    }
}

if ($total_pages > $PAGES_LISTED) {
    $pages .= '<a href="/mycaches.php?status=' . $cachesStatus . '&amp;start=' . (($i - 1) * $LOGS_PER_PAGE) . '&col=' . $sort_col . '&sort=' . $sort_sort . '">{last_img}</a> ';
} else {
    $pages .= '{last_img_inactive}';
}

$caches_query = "
            SELECT
                `caches`.`cache_id`,
                `caches`.`name`,
                `caches`.`type` AS `cache_type`,
                `date_hidden`,
                `caches`.`founds` AS `founds`,
                `caches`.`notfounds` AS `notfounds`,
                `caches`.`topratings` AS `topratings`,
                datediff(now(),`caches`.`last_found` ) as `ilosc_dni`,
                datediff(now(),`caches`.`last_modified` ) as `dni_od_zmiany`,
                COUNT(`gk_item`.`id`) AS `gkcount`,
                `caches`.`watcher` AS `watchers`,
                COALESCE(`cv`.`count`,0) AS `visits`
            FROM `caches`
                LEFT JOIN `gk_item_waypoint` ON `gk_item_waypoint`.`wp` = `caches`.`wp_oc`
                LEFT JOIN `gk_item`
                    ON `gk_item`.`id` = `gk_item_waypoint`.`id`
                        AND `gk_item`.`stateid`<>1
                        AND `gk_item`.`stateid`<>4
                        AND `gk_item`.`typeid`<>2
                        AND `gk_item`.`stateid` <>5
                LEFT JOIN ( SELECT `count`,`cache_id` FROM `cache_visits2` WHERE type = 'C' ) `cv`
                    ON `caches`.`cache_id` = `cv`.`cache_id`
            WHERE
                `user_id`=:user_id
                AND `caches`.`status` = :stat_cache
            GROUP BY `caches`.`cache_id`
            ORDER BY `{$sort_warunek}` {$sort_txt}
            LIMIT " . $start . ', ' . $LOGS_PER_PAGE;

$params['user_id']['value'] = $loggedUser->getUserId();
$params['user_id']['data_type'] = 'integer';
$params['stat_cache']['value'] = (int) $cachesStatus;
$params['stat_cache']['data_type'] = 'integer';

$s = $dbc->paramQuery($caches_query, $params);
unset($params);
$log_record_all = $dbc->dbResultFetchAll($s);

$log_record_count = count($log_record_all);
$file_content = '';

//prepare second query
$logs_query = "
            SELECT
                cache_logs.id,
                cache_logs.type AS log_type,
                cache_logs.text AS log_text,
                DATE_FORMAT(cache_logs.date,'%Y-%m-%d') AS log_date,
                caches.user_id AS cache_owner,
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
    $table = '<td>' . htmlspecialchars(
        Formatter::date($log_record['date_hidden']),
        ENT_COMPAT,
        'UTF-8'
    ) . '</td>';
    $table .= '<td ><a href="/editcache.php?cacheid=' . htmlspecialchars(
        $log_record['cache_id'],
        ENT_COMPAT,
        'UTF-8'
    ) . '"><img src="/images/free_icons/pencil.png" alt="' . $edit_geocache_tr . '" title="' . $edit_geocache_tr . '"></a></td>';
    $table .= '<td><img class="icon16" src="' . GeoCacheCommons::CacheIconByType($log_record['cache_type'], $cachesStatus) . '" alt=""></td>';
    $table .= '<td><b><a class="links" href="/viewcache.php?cacheid=' . htmlspecialchars(
        $log_record['cache_id'],
        ENT_COMPAT,
        'UTF-8'
    ) . '">' . htmlspecialchars($log_record['name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
    $table .= '<td class="align-center">' . intval($log_record['founds']) . '</td>';
    $table .= '<td class="align-center">' . intval($log_record['notfounds']) . '</td>';
    $table .= '<td class="align-center">' . intval($log_record['topratings']) . '</td>';
    $table .= '<td class="align-center">' . intval($log_record['gkcount']) . '</td>';
    $table .= '<td class="align-center">' . intval($log_record['watchers']) . '</td>';
    $table .= '<td class="align-center">' . intval($log_record['visits']) . '</td>';
    $table .= '<td>';

    if ($cachesStatus == GeoCacheCommons::STATUS_UNAVAILABLE) {
        $dni = $log_record['dni_od_zmiany'];
    } else {
        $dni = $log_record['ilosc_dni'];
    }

    if (is_null($dni)) {
        $table .= tr('not_found');
    } elseif ($dni == 0) {
        $table .= tr('today');
    } elseif ($dni == 1) {
        $table .= tr('yesterday');
    } elseif ($dni > 180) {
        $table .= '<b>' . intval($dni) . ' ' . tr('days_ago') . '</b>';
    } elseif ($dni > 1) {
        $table .= intval($dni) . ' ' . tr('days_ago');
    }
    $table .= '</td>';

    $params['v1']['value'] = (int) $log_record['cache_id'];
    $params['v1']['data_type'] = 'integer';
    $s = $dbc->paramQuery($logs_query, $params);

    $table .= '<td>';
    $warning = 0;
    $dnf = 0;
    $sprawdzaj = 0;
    $log_entries_all = $dbc->dbResultFetchAll($s);

    $log_entries_count = count($log_entries_all);

    for ($yy = 0; $yy < $log_entries_count; $yy++) {
        $logs = $log_entries_all[$yy];
        $table .= '<a class="links" href="/viewlogs.php?logid=' . htmlspecialchars(
            $logs['id'],
            ENT_COMPAT,
            'UTF-8'
        ) . '" onmouseover="Tip(\'';
        $table .= '<b>' . htmlspecialchars($logs['user_name']) . '</b> (' . htmlspecialchars(
            Formatter::date($logs['log_date']),
            ENT_COMPAT,
            'UTF-8'
        ) . '):<br>';
        $table .= GeoCacheLogCommons::cleanLogTextForToolTip($logs['log_text']);
        // sprawdź ile dni minęło od wpisania logu
        if ($logs['ilosc_dni'] < 3) {
            $oznacz = 'style="border: 1px green solid;"';
        } else {
            $oznacz = '';
        }
        $table .= '\',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"><img src="/images/' . $logs['icon_small'] . '" ' . $oznacz . ' alt=""></a></b>';

        if ($cachesStatus == GeoCacheCommons::STATUS_READY) { //obsługa DNF i serwisu tylko dla skrzynek aktywnych
            if ($sprawdzaj < 2) { // sprawdzaj logi
                if ($logs['log_type'] == GeoCacheLogCommons::LOGTYPE_READYTOSEARCH) {
                    $sprawdzaj = 2;
                }                                // skrzynka gotowa do szukania więc nie trzeba już nic sprawdzać

                if (($logs['log_type'] == GeoCacheLogCommons::LOGTYPE_COMMENT) && ($logs['cache_owner'] == $logs['luser_id'])) {
                    $sprawdzaj = 2;
                }  //if comment by cache owner don't check

                if ($sprawdzaj < 1) {
                    if ($logs['log_type'] == GeoCacheLogCommons::LOGTYPE_DIDNOTFIND) {
                        $dnf++;
                    }           // jeśli DNF zwiększ licznik

                    if ($logs['log_type'] == GeoCacheLogCommons::LOGTYPE_NEEDMAINTENANCE) {
                        $warning = 1;
                    }       // zgłoszono potrzebę serwisu

                    if ($logs['log_type'] == GeoCacheLogCommons::LOGTYPE_FOUNDIT || $logs['log_type'] == GeoCacheLogCommons::LOGTYPE_MADEMAINTENANCE) {
                        $sprawdzaj = 1;
                    }     // skrzynka znaleziona lub po serwisie więc nie trzeba szukać DNF
                } elseif ($logs['log_type'] == GeoCacheLogCommons::LOGTYPE_NEEDMAINTENANCE) {
                    $warning = 1;
                }       // zgłoszono potrzebę serwisu
            }
        }
    }
    $pokaz_problem = '';

    if ($cachesStatus == GeoCacheCommons::STATUS_READY) {
        if ($dnf > 1) {
            $pokaz_problem = 'bgcolor=red';
        } elseif ($dnf == 1) {
            $pokaz_problem = 'bgcolor=yellow';
        } elseif ($warning > 0) {
            $pokaz_problem = 'bgcolor=#EEEEEE';
        }
    } elseif ($cachesStatus == GeoCacheCommons::STATUS_UNAVAILABLE) {
        if ($log_record['dni_od_zmiany'] > 155) {
            $pokaz_problem = 'bgcolor=red';
        } elseif ($log_record['dni_od_zmiany'] > 124) {
            $pokaz_problem = 'bgcolor=yellow';
        }
    }
    $file_content .= '<tr ' . $pokaz_problem . '>' . $table . "</td></tr>\n";
}
unset($dbc);
$pages = mb_ereg_replace('{last_img}', $last_img, $pages);
$pages = mb_ereg_replace('{first_img}', $first_img, $pages);

$pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
$pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);

$view->setVar('fileContent', $file_content)
    ->setVar('pages', $pages);

if ($cachesStatus == GeoCacheCommons::STATUS_UNAVAILABLE) {
    $view->setVar('col5Header', 'last_modified2_label');
} else {
    $view->setVar('col5Header', 'last_found');
}

//make the template and send it out
$view->setTemplate('mycaches')
    ->buildView();
