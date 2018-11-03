<?php

namespace Controllers;

/**
 * This controller handles recommendations list view
 */

use lib\Objects\ChunkModels\ListOfCaches\Column_CacheName;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use lib\Objects\ChunkModels\ListOfCaches\Column_OnClickActionIcon;
use lib\Objects\ChunkModels\ListOfCaches\Column_UserName;
use lib\Objects\ChunkModels\ListOfCaches\ListOfCachesModel;
use lib\Objects\ChunkModels\PaginationModel;
use lib\Objects\GeoCache\CacheRecommendation;
use lib\Objects\GeoCache\MultiLogStats;
use Utils\Uri\Uri;

class MyRecommendationsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {

        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
            exit;
        }

        $this->view->setTemplate('myRecommendations/myRecommendations');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/myRecommendations/myRecommendations.css'));

        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/myRecommendations/myRecommendations.js'));

        $this->view->addLocalCss('/tpl/stdstyle/css/lightTooltip.css');
        $this->view->loadJQuery();

        //load lightPopup
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/js/lightPopup/lightPopup.css'));
        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/js/lightPopup/lightPopup.js'));



        $rowCount = CacheRecommendation::getCountOfUserRecommendations($this->loggedUser->getUserId());
        $this->view->setVar('rowCount', $rowCount);

        if ($rowCount > 0) {
            $model = new ListOfCachesModel();

            $model->addColumn(new Column_CacheTypeIcon(tr('myRecommendations_status'),
                function($row){
                    return [
                        'type' => $row['type'],
                        'status' => $row['status'],
                        'user_sts' => isset($row['user_sts'])?$row['user_sts']:null,
                    ];
            }));
            $model->addColumn(new Column_CacheName(tr('myRecommendations_cacheName'),
                function($row) {
                    return [
                        'cacheWp' => $row['wp_oc'],
                        'cacheName' => $row['name'],
                        'cacheStatus' => $row['status'],
                        'isStatusAware' => true,
                    ];
                }));

            $model->addColumn(new Column_UserName(tr('myRecommendations_cacheOwner'),
                function($row) {
                    return [
                        'userId' => $row['user_id'],
                        'userName' => $row['username'],
                    ];
                }));

            $model->addColumn(new Column_OnClickActionIcon(tr('myRecommendations_actionRemove'),
                function($row) {
                    return [
                        'icon' => '/tpl/stdstyle/images/log/16x16-trash.png',
                        'onClick' => "removeRecommendation(this, {$row['cache_id']})",
                        'title' => tr('myRecommendations_removeRecommendation')
                    ];
                }
            ));

            $pagination = new PaginationModel(50); //per-page number of caches
            $pagination->setRecordsCount($rowCount);
            list($queryLimit, $queryOffset) = $pagination->getQueryLimitAndOffset();
            $model->setPaginationModel($pagination);

            $model->addDataRows( self::completeDataRows(
                    $this->loggedUser->getUserId(), $queryLimit, $queryOffset));

            $this->view->setVar('listCacheModel', $model);
        }

        $this->view->buildView();
    }

    /**
     * This is called from ajax only
     * @param int cacheId
     */
    public function removeRecommendation($cacheId)
    {
        if(!$this->isUserLogged()) {
            $this->ajaxErrorResponse("User not logged", 401);
            return;
        }

        //check cacheId
        if(!is_numeric($cacheId)) {
            $this->ajaxErrorResponse("Invalid param", 400);
            exit;
        }

        CacheRecommendation::deleteRecommendation( $this->loggedUser->getUserId(), $cacheId);
        $this->ajaxSuccessResponse();

    }

    private function completeDataRows($userId, $limit=null, $offset=null) {
        $results = CacheRecommendation::getCachesRecommendedByUser($userId);

        // find cache status for user (found/not-found)
        foreach ( MultiLogStats::getStatusForUser($userId, array_keys($results)) as $s) {
            $results[ $s['cache_id']] ['user_sts'] = $s['type'];
        }

        return $results;
    }


/*    private function completeDataRows($userId, $limit=null, $offset=null)
    {
        $cacheIds = CacheNote::getCachesIdsForNotesAndModCoords($userId, $limit, $offset);

        $result = array_fill_keys($cacheIds, []);

        // fill notes
        foreach (CacheNote::getNotesByCacheIds($cacheIds, $userId) as $note){
            $result[ $note['cache_id'] ]['noteTxt'] = $note['desc'];
        }

        // fill mod-coords
        foreach ( UserCacheCoords::getCoordsByCacheIds($cacheIds, $userId) as $coord){
            $result[ $coord['cache_id'] ]['coords'] =
                Coordinates::FromCoordsFactory($coord['lat'], $coord['lot']);

            $result[ $coord['cache_id'] ]['coordsDate'] = $coord['date'];
        }

        // fill caches data
        $cacheFields = ['cache_id', 'name', 'type', 'status', 'wp_oc'];
        foreach ( MultiCacheStats::getGeocachesById($cacheIds, $cacheFields) as $c){
            foreach($cacheFields as $col){
                $result[ $c['cache_id'] ][$col] = $c[$col];
            }
        }

        // find last logs
        $logFields = ['id','text','type','user_id','date'];
        foreach( MultiLogStats::getLastLogForEachCache($cacheIds) as $log) {
            foreach($logFields as $col){
                $result[ $log['cache_id'] ]['llog_'.$col] = $log[$col];
            }
        }

        // find cache status for user (found/not-found)
        foreach ( MultiLogStats::getStatusForUser($userId, $cacheIds) as $s){
            $result[ $s['cache_id']] ['user_sts'] = $s['type'];
        }

        // find necessary-users
        $userIds = [];
        foreach ($result as $r){
            if (isset($r['llog_user_id'])){
                $userIds[$r['cache_id']] = $r['llog_user_id'];
            }
        }

        $userNames = MultiUserQueries::GetUserNamesForListOfIds($userIds);
        foreach ($result as &$r){
            if (isset($r['llog_user_id'])){
                $r['llog_userName'] = $userNames[$r['llog_user_id']];
            }
        }

        return $result;
    }*/



}

