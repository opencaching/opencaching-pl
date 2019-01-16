<?php
namespace Controllers;

use Utils\Cache\OcMemCache;
use Utils\Feed\AtomFeed;
use Utils\Feed\AtomFeedAuthor;
use Utils\Feed\AtomFeedEntry;
use Utils\Text\Formatter;
use Utils\Uri\Uri;
use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\GeoCache\MultiCacheStats;
use lib\Objects\GeoCache\MultiLogStats;
use lib\Objects\News\News;
use lib\Objects\User\User;
use lib\Objects\Neighbourhood\MyNbhSets;
use lib\Objects\Neighbourhood\Neighbourhood;
use Utils\Uri\SimpleRouter;
use Utils\I18n\I18n;

class RSSController extends BaseController
{

    const ENTRIES_PER_FEED = 20;

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    /**
     * Displays page with RSS list
     */
    public function index($errorMsgString = null)
    {
        $this->view->setTemplate('rss/main');
        if (empty($errorMsgString)) {
            $this->view->setVar('errorMsg', null);
        } else {
            $this->view->setVar('errorMsg', tr($errorMsgString));
        }
        $this->view->setVar('infoMsg', null);
        $this->view->buildView();
    }

    /**
     * Publishes Atom feed with newest log
     * /rss/newlogs.xml
     */
    public function newLogs()
    {
        $rss = new AtomFeed();
        $rss->setId(Uri::getAbsUri('/rss/newlogs.xml'));
        $rss->setTitle($this->ocConfig->getShortSiteName() . ' - ' . tr('rss_latestLogs'));

        $logs = OcMemCache::getOrCreate(
            __CLASS__ . '::newLogs',
            1 * 60 * 60,
            function() {
                return self::newLogsDataPrepare();
            });

        foreach ($logs as $log) {
            $entry = new AtomFeedEntry();
            $entry->setTitle($log['cacheName'] . ' (' . $log['cacheWp'] . ')');
            $entry->setPublished($log['datePublished']);
            $entry->setUpdated($log['dateUpdated']);
            $entry->setId($log['logUri']);
            $entry->setLink($log['logUri']);
            $entry->setContent($log['content']);

            $entry->setAuthor(new AtomFeedAuthor());
            $entry->getAuthor()->setName($log['authorName']);
            $entry->getAuthor()->setUri($log['authorUrl']);

            $rss->addEntry($entry);
            unset($entry);
        }

        $rss->publish();
        exit();
    }

    /**
     * Prepares data for newLogs() and APC store
     *
     * @return array
     */
    private static function newLogsDataPrepare()
    {
        $logs = MultiLogStats::getNewestLogs(self::ENTRIES_PER_FEED);

        $result = [];
        foreach ($logs as $log) {
            $entry = [];
            $entry['cacheName'] = $log->getGeoCache()->getCacheName();
            $entry['cacheWp'] = $log->getGeoCache()->getWaypointId();
            $entry['authorName'] = $log->getUser()->getUserName();
            $entry['authorUrl'] = Uri::getAbsUri($log->getUser()->getProfileUrl());
            $entry['datePublished'] = $log->getDateCreated();
            $entry['dateUpdated'] = $log->getLastModified();
            $entry['logUri'] = Uri::getAbsUri($log->getLogUrl());
            $entry['content'] = '<img src="' . Uri::getAbsUri($log->getLogIcon()) .'" alt="' . tr($log->getTypeTranslationKey()) . '" /> ';
            $entry['content'] .= '<strong>' . tr($log->getTypeTranslationKey()) . '</strong>';
            if ($log->getType() == GeoCacheLog::LOGTYPE_FOUNDIT
                && $log->isRecommendedByUser($log->getUser()->getUserId())) {
                    $entry['content'] .= ' <img src="' . Uri::getAbsUri('images/rating-star.png') . '" alt="' . tr('recommended') . '" />';
            }
            $entry['content'] .= ' (' . Formatter::dateTime($log->getDate()) . ')<br>';
            $entry['content'] .= $log->getText();

            $result[] = $entry;
            unset($entry);
        }
        return $result;
    }

