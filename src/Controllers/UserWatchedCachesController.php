<?php
namespace src\Controllers;

use src\Utils\Text\Formatter;
use src\Utils\Uri\Uri;
use src\Models\ChunkModels\PaginationModel;
use src\Models\ChunkModels\DynamicMap\CacheWithLogMarkerModel;
use src\Models\ChunkModels\DynamicMap\DynamicMapModel;
use src\Models\ChunkModels\ListOfCaches\Column_CacheLog;
use src\Models\ChunkModels\ListOfCaches\Column_CacheName;
use src\Models\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use src\Models\ChunkModels\ListOfCaches\Column_OnClickActionIcon;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\GeoCache\GeoCacheLogCommons;
use src\Models\User\UserWatchedCache;

class UserWatchedCachesController extends BaseController
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
        $this->listOfWatches();
    }

    public function mapOfWatches()
    {
        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
        }
        $this->view->setTemplate('userWatchedCaches/mapOfWatched');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/views/userWatchedCaches/userWatchedCaches.css'));
        $this->view->loadJQuery();

        $this->view->addHeaderChunk('openLayers5');

        $mapModel = new DynamicMapModel();

        $mapModel->addMarkersWithExtractor(
            CacheWithLogMarkerModel::class,
            UserWatchedCache::getWatchedCachesWithLastLogs($this->loggedUser->getUserId()),
            function($row){

                $iconFile = GeoCacheCommons::CacheIconByType(
                    $row['type'], $row['status'], $row['user_sts']);

                $logIconFile = !empty($row['llog_type'])?
                    GeoCacheLogCommons::GetIconForType($row['llog_type']):null;

                $logTypeName = !empty($row['llog_type'])?
                    tr(GeoCacheLogCommons::typeTranslationKey($row['llog_type'])):null;

                $m = new CacheWithLogMarkerModel();

                $m->lat = $row['latitude'];
                $m->lon = $row['longitude'];
                $m->icon = $iconFile;

                $m->name = $row['name'];
                $m->wp = $row['wp_oc'];
                $m->username = '-';

                $m->link = GeoCache::GetCacheUrlByWp($row['wp_oc']);
                $m->log_icon = $logIconFile;
                $m->log_text = strip_tags($row['llog_text'], '<br><p>');
                $m->log_link = GeoCacheLog::getLogUrlByLogId($row['llog_id']);
                $m->log_typeName = $logTypeName;
                $m->log_username = $row['llog_username'];
                $m->log_date = Formatter::date($row['llog_date']);
                return $m;
        });

        $this->view->setVar('mapModel', $mapModel);

        $this->view->buildView();
    }

    /**
     * Display paginated list of caches watched by current user
     */
    public function listOfWatches()
    {
        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
        }

        $this->view->setTemplate('userWatchedCaches/userWatchedCaches');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/views/userWatchedCaches/userWatchedCaches.css'));
        $this->view->addLocalCss('/css/lightTooltip.css');

        $this->view->loadJQuery();

        // find the number of watched caches
        $watchedCachesCount = UserWatchedCache::getWatchedCachesCount(
            $this->loggedUser->getUserId());

        $this->view->setVar('cachesCount', $watchedCachesCount);

        if($watchedCachesCount > 0){
            // prepare model for list of watched caches
            $model = new ListOfCachesModel();

            $model->addColumn(new Column_CacheTypeIcon(tr('usrWatch_status')));
            $model->addColumn(new Column_CacheName(tr('usrWatch_watchedCache'),
                function($row) {
                    return [
                        'cacheWp' => $row['wp_oc'],
                        'cacheName' => $row['name'],
                        'cacheStatus' => $row['status'],
                    ];
                }));
            $model->addColumn(new Column_CacheLog(tr('usrWatch_lastLog'),
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

            $model->addColumn(new Column_OnClickActionIcon(tr('usrWatch_actionRemove'),
                function($row){
                    return [
                        'icon' => '/images/log/16x16-trash.png',
                        'onClick' => "removeFromWatched(this, '".$row['wp_oc']."')",
                        'title' => tr('usrWatch_removeWatched')
                    ];
                }
            ));

            $pagination = new PaginationModel(50); //per-page number of caches
            $pagination->setRecordsCount($watchedCachesCount);

            list($queryLimit, $queryOffset) = $pagination->getQueryLimitAndOffset();
            $model->setPaginationModel($pagination);

            $model->addDataRows(
                UserWatchedCache::getWatchedCachesWithLastLogs(
                    $this->loggedUser->getUserId(), $queryLimit, $queryOffset)
                );

            $this->view->setVar('listCacheModel', $model);
        }

        $this->view->buildView();
    }

    /**
     * This method removed given cache from list of watched geocaches for current user.
     * This should be called by AJAX.
     *
     * (This is former removewatch.php script)
     *
     */
    public function removeFromWatchesAjax($cacheWp)
    {
        if(!$this->isUserLogged()){
            $this->ajaxErrorResponse("User not logged", 401);
            return;
        }

        $resp = UserWatchedCache::removeFromWatched(
            $this->loggedUser->getUserId(), $cacheWp);

        if($resp){
            $cache = GeoCache::fromWayPointFactory($cacheWp);
            $this->ajaxSuccessResponse($cache->getWatchingUsersCount());
        }else{
            $this->ajaxErrorResponse("Unknown OKAPI error", 500);
        }
    }

    /**
     * This method add given cache to list of watched geocaches for current user.
     * This should be called by AJAX.
     *
     * (This is former watchcache.php script)
     */
    public function addToWatchesAjax($cacheWp)
    {
        if(!$this->isUserLogged()){
            $this->ajaxErrorResponse("User not logged", 401);
            return;
        }

        $resp = UserWatchedCache::addCacheToWatched(
            $this->loggedUser->getUserId(), $cacheWp);

        if($resp){
            $cache = GeoCache::fromWayPointFactory($cacheWp);
            $this->ajaxSuccessResponse($cache->getWatchingUsersCount());
        }else{
            $this->ajaxErrorResponse("Unknown OKAPI error", 500);
        }

    }

}
