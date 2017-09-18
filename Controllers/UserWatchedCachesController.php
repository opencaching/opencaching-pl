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
use lib\Objects\ChunkModels\DynamicMap\LastLogMapModel;
use lib\Objects\GeoCache\GeoCacheCommons;
use lib\Objects\GeoCache\GeoCacheLogCommons;

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
        $this->view->loadGMapApi(); /*initializeMap*/

        $mapModel = new LastLogMapModel();

        $mapModel->setDataRowExtractor(function($row){

            $iconFile = GeoCacheCommons::CacheIconByType(
                $row['type'], $row['status'], $row['user_sts'], true);

            $logIconFile = !empty($row['llog_type'])?
                GeoCacheLogCommons::GetIconForType($row['llog_type'], true):null;

            $logTypeName = !empty($row['llog_type'])?
                tr(GeoCacheLogCommons::typeTranslationKey($row['llog_type'])):null;

            return [
                'name' => $row['name'],
                'wp_oc' => $row['wp_oc'],
                'lon' => $row['longitude'],
                'lat' => $row['latitude'],
                'icon' => $iconFile,
                'llog_id' => $row['llog_id'],
                'llog_text' => strip_tags($row['llog_text']),
                'llog_icon' => $logIconFile,
                'llog_type_name' => $logTypeName,
                'llog_date' => $row['llog_date'],
                'llog_user_id' => $row['llog_user_id'],
                'llog_username' => $row['llog_username']
            ];
        });


        $mapModel->addMarkersDataRows(
            UserWatchedCache::getWatchedCachesWithLastLogs($this->loggedUser->getUserId()));

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

        // find the number of watched caches
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

            $pagination = new PaginationModel(50); //per-page number of caches
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
     * TODO: this should be moved to user profile
     */
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

        // check settings and reset to defaults if necessary
        $watchmailMode = $settings['watchmail_mode'];
        $watchmailHour = $settings['watchmail_hour'];
        $watchmailDay = $settings['watchmail_day'];

        if(!$this->areEmailSettingsInScope(
            $watchmailMode, $watchmailHour, $watchmailDay )){

            // email settings are wrong - reset to defaults

            // by default send notification: hourly
            $watchmailMode = UserWatchedCache::SEND_NOTIFICATION_HOURLY;
            $watchmailHour = 0; // default at midnight
            $watchmailDay = 7;  // default sunday

            $this->loggedUser->updateCacheWatchEmailSettings(
                $watchmailMode, $watchmailHour, $watchmailDay);
        }

        $this->view->setVar('intervalSelected', $watchmailMode);
        $this->view->setVar('weekDaySelected', $watchmailDay);
        $this->view->setVar('hourSelected', $watchmailHour);

        $this->view->buildView();
    }

    private function areEmailSettingsInScope(
        $watchmailMode, $watchmailHour, $watchmailDay){

        return (is_numeric($watchmailMode) &&
            in_array($watchmailMode,
                [UserWatchedCache::SEND_NOTIFICATION_DAILY,
                    UserWatchedCache::SEND_NOTIFICATION_HOURLY,
                    UserWatchedCache::SEND_NOTIFICATION_WEEKLY]) &&
            is_numeric($watchmailHour) &&
            $watchmailHour >= 0 && $watchmailHour <= 23 &&
            is_numeric($watchmailDay) &&
            $watchmailDay >= 1 && $watchmailDay <= 7);
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


        if($this->areEmailSettingsInScope(
            $watchmailMode, $watchmailHour, $watchmailDay)){

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


