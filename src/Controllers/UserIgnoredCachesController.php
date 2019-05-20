<?php
namespace src\Controllers;

use src\Models\ChunkModels\PaginationModel;
use src\Models\ChunkModels\ListOfCaches\Column_CacheLog;
use src\Models\ChunkModels\ListOfCaches\Column_CacheName;
use src\Models\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use src\Models\ChunkModels\ListOfCaches\Column_OnClickActionIcon;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\GeoCache\UserIgnoredCache;

class UserIgnoredCachesController extends BaseController
{

    private $infoMsg = null;
    private $errorMsg = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return TRUE;
    }

    public function index()
    {
        $this->listOfIgnored();
    }

    /**
     * Display paginated list of caches ignored by current user
     */
    public function listOfIgnored()
    {
        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
        }

        $this->view->setTemplate('userIgnoredCaches/userIgnoredCaches');
        $this->view->addLocalCss('/css/lightTooltip.css');

        $this->view->loadJQuery();

        // find the number of ignored caches
        $IgnoredCachesCount = UserIgnoredCache::getIgnoredCachesCount(
            $this->loggedUser->getUserId());

        $this->view->setVar('cachesCount', $IgnoredCachesCount);

        if($IgnoredCachesCount > 0){
            // prepare model for list of ignored caches
            $model = new ListOfCachesModel();

            $model->addColumn(new Column_CacheTypeIcon(tr('usrIgnore_statusColumn')));
            $model->addColumn(new Column_CacheName(tr('usrIgnore_ignoredCache'),
                function($row) {
                    return [
                        'cacheWp' => $row['wp_oc'],
                        'cacheName' => $row['name'],
                        'cacheStatus' => $row['status'],
                    ];
                }));
            $model->addColumn(new Column_CacheLog(tr('usrIgnore_lastLogColumn'),
                function($row){
                    return [
                        'logId'         => $row['llog_id'],
                        'logType'       => $row['llog_type'],
                        'logText'       => $row['llog_text'],
                        'logUserName'   => $row['llog_username'],
                        'logDate'       => $row['llog_date']
                    ];
                }
            ));

            $model->addColumn(new Column_OnClickActionIcon(tr('usrIgnore_actionRemoveColumn'),
                function($row){
                    return [
                        'icon' => 'images/log/16x16-trash.png',
                        'onClick' => "removeFromIgnored(this, '".$row['wp_oc']."')",
                        'title' => tr('usrIgnore_off_ignore')
                    ];
                }
            ));

            $pagination = new PaginationModel(50); //per-page number of caches
            $pagination->setRecordsCount($IgnoredCachesCount);

            list($queryLimit, $queryOffset) = $pagination->getQueryLimitAndOffset();
            $model->setPaginationModel($pagination);

            $model->addDataRows(
                UserIgnoredCache::getIgnoredCachesWithLastLogs(
                    $this->loggedUser->getUserId(), $queryLimit, $queryOffset)
                );

            $this->view->setVar('listCacheModel', $model);
        }
        $this->view->buildView();
    }

    /**
     * This method removed given cache from list of ignored geocaches for current user.
     * This should be called by AJAX.
     *
     */
    public function removeFromIgnoredAjax($cacheWp)
    {
        if(!$this->isUserLogged()){
            $this->ajaxErrorResponse("User not logged", 401);
            return;
        }

        $resp = UserIgnoredCache::removeFromIgnored(
            $this->loggedUser->getUserId(), $cacheWp);

        if ($resp) {
            $this->ajaxSuccessResponse();
        } else {
            $this->ajaxErrorResponse("Unknown OKAPI error", 500);
        }
    }

    /**
     * This method add given cache to list of ignored geocaches for current user.
     * This should be called by AJAX.
     */
    public function addToIgnoredAjax($cacheWp)
    {
        if(!$this->isUserLogged()){
            $this->ajaxErrorResponse("User not logged", 401);
            return;
        }

        $resp = UserIgnoredCache::addCacheToIgnored(
            $this->loggedUser->getUserId(), $cacheWp);

        if ($resp) {
            $this->ajaxSuccessResponse();
        } else {
            $this->ajaxErrorResponse("Unknown OKAPI error", 500);
        }
    }
}
