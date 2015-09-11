<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //get the article name to display
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
        //no article specified => sitemap
        $tplname = 'sitemap';
    } else if (!file_exists($stylepath . '/articles/' . $article . '.tpl.php')) {
        //article doesn't exists => sitemap
        $tplname = 'sitemap';
    } else {
        //set article inside the articles-directory
        switch ($_REQUEST['page']) {
            case 'stat':
                require_once('./graphs/cachetypes-oc.php');
                tpl_set_var('oc_statistics_link', genStatPieUrl());
                break;
            default:
                break;
        }
        if (file_exists($stylepath . '/articles/' . $article . '.inc.php')) {
            require_once $stylepath . '/articles/' . $article . '.inc.php';
        }
        $tplname = 'articles/' . $article;
    }
}
if ((date('m') == 4) and ( date('d') == 1)) {
    tpl_set_var('list_of_news', tr('PrimaAprilis'));
    tpl_set_var('news', tr('PMOnly'));
    $tplname = 'news';
}

tpl_set_var('language4js', $lang);

//make the template and send it out
tpl_BuildTemplate();
?>
