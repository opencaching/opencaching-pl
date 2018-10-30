<?php
namespace Controllers;

use lib\Controllers\LogEntryController;
use lib\Objects\GeoCache\GeoCacheLog;
use Utils\EventHandler\EventHandler;
use Utils\Uri\Uri;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;
use lib\Objects\GeoCache\MultiLogStats;
use lib\Objects\ChunkModels\DynamicMap\CacheWithLogMarkerModel;
use lib\Objects\ChunkModels\DynamicMap\LogMarkerModel;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\User\MultiUserQueries;

class CacheLogController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public method can be called by router
        return true;
    }

    public function index()
    {
        // there is nothing to do here yet...
    }

    public function removeLog()
    {
        if (! $this->loggedUser) {
            echo "User not authorized!";
            return;
        }

        if (!isset($_REQUEST['logid'])) {
            echo "Remove unknown log?!";
            return;
        }

        $logId = intval($_REQUEST['logid']);

        $logEntryController = new LogEntryController();
        $result = $logEntryController->removeLogById($logId);

        echo json_encode( array (
            'removeLogResult' => $result,
            'errors' => $logEntryController->getErrors())
            );

    }

    /**
     * Reverts (undelete) cache log
     * Called via AJAX like /CacheLog/revertLogAjax/{logid}
     *
     * @param int $logId
     */
    public function revertLogAjax($logId)
    {
        $this->checkUserLoggedAjax();
        $log = GeoCacheLog::fromLogIdFactory($logId);
        if (is_null($log)) {
            $this->ajaxErrorResponse('Incorrect logId', 400);
        }
        if (! $this->loggedUser->hasOcTeamRole()) {
            $this->ajaxErrorResponse('User is not authorized to revert log', 401);
        }
        if (! $log->canBeReverted()) {
            $this->ajaxErrorResponse('This log cannot be reverted', 400);
        }
        $log->revertLog();
        $log->getGeoCache()->recalculateCacheStats();
        $log->getUser()->recalculateAndUpdateStats();
        EventHandler::logRemove($log);
        $this->ajaxSuccessResponse('OK');
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
            Uri::getLinkWithModificationTime('/tpl/stdstyle/lastLogs/lastLogs.css'));

        $lastLogs = MultiLogStats::getLastLogs();

        // find cacheOwners and logAuthor usernames
        $userIds = [];
        foreach($lastLogs as $row){
            $userIds[$row['logAuthor']] = '';
            $userIds[$row['cacheOwner']] = '';
        }

        $usernameDict = MultiUserQueries::GetUserNamesForListOfIds(array_keys($userIds));

        $mapModel = new DynamicMapModel();
        $mapModel->addMarkersWithExtractor(
            LogMarkerModel::class, $lastLogs, function ($row) use($usernameDict){

                $marker = new LogMarkerModel();

                $marker->log_link = GeoCacheLog::getLogUrlByLogId($row['id']);
                $marker->log_text = $row['text'];
                $marker->log_icon = GeoCacheLog::GetIconForType($row['type']);
                $marker->log_typeName = tr(GeoCacheLog::getLogTypeTplKeys(
                    $row['cacheType'])[$row['type']]);

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

}
