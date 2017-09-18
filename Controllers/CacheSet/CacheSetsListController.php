<?php

namespace Controllers\CacheSet;

use Controllers\BaseController;
use Utils\Uri\Uri;
use lib\Objects\CacheSet\CacheSet;
use lib\Objects\ChunkModels\PaginationModel;
use lib\Objects\CacheSet\CacheSetCommon;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheSetNameAndIcon;
use lib\Objects\ChunkModels\ListOfCaches\ListOfCachesModel;
use lib\Objects\ChunkModels\DynamicMap\CacheSetsMapModel;
use Utils\Debug\Debug;

class CacheSetsListController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
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
        $mapModel = new CacheSetsMapModel();
        $mapModel->setDataRowExtractor(function($row){
            if(is_null($row->getCoordinates())){
                // skip cachesets without coords
                return null;
            }
            return [
                'id' => $row->getId(),
                'type' => $row->getType(),
                'name' => $row->getName(),
                'icon' => $row->getIcon(),
                'lon' => $row->getCoordinates()->getLongitude(),
                'lat' => $row->getCoordinates()->getLatitude()
            ];
        });
        $mapModel->addMarkersDataRows($allCacheSets);
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
        $this->view->loadGMapApi();

        tpl_BuildTemplate();
    }

}

