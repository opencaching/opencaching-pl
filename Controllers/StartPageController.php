<?php
namespace Controllers;

use Controllers\News\NewsListController;
use Utils\Uri\Uri;
use lib\Objects\Stats\TotalStats;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\MultiCacheStats;
use lib\Objects\User\User;
use Utils\Cache\OcMemCache;
use Utils\Feed\RssFeed;
use Utils\Gis\Gis;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\GeoCache\CacheTitled;
use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\CacheSet\CacheSet;
use Utils\Text\Formatter;
use lib\Objects\ChunkModels\StaticMapModel;
use lib\Objects\ChunkModels\StaticMapMarker;

class StartPageController extends BaseController
{
    private $staticMapModel = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return TRUE;
    }

    public function index()
    {
        $this->view->setTemplate('startPage/startPage');

        $this->view->addLocalCss('/tpl/stdstyle/css/lightTooltip.css');

        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/startPage/startPage.css'));

        $this->view->loadJQuery();


        $this->view->setVar('isUserLogged', $this->isUserLogged());
        if($this->isUserLogged()){
            $this->view->setVar('username', $this->loggedUser->getUserName());
        }

        $this->staticMapModel = StaticMapModel::defaultFullCountryMap();

        $this->view->setVar('introText',
            tr('startPage_intro_' . $this->ocConfig->getOcNode() ));

        $this->processNewCaches();
        $this->processNews();
        $this->processTotalStats();
        $this->processFeeds();
        $this->processTitleCaches();
        $this->processLastCacheSets();

        $this->view->setVar('staticMapModel', $this->staticMapModel);

        $this->view->buildView();

    }

    private function processNewCaches()
    {
        // map prepare
        global $main_page_map_center_lat, $main_page_map_center_lon, $main_page_map_zoom;
        global $main_page_map_width, $main_page_map_height;
        global $config;


        $map_center_lat = $main_page_map_center_lat;
        $map_center_lon = $main_page_map_center_lon;
        $map_zoom = $main_page_map_zoom;
        $map_width = $main_page_map_width;
        $map_height = $main_page_map_height;

        $newestCaches = OcMemCache::getOrCreate(
            __CLASS__.':newestCaches', 3*60*60,
            function()
                use($map_center_lat, $map_center_lon, $map_zoom, $map_width, $map_height){

                $result = new \stdClass();
                $result->markers = [];
                $result->latestCaches = [];
                $result->incomingEvents = [];

                $mapCenterCoords = Coordinates::FromCoordsFactory(
                    $map_center_lat, $map_center_lon);

                // find latest caches
                foreach (MultiCacheStats::getLatestCaches(7) as $c){

                    $loc = $c['location'];

                    $result->latestCaches[] = [
                        'icon' => GeoCache::CacheIconByType($c['type'], $c['status']),
                        'date' => Formatter::date($c['date']),
                        'link' => GeoCache::GetCacheUrlByWp($c['wp_oc']),
                        'markerId' => $c['wp_oc'],
                        'cacheName' => $c['name'],
                        'userName' => $c['username'],
                        'userUrl' => User::GetUserProfileUrl($c['user_id']),
                        'location' => $loc->getLocationDesc(' > '),
                    ];


                    $markerCoords = Coordinates::FromCoordsFactory(
                        $c['latitude'], $c['longitude']);

                    list($left, $top) = Gis::positionAtMapImg(
                        $markerCoords, $mapCenterCoords, $map_zoom,
                        $map_width, $map_height);

                    $result->markers[] = StaticMapMarker::createWithImgPosition(
                        $c['wp_oc'], $top, $left,
                        '#ff0000', $c['wp_oc'].': '.$c['name']);
                }

                // find incoming events

                foreach (MultiCacheStats::getIncomingEvents(7) as $c){

                    $loc = $c['location'];

                    $result->incomingEvents[] = [
                        'icon' => GeoCache::CacheIconByType($c['type'], $c['status']),
                        'date' => Formatter::date($c['date']),
                        'link' => GeoCache::GetCacheUrlByWp($c['wp_oc']),
                        'markerId' => $c['wp_oc'],
                        'cacheName' => $c['name'],
                        'userName' => $c['username'],
                        'userUrl' => User::GetUserProfileUrl($c['user_id']),
                        'location' => $loc->getLocationDesc(' > '),
                    ];

                    $markerCoords = Coordinates::FromCoordsFactory(
                        $c['latitude'], $c['longitude']);

                    list($left, $top) = Gis::positionAtMapImg(
                        $markerCoords, $mapCenterCoords, $map_zoom,
                        $map_width, $map_height);


                    $result->markers[] = StaticMapMarker::createWithImgPosition(
                        $c['wp_oc'], $top, $left,
                        '#00ff00', $c['wp_oc'].': '.$c['name']);
                }

                return $result;
            });

        $this->view->setVar('latestCaches', $newestCaches->latestCaches);
        $this->view->setVar('incomingEvents', $newestCaches->incomingEvents);
        $this->staticMapModel->addMarkers($newestCaches->markers);

    }

    private function processLastCacheSets()
    {
        $this->view->setVar('displayLastCacheSets',
            $this->ocConfig->isPowertrailsEnabled());

        $lastCacheSets = CacheSet::getLastCreatedSets(3);

        foreach($lastCacheSets as $cs){

            $this->staticMapModel->addMarker(
                StaticMapMarker::createWithImgPosition(
                    'cs_'.$cs->getId(), 10, 10, /* TODO */
                    '#0000ff', $cs->getName()));
        }

        $this->view->setVar('lastCacheSets', $lastCacheSets);
    }

    private function processNews()
    {
        $this->view->setVar('newsList',
            NewsListController::listNewsOnMainPage($this->isUserLogged()));
    }

    private function processTotalStats()
    {
        $this->view->setVar('totalStats', TotalStats::getBasicTotalStats());
    }

    private function processFeeds()
    {
        $feeds = OcMemCache::getOrCreate(__CLASS__.':feeds', 12*60*60,
            function(){
                global $config;//TODO

                $feeds = [];
                foreach ($config['feed']['enabled'] as $feedName) {

                    $feed = new RssFeed($config['feed'][$feedName]['url']);
                    $postsCount = min($config['feed'][$feedName]['posts'], $feed->count());
                    $feeds[$feedName] = [];

                    for ($i=0; $i<$postsCount; $i++) {
                        $post = new \stdClass();
                        $post->author = ( !empty($feed->next()->author)
                            && $config['feed'][$feedName]['showAuthor']) ? $feed->current()->author : '';

                        $post->link = $feed->current()->link;
                        $post->title = $feed->current()->title;
                        $post->date = Formatter::date($feed->current()->date);
                        $feeds[$feedName][] = $post;
                    }
                }//foreach

                return $feeds;
            });

        $this->view->setVar('feeds', $feeds);
    }

    private function processTitleCaches()
    {

        $mapModel = $this->staticMapModel;

        $titledCacheData = OcMemCache::getOrCreate(
            __CLASS__.':titledCacheData', 5*60*60,
            function() use($mapModel){

                $lastTitledCache = CacheTitled::getLastCacheTitled();
                if(is_null($lastTitledCache)){
                    return null;
                } else {

                    $lastTitledCache->prepareForSerialization();

                    /** @var GeoCache */
                    $geocache = GeoCache::fromCacheIdFactory($lastTitledCache->getCacheId());
                    if(!$geocache){
                        return null;
                    }
                    $geocache->prepareForSerialization();

                    $log = GeoCacheLog::fromLogIdFactory($lastTitledCache->getLogId());
                    if(!$log){
                        return null;
                    }
                    $log->prepareForSerialization();

                    $titleCacheDataObj = new \stdClass();
                    $titleCacheDataObj->geocache = $geocache;
                    $titleCacheDataObj->log = $log;
                    $titleCacheDataObj->cacheTitled = $lastTitledCache;

                    list($left, $top) = Gis::positionAtMapImg(
                        $geocache->getCoordinates(),
                        $mapModel->getMapCenterCoords(),
                        $mapModel->getZoom(),
                        $mapModel->getImgWidth(), $mapModel->getImgHeight());

                    $titleCacheDataObj->marker = StaticMapMarker::createWithImgPosition(
                        'titled_'.$geocache->getWaypointId(),
                        $top, $left,
                        '#000000', $geocache->getWaypointId().': '.$geocache->getCacheName());

                    return $titleCacheDataObj;
                }
        });

        if(!$titledCacheData){
            // some error occured!
            return;
        }
        $geocache = $titledCacheData->geocache;
        $log = $titledCacheData->log;
        $lastTitledCache = $titledCacheData->cacheTitled;
        $marker = $titledCacheData->marker;

        $this->staticMapModel->addMarker($marker);

        $titledCacheData = [
            'date' => Formatter::date($lastTitledCache->getTitledDate()),
            'cacheIcon' => $geocache->getCacheIcon($this->loggedUser),
            'cacheUrl' => $geocache->getCacheUrl(),
            'cacheName' => $geocache->getCacheName(),
            'cacheOwnerName' => $geocache->getOwner()->getUserName(),
            'cacheOwnerUrl' => $geocache->getOwner()->getProfileUrl(),
            'cacheLocation' => $geocache->getCacheLocationObj()->getLocationDesc(' > '),
            'logText' => $log->getText(),
            'logOwnerName' => $log->getUser()->getUserName(),
            'logOwnerUrl' => $log->getUser()->getProfileUrl(),
        ];

        $this->view->setVar('titledCacheData',$titledCacheData);

    }

}

