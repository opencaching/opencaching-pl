<?php
namespace src\Controllers;

use Exception;
use src\Controllers\LogEntryController;
use src\Models\GeoCache\GeoCacheLog;
use Utils\EventHandler\EventHandler;
use Utils\Uri\Uri;
use src\Models\ChunkModels\DynamicMap\DynamicMapModel;
use src\Models\GeoCache\MultiLogStats;
use src\Models\ChunkModels\DynamicMap\LogMarkerModel;
use src\Models\GeoCache\GeoCache;
use src\Models\User\MultiUserQueries;

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

    /**
     * Remove cache log
     * Called via AJAX like /CacheLog/removeLogAjax/{logid}
     *
     * @param int $logId
     */
    public function removeLogAjax($logId)
    {
        $this->checkUserLoggedAjax();

        if(!$logId || !is_numeric($logId)){
            $this->ajaxErrorResponse('Improper logId', 400);
        }

        $log = GeoCacheLog::fromLogIdFactory($logId);
        if(!$log){
            $this->ajaxErrorResponse('Incorrect logId', 400);
        }

        try{
            $log->removeLog();
        }catch (Exception $ex){
            $this->ajaxErrorResponse('Can\'t remove log', 400);
            exit;
        }

        $this->ajaxSuccessResponse();
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
        if (!$log) {
            $this->ajaxErrorResponse('Incorrect logId', 400);
        }

        try{
            $log->revertLog();
        }catch (Exception $ex){
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
