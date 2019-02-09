<?php

namespace src\Controllers\CacheSet;

use src\Controllers\BaseController;
use Utils\Uri\Uri;
use src\Models\CacheSet\CacheSet;
use src\Models\ChunkModels\PaginationModel;
use src\Models\CacheSet\CacheSetCommon;
use src\Models\ChunkModels\ListOfCaches\Column_CacheSetNameAndIcon;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\ChunkModels\DynamicMap\CacheSetMarkerModel;
use src\Models\ChunkModels\DynamicMap\DynamicMapModel;

class CacheSetsListController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return TRUE;
    }

    public function index()
    {
        // default action
        $this->showAll();
    }

    /**
     * Display list of GPs with all GPs
     */
    public function showAll()
    {

        $allowedStatuses = array(CacheSetCommon::STATUS_OPEN);

        // prepare pagination for cacheSets list
        $paginationModel = new PaginationModel(50);
        $paginationModel->setRecordsCount(
            CacheSet::GetAllCacheSetsCount($allowedStatuses) );

        list($limit, $offset) = $paginationModel->getQueryLimitAndOffset();

        $allCacheSets = CacheSet::GetAllCacheSets($allowedStatuses, $offset, $limit);

        // init model for list of watched geopaths
        $listModel = new ListOfCachesModel();
        $listModel->addColumn(
            new Column_CacheSetNameAndIcon( tr('cacheSet_name'),
                /** @var CacheSet  $row*/
                function($row){
                    return [
                        'id' => $row->getId(),
                        'type' => $row->getType(),
                        'name' => $row->getName()
                    ];
        }));
        $listModel->setPaginationModel($paginationModel);

        // load rows to display
        $listModel->addDataRows($allCacheSets);
        $this->view->setVar('listCacheModel', $listModel);


        // init map-chunk model
        $this->view->addHeaderChunk('openLayers5');

        $mapModel = new DynamicMapModel();
        $mapModel->addMarkersWithExtractor(CacheSetMarkerModel::class, $allCacheSets,
            function(CacheSet $cs){

            if(is_null($cs->getCoordinates())){
                // skip cachesets without coords
                return null;
            }
            return CacheSetMarkerModel::fromCacheSetFactory($cs);
        });

        $this->view->setVar('mapModel', $mapModel);

        $this->showList();
    }

    /**
     * Display list of GPs owned by user
     */
    public function showMyOwn()
    {

    }

    private function showList()
    {
        $this->view->setTemplate('cacheSet/cacheSetsList');
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/cacheSet/cacheSetsList.css'));

        $this->view->loadJQuery();

        tpl_BuildTemplate();
    }

}
