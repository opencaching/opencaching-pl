<?php
use Controllers\News\NewsListController;
use Utils\Database\OcDb;

global $usr;
global $dynstylepath;

// prepare the templates and include all neccessary
require_once ('./lib/common.inc.php');

// set the template to process
$tplname = 'start';

// here is the right place to set up template replacements
// example:
// tpl_set_var('foo', 'myfooreplacement');
// will replace {foo} in the templates

// news
$newsList = NewsListController::listNewsOnMainPage($usr != false);
require ($stylepath . '/news.inc.php');
$newscontent = '';
if (! empty($newsList)) {
    $newscontent = $tpl_news_start;
    foreach ($newsList as $news) {
        $newsTxt = $tpl_news_body;
        $newsTxt = mb_ereg_replace('{date}', $news->getDatePublication(true), $newsTxt);
        $newsTxt = mb_ereg_replace('{title}', $news->getTitle(), $newsTxt);
        $newsTxt = mb_ereg_replace('{content}', $news->getContent(), $newsTxt);
        if ($news->isAuthorHidden()) {
            $newsTxt = mb_ereg_replace('{author}', tr('news_OCTeam'), $newsTxt);
        } else {
            $newsTxt = mb_ereg_replace('{author}', '<a href="viewprofile.php?userid=' . $news->getAuthor()->getUserId() . '" class="links">' . $news->getAuthor()->getUserName() . '</a>', $newsTxt);
        }
        $newscontent .= $newsTxt;
    }
    $newscontent .= $tpl_news_end;
}
tpl_set_var('display_news', $newscontent);

include ($dynstylepath . "totalstats.inc.php");

// diffrent oc server handling: display proper info depend on server running the code
tpl_set_var('what_do_you_find_intro', tr('what_do_you_find_intro_' . $config['ocNode']));

if ($powerTrailModuleSwitchOn)
    tpl_set_var('ptDisplay', 'block');
else
    tpl_set_var('ptDisplay', 'none');

// Feeds
$feeds = '';
foreach ($config['feed']['enabled'] as $feed_position) {
    $feed_txt = file_get_contents($dynstylepath . "feed." . $feed_position . ".html");
    $feed_txt = mb_ereg_replace('{feed_' . $feed_position . '}', tr('feed_' . $feed_position), $feed_txt);
    $feeds .= $feed_txt;
}
tpl_set_var('Feeds', $feeds);

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

// make the template and send it out
tpl_BuildTemplate(false);
