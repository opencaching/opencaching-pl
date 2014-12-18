<?php

/* * *************************************************************************
  ./articles.php
  -------------------
  begin                : Mon June 14 2004
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Display documents/articles with a minimum of code and no preprocessing

  used template(s): articles/*, sitemap
  parameter(s):     page        specifies the document which should be
  displayed

 * ************************************************************************** */

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
