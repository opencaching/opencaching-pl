<?php

namespace Controllers;

use Utils\Uri\Uri;
use Controllers\PageLayout\MainLayoutController;
use lib\Objects\User\UserPreferences\UserPreferences;
use lib\Objects\User\UserPreferences\UserMapSettings;
use Utils\Debug\Debug;

class CacheMapController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        // map is only for logged users
        $this->redirectNotLoggedUsers();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {
        $this->fullScreeenMap();
    }

    public function test($type=null)
    {
        $this->view->setTemplate('cacheMap/testMap');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/cacheMap/cacheMap.css'));

        switch($type){
            case 'leafLet':
                $this->view->addHeaderChunk('leafLet');
                break;
            case 'openLayers':
                $this->view->addHeaderChunk('openLayers');
                break;
            default:
                d("What?!",$type);
                exit;
        }

        $this->view->setVar('mapType', $type);

        $this->view->buildOnlySelectedTpl();
    }

    /**
     * Display fullscreen map
     */
    public function fullScreen()
    {
        $this->view->loadJQuery();
        $this->view->setTemplate('cacheMap/map');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/cacheMap/cacheMap.css'));

        $this->view->addHeaderChunk('openLayers', [true]);
        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/cacheMap/mapv4Common.js'));


        // read map config + run keys injector
        $mapConfigArray = $this->ocConfig->getMapConfig();
        $mapConfigInitFunc = $mapConfigArray['keyInjectionCallback'];
        if( !$mapConfigInitFunc($mapConfigArray) ){
            Debug::errorLog('mapConfig init failed');
            exit;
        }
        $this->view->setVar('extMapConfigs',$mapConfigArray['jsConfig']);


        $this->view->setVar('userId', $this->loggedUser->getUserId());

        $userPref = UserPreferences::getUserPrefsByKey(UserMapSettings::KEY);
        $this->view->setVar('filterVal', $userPref->getJsonValues());

        $this->view->display(MainLayoutController::MINI_TEMPLATE);
    }

    public function saveMapSettingsAjax()
    {
        if(!isset($_POST['userMapSettings'])){
            $this->ajaxErrorResponse('no filtersData var in JSON', 400);
        }

        $json = $_POST['userMapSettings'];

        if(UserPreferences::savePreferencesJson(UserMapSettings::KEY, $json)){
            $this->ajaxSuccessResponse("Data saved");
        }else{
            $this->ajaxErrorResponse("Can't save a data", 500);
        }

    }
}

