<?php

use lib\Objects\Stats\CacheStats;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

// get the article name to display
$article = '';

if (isset($_REQUEST['region'])) {
    tpl_set_var('region', $_REQUEST['region']);
    $region = $_REQUEST['region'];
}

if (isset($_REQUEST['page']) &&
   (mb_strpos($_REQUEST['page'], '.') === false) &&
   (mb_strpos($_REQUEST['page'], '/') === false) &&
   (mb_strpos($_REQUEST['page'], '\\') === false)
) {
    $article = $_REQUEST['page'];
}

if ($article == '') {
    // no article specified => sitemap
    $tplname = 'contact';
} else if (! file_exists($stylepath . '/articles/' . $article . '.tpl.php')) {
    // article doesn't exists => sitemap
    $tplname = 'contact';
} else {
    // set article inside the articles-directory
    switch ($_REQUEST['page']) {
        case 'stat':
            tpl_set_var('cachetype_chart_data', CacheStats::getChartDataCacheTypes());
            tpl_set_var('cachesfound_chart_data', CacheStats::getChartDataCachesFound());
            break;
        case 's102':
            $view->loadJQueryUI();
            break;
        default:
            break;
    }
    if (file_exists($stylepath . '/articles/' . $article . '.inc.php')) {
        require_once $stylepath . '/articles/' . $article . '.inc.php';
    }
    $tplname = 'articles/' . $article;
}

tpl_set_var('language4js', $lang);

// make the template and send it out
tpl_BuildTemplate();
