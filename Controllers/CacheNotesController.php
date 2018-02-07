<?php

namespace Controllers;

/**
 * This controller handled user notes list view
 */

use Utils\Uri\Uri;
use lib\Objects\ChunkModels\PaginationModel;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheLastLog;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheName;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use lib\Objects\ChunkModels\ListOfCaches\Column_EllipsedText;
use lib\Objects\ChunkModels\ListOfCaches\Column_OnClickActionIcon;
use lib\Objects\ChunkModels\ListOfCaches\ListOfCachesModel;
use lib\Objects\GeoCache\CacheNote;
use lib\Objects\GeoCache\UserCacheCoords;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\GeoCache\MultiCacheStats;
use lib\Objects\GeoCache\MultiLogStats;
use lib\Objects\User\MultiUserQueries;
use lib\Objects\ChunkModels\ListOfCaches\Column_SimpleText;
use Utils\Debug\Debug;

class CacheNotesController extends BaseController
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

        $this->view->setTemplate('cacheNotes/cacheNotes');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/cacheNotes/cacheNotes.css'));

        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/cacheNotes/cacheNotes.js'));

        $this->view->addLocalCss('/tpl/stdstyle/css/lightTooltip.css');
        $this->view->loadJQuery();

        //load lightPopup
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/js/lightPopup/lightPopup.css'));
        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/js/lightPopup/lightPopup.js'));



        $rowCount = CacheNote::getCountOfUserNotesAndModCoords($this->loggedUser->getUserId());
        $this->view->setVar('rowCount', $rowCount);

        if($rowCount > 0){
            $model = new ListOfCachesModel();

            $model->addColumn(new Column_CacheTypeIcon(tr('myNotes_status'),
                function($row){
                    return [
                        'type' => $row['type'],
                        'status' => $row['status'],
                        'user_sts' => isset($row['user_sts'])?$row['user_sts']:null,
                    ];
            }));
            $model->addColumn(new Column_CacheName(tr('myNotes_cacheName')));
            $model->addColumn(new Column_CacheLastLog(tr('myNotes_lastLogEntry'),
                function($row){
                    if(isset($row['llog_id'])){
                        return [
                            'logId'         => $row['llog_id'],
                            'logType'       => $row['llog_type'],
                            'logText'       => $row['llog_text'],
                            'logUserName'   => $row['llog_userName'],
                            'logDate'       => $row['llog_date']
                        ];
                    }else{
                        return [];
                    }
                }
            ));

            // column with notes (+id of this col.)
            $model->addColumn(new Column_EllipsedText(tr('myNotes_note'),function($row){
                return [
                    'text' => isset($row['noteTxt'])?$row['noteTxt']:'-',
                    'maxChars' => 10,
                    'labelShow' => tr('myNotes_showFullNote'),
                    'labelHide' => tr('myNotes_hideFullNote'),
                ];
            }));

            $model->addColumn(new Column_OnClickActionIcon(tr('myNotes_removeNote'),
                function($row){
                    return [
                        'icon' => isset($row['noteTxt'])?'/tpl/stdstyle/images/log/16x16-trash.png':null,
                        'onClick' => "removeNote(this, {$row['cache_id']})",
                        'title' => tr('myNotes_removeNote')
                    ];
                }
            ));

            $model->addColumn(new Column_SimpleText(tr('myNotes_modCacheCoords'),
                function($row){
                    if(isset($row['coords'])){
                        return ($row['coords'])->getAsText();
                    }else{
                        return '-';
                    }
                }
            ));

            $model->addColumn(new Column_OnClickActionIcon(tr('myNotes_removeCoords'),
                function($row){

                    return [
                        'icon' => isset($row['coords'])?'/tpl/stdstyle/images/log/16x16-trash.png':null,
                        'onClick' => "removeCoords(this, {$row['cache_id']})",
                        'title' => tr('myNotes_removeCoords')
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
     * This is called from AJAX only
     * @param int $cacheId
     */
    public function removeCoords($cacheId)
    {
        if(!$this->isUserLogged()){
            $this->ajaxErrorResponse("User not logged", 401);
            return;
        }

        //check cacheId
        if(!is_numeric($cacheId)){
            $this->ajaxErrorResponse("Invalid param", 400);
            exit;
        }

        UserCacheCoords::deleteCoords($cacheId, $this->loggedUser->getUserId());
        $this->ajaxSuccessResponse();
    }

    /**
     * This is called from ajax only
     * @param int cacheId
     */
    public function removeNote($cacheId)
    {
        if(!$this->isUserLogged()){
            $this->ajaxErrorResponse("User not logged", 401);
            return;
        }

        Debug::errorLog($cacheId);

        //check cacheId
        if(!is_numeric($cacheId)){
            $this->ajaxErrorResponse("Invalid param", 400);
            exit;
        }

        CacheNote::deleteNote( $this->loggedUser->getUserId(), $cacheId);
        $this->ajaxSuccessResponse();

    }

    private function completeDataRows($userId, $limit=null, $offset=null)
    {
        $cacheIds = CacheNote::getCachesIdsForNotesAndModCoords($userId, $limit, $offset);

        $result = array_fill_keys($cacheIds, []);

        // fill notes
        foreach (CacheNote::getNotesByCacheIds($cacheIds) as $note){
            $result[ $note['cache_id'] ]['noteTxt'] = $note['desc'];
        }

        // fill mod-coords
        foreach ( UserCacheCoords::getCoordsByCacheIds($cacheIds) as $coord){
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
    }



}

