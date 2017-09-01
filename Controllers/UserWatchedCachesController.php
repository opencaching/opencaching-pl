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
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;

class UserWatchedCachesController extends BaseController
{

    private $infoMsg = null;
    private $errorMsg = null;

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
        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
        }
        $this->view->setTemplate('userWatchedCaches/mapOfWatched');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/tpl/stdstyle/userWatchedCaches/userWatchedCaches.css'));
        $this->view->loadJQuery();
        $this->view->loadGMapApi();

        $mapModel = new DynamicMapModel();


        $this->view->setVar('mapModel', $mapModel);

        $this->view->buildView();
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

    public function emailSettings(){
        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
        }

        $this->view->setTemplate('userWatchedCaches/emailSettings');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/tpl/stdstyle/userWatchedCaches/userWatchedCaches.css'));

        $this->view->loadJQuery();

        $this->view->setVar('infoMsg', $this->infoMsg);
        $this->view->setVar('errorMsg', $this->errorMsg);


        $settings = $this->loggedUser->getCacheWatchEmailSettings();
        $this->view->setVar('intervalSelected', $settings['watchmail_mode']);
        $this->view->setVar('weekDaySelected', $settings['watchmail_day']);
        $this->view->setVar('hourSelected', $settings['watchmail_hour']);

        $this->view->buildView();
    }

    /**
     * Save new email settings and display settings form
     */
    public function updateEmailSettings(){
        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
        }

        $watchmailMode = isset($_POST['watchmail_mode'])?$_POST['watchmail_mode']:'';
        $watchmailHour = isset($_POST['watchmail_hour'])?$_POST['watchmail_hour']:'';
        $watchmailDay = isset($_POST['watchmail_day'])?$_POST['watchmail_day']:'';

        if(is_numeric($watchmailMode) &&
            in_array($watchmailMode,
                [UserWatchedCache::SEND_NOTIFICATION_DAILY,
                    UserWatchedCache::SEND_NOTIFICATION_HOURLY,
                    UserWatchedCache::SEND_NOTIFICATION_WEEKLY]) &&
            is_numeric($watchmailHour) &&
            $watchmailHour >= 0 && $watchmailHour <= 23 &&
            is_numeric($watchmailDay) &&
            $watchmailDay >= 1 && $watchmailDay <= 7){

            $this->loggedUser->updateCacheWatchEmailSettings(
                $watchmailMode, $watchmailHour, $watchmailDay);

            $this->infoMsg = tr('usrWatch_settingsSaved');

        }else{
            $this->errorMsg = tr('usrWatch_settingsSavedError');
        }

        $this->emailSettings();
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
            $this->ajaxErrorResposne("User not logged", 401);
            return;
        }

        $resp = UserWatchedCache::removeFromWatched(
            $this->loggedUser->getUserId(), $cacheWp);

        if($resp){
            $this->ajaxSuccessResposne();
        }else{
            $this->ajaxErrorResposne("Unknown OKAPI error", 500);
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
            $this->ajaxErrorResposne("User not logged", 401);
            return;
        }

        $resp = UserWatchedCache::addCacheToWatched(
            $this->loggedUser->getUserId(), $cacheWp);

        if($resp){
            $this->ajaxSuccessResposne();
        }else{
            $this->ajaxErrorResposne("Unknown OKAPI error", 500);
        }

    }

}