    /**
     * Publishes Atom feed with newest caches
     * /rss/newcaches.xml
     */
    public function newCaches()
    {
        $rss = new AtomFeed();
        $rss->setId(Uri::getAbsUri('/rss/newcaches.xml'));
        $rss->setTitle($this->ocConfig->getShortSiteName() . ' - ' . tr('rss_latestCaches'));

        $caches = OcMemCache::getOrCreate(
            __CLASS__ . '::newCaches',
            1 * 60 * 60,
            function() {
                return self::newCachesDataPrepare(I18n::getCurrentLang());
            });

        foreach ($caches as $cache) {
            $entry = new AtomFeedEntry();
            $entry->setTitle($cache['cacheName'] . ' (' . $cache['cacheWp'] . ')');
            $entry->setPublished($cache['datePublished']);
            $entry->setUpdated($cache['dateUpdated']);
            $entry->setId($cache['cacheUri']);
            $entry->setLink($cache['cacheUri']);
            $entry->setContent($cache['content']);

            $entry->setAuthor(new AtomFeedAuthor());
            $entry->getAuthor()->setName($cache['authorName']);
            $entry->getAuthor()->setUri($cache['authorUrl']);

            $rss->addEntry($entry);
            unset($entry);
        }

        $rss->publish();
        exit();
    }

    /**
     * Prepares data for newCaches() and APC store
     *
     * @param string $lang
     * @return array
     */
    private static function newCachesDataPrepare($lang)
    {
        $caches = MultiCacheStats::getAllLatestCaches(self::ENTRIES_PER_FEED);

        $result = [];
        foreach ($caches as $cache) {
            $entry = [];
            $entry['cacheName'] = $cache->getCacheName();
            $entry['cacheWp'] = $cache->getGeocacheWaypointId();
            $entry['authorName'] = $cache->getOwner()->getUserName();
            $entry['authorUrl'] = Uri::getAbsUri($cache->getOwner()->getProfileUrl());
            $entry['datePublished'] = $cache->getDatePublished();
            $entry['dateUpdated'] = $cache->getLastModificationDate();
            $entry['cacheUri'] = $cache->getCacheUrl();
            $entry['content'] =  '<img src="' . Uri::getAbsUri($cache->getCacheIcon()) . '" alt="' . tr($cache->getCacheTypeTranslationKey()) .'" style="height: 24px" /> ';
            $entry['content'] .= '<img src="' . Uri::getAbsUri($cache->getTerrainIcon()) . '" alt="Terrain icon" style="height: 24px" /> ';
            $entry['content'] .= '<img src="' . Uri::getAbsUri($cache->getDifficultyIcon()) . '" alt="Difficulty icon" style="height: 24px" /> ';
            $entry['content'] .= $cache->getCacheLocationObj()->getLocationDesc(' > ') . ' | ';
            $entry['content'] .= tr($cache->getSizeTranslationKey());
            if (strpos($cache->getDescLanguagesList(), strtoupper($lang)) !== false) {
                $entry['content'] .= '<br>' . $cache->getCacheDescription($lang)->getDescToDisplay();
            }

            $result[] = $entry;
            unset($entry);
        }
        return $result;
    }

    /**
     * Publishes Atom feed with newest News
     * /rss/newnews.xml
     */
    public function newNews()
    {
        $rss = new AtomFeed();
        $rss->setId(Uri::getAbsUri('/rss/newnews.xml'));
        $rss->setTitle($this->ocConfig->getShortSiteName() . ' - ' . tr('rss_latestNews'));


        $allNews = OcMemCache::getOrCreate(
            __CLASS__ . '::newNews',
            1 * 60 * 60,
            function() {
                return self::newNewsDataPrepare(I18n::getCurrentLang());
            });

        foreach ($allNews as $news) {
            $entry = new AtomFeedEntry();
            $entry->setTitle($news['title']);
            $entry->setPublished($news['datePublished']);
            $entry->setUpdated($news['dateUpdated']);
            $entry->setId($news['newsUri']);
            $entry->setLink($news['newsUri']);
            $entry->setContent($news['content']);

            $entry->setAuthor(new AtomFeedAuthor());
            $entry->getAuthor()->setName($news['authorName']);
            $entry->getAuthor()->setUri($news['authorUrl']);

            $rss->addEntry($entry);
            unset($entry);
        }
        $rss->publish();
        exit();
    }

