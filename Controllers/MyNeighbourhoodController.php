<?php
namespace Controllers;

use Utils\Uri\SimpleRouter;
use Utils\Uri\Uri;
use lib\Objects\ChunkModels\PaginationModel;
use lib\Objects\ChunkModels\DynamicMap\CacheMarkerModel;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;
use lib\Objects\ChunkModels\DynamicMap\LogMarkerModel;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\Neighbourhood\MyNbhSets;
use lib\Objects\Neighbourhood\Neighbourhood;
use lib\Objects\User\UserPreferences\UserPreferences;
use Utils\Text\UserInputFilter;
use lib\Objects\User\UserPreferences\NeighbourhoodPref;

class MyNeighbourhoodController extends BaseController
{

    // Minimum MyNeighbourhood radius (in km)
    const NBH_RADIUS_MIN = 1;

    // Maximum MyNeighbourhood radius (in km)
    const NBH_RADIUS_MAX = 150;

    // Minimum caches/log per page
    const CACHES_PER_PAGE_MIN = 2;

    // Minimum caches/log per page
    const CACHES_PER_PAGE_MAX = 10;

    // Maximum additional Neighbourhoods user can have
    const MAX_NEIGHBOURHOODS = 5;

    // Caches/Logs per page (on details pages)
    const ITEMS_PER_DETAIL_PAGE = 25;

    /** @var string */
    private $infoMsg = null;

