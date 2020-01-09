<?php

namespace src\Controllers;

/**
 * This controller handles recommendations list view
 */

use src\Models\ChunkModels\ListOfCaches\Column_CacheName;
use src\Models\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use src\Models\ChunkModels\ListOfCaches\Column_OnClickActionIcon;
use src\Models\ChunkModels\ListOfCaches\Column_UserName;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\ChunkModels\PaginationModel;
use src\Models\GeoCache\CacheRecommendation;
use src\Models\GeoCache\MultiLogStats;
use src\Models\User\User;
use src\Utils\Uri\Uri;

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

    public function index () {
        $this->recommendations();
    }

    public function recommendations($userId = null) {
        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
            exit;
        }

        $this->view->setTemplate('myRecommendations/myRecommendations');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/views/myRecommendations/myRecommendations.css'));

        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/views/myRecommendations/myRecommendations.js'));

        $this->view->addLocalCss('/css/lightTooltip.css');
        $this->view->loadJQuery();

        //load lightPopup
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/js/lightPopup/lightPopup.css'));
        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/js/lightPopup/lightPopup.js'));

        if ($userId == null || $userId == $this->loggedUser->getUserId()) {
            $this->myRecommendations();
        } else {
            $this->userRecommendations($userId);
        }
    }

    private function userRecommendations($userId) {
        if (is_null($user = User::fromUserIdFactory($userId))) {
            $this->displayCommonErrorPageAndExit('no such user');
        }
        $this->view->setVar('pageTitle', tr('userRecommendations', [$user->getUserName()]));
        $this->recommendationsTable($userId, false);
    }

    private function myRecommendations()
    {
        $this->view->setVar('pageTitle', tr('my_recommendations'));
        $this->recommendationsTable($this->loggedUser->getUserId(), true);
    }

    private function recommendationsTable($userId, $isRemovingAllowed)
    {
        $rowCount = CacheRecommendation::getCountOfUserRecommendations($userId);
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

            if ($isRemovingAllowed) {
                $model->addColumn(new Column_OnClickActionIcon(tr('myRecommendations_actionRemove'),
                    function ($row) {
                        return [
                            'icon' => '/images/log/16x16-trash.png',
                            'onClick' => "removeRecommendation(this, {$row['cache_id']})",
                            'title' => tr('myRecommendations_removeRecommendation')
                        ];
                    }
                ));
            }

            $pagination = new PaginationModel(50); //per-page number of caches
            $pagination->setRecordsCount($rowCount);
            list($queryLimit, $queryOffset) = $pagination->getQueryLimitAndOffset();
            $model->setPaginationModel($pagination);


            $model->addDataRows(self::completeDataRows($userId, $queryLimit, $queryOffset));
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
        $results = CacheRecommendation::getCachesRecommendedByUser($userId, $limit, $offset);

        // find cache status for user (found/not-found)
        foreach ( MultiLogStats::getStatusForUser($userId, array_keys($results)) as $s) {
            $results[ $s['cache_id']] ['user_sts'] = $s['type'];
        }

        return $results;
    }
}
