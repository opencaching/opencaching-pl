<?php

namespace Controllers\CacheSet;

use Controllers\BaseController;
use lib\Objects\GeoPath\GeoPath;
use Utils\Uri\Uri;


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

        $this->showList(array());

    }

    /**
     * Display list of GPs owned by user
     */
    public function showMyOwn()
    {

    }

    private function showList(array $geoPathList)
    {
        tpl_set_tplname('cacheSet/cacheSetsList');
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/cacheSet/cacheSetsList.css'));

        $this->view->loadJQuery();
        $this->view->loadGMapApi();

        $this->view->setVar('geoPathList', GeoPath::GetAllGeoPaths());

        tpl_BuildTemplate();
    }

}

