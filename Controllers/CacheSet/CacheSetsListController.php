<?php

namespace Controllers\CacheSet;

use Controllers\BaseController;
use Utils\Uri\Uri;
use lib\Objects\CacheSet\CacheSet;


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
        $this->showList(CacheSet::GetAllCacheSets());
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

