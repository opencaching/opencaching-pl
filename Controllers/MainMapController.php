<?php

namespace Controllers;

use Utils\Uri\Uri;
use lib\Objects\User\UserPreferences\MainMapSettings;
use lib\Objects\User\UserPreferences\UserPreferences;
use lib\Objects\User\User;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\CacheSet\CacheSet;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;

class MainMapController extends BaseController
{
    public function __construct($requestedAction)
    {
        parent::__construct();

        if($requestedAction == 'getPopupData') {
            // popupData is called by ajax only
            $this->checkUserLoggedAjax();
        } else {
            // map is only for logged users
            $this->redirectNotLoggedUsers();
        }
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {
        $this->fullScreen();
    }

    /**
     * Display fullscreen map
     */
    public function fullScreen()
    {
        $this->view->setTemplate('mainMap/fullScreenMap');

        $this->mapCommonInit();

        $this->view->buildInMiniTpl();
    }

    public function embeded()
    {
        $this->view->setTemplate('mainMap/embededMap');

        $this->mapCommonInit();

        $this->view->buildView();
    }

    public function mini()
    {
        $this->view->setTemplate('mainMap/miniMap');

        $this->mapCommonInit(false);

        $this->view->buildView();
    }

    private function mapCommonInit()
    {
        $this->view->addHeaderChunk('openLayers5');
        $this->view->loadJQuery();

        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/tpl/stdstyle/mainMap/mainMap.css'));

        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/tpl/stdstyle/mainMap/mainMapPopup.css'));

        $this->view->addHeaderChunk('handlebarsJs');

        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/mainMap/mainMap.js'));

        // find user for this map display
        $user = null;
        if (isset($_REQUEST['userid'])) {
            $user = User::fromUserIdFactory($_REQUEST['userid']);
        }
        if (!$user) {
            $user = $this->loggedUser;
        }
        $this->view->setVar('mapUserId', $user->getUserId());
        $this->view->setVar('mapUserName', $user->getUserName());

        $mapModel = new DynamicMapModel();

        // set map center based on requested coords&zoom
        if (isset($_GET['lat']) && isset($_GET['lon']) && isset($_GET['zoom'])) {

            $mapCenter = Coordinates::FromCoordsFactory(
                floatval($_GET['lat']), floatval($_GET['lon']));
            $zoom = intval($_GET['zoom']);

        // set map center based on requested coords and open popup at center (used to show geocache)
        } else if(isset($_GET['lat']) && isset($_GET['lon']) && isset($_GET['openPopup'])) {

            $mapCenter = Coordinates::FromCoordsFactory(
                floatval($_GET['lat']), floatval($_GET['lon']));
            $zoom = 14;
            $this->view->setVar('openPopup', true);

        // set map center based on user home-coords
        } else {
            $mapCenter = $user->getHomeCoordinates();
            $zoom = 11; //default zoom for user-home coords
        }

        if(is_object($mapCenter) && $mapCenter->areCordsReasonable()){
            $mapModel->setCoords($mapCenter);
            $mapModel->setZoom($zoom);
        }


        $savedUserPrefs = UserPreferences::getUserPrefsByKey(MainMapSettings::KEY);
        $this->view->setVar('savedUserPrefs', $savedUserPrefs->getJsonValues());

        $mapModel->setInitLayerName($savedUserPrefs->getValues()['map']);

        $this->view->setVar('mapModel', $mapModel);


        // TODO: cacheid=??? is not supported yet

        // parse searchData if given
        if (isset($_GET['searchdata'])) {
            $this->view->setVar('searchData', $_GET['searchdata']);
        }

        // parse powerTrailIds if given
        if (isset($_GET['cs'])) {

            //TODO: remove it
            $this->view->setVar('powerTrailIds', $_GET['cs']);

            $this->view->setVar('cacheSet', CacheSet::fromCacheSetIdFactory($_GET['cs']));
        }


    }

}