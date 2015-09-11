<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //get the news
    $tplname = 'news';
    $newscontent = '';
    require($stylepath . '/news.inc.php');

    $rsNewsTopics = sql('SELECT `name`, `id` FROM `news_topics` ORDER BY `id` ASC');
    while ($rNewsTopics = sql_fetch_array($rsNewsTopics)) {
//          $newscontent .= mb_ereg_replace('{topic}', htmlspecialchars($rNewsTopics['name'], ENT_COMPAT, 'UTF-8'), $tpl_newstopic_header) . "\n";

        $rsNews = sql("SELECT `date_posted`, `content` FROM `news` WHERE `topic`='&1' AND `display`=1 ORDER BY `date_posted` DESC LIMIT 0, 20", $rNewsTopics['id']);
        while ($rNews = sql_fetch_array($rsNews)) {
            $thisnewscontent = $tpl_newstopic_without_topic;
            $thisnewscontent = mb_ereg_replace('{date}', date('d-m-Y', strtotime($rNews['date_posted'])), $thisnewscontent);
            $thisnewscontent = mb_ereg_replace('{message}', $rNews['content'], $thisnewscontent);
            $newscontent .= $thisnewscontent . "\n";
        }
        mysql_free_result($rsNews);
    }
    mysql_free_result($rsNewsTopics);

    //$newscontent .= "</table>";
    tpl_set_var('list_of_news', $newscontent);
}

//make the template and send it out
tpl_BuildTemplate();
?>
