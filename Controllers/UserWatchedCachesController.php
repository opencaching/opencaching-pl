<?php
namespace Controllers;

use lib\Objects\ChunkModels\ListOfCaches\ListOfCachesModel;
use lib\Objects\User\UserWatchedCache;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheName;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheLastLog;
use lib\Objects\ChunkModels\ListOfCaches\Column_OnClickActionIcon;
use lib\Objects\ChunkModels\PaginationModel;
use Utils\Uri\Uri;

class UserWatchedCachesController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->listOfWatches();
    }

    public function mapOfWatches()
    {
        echo "temporary-unavailable :( ";
    }

    public function listOfWatches()
    {
        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
        }

        $this->view->setTemplate('userWatchedCaches/userWatchedCaches');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/tpl/stdstyle/userWatchedCaches/userWatchedCaches.css'));

        $this->view->loadJQuery();

        $watchedCachesCount = UserWatchedCache::getWatchedCachesCount(
            $this->loggedUser->getUserId());

        $this->view->setVar('cachesCount', $watchedCachesCount);

        if($watchedCachesCount > 0){
            // prepare model for list of watched caches
            $model = new ListOfCachesModel();

            $model->addColumn(new Column_CacheTypeIcon(tr('usrWatch_status')));
            $model->addColumn(new Column_CacheName(tr('usrWatch_watchedCache')));
            $model->addColumn(new Column_CacheLastLog(tr('usrWatch_lastLog'),
                function($row){
                    return [
                        'logId'         => $row['llog_id'],
                        'logType'       => $row['llog_type'],
                        'logUserId'     => $row['llog_user_id'],
                        'logText'       => $row['llog_text'],
                        'logUserName'   => $row['llog_username'],
                        'logDate'       => $row['llog_date']
                    ];
                }
            ));

            $model->addColumn(new Column_OnClickActionIcon(tr('usrWatch_actionRemove'),
                function($row){
                    return [
                        'icon' => 'tpl/stdstyle/images/log/16x16-trash.png',
                        'onClick' => "removeFromWatched(this, '".$row['wp_oc']."')",
                        'title' => tr('usrWatch_removeWatched')
                    ];
                }
            ));

            $pagination = new PaginationModel(20); //per-page number of caches
            $pagination->setRecordsCount($watchedCachesCount);

            list($queryLimit, $queryOffset) = $pagination->getQueryLimitAndOffset();;
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
     * (This is former removewatch.php script)
     *
     */
    public function removeFromWatchesAjax($cacheId)
    {
        if(!$this->isUserLogged()){
            $this->ajaxErrorResposne("User not logged", 401);
            return;
        }

        $resp = UserWatchedCache::removeFromWatched(
            $this->loggedUser->getUserId(), $_GET['cacheWp']);

        if($resp){
            $this->ajaxSuccessResposne();
        }else{
            $this->ajaxErrorResposne("Unknown OKAPI error", 500);
        }
    }

    /**
     * (This is former watchcache.php script)
     */
    public function addToWatchesAjax($cacheId)
    {
        if(!$this->isUserLogged()){
            $this->ajaxErrorResposne("User not logged", 401);
            return;
        }

        $resp = UserWatchedCache::addCacheToWatched(
            $this->loggedUser->getUserId(), $_GET['cacheWp']);

        if($resp){
            $this->ajaxSuccessResposne();
        }else{
            $this->ajaxErrorResposne("Unknown OKAPI error", 500);
        }

    }

}