    /**
     * Prepares data for newNews() and APC store
     *
     * @return array
     */
    private static function newNewsDataPrepare()
    {
        $allNews = News::getAllNews(false, false, 0, self::ENTRIES_PER_FEED);

        $result = [];
        foreach ($allNews as $news) {
            $entry = [];

            if (empty($news->getTitle())) {
                $entry['title'] = tr('news');
            } else {
                $entry['title'] = $news->getTitle();
            }

            if ($news->getHideAuthor()) {
                $entry['authorName'] = tr('news_OCTeam');
                $entry['authorUrl'] = '';
            } else {
                $entry['authorName'] = $news->getAuthor()->getUserName();
                $entry['authorUrl'] = Uri::getAbsUri($news->getAuthor()->getProfileUrl());
            }

            $entry['datePublished'] = $news->getDatePublication();
            $entry['dateUpdated'] = $news->getDateLastModified();
            $entry['newsUri'] = Uri::getAbsUri($news->getNewsUrl());
            $entry['content'] = $news->getContent();

            $result[] = $entry;
            unset($entry);
        }
        return $result;
    }

    /**
     * Publishes Atom feed with newest logs written by user given as param <userid>
     * /rss/my_logs.xml?userid=<userid>
     */
    public function myLogs()
    {
        $user = $this->getUserFromParams();

        $rss = new AtomFeed();
        $rss->setId(Uri::getAbsUri('/rss/my_logs.xml?userid=' . $user->getUserId()));
        $rss->setTitle(tr('rss_latestUserLogs') . ' ' . $user->getUserName());

        $rss->setAuthor(new AtomFeedAuthor());
        $rss->getAuthor()->setName($user->getUserName());
        $rss->getAuthor()->setUri($user->getProfileUrl());

        foreach (MultiLogStats::getNewestLogsForUser($user, self::ENTRIES_PER_FEED) as $log) {
            $entry = new AtomFeedEntry();
            $entry->setTitle($log->getGeoCache()->getCacheName() . ' (' . $log->getGeoCache()->getWaypointId() . ')');
            $entry->setPublished($log->getDateCreated());
            $entry->setUpdated($log->getLastModified());
            $entry->setId(Uri::getAbsUri($log->getLogUrl()));
            $entry->setLink(Uri::getAbsUri($log->getLogUrl()));

            $content = '<img src="' . Uri::getAbsUri($log->getLogIcon()) .'" alt="' . tr($log->getTypeTranslationKey()) . '" /> ';
            $content .= '<strong>' . tr($log->getTypeTranslationKey()) . '</strong>';
            if ($log->getType() == GeoCacheLog::LOGTYPE_FOUNDIT
                && $log->isRecommendedByUser($log->getUser()->getUserId())) {
                    $content .= ' <img src="' . Uri::getAbsUri('images/rating-star.png') . '" alt="' . tr('recommended') . '" />';
                }
                $content .= ' (' . Formatter::dateTime($log->getDate()) . ')<br>';
                $content .= $log->getText();
            $entry->setContent($content);

            $entry->setAuthor(new AtomFeedAuthor());
            $entry->getAuthor()->setName($log->getUser()->getUserName());
            $entry->getAuthor()->setUri(Uri::getAbsUri($log->getUser()->getProfileUrl()));

            $rss->addEntry($entry);
            unset($entry);
            unset($content);
        }

        $rss->publish();
    }

