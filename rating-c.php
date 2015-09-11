<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //get the article name to display
    $article = '';
    if (isset($_REQUEST['page']) &&
            (strpos($_REQUEST['page'], '.') === false) &&
            (strpos($_REQUEST['page'], '/') === false) &&
            (strpos($_REQUEST['page'], '\\') === false)
    ) {
        $article = $_REQUEST['page'];
    }

    if ($article == '') {
        //no article specified => sitemap
        $tplname = 'rating-c';
    } else if (!file_exists($stylepath . '/articles/' . $article . '.tpl.php')) {
        //article doesn't exists => sitemap
        $tplname = 'rating-c';
    } else {
        //set article inside the articles-directory
        $tplname = 'articles/' . $article;
    }
}
//make the template and send it out

tpl_BuildTemplate();
?>
