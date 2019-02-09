<?php

use src\Models\Stats\CacheStats;
use Utils\I18n\I18n;

//prepare the templates and include all neccessary
require_once(__DIR__.'/lib/common.inc.php');

// get the article name to display
if (isset($_REQUEST['page']) &&
    mb_ereg_match('^[A-Za-z0-9_]+$', $_REQUEST['page'])
) {
    $article = $_REQUEST['page'];
} else {
    $article = '';
}

if (!file_exists('./tpl/stdstyle/articles/' . $article . '.tpl.php')) {
    // article doesn't exists
    tpl_errorMsg('Article "'.$article.'"', tr('page_not_found'));
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
    if (file_exists(__DIR__.'/tpl/stdstyle/articles/' . $article . '.inc.php')) {
        require_once __DIR__.'/tpl/stdstyle/articles/' . $article . '.inc.php';
    }
    $tplname = 'articles/' . $article;
}

tpl_set_var('language4js', I18n::getCurrentLang());

// make the template and send it out
tpl_BuildTemplate();
