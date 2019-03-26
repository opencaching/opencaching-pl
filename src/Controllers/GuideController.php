<?php
namespace src\Controllers;

use src\Utils\Uri\Uri;
use src\Models\ChunkModels\DynamicMap\DynamicMapModel;
use src\Models\User\MultiUserQueries;
use src\Models\ChunkModels\DynamicMap\GuideMarkerModel;
use src\Models\User\User;
use src\Utils\Cache\OcMemCache;
use src\Utils\Text\Formatter;
use src\Models\OcConfig\OcConfig;

class GuideController extends BaseController
{
    /** Maxiumum length of guide description passed to marker model */
    const MAX_DSCR_LEN = 100;

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
            Uri::getLinkWithModificationTime('/views/guide/guides.css'));

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
            $mapModel->setZoom(OcConfig::getStartPageMapZoom());
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
                $marker->userDesc = Formatter::truncateText(
                    strip_tags($row['description']),
                    self::MAX_DSCR_LEN
                );
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
