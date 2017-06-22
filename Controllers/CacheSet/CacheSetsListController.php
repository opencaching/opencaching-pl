<?php

namespace Controllers\CacheSet;

use Controllers\BaseController;
use Utils\Uri\Uri;
use lib\Objects\CacheSet\CacheSet;
use lib\Objects\ChunkModels\PaginationModel;
use lib\Objects\CacheSet\CacheSetCommon;


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

        $paginationModel = new PaginationModel(50);
        $paginationModel->setRecordsCount(
            CacheSet::GetAllCacheSetsCount($allowedStatuses));

        list($limit, $offset) = $paginationModel->getQueryLimitAndOffset();

        $this->view->setVar('paginationModel', $paginationModel);
        $this->showList(CacheSet::GetAllCacheSets($allowedStatuses, $offset, $limit));
    }

    /**
     * Display list of GPs owned by user
     */
    public function showMyOwn()
    {

    }

    private function showList(array $cacheSetList)
    {
        tpl_set_tplname('cacheSet/cacheSetsList');
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/cacheSet/cacheSetsList.css'));

        $this->view->loadJQuery();
        $this->view->loadGMapApi();


        $this->view->setVar('cacheSetList', $cacheSetList);

        tpl_BuildTemplate();
    }

}

