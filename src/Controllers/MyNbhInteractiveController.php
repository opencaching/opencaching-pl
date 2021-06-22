<?php

namespace src\Controllers;

use src\Models\ChunkModels\InteractiveMap\CacheMarkerModel;
use src\Models\ChunkModels\InteractiveMap\InteractiveMapModel;
use src\Models\ChunkModels\InteractiveMap\LogMarkerModel;
use src\Models\ChunkModels\PaginationModel;
use src\Models\Coordinates\Coordinates;
use src\Models\Neighbourhood\MyNbhSets;
use src\Models\Neighbourhood\Neighbourhood;
use src\Models\User\UserPreferences\NeighbourhoodPref;
use src\Models\User\UserPreferences\UserPreferences;
use src\Utils\Text\UserInputFilter;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;

class MyNbhInteractiveController extends BaseController
{
    // an URL path to public resources
    public const PUBLIC_SRC_PATH = '/views/myNbhInteractive/';

    // a path to templates
    public const TEMPLATES_PATH = 'myNbhInteractive/';

    // If true, only OC Team Members, Adv Users and Sys Admins has access here
    public const VALIDATION_MODE = true;

    // Minimum MyNeighbourhood radius (in km)
    public const NBH_RADIUS_MIN = 1;

    // Maximum MyNeighbourhood radius (in km)
    public const NBH_RADIUS_MAX = 150;

    // Minimum caches/log per page
    public const CACHES_PER_PAGE_MIN = 2;

    // Minimum caches/log per page
    public const CACHES_PER_PAGE_MAX = 10;

    // Maximum additional Neighbourhoods user can have
    public const MAX_NEIGHBOURHOODS = 5;

    // Caches/Logs per page (on details pages)
    public const ITEMS_PER_DETAIL_PAGE = 25;

    /** @var string */
    private $infoMsg = null;

    /** @var string */
    private $errorMsg = null;

    public function __construct()
    {
        parent::__construct();
    }

    private function accessControl()
    {
        if (
            ! $this->isUserLogged()
            || self::VALIDATION_MODE && ! (
                $this->loggedUser->hasOcTeamRole()
                || $this->loggedUser->hasAdvUserRole()
                || $this->loggedUser->hasSysAdminRole()
            )
        ) {
            $this->view->redirect(
                Uri::setOrReplaceParamValue('target', Uri::getCurrentUri(), '/')
            );

            exit();
        }
    }

