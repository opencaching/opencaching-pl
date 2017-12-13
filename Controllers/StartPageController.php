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

class StartPageController extends BaseController
{
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

        $this->view->setVar('introText',
            tr('startPage_intro_' . $this->ocConfig->getOcNode() ));

        $this->processNewCaches();
        $this->processNews();
        $this->processTotalStats();
        $this->processFeeds();
        $this->processTitleCaches();
        $this->processLastCacheSets();

        $this->view->buildView();

    }

    private function processNewCaches()
    {
        // map prepare
        global $main_page_map_center_lat, $main_page_map_center_lon, $main_page_map_zoom;
        global $main_page_map_width, $main_page_map_height;
        global $config;

        $map_type = $config['maps']['main_page_map']['source'] ;

        $map_center_lat = $main_page_map_center_lat;
        $map_center_lon = $main_page_map_center_lon;
        $map_zoom = $main_page_map_zoom;
        $map_width = $main_page_map_width;
        $map_height = $main_page_map_height;

        $staticMapUrl = sprintf("/lib/staticmap.php?center=%F,%F&amp;zoom=%d&amp;size=%dx%d&amp;maptype=%s",
            $map_center_lat, $map_center_lon, $map_zoom, $map_width, $map_height, $map_type);
        $this->view->setVar('staticMapUrl', $staticMapUrl);


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

                    $result->markers[] = [
                        "id" => $c['wp_oc'],
                        "toolTip" => $c['wp_oc'].': '.$c['name'],
                        "left" => ($left-7),
                        "top" => ($top-21),
                        "img" => "/images/markers/mark-small-orange.png",
                    ];
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

                    $result->markers[] = [
                        "id" => $c['wp_oc'],
                        "toolTip" => $c['wp_oc'].': '.$c['name'],
                        "left" => ($left-7),
                        "top" => ($top-21),
                        "img" => "/images/markers/mark-small-blue.png",
                    ];
                }

                return $result;
            });

        $this->view->setVar('latestCaches', $newestCaches->latestCaches);
        $this->view->setVar('incomingEvents', $newestCaches->incomingEvents);
        $this->view->setVar('mapMarkers', $newestCaches->markers);
    }

    private function processLastCacheSets()
    {
        $this->view->setVar('displayLastCacheSets',
            $this->ocConfig->isPowertrailsEnabled());

        $lastCacheSets = CacheSet::getLastCreatedSets(3);
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

        $titledCacheData = OcMemCache::getOrCreate(
            __CLASS__.':titledCacheData', 5*60*60,
            function(){

                $lastTitledCache = CacheTitled::getLastCacheTitled();
                if(is_null($lastTitledCache)){
                    return null;
                } else {
                    $geocache = GeoCache::fromCacheIdFactory($lastTitledCache->getCacheId());
                    $log = GeoCacheLog::fromLogIdFactory($lastTitledCache->getLogId());

                    $geocache->getCacheIcon($this->loggedUser);

                    return [
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
                }
        });

        $this->view->setVar('titledCacheData',$titledCacheData);

    }

}