    /**
     * Publishes Atom feed with newest logs on caches owned by user given as param <userid>
     * /rss/mycaches_logs.xml?userid=<userid>
     */
    public function myCachesLogs()
    {
        $user = $this->getUserFromParams();

        $rss = new AtomFeed();
        $rss->setId(Uri::getAbsUri('/rss/mycaches_logs.xml?userid=' . $user->getUserId()));
        $rss->setTitle(tr('rss_latestUserCaches') . ' ' . $user->getUserName());

        $rss->setAuthor(new AtomFeedAuthor());
        $rss->getAuthor()->setName($user->getUserName());
        $rss->getAuthor()->setUri($user->getProfileUrl());

        foreach (MultiLogStats::getNewestLogsForUserCaches($user, self::ENTRIES_PER_FEED) as $log) {
            $entry = new AtomFeedEntry();
            $entry->setTitle($log->getGeoCache()->getCacheName() . ' (' . $log->getGeoCache()->getWaypointId() . ')');
            $entry->setPublished($log->getDateCreated());
            $entry->setUpdated($log->getLastModified());
            $entry->setId(Uri::getAbsUri($log->getLogUrl()));
            $entry->setLink(Uri::getAbsUri($log->getLogUrl()));

            $content = '<img src="' . Uri::getAbsUri($log->getLogIcon()) .'" alt="' . tr($log->getTypeTranslationKey()) . '" /> ';
            $content .= '<strong>' . tr($log->getTypeTranslationKey()) . '</strong>';
            if ($log->getType() == GeoCacheLog::LOGTYPE_FOUNDIT
                && $log->isRecommendedByUser($log->getUser()->getUserId())) {
                    $content .= ' <img src="' . Uri::getAbsUri('images/rating-star.png') . '" alt="' . tr('recommended') . '" />';
                }
                $content .= ' (' . Formatter::dateTime($log->getDate()) . ')<br>';
                $content .= $log->getText();
                $entry->setContent($content);

                $entry->setAuthor(new AtomFeedAuthor());
                $entry->getAuthor()->setName($log->getUser()->getUserName());
                $entry->getAuthor()->setUri(Uri::getAbsUri($log->getUser()->getProfileUrl()));

                $rss->addEntry($entry);
                unset($entry);
                unset($content);
        }

        $rss->publish();
    }

    public function nbhLatestCaches($userId = null, $nbhId = null)
    {
        $user = User::fromUserIdFactory($userId);
        if (is_null($user)) {
            $this->index('message_user_not_found');
            exit();
        }

        $nbhId = intval($nbhId);


        $nbhData = Neighbourhood::getCoordsAndRadius($user, $nbhId);
        if (empty($nbhData['coords'])) {
            //TODO: Wywalić błąd!
            exit();
        }

        $nbhDataSet = new MyNbhSets($nbhData['coords'], $nbhData['radius']);

        $caches = $nbhDataSet->getLatestCaches(self::ENTRIES_PER_FEED);

        $rss = new AtomFeed();
        $rss->setId(SimpleRouter::getAbsLink('RSS', 'nbhLatestCaches', [$userId, $nbhId]));
        $rss->setTitle($this->ocConfig->getShortSiteName() . ' - ' . tr('rss_latestCaches')); //TODO !!!!!


        foreach ($caches as $cache) {
            $entry = new AtomFeedEntry();
            $entry->setTitle($cache['cacheName'] . ' (' . $cache['cacheWp'] . ')');
            $entry->setPublished($cache['datePublished']);
            $entry->setUpdated($cache['dateUpdated']);
            $entry->setId($cache['cacheUri']);
            $entry->setLink($cache['cacheUri']);
            $entry->setContent($cache['content']);

            $entry->setAuthor(new AtomFeedAuthor());
            $entry->getAuthor()->setName($cache['authorName']);
            $entry->getAuthor()->setUri($cache['authorUrl']);

            $rss->addEntry($entry);
            unset($entry);
        }

        $rss->publish();
        exit();

    }

    public function nbhLatestLogs($userId = null, $nbhId = null)
    {
        $user = User::fromUserIdFactory($userId);
        if (is_null($user)) {
            $this->index('message_user_not_found');
            exit();
        }
    }

    /**
     * Checks if there is userid param ($_GET)
     * If no - redirects to main RSS page with error message
     * If yes - returns User object
     *
     * @return User
     */
    private function getUserFromParams()
    {
        $userId = (isset($_GET['userid'])) ? $_GET['userid'] : null;
        $user = User::fromUserIdFactory($userId);
        if (is_null($user)) {
            $this->index('message_user_not_found');
            exit();
        }
        return $user;
    }

}