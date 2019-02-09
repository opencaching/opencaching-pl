<?php
namespace src\Controllers;

use Utils\Text\UserInputFilter;
use src\Models\CacheSet\CacheSet;

class GeoPathController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {
        $this->searchByName(); // Temporary. To be removed in the future
    }

    /**
     * Search GeoPaths by name. Used by search engine in topline
     */
    public function searchByName()
    {
        if (isset($_REQUEST['name'])) {
            $searchStr = UserInputFilter::purifyHtmlString($_REQUEST['name']);
            $searchStr = strip_tags($searchStr);
        } else {
            $searchStr = null;
        }
        $this->view->setVar('geoPaths', CacheSet::getCacheSetsByName($searchStr));
        $this->view->setVar('searchStr', $searchStr);
        $this->view->setTemplate('geoPath/searchByName');
        $this->view->buildView();
    }

}
