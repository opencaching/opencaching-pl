<?php

namespace Controllers;


use Utils\Uri\Uri;

class CacheMapController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // map is only for logged users
        $this->redirectNotLoggedUsers();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {
        $this->fullScreeenMap();
    }

    public function fullScreeen($type=null)
    {
        $this->view->setTemplate('cacheMap/fullScreenMap');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/cacheMap/cacheMap.css'));

        switch($type){
            case 'leafLet':
                $this->view->addHeaderChunk('leafLet');
                break;
            case 'openLayers':
                $this->view->addHeaderChunk('openLayers');
                break;
            default:
                d("What?!",$type);
                exit;
        }

        $this->view->setVar('mapType', $type);

        //$this->view->buildInMiniTpl();
        $this->view->buildOnlySelectedTpl();
    }



    private function loadLeafletHeaders()
    {

    }


}

