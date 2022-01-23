<?php

use src\Controllers\LogEntryController;
use src\Models\GeoCache\GeoCache;
use src\Utils\Log\CacheAccessLog;
use src\Utils\Text\Formatter;

const LOGS_PER_PAGE = 10;

require_once './lib/common.inc.php';

function find_news($start, $limit)
{
    global $tpl, $znalezione, $cache;

    $logEntryController = new LogEntryController();
    $logs = $logEntryController->loadLogsFromDb($cache->getCacheId(), false, $start, $limit);

    // detailed cache access logging
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
        $user_id = $_SESSION['user_id'];
    } else {
        $user_id = null;
    }
    CacheAccessLog::logCacheAccess(
        $cache->getCacheId(),
        $user_id,
        CacheAccessLog::EVENT_VIEW_LOGS,
        CacheAccessLog::SOURCE_MOBILE
    );

    $znalezione = [];

    foreach ($logs as $log) {
        $tmplog = [];
        $tmplog['id'] = $log['logid'];
        $tmplog['user_id'] = $log['user_id'];
        $tmplog['newtype'] = $log['type'];
        $tmplog['newdate'] = Formatter::date($log['date']);
        $tmplog['username'] = $log['username'];
        $tmplog['newtext'] = html2log($log['text']);
        $znalezione[] = $tmplog;
    }

    $tpl->assign('name', $cache->getCacheName());
    $tpl->assign('wp_oc', $cache->getWaypointId());
    $tpl->assign('logs', $znalezione);
}

if (isset($_GET['wp']) && strlen($_GET['wp']) == 6) {
    try {
        $cache = new GeoCache(['cacheWp' => $_GET['wp']]);
    } catch (Exception $e) {
        header('Location: ./index.php');

        exit();
    }
    $ile = $cache->getLogEntriesCount(false);
    $na_stronie = LOGS_PER_PAGE;
    $max = ceil($ile / $na_stronie);

    if ($max == 0) {
        $max = 1;
    }

    $tpl->assign('ile', $ile);
    $tpl->assign('max', $max);

    $next_page = null;
    $prev_page = null;

    require_once './lib/paging.inc.php';

    $tpl->assign('next_page', $next_page);
    $tpl->assign('prev_page', $prev_page);
} else {
    header('Location: ./index.php');

    exit();
}

$tpl->display('tpl/logs.tpl');
