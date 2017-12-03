<?php
namespace Controllers;

use Controllers\News\NewsListController;
use Utils\Database\OcDb;
use Utils\Uri\Uri;
use myninc;

class StartPageController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->setTemplate('startPage/startPage');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('tpl/stdstyle/startPage/startPage.css'));

        $this->view->setVar('introText',
            tr('what_do_you_find_intro_' . $this->ocConfig->getOcNode() ));

        $this->processNews();
        $this->processTotalStats();
        $this->processFeeds();
        $this->processTitleCaches();

        $this->view->buildView();

    }

    private function processGeopathOfTheDay()
    {
        $this->view->setVar('displayGeoPathOfTheDay',
            $this->ocConfig->isPowertrailsEnabled());
    }

    private function processNews()
    {
        $this->view->setVar('newsList',
            NewsListController::listNewsOnMainPage($this->isUserLogged()));
    }

    private function processTotalStats()
    {
        //TODO
        //include ($dynstylepath . "totalstats.inc.php");

        $totalStats = new \stdClass();
        $totalStats->totalHidden = 1234;
        $totalStats->hidden = 456;
        $totalStats->founds = 789;
        $totalStats->registeredUsers = 1011;

        $this->view->setVar('totalStats', $totalStats);
    }

    private function processFeeds()
    {
        $feeds = '';
        foreach ($config['feed']['enabled'] as $feed_position) {
            $feed_txt = file_get_contents($dynstylepath . "feed." . $feed_position . ".html");
            $feed_txt = mb_ereg_replace('{feed_' . $feed_position . '}', tr('feed_' . $feed_position), $feed_txt);
            $feeds .= $feed_txt;
        }
        tpl_set_var('Feeds', $feeds);
    }

    private function processTitleCaches()
    {

        // ///////////////////////////////////////////////////
        // Titled Caches
        // /////////////////////////////////////////////////

        $usrid = - 1;
        $TitledCaches = "";
        $dbc = OcDb::instance();

        if ($usr != false)
            $usrid = $usr['userid'];

            $query = "SELECT caches.cache_id, caches.name cacheName, adm1 cacheCountry, adm3 cacheRegion, caches.type cache_type,
        caches.user_id, user.username userName, cache_titled.date_alg, cache_logs.text,
        logUser.user_id logUserId, logUser.username logUserName
        FROM cache_titled
            JOIN caches ON cache_titled.cache_id = caches.cache_id
            LEFT JOIN cache_desc ON caches.cache_id = cache_desc.cache_id and language=:1
            JOIN cache_location ON caches.cache_id = cache_location.cache_id
            JOIN user ON caches.user_id = user.user_id
            JOIN cache_logs ON cache_logs.id = cache_titled.log_id
            JOIN user logUser ON logUser.user_id = cache_logs.user_id
        ORDER BY date_alg DESC
        LIMIT 1";

            $s = $dbc->multiVariableQuery($query, $lang);

            $pattern = "<img src='{cacheIcon}' class='icon16' alt='Cache' title='Cache'>
        <a href='viewcache.php?cacheid={cacheId}' class='links'>{cacheName}</a>&nbsp;" . tr('hidden_by') . "
        <a href='viewprofile.php?userid={userId}' class='links'>{userName}</a><br>

        <p class='content-title-noshade'>{country} > {region}</p>
        <div class='CacheTitledLog'>
                <img src='images/rating-star.png' alt='Star'>&nbsp;<a href='viewprofile.php?userid={logUserId}' class='links'>{logUserName}</a>:<br><br>
                {logText}
        </div>";

            while ($rec = $dbc->dbResultFetch($s)) {

                $line = $pattern;

                $line = mb_ereg_replace('{cacheIcon}', myninc::checkCacheStatusByUser($rec, $usrid), $line);
                $line = mb_ereg_replace('{dateAlg}', $rec["date_alg"], $line);
                $line = mb_ereg_replace('{cacheName}', $rec["cacheName"], $line);
                $line = mb_ereg_replace('{userId}', $rec["user_id"], $line);
                $line = mb_ereg_replace('{userName}', $rec["userName"], $line);
                $line = mb_ereg_replace('{cacheId}', $rec["cache_id"], $line);
                $line = mb_ereg_replace('{country}', $rec["cacheCountry"], $line);
                $line = mb_ereg_replace('{region}', $rec["cacheRegion"], $line);
                $line = mb_ereg_replace('{logUserId}', $rec["logUserId"], $line);
                $line = mb_ereg_replace('{logUserName}', $rec["logUserName"], $line);

                $text = mb_ereg_replace('<p>', '', $rec["text"]);
                $text = mb_ereg_replace('</p>', '<br>', $text);

                $line = mb_ereg_replace('{logText}', $text, $line);

                $TitledCaches .= $line;
            }

            $is_titled = ($dbc->rowCount($s) ? '1' : '0');
            if ($is_titled == '0')
                $TitledCaches = '';

                tpl_set_var('TitledCaches', $TitledCaches);
                tpl_set_var('is_titled', $is_titled);
    }

}

