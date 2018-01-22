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
use lib\Objects\ChunkModels\StaticMap\StaticMapModel;
use Utils\Uri\SimpleRouter;
use lib\Objects\ChunkModels\StaticMap\StaticMapMarker;
use lib\Objects\CacheSet\CacheSetOwner;

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

        // extended tooltips
        $this->view->addLocalCss('/tpl/stdstyle/css/lightTooltip.css');

        // local css
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/startPage/startPage.css'));

        // local JS
        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/startPage/startPage.js'));

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
                $result->createdAt = time();
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

        //legend marker
        $this->view->setVar('newestCachesLegendMarker',
            StaticMapMarker::getCssMarkerForLegend(StaticMapMarker::COLOR_CACHE));

        // add markers
        foreach($newestCaches->latestCaches as $c){
            $this->staticMapModel->createMarker($c['markerId'], $c['coords'],
                StaticMapMarker::COLOR_CACHE,
                $c['markerId'].': '.$c['cacheName'], $c['link']);
        }

        $this->view->setVar('incomingEvents', $newestCaches->incomingEvents);

        //legend marker
        $this->view->setVar('newestEventsLegendMarker',
            StaticMapMarker::getCssMarkerForLegend(StaticMapMarker::COLOR_EVENT));

        // add markers
        foreach($newestCaches->incomingEvents as $c){
            $this->staticMapModel->createMarker($c['markerId'], $c['coords'],
                StaticMapMarker::COLOR_EVENT,
                $c['markerId'].': '.$c['cacheName'], $c['link']);
        }

        $this->view->setVar('newestCachesValidAt',
            Formatter::dateTime($newestCaches->createdAt));
    }

    private function processLastCacheSets()
    {
        $cacheSetsEnabledInConfig = $this->ocConfig->isPowertrailsEnabled();

        $this->view->setVar('displayLastCacheSets', $cacheSetsEnabledInConfig);
        if(!$cacheSetsEnabledInConfig){
            return;
        }

        $lastCacheSetsData = OcMemCache::getOrCreate(
            __CLASS__.':latestCacheSets', 3*60*60,
            function(){
                global $config;

                $lastCacheSets = CacheSet::getLastCreatedSets(
                    $config['startPage']['latestCacheSetsCount']);

                $lastCacheSets = CacheSetOwner::setOwnersToCacheSets($lastCacheSets);

                array_walk($lastCacheSets, function(&$cs){
                    /** @var CacheSet */
                    $cs->prepareForSerialization();
                });

                $result = new \stdClass();
                $result->createdAt = time();
                $result->lastCacheSets = $lastCacheSets;
                return $result;
            });

        foreach($lastCacheSets as $cs){
            $this->staticMapModel->createMarker(
                'cs_'.$cs->getId(), $cs->getCoordinates(),
                StaticMapMarker::COLOR_CACHESET, $cs->getName(), $cs->getUrl());
        }

        if(is_object($lastCacheSetsData)){
            $this->view->setVar('lastCacheSets', $lastCacheSetsData->lastCacheSets);
            $this->view->setVar('latestCacheSetsValidAt',
                Formatter::dateTime($lastCacheSetsData->createdAt));
        }else{
            if(is_array($lastCacheSetsData)){
                // TODO: old style: remove in next comit
                // it needst to be handled because old version of data in cache
                $this->view->setVar('lastCacheSets', $lastCacheSetsData);
                $this->view->setVar('latestCacheSetsValidAt', null);
            }
        }

        //legend marker
        $this->view->setVar('newestCsLegendMarker',
            StaticMapMarker::getCssMarkerForLegend(StaticMapMarker::COLOR_CACHESET));
    }

    private function processNews()
    {
        $this->view->setVar('newsList',
            NewsListController::listNewsOnMainPage($this->isUserLogged()));
    }

    private function processTotalStats()
    {
        /** @var BasicStats */
        $ts = TotalStats::getBasicTotalStats();

        // prepare total-stats array
        $totStsArr = [];
        $totStsArr[] = ['val'=>$ts->totalCaches, 'desc'=>tr('startPage_totalCaches'), 'ldesc'=>tr('startPage_totalCachesDesc')];
        $totStsArr[] = ['val'=>$ts->activeCaches, 'desc'=>tr('startPage_readyToSearch'), 'ldesc'=>tr('startPage_readyToSearchDesc')];
        $totStsArr[] = ['val'=>$ts->topRatedCaches, 'desc'=>tr('startPage_topRatedCaches'), 'ldesc'=>tr('startPage_topRatedCachesDesc')];
        $totStsArr[] = ['val'=>$ts->totalUsers, 'desc'=>tr('startPage_totalUsers'), 'ldesc'=>tr('startPage_totalUsersDesc')];
        if($this->ocConfig->isPowertrailsEnabled()){
            $totStsArr[] = ['val'=>$ts->activeCacheSets, 'desc'=>tr('startPage_activeCacheSets'), 'ldesc'=>tr('startPage_activeCacheSetsDesc')];
        }
        $totStsArr[] = ['val'=>$ts->totalSearches, 'desc'=>tr('startPage_totalSearches'), 'ldesc'=>tr('startPage_totalSearchesDesc')];
        $totStsArr[] = ['val'=>$ts->latestCaches, 'desc'=>tr('startPage_newCaches'), 'ldesc'=>tr('startPage_newCachesDesc')];
        $totStsArr[] = ['val'=>$ts->newUsers, 'desc'=>tr('startPage_newUsers'), 'ldesc'=>tr('startPage_newUsersDesc')];
        $totStsArr[] = ['val'=>$ts->latestSearches, 'desc'=>tr('startPage_newSearches'), 'ldesc'=>tr('startPage_newSearchesDesc')];
        $totStsArr[] = ['val'=>$ts->latestRecomendations, 'desc'=>tr('startPage_newoRecom'), 'ldesc'=>tr('startPage_newoRecomDesc')];

        // rotate stats tabele random number of times
        $rotator = rand(0,9);
        for($i=0; $i<$rotator; $i++){
            array_push($totStsArr, array_shift($totStsArr));
        }

        $this->view->setVar('totStsArr', $totStsArr);
        $this->view->setVar('totStsValidAt', Formatter::dateTime($ts->createdAt));

    }

    private function processTitleCaches()
    {
        $titledCacheDataObj = OcMemCache::getOrCreate(
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
                    $titleCacheDataObj->createdAt = time();

                    return $titleCacheDataObj;
                }
        });

        if(!$titledCacheDataObj){
            // there is no titledCache? - some errror occured?!
            $this->view->setVar('titledCacheData',null);
            return;
        }
        /** @var GeoCache */
        $geocache = $titledCacheDataObj->geocache;
        $log = $titledCacheDataObj->log;
        $lastTitledCache = $titledCacheDataObj->cacheTitled;

        $markerId = 'titled_'.$geocache->getWaypointId();
        $this->staticMapModel->createMarker(
            $markerId,
            $geocache->getCoordinates(), StaticMapMarker::COLOR_TITLED_CACHE,
            $geocache->getWaypointId().': '.$geocache->getCacheName(),
            $geocache->getCacheUrl());

        //legend marker
        $this->view->setVar('newestTitledLegendMarker',
            StaticMapMarker::getCssMarkerForLegend(StaticMapMarker::COLOR_TITLED_CACHE));

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
        $this->view->setVar('titledCacheValidAt',
            Formatter::dateTime($titledCacheDataObj->createdAt));

    }

    private function processFeeds()
    {
        $feedsData = $this->getFeedsData(true); // get feeds if ready in cache
        $this->view->setVar('feedsData', $feedsData);

        if(!$feedsData){
            $feedsUrl = SimpleRouter::getLink(self::class, 'getFeeds');
            $this->view->setVar('feedsUrl', $feedsUrl);
        }
    }

    /**
     * This action is called by ajax from startPage.
     * This is loaded by ajax because feeds can be downloaded
     * from remote server what can increse page load time to few seconds.
     *
     */
    public function getFeeds()
    {
        $this->view->setTemplate('startPage/feeds');
        $feedsData = $this->getFeedsData();

        if(is_object($feedsData)){
            $this->view->setVar('feedsData',$feedsData->feeds);
            $this->view->setVar('feedsDataValidAt',
                Formatter::dateTime($feedsData->createdAt));
        }else{
            // TODO: this version is only to handle old value stored in cache
            // it should be removed in next commit
            if(is_array($feedsData)){
                $this->view->setVar('feedsData', $feeds);
                $this->view->setVar('feedsDataValidAt',null);
            }
        }


        $this->view->buildOnlySelectedTpl();
    }

    private function getFeedsData($onlyIfReady=false)
    {
        $feedsKey = __CLASS__.':feeds';
        if($onlyIfReady){
            return OcMemCache::get($feedsKey);
        }else{
            return OcMemCache::getOrCreate($feedsKey, 60*60 /*1h*/,
                function(){
                    global $config;//TODO

                    $result = new \stdClass();
                    $result->feeds = [];

                    foreach ($config['feed']['enabled'] as $feedName) {

                        $feed = new RssFeed($config['feed'][$feedName]['url']);
                        $postsCount = min($config['feed'][$feedName]['posts'], $feed->count());
                        $result->feeds[$feedName] = [];

                        for ($i=0; $i<$postsCount; $i++) {
                            $post = new \stdClass();
                            $post->author = ( !empty($feed->next()->author) &&
                                $config['feed'][$feedName]['showAuthor'] ) ? $feed->current()->author : '';

                                $post->link = $feed->current()->link;
                                $post->title = $feed->current()->title;
                                $post->date = Formatter::date($feed->current()->date);
                                $result->feeds[$feedName][] = $post;
                        }
                    }//foreach

                    $result->createdAt = time();
                    return $result;
                });
        }
    }
}

