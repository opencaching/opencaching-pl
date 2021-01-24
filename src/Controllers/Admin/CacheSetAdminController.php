<?php
namespace src\Controllers\Admin;

use src\Controllers\BaseController;
use src\Utils\Uri\Uri;
use src\Models\CacheSet\CacheSet;
use src\Models\ChunkModels\ListOfCaches\Column_CacheSetNameAndIcon;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\ChunkModels\ListOfCaches\Column_SimpleText;
use src\Models\ChunkModels\DynamicMap\DynamicMapModel;
use src\Models\ChunkModels\DynamicMap\CacheSetMarkerModel;
use src\Models\GeoCache\MultiCacheStats;
use src\Models\CacheSet\MultiGeopathsStats;
use src\Models\GeoCache\GeoCache;
use src\Models\PowerTrail\PowerTrail;
use src\Controllers\GeoPathController;
use src\Utils\Debug\Debug;

class CacheSetAdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // this controller is only for Admins
        $this->redirectNotLoggedUsers();

        /* !!!temporary disabled for tests:

        if(!$this->loggedUser->hasOcTeamRole()){
            $this->view->redirect('/');
            exit;
        }
        */

    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return true;
    }

    public function index()
    {

    }

    /**
     * Display list of cacheSets(geopaths) which should be archived because
     * the number of active caches is lower than number requested for completion.
     */
    public function cacheSetsToArchive()
    {
        $this->view->setTemplate('admin/cacheSet/cacheSetsToArchive');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/views/admin/cacheSet/cacheSetsToArchive.css'));

        $this->view->loadJQuery();

        $csToArchive = CacheSet::getCacheSetsToArchive();

        if( empty($csToArchive)){
            $this->view->setVar('noCsToArchive', true);
            $this->view->buildView();
            exit;
        } else {
            $this->view->setVar('noCsToArchive', false);
        }

        // prepare model for list of watched caches
        $listModel = new ListOfCachesModel();
        $listModel->addColumn(
            new Column_CacheSetNameAndIcon( tr('admCs_cacheSet'),
                function($row){
                    return [
                        'id' => $row['id'],
                        'type' => $row['type'],
                        'name' => $row['name']
                    ];
                }));
        $listModel->addColumn(
            new Column_SimpleText( tr('admCs_currentRatio'), function($row){
                return $row['activeCaches'] . ' ( '. round($row['currentRatio']).'% ) ';
            }));

        $listModel->addColumn(
            new Column_SimpleText( tr('admCs_requiredRatio'), function($row){
                // find number of required caches
                $requiredCachesNum = ceil($row['cacheCount']*$row['ratioRequired']/100);
                return $requiredCachesNum . ' ( '. round($row['ratioRequired']).'% )';
            }));

        $listModel->addDataRows($csToArchive);
        $this->view->setVar('listOfCssToArchiveModel', $listModel);



        // init map-chunk model
        $this->view->addHeaderChunk('openLayers5');

        $mapModel = new DynamicMapModel();
        $mapModel->addMarkersWithExtractor(CacheSetMarkerModel::class, $csToArchive, function($row){

            $ratioTxt = round($row['currentRatio']).'/'.$row['ratioRequired'].'%';

            $marker = new CacheSetMarkerModel();

            $marker->lon = $row['centerLongitude'];
            $marker->lat = $row['centerLatitude'];
            $marker->icon = CacheSet::GetTypeIcon($row['type']);

            $marker->link = CacheSet::getCacheSetUrlById($row['id']);
            $marker->name = $row['name']." ($ratioTxt)";
            return $marker;
        });

        $this->view->setVar('mapModel', $mapModel);

        $this->view->buildView();

    }

    public function showDuplicatesInGeopaths()
    {
        $this->redirectNotLoggedUsers();

        $this->view->loadJQuery();

        $cacheIds = MultiGeopathsStats::getDuplicatedCachesList();
        $caches = MultiCacheStats::getGeocachesById($cacheIds);

        usort($caches, function($c1, $c2) {
            return strcmp($c1->getOwner()->getUserName(), $c2->getOwner()->getUserName());
        });

        $this->view->setTemplate('cacheSet/duplicatedCachesList');
        $this->view->setVar('caches', $caches);

        $pts=[];
        foreach ($cacheIds as $cacheid) {
            $ptArr = PowerTrail::CheckForPowerTrailByCache($cacheid, TRUE);
            $pts[$cacheid] = [];
            foreach ($ptArr as $ptRow) {
                $pts[$cacheid][] = new PowerTrail(array('dbRow' => $ptRow));
            }
        }
        $this->view->setVar('pts', $pts);


        $this->view->buildView();
    }

    public function removeDuplicatedCachesAjax($gpId, $cacheId) {

        $this->checkUserLoggedAjax();

        if (!is_numeric($gpId) || !is_numeric($cacheId)) {
            $this->ajaxErrorResponse("Incorrect params");
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);
        if (!$cache) {
            $this->ajaxErrorResponse("No such geocache: $cacheId");
        }

        if (!$this->loggedUser->hasOcTeamRole() &&
            !$cache->getOwnerId() != $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse("User is not allowed to remove this geocache from goepath");
        }

        // check if this cache is on the list of duplicates
        $cacheIds = MultiGeopathsStats::getDuplicatedCachesList();
        Debug::dumpToLog($cacheIds);
        Debug::dumpToLog($cacheId);

        if (!in_array($cacheId, $cacheIds)) {
            $this->ajaxErrorResponse("This cache is not a duplicate");
        }

        $gp = CacheSet::fromCacheSetIdFactory($gpId);
        if (!$gp) {
            $this->ajaxErrorResponse("No such GP");
        }

        $gp->removeCache($cache);
        $this->ajaxSuccessResponse("Cache removed");
    }
}
