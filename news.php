<?php

use Utils\Database\XDb;
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //get the news
    $tplname = 'news';
    $newscontent = '';
    require($stylepath . '/news.inc.php');

    $rsNewsTopics = XDb::xSql('SELECT `name`, `id` FROM `news_topics` ORDER BY `id` ASC');
    while ($rNewsTopics = XDb::xFetchArray($rsNewsTopics)) {

        $rsNews = XDb::xSql(
            "SELECT `date_posted`, `content` FROM `news`
            WHERE `topic`= ? AND `display`=1
            ORDER BY `date_posted` DESC LIMIT 0, 20",
            $rNewsTopics['id']);

        while ($rNews = XDb::xFetchArray($rsNews)) {
            $thisnewscontent = $tpl_newstopic_without_topic;
            $thisnewscontent = mb_ereg_replace('{date}', date('d-m-Y', strtotime($rNews['date_posted'])), $thisnewscontent);
            $thisnewscontent = mb_ereg_replace('{message}', $rNews['content'], $thisnewscontent);
            $newscontent .= $thisnewscontent . "\n";
        }
        XDb::xFreeResults($rsNews);
    }
    XDb::xFreeResults($rsNewsTopics);

    //$newscontent .= "</table>";
    tpl_set_var('list_of_news', $newscontent);
}

//make the template and send it out
tpl_BuildTemplate();

