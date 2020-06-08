<?php

namespace src\Controllers;

use Exception;
use src\Models\CacheSet\MultiGeopathsStats;
use src\Models\ChunkModels\DynamicMap\DynamicMapModel;
use src\Models\ChunkModels\DynamicMap\LogMarkerModel;
use src\Models\ChunkModels\ListOfCaches\Column_CacheLog;
use src\Models\ChunkModels\ListOfCaches\Column_CacheName;
use src\Models\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use src\Models\ChunkModels\ListOfCaches\Column_GeoPathIcon;
use src\Models\ChunkModels\ListOfCaches\Column_SimpleText;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\ChunkModels\PaginationModel;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\GeoCache\MultiLogStats;
use src\Models\User\MultiUserQueries;
use src\Utils\Text\Formatter;
use src\Utils\Uri\Uri;

class CacheLogController extends BaseController
{

    public function isCallableFromRouter($actionName)
    {
        // all public method can be called by router
        return true;
    }

    public function index()
    {
        // there is nothing to do here yet...
    }

    /**
     * Remove cache log
     * Called via AJAX like /CacheLog/removeLogAjax/{logId}
     *
     * @param int $logId
     */
    public function removeLogAjax($logId)
    {
        $this->checkUserLoggedAjax();

        if (!$logId || !is_numeric($logId)) {
            $this->ajaxErrorResponse('Improper logId', 400);
        }

        $log = GeoCacheLog::fromLogIdFactory($logId);
        if (!$log) {
            $this->ajaxErrorResponse('Incorrect logId', 400);
        }

        try {
            $log->removeLog();
        } catch (Exception $ex) {
            $this->ajaxErrorResponse('Can\'t remove log', 400);
            exit;
        }

        $this->ajaxSuccessResponse();
    }

    /**
     * Reverts (undelete) cache log
     * Called via AJAX like /CacheLog/revertLogAjax/{logId}
     *
     * @param int $logId
     */
    public function revertLogAjax($logId)
    {
        $this->checkUserLoggedAjax();

        $log = GeoCacheLog::fromLogIdFactory($logId);
        if (!$log) {
            $this->ajaxErrorResponse('Incorrect logId', 400);
        }

        try {
            $log->revertLog();
        } catch (Exception $ex) {
            $this->ajaxErrorResponse('Can\'t revert log', 400);
            exit;
        }

        $this->ajaxSuccessResponse();
    }

    /**
     * Maps of last logs
     */
    public function lastLogsMap()
    {
        $this->redirectNotLoggedUsers();

        $this->view->setTemplate('lastLogs/lastLogsMap');
        $this->view->loadJQuery();

        $this->view->addHeaderChunk('openLayers5');

        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/views/lastLogs/lastLogs.css'));

        $lastLogs = MultiLogStats::getLastLogs(100);

        // find cacheOwners and logAuthor usernames
        $userIds = [];
        foreach ($lastLogs as $row) {
            $userIds[$row['logAuthor']] = '';
            $userIds[$row['cacheOwner']] = '';
        }

        $usernameDict = MultiUserQueries::GetUserNamesForListOfIds(array_keys($userIds));

        $mapModel = new DynamicMapModel();
        $mapModel->addMarkersWithExtractor(
            LogMarkerModel::class, $lastLogs, function ($row) use ($usernameDict) {

            $marker = new LogMarkerModel();

            $marker->log_link = GeoCacheLog::getLogUrlByLogId($row['id']);
            $marker->log_text = $row['text'];
            $marker->log_icon = GeoCacheLog::GetIconForType($row['type']);
            $marker->log_typeName = tr(GeoCacheLog::getLogTypeTplKeys($row['cacheType'])[$row['type']]);

            $marker->log_username = $usernameDict[$row['logAuthor']];
            $marker->log_date = $row['date'];

            $marker->icon = GeoCache::CacheIconByType($row['cacheType'], $row['status']);
            $marker->lat = $row['latitude'];
            $marker->lon = $row['longitude'];
            $marker->link = GeoCache::GetCacheUrlByWp($row['wp_oc']);
            $marker->name = $row['name'];
            $marker->wp = $row['wp_oc'];
            $marker->username = $usernameDict[$row['cacheOwner']];

            return $marker;
        });
        $this->view->setVar('mapModel', $mapModel);


        $this->view->buildView();

    }