    /** @var string */
    private $errorMsg = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Displays main MyNeighbourhood page
     *
     * @param int $nbhSeq - MyNbh number (seq). 0 = default user's Nbh
     */
    public function index($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();

        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList($this->loggedUser);
        if (empty($neighbourhoodsList)) { // User doesn't have any MyNeighbourhoods set, so redirect to config
            $this->view->redirect(SimpleRouter::getLink('MyNeighbourhood', 'config'));
            exit();
        }
        $selectedNbh = (int) $nbhSeq;
        if (! array_key_exists($selectedNbh, $neighbourhoodsList)) { // Selected MyNeighbourhood not found
            if ($selectedNbh == 0) { // User has no Home Coords -> redirect to config
                $this->view->redirect(SimpleRouter::getLink('MyNeighbourhood', 'config'));
            } else { // Redirect to default MyNeighbourhood
                $this->view->redirect(SimpleRouter::getLink('MyNeighbourhood', 'index', 0));
            }
            exit();
        }
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $nbhItemSet = new MyNbhSets($neighbourhoodsList[$selectedNbh]->getCoords(), $neighbourhoodsList[$selectedNbh]->getRadius());
        $latestCaches = $nbhItemSet->getLatestCaches($preferences['style']['caches-count'], 0, false);
        $upcomingEvents = $nbhItemSet->getUpcomingEvents($preferences['style']['caches-count'], 0);
        $ftfCaches = $nbhItemSet->getLatestCaches($preferences['style']['caches-count'], 0, true);
        $latestLogs = $nbhItemSet->getLatestLogs($preferences['style']['caches-count'], 0);
        $topRatedCaches = $nbhItemSet->getTopRatedCaches($preferences['style']['caches-count'], 0);
        $latestTitled = $nbhItemSet->getLatestTitledCaches($preferences['style']['caches-count'], 0);
        $this->view->setVar('latestCaches', $latestCaches);
        $this->view->setVar('upcomingEvents', $upcomingEvents);
        $this->view->setVar('FTFCaches', $ftfCaches);
        $this->view->setVar('latestLogs', $latestLogs);
        $this->view->setVar('topRatedCaches', $topRatedCaches);
        $this->view->setVar('latestTitled', $latestTitled);
        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('preferences', $preferences);
        $this->view->setVar('user', $this->loggedUser);
        $mapModel = new DynamicMapModel();
        $allCaches = [];
        foreach ($preferences['items'] as $sectionName => $sectionConfig) {
            if ($sectionConfig['show']) {
                switch ($sectionName) {
                    case Neighbourhood::ITEM_LATESTCACHES:
                        $allCaches = array_merge($allCaches, $latestCaches);
                        break;
                    case Neighbourhood::ITEM_UPCOMINGEVENTS:
                        $allCaches = array_merge($allCaches, $upcomingEvents);
                        break;
                    case Neighbourhood::ITEM_FTFCACHES:
                        $allCaches = array_merge($allCaches, $ftfCaches);
                        break;
                    case Neighbourhood::ITEM_TITLEDCACHES:
                        $allCaches = array_merge($allCaches, $latestTitled);
                        break;
                    case Neighbourhood::ITEM_RECOMMENDEDCACHES:
                        $allCaches = array_merge($allCaches, $topRatedCaches);
                        break;
                    default:
                        break;
                }
            }
        }
        $mapModel->addMarkers(CacheMarkerModel::class, $allCaches, function($row){
            return CacheMarkerModel::fromGeocacheFactory($row, $this->loggedUser);
        });
        if ($preferences['items'][Neighbourhood::ITEM_LATESTLOGS]['show']) {
            $mapModel->addMarkers(LogMarkerModel::class, $latestLogs, function($row){
                return LogMarkerModel::fromGeoCacheLogFactory($row, $this->loggedUser);
            });
            }
        $this->view->setVar('mapModel', $mapModel);
        $this->view->loadGMapApi();
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/myNeighbourhood-' . $preferences['style']['name'] . '.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/common.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/css/lightTooltip.css'));
        $this->view->addLocalJs(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/myNeighbourhood.js'), true, true);
        $this->view->loadJQueryUI();
        $this->view->setTemplate('myNeighbourhood/myNeighbourhood');
        $this->view->buildView();
    }

    /**
     * Displays latest caches detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq
     */
    public function latestCaches($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();
        $selectedNbh = (int) $nbhSeq;
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList($this->loggedUser);
        if (! array_key_exists($selectedNbh, $neighbourhoodsList)) { // Selected MyNeighbourhood not found
            $this->view->redirect(SimpleRouter::getLink('MyNeighbourhood', 'index'));
            exit();
        }
        $coords = $neighbourhoodsList[$selectedNbh]->getCoords();
        $nbhItemSet = new MyNbhSets($coords, $neighbourhoodsList[$selectedNbh]->getRadius());
        $paginationModel = new PaginationModel(self::ITEMS_PER_DETAIL_PAGE);
        $paginationModel->setRecordsCount($nbhItemSet->getLatestCachesCount(false));
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('caches', $nbhItemSet->getLatestCaches($limit, $offset, false));
        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('user', $this->loggedUser);
        $this->view->setVar('coords', $coords);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/common.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/myNeighbourhood-' . $preferences['style']['name'] . '.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/css/lightTooltip.css'));
        $this->view->setTemplate('myNeighbourhood/detail_LatestCaches');
        $this->view->buildView();
    }

    /**
     * Displays most recommended caches detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq
     */
    public function mostRecommended($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();
        $selectedNbh = (int) $nbhSeq;
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList($this->loggedUser);
        if (! array_key_exists($selectedNbh, $neighbourhoodsList)) { // Selected MyNeighbourhood not found
            $this->view->redirect(SimpleRouter::getLink('MyNeighbourhood', 'index'));
            exit();
        }
        $coords = $neighbourhoodsList[$selectedNbh]->getCoords();
        $nbhItemSet = new MyNbhSets($coords, $neighbourhoodsList[$selectedNbh]->getRadius());
        $paginationModel = new PaginationModel(self::ITEMS_PER_DETAIL_PAGE);
        $paginationModel->setRecordsCount($nbhItemSet->getTopRatedCachesCount());
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('caches', $nbhItemSet->getTopRatedCaches($limit, $offset));
        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('user', $this->loggedUser);
        $this->view->setVar('coords', $coords);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/common.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/myNeighbourhood-' . $preferences['style']['name'] . '.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/css/lightTooltip.css'));
        $this->view->setTemplate('myNeighbourhood/detail_RecommendedCaches');
        $this->view->buildView();
    }

    /**
     * Displays FTF caches detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq
     */
    public function ftfCaches($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();
        $selectedNbh = (int) $nbhSeq;
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList($this->loggedUser);
        if (! array_key_exists($selectedNbh, $neighbourhoodsList)) { // Selected MyNeighbourhood not found
            $this->view->redirect(SimpleRouter::getLink('MyNeighbourhood', 'index'));
            exit();
        }
        $coords = $neighbourhoodsList[$selectedNbh]->getCoords();
        $nbhItemSet = new MyNbhSets($coords, $neighbourhoodsList[$selectedNbh]->getRadius());
        $paginationModel = new PaginationModel(self::ITEMS_PER_DETAIL_PAGE);
        $paginationModel->setRecordsCount($nbhItemSet->getLatestCachesCount(true));
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('caches', $nbhItemSet->getLatestCaches($limit, $offset, true));
        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('user', $this->loggedUser);
        $this->view->setVar('coords', $coords);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/common.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/myNeighbourhood-' . $preferences['style']['name'] . '.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/css/lightTooltip.css'));
        $this->view->setTemplate('myNeighbourhood/detail_FTFCaches');
        $this->view->buildView();
    }

    /**
     * Displays titled caches detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq
     */
    public function titledCaches($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();
        $selectedNbh = (int) $nbhSeq;
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList($this->loggedUser);
        if (! array_key_exists($selectedNbh, $neighbourhoodsList)) { // Selected MyNeighbourhood not found
            $this->view->redirect(SimpleRouter::getLink('MyNeighbourhood', 'index'));
            exit();
        }
        $coords = $neighbourhoodsList[$selectedNbh]->getCoords();
        $nbhItemSet = new MyNbhSets($coords, $neighbourhoodsList[$selectedNbh]->getRadius());
        $paginationModel = new PaginationModel(self::ITEMS_PER_DETAIL_PAGE);
        $paginationModel->setRecordsCount($nbhItemSet->getLatestTitledCachesCount());
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('caches', $nbhItemSet->getLatestTitledCaches($limit, $offset));
        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('user', $this->loggedUser);
        $this->view->setVar('coords', $coords);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/common.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/myNeighbourhood-' . $preferences['style']['name'] . '.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/css/lightTooltip.css'));
        $this->view->setTemplate('myNeighbourhood/detail_TitledCaches');
        $this->view->buildView();
    }

    /**
     * Displays upcomming events detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq
     */
    public function upcommingEvents($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();
        $selectedNbh = (int) $nbhSeq;
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList($this->loggedUser);
        if (! array_key_exists($selectedNbh, $neighbourhoodsList)) { // Selected MyNeighbourhood not found
            $this->view->redirect(SimpleRouter::getLink('MyNeighbourhood', 'index'));
            exit();
        }
        $coords = $neighbourhoodsList[$selectedNbh]->getCoords();
        $nbhItemSet = new MyNbhSets($coords, $neighbourhoodsList[$selectedNbh]->getRadius());
        $paginationModel = new PaginationModel(self::ITEMS_PER_DETAIL_PAGE);
        $paginationModel->setRecordsCount($nbhItemSet->getUpcomingEventsCount());
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('caches', $nbhItemSet->getUpcomingEvents($limit, $offset));
        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('user', $this->loggedUser);
        $this->view->setVar('coords', $coords);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/common.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/myNeighbourhood-' . $preferences['style']['name'] . '.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/css/lightTooltip.css'));
        $this->view->setTemplate('myNeighbourhood/detail_UpcommingEvents');
        $this->view->buildView();
    }

    /**
     * Displays latest logs detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq
     */
    public function latestLogs($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();
        $selectedNbh = (int) $nbhSeq;
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList($this->loggedUser);
        if (! array_key_exists($selectedNbh, $neighbourhoodsList)) { // Selected MyNeighbourhood not found
            $this->view->redirect(SimpleRouter::getLink('MyNeighbourhood', 'index'));
            exit();
        }
        $coords = $neighbourhoodsList[$selectedNbh]->getCoords();
        $logset = new MyNbhSets($coords, $neighbourhoodsList[$selectedNbh]->getRadius());
        $paginationModel = new PaginationModel(self::ITEMS_PER_DETAIL_PAGE);
        $paginationModel->setRecordsCount($logset->getLatestLogsCount());
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('logs', $logset->getLatestLogs($limit, $offset));
        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('user', $this->loggedUser);
        $this->view->setVar('coords', $coords);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/common.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/myNeighbourhood-' . $preferences['style']['name'] . '.css'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/css/lightTooltip.css'));
        $this->view->setTemplate('myNeighbourhood/detail_LatestLogs');
        $this->view->buildView();
    }

    /**
     * Displays MyNeighbour config
     *
     * @param number $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function config($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();
        $selectedNbh = (int) $nbhSeq;

        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();

        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList($this->loggedUser);
        if ($selectedNbh != - 1 && ! array_key_exists($selectedNbh, $neighbourhoodsList)) {
            $selectedNbh = 0;
        }
        if (array_key_exists($selectedNbh, $neighbourhoodsList)) {
            $this->view->setVar('coordsOK', 1);
        } else {
            $this->view->setVar('coordsOK', 0);
        }
        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('preferences', $preferences);
        $this->view->setVar('maxnbh', self::MAX_NEIGHBOURHOODS);
        $this->view->setVar('minCaches', self::CACHES_PER_PAGE_MIN);
        $this->view->setVar('maxCaches', self::CACHES_PER_PAGE_MAX);
        $this->view->setVar('minRadius', self::NBH_RADIUS_MIN);
        $this->view->setVar('maxRadius', self::NBH_RADIUS_MAX);
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setVar('infoMsg', $this->infoMsg);
        $mapModel = new DynamicMapModel();
        $mapModel->setZoom(6);
        $this->view->setVar('mapModel', $mapModel);
        $this->view->loadGMapApi();
        $this->view->addLocalJs(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/config.js'), true, true);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/myNeighbourhood/common.css'));
        $this->view->setTemplate('myNeighbourhood/config');
        $this->view->loadJQueryUI();
        $this->view->buildView();
    }

    /**
     * Saves new/modified MyNbh. Called by MyNbh config form
     *
     * @param number $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function save($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();
        $error = null;
        $seq = null;
        $definedNbh = count(Neighbourhood::getAdditionalNeighbourhoodsList($this->loggedUser));
        // Store MyNeighbourhood data
        if (isset($_POST['lon']) && isset($_POST['lat']) && isset($_POST['radius'])) {
            $coords = Coordinates::FromCoordsFactory($_POST['lat'], $_POST['lon']);
            $radius = (int) $_POST['radius'];
            if ($radius > self::NBH_RADIUS_MAX) {
                $radius = self::NBH_RADIUS_MAX;
            } elseif ($radius < self::NBH_RADIUS_MIN) {
                $radius = self::NBH_RADIUS_MIN;
            }
            if ($coords !== null) {
                if ($nbhSeq == 0) { // Update Home Coords and radius
                    if (! $this->loggedUser->updateUserNeighbourhood($coords, $_POST['radius'])) {
                        $error = tr('myn_save_error'); // Error during save MyNbh (should never happen, but...)
                    }
                } elseif (isset($_POST['name']) && ($nbhSeq > 0 || $definedNbh < self::MAX_NEIGHBOURHOODS)) { // Save additional neighbourhood
                    $seq = ($nbhSeq < 0) ? null : $nbhSeq;
                    $name = trim($_POST['name']);
                    $name = UserInputFilter::purifyHtmlString($name);
                    $name = strip_tags($name);
                    $name = mb_strcut($name, 0, 16);
                    if (mb_strlen($name) < 1) { // Name too short
                        $name = 'X';
                    }
                    if (! Neighbourhood::storeUserNeighbourhood($this->loggedUser, $coords, $radius, $name, $seq)) {
                        $error = tr('myn_save_error'); // Error during save additional MyNbh (should never happen, but...)
                    }
                } else { // Incorrect $_POST - without name or user exceeded max total nbh's
                    $error = tr('myn_coords_error');
                }
            } else { // Coords are not OK
                $error = tr('myn_coords_error');
            }
        } else { // User not choose coords | radius
            $error = tr('myn_coords_error');
        }
        // Store user preferences
        if ($nbhSeq == 0 && isset($_POST['caches-perpage']) && isset($_POST['style']) && ($_POST['style'] == 'full' || $_POST['style'] == 'min')) {
            $cachesPerpage = (int) $_POST['caches-perpage'];
            if ($cachesPerpage > self::CACHES_PER_PAGE_MAX) {
                $cachesPerpage = self::CACHES_PER_PAGE_MAX;
            } elseif ($cachesPerpage < self::CACHES_PER_PAGE_MIN) {
                $cachesPerpage = self::CACHES_PER_PAGE_MIN;
            }
            $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
            $preferences['style']['name'] = $_POST['style'];
            $preferences['style']['caches-count'] = $cachesPerpage;
            if (! UserPreferences::savePreferencesJson(NeighbourhoodPref::KEY, json_encode($preferences))) {
                $error = tr('myn_save_error'); // Error during storing user preferences
            }
        }
        if (is_null($error)) {
            $this->infoMsg = tr('myn_save_success');
        } else {
            $this->errorMsg = $error;
        }
        $this->config($seq == null ? 0 : $seq);
    }

    /**
     * Deletes My Nbh - called by form in config
     *
     * @param number $nbhSeq - MyNbh number (seq). 0 = default user's Nbh
     */
    public function delete($nbhSeq = 0)
    {
        $this->redirectNotLoggedUsers();
        $success = true;
        if ($nbhSeq > 0) { // User cannot delete HomeCoords!
            if (! Neighbourhood::removeUserNeighbourhood($this->loggedUser, $nbhSeq)) {
                $success = false;
            }
        } else {
            $success = false; // User try to delete Home Coords
        }
        if ($success) {
            $this->infoMsg = tr('myn_delete_success');
        } else {
            $this->errorMsg = tr('myn_delete_error');
        }
        $this->config(0);
        exit();
    }

    /**
     * Saves changed order of MyNbh sections. Called via Ajax by MyNbh main page
     */
    public function changeOrderAjax()
    {
        $this->checkUserLoggedAjax();
        $this->paramAjaxCheck('order');
        $order = [];
        parse_str($_POST['order'], $order);
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $counter = 1;
        foreach ($order['item'] as $itemOrder) {
            $preferences['items'][$itemOrder]['order'] = $counter;
            $counter += 1;
        }
        if (! UserPreferences::savePreferencesJson(NeighbourhoodPref::KEY, json_encode($preferences))) {
            $this->ajaxErrorResponse('Error saving UserPreferences');
        }
        $this->ajaxSuccessResponse();
    }

    /**
     * Saves changed size of MyNbh section. Called via Ajax by MyNbh main page
     */
    public function changeSizeAjax()
    {
        $this->checkUserLoggedAjax();
        $this->paramAjaxCheck('size');
        $this->paramAjaxCheck('item');
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $itemNr = ltrim($_POST['item'], 'item_');
        $preferences['items'][$itemNr]['fullsize'] = filter_var($_POST['size'], FILTER_VALIDATE_BOOLEAN);
        if (! UserPreferences::savePreferencesJson(NeighbourhoodPref::KEY, json_encode($preferences))) {
            $this->ajaxErrorResponse('Error saving UserPreferences');
        }
        $this->ajaxSuccessResponse();
    }

    /**
     * Saves display status of MyNbh section. Called via Ajax by MyNbh main page
     */
    public function changeDisplayAjax()
    {
        $this->checkUserLoggedAjax();
        $this->paramAjaxCheck('hide');
        $this->paramAjaxCheck('item');
        $preferences = UserPreferences::getUserPrefsByKey(NeighbourhoodPref::KEY)->getValues();
        $itemNr = ltrim($_POST['item'], 'item_');
        $preferences['items'][$itemNr]['show'] = ! filter_var($_POST['hide'], FILTER_VALIDATE_BOOLEAN);
        if (! UserPreferences::savePreferencesJson(NeighbourhoodPref::KEY, json_encode($preferences))) {
            $this->ajaxErrorResponse('Error saving UserPreferences');
        }
        $this->ajaxSuccessResponse();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    /**
     * Check if $_POST[$paramName] is set. If not - generates 400 AJAX response
     *
     * @param string $paramName
     */
    private function paramAjaxCheck($paramName)
    {
        if (! isset($_POST[$paramName])) {
            $this->ajaxErrorResponse('No parameter: ' . $paramName, 400);
            exit();
        }
    }
}