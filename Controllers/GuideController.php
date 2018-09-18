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
            Uri::getLinkWithModificationTime('/tpl/stdstyle/guide/guides.css'));

        $guidesList = OcMemCache::getOrCreate('currentGuides', 8*3600, function(){
            return MultiUserQueries::getCurrentGuidesList();
        });

        $this->view->setVar('guidesNumber', count($guidesList));

        $this->view->addHeaderChunk('openLayers5');
        $this->view->loadJQuery();

        $mapModel = new DynamicMapModel();
        if ($this->loggedUser->getHomeCoordinates()) {
            $mapModel->setCoords($this->loggedUser->getHomeCoordinates());
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
                $marker->userDesc = $this->getTruncatedDescription(
                    $row['description']
                );
                $marker->recCount = $row['recomendations'];
                return $marker;
            }
        );

        $this->view->setVar('mapModel', $mapModel);

        $this->view->buildView();

    }

    /**
     * Truncates description to be at least MAX_DSCR_LEN, ending it with
     * ellipsis '(...)' if description is longer than MAX_DSCR_LEN.
     *
     * @param string $description Description to truncate
     *
     * @return string truncated description
     */
    private function getTruncatedDescription($description)
    {
        $result = "";
        if (mb_strlen($description) > self::MAX_DSCR_LEN) {
            $result = mb_substr($description, 0, self::MAX_DSCR_LEN - 5)
                . "(...)";
        } else {
            $result = mb_substr($description, 0, self::MAX_DSCR_LEN);
        }
        return $result;
    }
}