    /**
     * Displays main MyNeighbourhood page
     *
     * @param int $nbhSeq - MyNbh number (seq). 0 = default user's Nbh
     */
    public function index(int $nbhSeq = 0)
    {
        $this->accessControl();

        $neighbourhoodsList
             = Neighbourhood::getNeighbourhoodsList($this->loggedUser);

        if (empty($neighbourhoodsList)) {
            // User doesn't have any MyNeighbourhoods set, so redirect to config
            $this->view->redirect(
                SimpleRouter::getLink(self::class, 'config')
            );

            exit();
        }
        $selectedNbh = (int) $nbhSeq;

        if (! array_key_exists($selectedNbh, $neighbourhoodsList)) {
            // Selected MyNeighbourhood not found
            if ($selectedNbh == 0) {
                // User has no Home Coords -> redirect to config
                $this->view->redirect(
                    SimpleRouter::getLink(self::class, 'config')
                );
            } else {
                // Redirect to default MyNeighbourhood
                $this->view->redirect(
                    SimpleRouter::getLink(self::class, 'index', 0)
                );
            }

            exit();
        }
        $preferences = UserPreferences::getUserPrefsByKey(
            NeighbourhoodPref::KEY
        )->getValues();

        $nbhItemSet = new MyNbhSets(
            $neighbourhoodsList[$selectedNbh]->getCoords(),
            $neighbourhoodsList[$selectedNbh]->getRadius()
        );

        $this->view->addHeaderChunk('openLayers5');

        $mapModel = new InteractiveMapModel();
        $mapModel->setMarkersFamily($preferences['family']);

        $sectionsToSetVars = [
            Neighbourhood::ITEM_LATESTCACHES => 'latestCaches',
            Neighbourhood::ITEM_UPCOMINGEVENTS => 'upcomingEvents',
            Neighbourhood::ITEM_FTFCACHES => 'FTFCaches',
            Neighbourhood::ITEM_LATESTLOGS => 'latestLogs',
            Neighbourhood::ITEM_RECOMMENDEDCACHES => 'topRatedCaches',
            Neighbourhood::ITEM_TITLEDCACHES => 'latestTitled',
        ];

        foreach ($sectionsToSetVars as $sectionId => $varName) {
            $setInterface = $nbhItemSet->createSet($sectionId);
            $sectionResults = $setInterface->getResults(
                $preferences['style']['caches-count'],
                0
            );
            $this->view->setVar($varName, $sectionResults);
            $sectionConfig = $preferences['items'][$sectionId] ?? [];

            if ($sectionId == Neighbourhood::ITEM_LATESTLOGS) {
                $mapModel->addMarkersWithExtractor(
                    LogMarkerModel::class,
                    $sectionResults,
                    function ($row) use ($sectionId) {
                        $marker = LogMarkerModel::fromGeoCacheLogFactory(
                            $row,
                            $this->loggedUser
                        );
                        $marker->section = $sectionId;

                        return $marker;
                    }
                );
            } else {
                $mapModel->addMarkersWithExtractor(
                    CacheMarkerModel::class,
                    $sectionResults,
                    function ($row) use ($sectionId) {
                        $marker = CacheMarkerModel::fromGeocacheFactory(
                            $row,
                            $this->loggedUser
                        );
                        $marker->section = $sectionId;

                        return $marker;
                    }
                );
            }
            $mapModel->setSectionProperties($sectionId, [
                'visible' => $sectionConfig['show'] ?? false,
                'order' => $sectionConfig['order'] ?? -1,
            ]);
        }

        $mapModel->setSectionsKeys(Neighbourhood::SECTIONS);

        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('preferences', $preferences);
        $this->view->setVar('user', $this->loggedUser);
        $this->view->setVar('mapModel', $mapModel);

        $this->setPublicResourcesAndTemplate(
            $preferences,
            'myNeighbourhood',
            true,
            ['myNeighbourhood.js'],
            true
        );
        $this->view->setVar('controller', self::class);
        $this->view->setVar('templatesPath', self::TEMPLATES_PATH);
        $this->view->setVar('publicSrcPath', self::PUBLIC_SRC_PATH);
        $this->view->buildView();
    }

