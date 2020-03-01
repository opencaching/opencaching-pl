<?php
namespace src\Controllers;

use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheAttribute;
use src\Models\GeoCache\Mp3Attachment;
use src\Utils\Gis\Countries;
use src\Models\GeoCache\Waypoint;
use src\Models\Pictures\OcPicture;

class CacheEditController extends BaseController
{
    public function __construct(){
        parent::__construct();
        $this->redirectNotLoggedUsers();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {}

    public function newCache()
    {

    }


    /**
     * Cache details editor
     * @param int $cacheId
     */
    public function editor ($cacheId)
    {
        $cache = GeoCache::fromCacheIdFactory($cacheId);
        if (is_null ($cache)) {
            $this->displayCommonErrorPageAndExit("Unknown cache");
        }

        if (!$cache->isEditableByUser($this->loggedUser)) {
            $this->displayCommonErrorPageAndExit("You can't edit this geocache!");
        }

        $this->view->setTemplate('cacheEdit/cacheEditor');
        $this->view->setVar('cache', $cache);

        // detect next allowed status
        $allowedNextStatuses = $cache->getAllowedNextStatuses($this->loggedUser);
        if (empty($allowedNextStatuses)) {
            $this->displayCommonErrorPageAndExit("You don't have a privilages to edit this geocache!");
        }
        $this->view->setVar('allowedNextStatuses', $allowedNextStatuses);

        $this->view->setVar('allowedTypes', $cache->getAllowedTypes());
        $this->view->setVar('allowedSizes', $cache->getAllowedSizes());

        // countries list
        $this->view->setVar('countries', Countries::getCountriesList(true));

        $this->view->setVar('selectedCountryCode', $cache->getCacheLocationObj()->getCountryCode());

        $this->view->setVar('cacheDifficulties', GeoCache::CacheDifficultyLevels());
        $this->view->setVar('terreinDifficulties', GeoCache::TerreinDifficultyLevels());

        $this->view->setVar('allAttributes', GeoCacheAttribute::getAll());

        $this->view->setVar('skipWaypointsSection', $cache->isMovable());
        $this->view->setVar('allWaypoints', Waypoint::GetWaypointsForCache ($cache, false));

        // waypoints have additional infor "stage" showed only for some types of geocaches
        $this->view->setVar('waypointsHasStages', in_array($cache->getCacheType(),
                [GeoCache::TYPE_OTHERTYPE,
                 GeoCache::TYPE_MULTICACHE,
                 GeoCache::TYPE_QUIZ]));

        $this->view->setVar('descriptions', GeoCache::getDescriptions($cache->getCacheId()));

        $this->view->setVar('pictures', OcPicture::getAllForGeocache($cache));

        $this->view->setVar('skipMp3sSection', $cache->isMp3Allowed());
        $this->view->setVar('mp3s', Mp3Attachment::getAllForGeocache($cache));

        $this->view->setVar('skipActivationSection', $cache->getStatus() != GeoCache::STATUS_NOTYETAVAILABLE);

        if ($this->loggedUser->getUserId() != $cache->getOwnerId()) {
            // only owner can see the password
            $this->view->setVar('skipPasswordSection', true);
        } else {
            $this->view->setVar('skipPasswordSection', !$cache->isPasswordAllowed());
        }

        $this->view->buildView();
    }

    /**
     * Save cache data
     * @param int $cacheId
     */
    public function save ($cacheId)
    {

        d($_POST);
    }
}
