<?php

namespace Controllers\GeoPath;

use Controllers\BaseController;
use lib\Objects\GeoPath\GeoPath;
use Utils\Uri\Uri;


class GeoPathsListController extends BaseController
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

        tpl_set_tplname('geoPath/geoPathsList');

        $this->view->setVar('geoPathList', GeoPath::GetAllGeoPaths());
        $this->view->setVar('gMapKey', $GLOBALS['googlemap_key']);
        $this->view->setVar('geoPathList_css', Uri::getLinkWithModificationTime('tpl/stdstyle/geoPath/geoPathsList.css'));


        tpl_BuildTemplate();
    }

}