    /**
     * Displays latest caches detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function latestCaches(int $nbhSeq = 0)
    {
        $this->viewDetails($nbhSeq, Neighbourhood::ITEM_LATESTCACHES);
    }

    /**
     * Displays most recommended caches detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function mostRecommended(int $nbhSeq = 0)
    {
        $this->viewDetails($nbhSeq, Neighbourhood::ITEM_RECOMMENDEDCACHES);
    }

    /**
     * Displays FTF caches detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function ftfCaches(int $nbhSeq = 0)
    {
        $this->viewDetails($nbhSeq, Neighbourhood::ITEM_FTFCACHES);
    }

    /**
     * Displays titled caches detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function titledCaches(int $nbhSeq = 0)
    {
        $this->viewDetails($nbhSeq, Neighbourhood::ITEM_TITLEDCACHES);
    }

    /**
     * Displays upcomming events detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function upcommingEvents(int $nbhSeq = 0)
    {
        $this->viewDetails($nbhSeq, Neighbourhood::ITEM_UPCOMINGEVENTS);
    }

    /**
     * Displays latest logs detailed page for Nbh selected as $nbhSeq
     *
     * @param int $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function latestLogs(int $nbhSeq = 0)
    {
        $this->viewDetails(
            $nbhSeq,
            Neighbourhood::ITEM_LATESTLOGS,
            'logs'
        );
    }

    /**
     * Displays MyNeighbour config
     *
     * @param int $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function config(int $nbhSeq = 0)
    {
        $this->accessControl();

        $selectedNbh = (int) $nbhSeq;

        $preferences = UserPreferences::getUserPrefsByKey(
            NeighbourhoodPref::KEY
        )->getValues();

        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList(
            $this->loggedUser
        );

        if (
            $selectedNbh != -1
            && ! array_key_exists($selectedNbh, $neighbourhoodsList)
        ) {
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
        $this->view->setVar(
            'markersFamilies',
            InteractiveMapModel::MARKERS_FAMILIES
        );
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setVar('infoMsg', $this->infoMsg);

        $this->view->addHeaderChunk('openLayers5');

        $mapModel = new InteractiveMapModel();
        $mapModel->setZoom(6);
        $this->view->setVar('mapModel', $mapModel);
        $this->setPublicResourcesAndTemplate(
            $preferences,
            'config',
            false,
            ['config.js', 'config_draw.js'],
            true
        );
        $this->view->buildView();
    }

    /**
     * Saves new/modified MyNbh. Called by MyNbh config form
     *
     * @param int $nbhSeq - number of MyNbh (seq). 0 = default user's Nbh
     */
    public function save(int $nbhSeq = 0)
    {
        $this->accessControl();

        $error = null;
        $seq = null;
        $definedNbh = count(
            Neighbourhood::getAdditionalNeighbourhoodsList($this->loggedUser)
        );
        // Store MyNeighbourhood data
        if (isset($_POST['lon'], $_POST['lat'], $_POST['radius'])) {
            $coords = Coordinates::FromCoordsFactory(
                $_POST['lat'],
                $_POST['lon']
            );
            $radius = (int) $_POST['radius'];

            if ($radius > self::NBH_RADIUS_MAX) {
                $radius = self::NBH_RADIUS_MAX;
            } elseif ($radius < self::NBH_RADIUS_MIN) {
                $radius = self::NBH_RADIUS_MIN;
            }

            if ($coords !== null) {
                if ($nbhSeq == 0) {
                    // Update Home Coords and radius
                    if (
                        ! $this->loggedUser->updateUserNeighbourhood(
                            $coords,
                            $_POST['radius']
                        )
                    ) {
                        // Error during save MyNbh (should never happen, but...)
                        $error = tr('myn_save_error');
                    }
                } elseif (
                    isset($_POST['name'])
                    && ($nbhSeq > 0 || $definedNbh < self::MAX_NEIGHBOURHOODS)
                ) {
                    // Save additional neighbourhood
                    $seq = ($nbhSeq < 0) ? null : $nbhSeq;
                    $name = trim($_POST['name']);
                    $name = UserInputFilter::purifyHtmlString($name);
                    $name = strip_tags($name);
                    $name = mb_strcut($name, 0, 16);

                    if (mb_strlen($name) < 1) {
                        // Name too short
                        $name = 'X';
                    }

                    if (
                        ! Neighbourhood::storeUserNeighbourhood(
                            $this->loggedUser,
                            $coords,
                            $radius,
                            $name,
                            $seq
                        )
                    ) {
                        // Error during save additional MyNbh
                        // (should never happen, but...)
                        $error = tr('myn_save_error');
                    }
                } else {
                    // Incorrect $_POST - without name or user exceeded
                    // max total nbh's
                    $error = tr('myn_coords_error');
                }
            } else { // Coords are not OK
                $error = tr('myn_coords_error');
            }
        } else { // User not choose coords | radius
            $error = tr('myn_coords_error');
        }
        // Store user preferences
        if (
            $nbhSeq == 0
            && isset($_POST['caches-perpage'], $_POST['style'])
            && ($_POST['style'] == 'full' || $_POST['style'] == 'min')
            && isset($_POST['family'])
            && in_array($_POST['family'], InteractiveMapModel::MARKERS_FAMILIES)
        ) {
            $cachesPerpage = (int) $_POST['caches-perpage'];

            if ($cachesPerpage > self::CACHES_PER_PAGE_MAX) {
                $cachesPerpage = self::CACHES_PER_PAGE_MAX;
            } elseif ($cachesPerpage < self::CACHES_PER_PAGE_MIN) {
                $cachesPerpage = self::CACHES_PER_PAGE_MIN;
            }
            $preferences = UserPreferences::getUserPrefsByKey(
                NeighbourhoodPref::KEY
            )->getValues();
            $preferences['style']['name'] = $_POST['style'];
            $preferences['style']['caches-count'] = $cachesPerpage;
            $preferences['family'] = $_POST['family'];

            if (
                ! UserPreferences::savePreferencesJson(
                    NeighbourhoodPref::KEY,
                    json_encode($preferences)
                )
            ) {
                // Error during storing user preferences
                $error = tr('myn_save_error');
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
     * @param int $nbhSeq - MyNbh number (seq). 0 = default user's Nbh
     */
    public function delete(int $nbhSeq = 0)
    {
        $this->accessControl();

        $success = true;

        if ($nbhSeq > 0) { // User cannot delete HomeCoords!
            if (
                ! Neighbourhood::removeUserNeighbourhood(
                    $this->loggedUser,
                    $nbhSeq
                )
            ) {
                $success = false;
            }
        } else {
            // User try to delete Home Coords
            $success = false;
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
        $preferences = UserPreferences::getUserPrefsByKey(
            NeighbourhoodPref::KEY
        )->getValues();
        $counter = 1;

        foreach ($order['item'] as $itemOrder) {
            $preferences['items'][$itemOrder]['order'] = $counter;
            $counter++;
        }

        if (
            ! UserPreferences::savePreferencesJson(
                NeighbourhoodPref::KEY,
                json_encode($preferences)
            )
        ) {
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
        $preferences = UserPreferences::getUserPrefsByKey(
            NeighbourhoodPref::KEY
        )->getValues();
        $itemNr = ltrim($_POST['item'], 'item_');
        $preferences['items'][$itemNr]['fullsize'] = filter_var(
            $_POST['size'],
            FILTER_VALIDATE_BOOLEAN
        );

        if (
            ! UserPreferences::savePreferencesJson(
                NeighbourhoodPref::KEY,
                json_encode($preferences)
            )
        ) {
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
        $preferences = UserPreferences::getUserPrefsByKey(
            NeighbourhoodPref::KEY
        )->getValues();
        $itemNr = ltrim($_POST['item'], 'item_');
        $preferences['items'][$itemNr]['show'] = ! filter_var(
            $_POST['hide'],
            FILTER_VALIDATE_BOOLEAN
        );

        if (
            ! UserPreferences::savePreferencesJson(
                NeighbourhoodPref::KEY,
                json_encode($preferences)
            )
        ) {
            $this->ajaxErrorResponse('Error saving UserPreferences');
        }
        $this->ajaxSuccessResponse();
    }

    /**
     * Abstract function implementation,
     * (definition has to be compliant with parent class)
     */
    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    private function setPublicResourcesAndTemplate(
        array $preferences,
        string $template,
        bool $addStyled = true,
        array $javascripts = [],
        bool $loadJQueryUI = false
    ) {
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                self::PUBLIC_SRC_PATH . 'common.css'
            )
        );

        if ($addStyled) {
            $this->view->addLocalCss(
                Uri::getLinkWithModificationTime(
                    self::PUBLIC_SRC_PATH . 'myNeighbourhood-'
                    . $preferences['style']['name'] . '.css'
                )
            );
        }
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/css/lightTooltip.css')
        );

        if (! empty($javascripts)) {
            if (! is_array($javascripts)) {
                $javascripts = [$javascripts];
            }

            foreach ($javascripts as $js) {
                $this->view->addLocalJs(
                    Uri::getLinkWithModificationTime(
                        self::PUBLIC_SRC_PATH . $js
                    ),
                    true,
                    true
                );
            }
        }

        if ($loadJQueryUI) {
            $this->view->loadJQueryUI();
        }
        $this->view->setVar('controller', self::class);
        $this->view->setTemplate(self::TEMPLATES_PATH . $template);
    }

    private function getSectionRequestCommons(int $nbhSeq = 0): array
    {
        $this->redirectNotLoggedUsers();
        $selectedNbh = $nbhSeq;
        $preferences = UserPreferences::getUserPrefsByKey(
            NeighbourhoodPref::KEY
        )->getValues();
        $neighbourhoodsList = Neighbourhood::getNeighbourhoodsList(
            $this->loggedUser
        );

        if (! array_key_exists($selectedNbh, $neighbourhoodsList)) {
            // Selected MyNeighbourhood not found
            $this->view->redirect(
                SimpleRouter::getLink(self::class, 'index')
            );

            exit();
        }
        $coords = $neighbourhoodsList[$selectedNbh]->getCoords();
        $nbhItemSet = new MyNbhSets(
            $coords,
            $neighbourhoodsList[$selectedNbh]->getRadius()
        );
        $paginationModel = new PaginationModel(self::ITEMS_PER_DETAIL_PAGE);

        return [
            $selectedNbh, $preferences, $neighbourhoodsList,
            $coords, $nbhItemSet, $paginationModel,
        ];
    }

    private function setSectionCommonVars(
        array $neighbourhoodsList,
        PaginationModel $paginationModel,
        int $selectedNbh,
        Coordinates $coords
    ) {
        $this->view->setVar('neighbourhoodsList', $neighbourhoodsList);
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('selectedNbh', $selectedNbh);
        $this->view->setVar('user', $this->loggedUser);
        $this->view->setVar('coords', $coords);
    }

    /**
     * Common method for detailed section view with a separate page.
     *
     * @param int $nbhSeq - MyNbh number (seq). 0 = default user's Nbh
     * @param int $sectionId - Number (id) of section to display
     * @param array $viewVars - Additional view variables to set (key => value)
     * @param string $itemSetVarName - Name of view var to store item set results
     * @param string $template - Name of template to build view from
     */
    private function viewDetails(
        int $nbhSeq,
        int $sectionId,
        string $itemSetVarName = 'caches'
    ) {
        $this->accessControl();

        [
            $selectedNbh, $preferences, $neighbourhoodsList,
            $coords, $nbhItemSet, $paginationModel
        ] = $this->getSectionRequestCommons($nbhSeq);

        $this->view->setVar('sectionId', $sectionId);

        $setInterface = $nbhItemSet->createSet($sectionId);
        $paginationModel->setRecordsCount(
            $setInterface->getCount()
        );
        [$limit, $offset] = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar(
            $itemSetVarName,
            $setInterface->getResults($limit, $offset)
        );
        $this->setSectionCommonVars(
            $neighbourhoodsList,
            $paginationModel,
            $selectedNbh,
            $coords
        );
        $this->setPublicResourcesAndTemplate($preferences, 'details');

        if (isset(Neighbourhood::SECTIONS[$sectionId])) {
            $this->view->setVar(
                'sectionTranslationKey',
                Neighbourhood::SECTIONS[$sectionId]
            );
        }

        $this->view->buildView();
    }

    /**
     * Check if $_POST[$paramName] is set. If not - generates 400 AJAX response
     *
     * @param string $paramName a name of parameter to check
     */
    private function paramAjaxCheck(string $paramName)
    {
        if (! isset($_POST[$paramName])) {
            $this->ajaxErrorResponse('No parameter: ' . $paramName, 400);

            exit();
        }
    }
}
