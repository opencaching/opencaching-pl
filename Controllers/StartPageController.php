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
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\GeoCache\CacheTitled;
use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\CacheSet\CacheSet;
use Utils\Text\Formatter;
use lib\Objects\ChunkModels\StaticMapModel;
use Utils\Uri\SimpleRouter;

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
        $this->processTitleCaches();
        $this->processLastCacheSets();
        $this->processFeeds();

        $this->view->setVar('staticMapModel', $this->staticMapModel);

        $this->view->buildView();

    }

    private function processNewCaches()
    {
        $newestCaches = OcMemCache::getOrCreate(
            __CLASS__.':newestCaches', 3*60*60,
            function(){

                $result = new \stdClass();
                $result->latestCaches = [];
                $result->incomingEvents = [];

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
                        'coords' => Coordinates::FromCoordsFactory(
                            $c['latitude'], $c['longitude']),
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
                        'coords' => Coordinates::FromCoordsFactory(
                            $c['latitude'], $c['longitude']),
                    ];
                }

                return $result;
            });

        $this->view->setVar('latestCaches', $newestCaches->latestCaches);
        // add markers
        foreach($newestCaches->latestCaches as $c){
            $this->staticMapModel->createMarker($c['markerId'], $c['coords'],
                '#ff0000', $c['markerId'].': '.$c['cacheName'], $c['link']);
        }

        $this->view->setVar('incomingEvents', $newestCaches->incomingEvents);
        // add markers
        foreach($newestCaches->incomingEvents as $c){
            $this->staticMapModel->createMarker($c['markerId'], $c['coords'],
                '#00ff00', $c['markerId'].': '.$c['cacheName'], $c['link']);
        }

    }

    private function processLastCacheSets()
    {
        $this->view->setVar('displayLastCacheSets',
            $this->ocConfig->isPowertrailsEnabled());

        $lastCacheSets = CacheSet::getLastCreatedSets(3);
        foreach($lastCacheSets as $cs){
            $this->staticMapModel->createMarker(
                'cs_'.$cs->getId(), $cs->getCoordinates(),
                '#0000ff', $cs->getName(), $cs->getUrl());
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

    private function processTitleCaches()
    {
        $titledCacheData = OcMemCache::getOrCreate(
            __CLASS__.':titledCacheData', 5*60*60,
            function(){

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

                    return $titleCacheDataObj;
                }
        });

        if(!$titledCacheData){
            // there is no titledCache? - some errror occured?!
            $this->view->setVar('titledCacheData',null);
            return;
        }
        /** @var GeoCache */
        $geocache = $titledCacheData->geocache;
        $log = $titledCacheData->log;
        $lastTitledCache = $titledCacheData->cacheTitled;

        $markerId = 'titled_'.$geocache->getWaypointId();
        $this->staticMapModel->createMarker(
            $markerId,
            $geocache->getCoordinates(), '#fff',
            $geocache->getWaypointId().': '.$geocache->getCacheName(),
            $geocache->getCacheUrl());

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
            'markerId' => $markerId,
        ];

        $this->view->setVar('titledCacheData',$titledCacheData);

    }

    private function processFeeds()
    {
        $feedsUrl = SimpleRouter::getLink(self::class, 'getFeeds');
        $this->view->setVar('feedsUrl', $feedsUrl);
    }

    /**
     * This action is called by ajax from startPage
     */
    public function getFeeds()
    {
        $this->view->setTemplate('startPage/feeds');

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
        $this->view->buildOnlySelectedTpl();
    }
}

