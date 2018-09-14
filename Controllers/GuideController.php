<?php
namespace Controllers;

use Utils\Uri\Uri;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;
use lib\Objects\User\MultiUserQueries;
use lib\Objects\ChunkModels\DynamicMap\GuideMarkerModel;
use lib\Objects\User\User;

class GuideController extends BaseController
{

    public function __construct(){
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {

        $this->view->setTemplate('guide/guides');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/guide/guides.css'));

        $guidesList = MultiUserQueries::getCurrentGuidesList();

        $this->view->setVar('guidesNumber', count($guidesList));

        $this->view->addHeaderChunk('openLayers5');
        $this->view->loadJQuery();

        $mapModel = new DynamicMapModel();

        $mapModel->addMarkersWithExtractor(GuideMarkerModel::class, $guidesList,
            function($row){
                $marker = new GuideMarkerModel();
                $marker->icon = '/images/actions/problem.png';
                $marker->link = User::GetUserProfileUrl($row['user_id']);
                $marker->lat = $row['latitude'];
                $marker->lon = $row['longitude'];
                $marker->userId = $row['user_id'];
                $marker->username = $row['username'];
                $marker->userDesc = $row['description'];

                return $marker;
            }
        );

        $this->view->setVar('mapModel', $mapModel);

        $this->view->buildView();

    }

}