    /**
     * Display list of last logs (former newlogs.php)
     */
    public function lastLogsList()
    {
        $this->redirectNotLoggedUsers();

        $this->view->setTemplate('lastLogs/lastLogsList');
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/lastLogs/lastLogs.css'));
        $this->view->addLocalCss('/css/lightTooltip.css');

        $this->view->loadJQuery();

        // prepare pagination for list
        $paginationModel = new PaginationModel(25);

        $paginationModel->setRecordsCount(MultiLogStats::getLastLogsNumber());
        list($limit, $offset) = $paginationModel->getQueryLimitAndOffset();

        $allLogs = MultiLogStats::getLastLogs($limit, $offset);

        // find logAuthor usernames, and status of the cache for current user (found/not-found etc.)
        $userIds = [];
        $userStsDict = [];
        $geopathDict = [];
        foreach ($allLogs as $row) {
            $userIds[$row['logAuthor']] = null;
            $userStsDict[$row['cache_id']] = null;
            $geopathDict[$row['cache_id']] = null;
        }

        $usernameDict = MultiUserQueries::GetUserNamesForListOfIds(array_keys($userIds));

        foreach (MultiLogStats::getStatusForUser($this->loggedUser->getUserId(), array_keys($userStsDict)) as $userSts) {
            $userStsDict[$userSts['cache_id']] = $userSts['type'];
        }

        foreach (MultiGeopathsStats::getGeopathForEachGeocache(array_keys($geopathDict)) as $gp) {
            $geopathDict[$gp['cacheId']] = $gp;
        }

        // init model for list of watched geopaths
        $listModel = new ListOfCachesModel();

        $listModel->addColumn(new Column_SimpleText(tr('lastLogList_logCreationDate'), function ($row) {
            return Formatter::date($row['date_created']);
        }, "width10"));

        $listModel->addColumn(new Column_GeoPathIcon('', function ($row) use ($geopathDict) {

            if (!$geopathDict[$row['cache_id']]) {
                return [];
            }
            return [
                'ptId' => $geopathDict[$row['cache_id']]['id'],
                'ptType' => $geopathDict[$row['cache_id']]['type'],
                'ptName' => $geopathDict[$row['cache_id']]['name'],
            ];
        }, "width5"));

        $listModel->addColumn(new Column_CacheTypeIcon("", function ($row) use ($userStsDict) {
            return [
                'type' => $row['cacheType'],
                'status' => $row['status'],
                'user_sts' => $userStsDict[$row['cache_id']]
            ];
        }, "width5"));

        $listModel->addColumn(new Column_CacheName(tr('lastLogList_geocacheName'), function ($row) {
            return [
                'cacheWp' => $row['wp_oc'],
                'cacheName' => $row['name']
            ];
        }, "width30"));

        $logColumn = new Column_CacheLog(tr('lastLogList_logEntry'), function ($row) use ($usernameDict) {
            return [
                'logId' => $row['id'],
                'logType' => $row['type'],
                'logText' => $row['text'],
                'logUserName' => $usernameDict[$row['logAuthor']],
                'logDate' => $row['date'],
                'recommended' => $row['recom']
            ];
        });
        $logColumn->showFullLogText();
        $listModel->addColumn($logColumn);

        $listModel->setPaginationModel($paginationModel);

        // load rows to display
        $listModel->addDataRows($allLogs);
        $this->view->setVar('listOfLogsModel', $listModel);

        $this->view->buildView();
    }
}
