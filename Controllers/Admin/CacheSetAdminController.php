<?php
namespace Controllers\Admin;

use Controllers\BaseController;
use Utils\Uri\Uri;
use lib\Objects\CacheSet\CacheSet;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheSetNameAndIcon;
use lib\Objects\ChunkModels\ListOfCaches\ListOfCachesModel;
use lib\Objects\ChunkModels\ListOfCaches\Column_SimpleText;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;
use lib\Objects\ChunkModels\DynamicMap\CacheSetMarkerModel;

class CacheSetAdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // this controller is only for Admins
        $this->redirectNotLoggedUsers();

        /* !!!temporary disabled for tests:

        if(!$this->loggedUser->isAdmin()){
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
                '/tpl/stdstyle/admin/cacheSet/cacheSetsToArchive.css'));

        $this->view->loadJQuery();
        $this->view->loadGMapApi();

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
        $mapModel = new DynamicMapModel();
        $mapModel->addMarkers(CacheSetMarkerModel::class, $csToArchive, function($row){

            $ratioTxt = round($row['currentRatio']).'/'.$row['ratioRequired'].'%';

            $marker = new CacheSetMarkerModel();
            $marker->icon = CacheSet::GetTypeIcon($row['type']);
            $marker->lat = $row['centerLatitude'];
            $marker->lon = $row['centerLongitude'];
            $marker->link = CacheSet::getCacheSetUrlById($row['id']);
            $marker->name = $row['name']." ($ratioTxt)";
            return $marker;
        });

        $this->view->setVar('mapModel', $mapModel);

        $this->view->buildView();

    }
}