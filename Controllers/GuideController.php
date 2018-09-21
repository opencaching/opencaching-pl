<?php
namespace Controllers;

use Utils\Uri\Uri;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;
use lib\Objects\User\MultiUserQueries;
use lib\Objects\ChunkModels\DynamicMap\GuideMarkerModel;
use lib\Objects\User\User;
use Utils\Cache\OcMemCache;

class GuideController extends BaseController
{
    /** Maxiumum length of guide description passed to marker model */
    const MAX_DSCR_LEN = 200;

    public function __construct(){
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {
        $this->redirectNotLoggedUsers();

        $this->view->setTemplate('guide/guides');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/guide/guides.css'));

        $guidesList = OcMemCache::getOrCreate('currentGuides', 8*3600, function(){
            return MultiUserQueries::getCurrentGuidesList();
        });

        $this->view->setVar('guidesNumber', count($guidesList));

        $this->view->addHeaderChunk('openLayers5');
        $this->view->loadJQuery();

        $mapModel = new DynamicMapModel();
        $coords = $this->loggedUser->getHomeCoordinates();
        if (
            $coords
            && $coords->getLatitude() != null
            && $coords->getLongitude() != null
        ) {
            $mapModel->setCoords($coords);
            $mapModel->setZoom(11);
        } else {
            $mapModel->setZoom($this->ocConfig->getMainPageMapZoom());
        }

        $mapModel->addMarkersWithExtractor(GuideMarkerModel::class, $guidesList,
            function($row){
                $marker = new GuideMarkerModel();
                $marker->icon = '/images/guide_map_marker.png';
                $marker->link = User::GetUserProfileUrl($row['user_id']);
                $marker->lat = $row['latitude'];
                $marker->lon = $row['longitude'];
                $marker->userId = $row['user_id'];
                $marker->username = $row['username'];
                $text = $row['description'];
                if (mb_strlen($text) > self::MAX_DSCR_LEN) {
                    $text = mb_strcut($text, 0, self::MAX_DSCR_LEN);
                    $text .= '...';
                }
                $marker->userDesc = nl2br($text);
                $marker->recCount = $row['recomendations'];
                return $marker;
            }
        );

        $this->view->setVar('mapModel', $mapModel);


        $guideConfig = $this->ocConfig->getGuidesConfig();
        $this->view->setVar('requiredRecomCount', $guideConfig['guideGotRecommendations']);
        $this->view->setVar('requiredActivity', $guideConfig['guideActivePeriod']);

        $this->view->buildView();

    }
}
