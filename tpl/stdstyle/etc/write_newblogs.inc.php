<?php
/* This file to be run with CRON. Generate last blog and forum entry list on main page */

if (!isset($rootpath)) {
    $rootpath = __DIR__ . '/../../../';
}

require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/feed.php');

foreach ($config['feed']['enabled'] as $feed_position) {
    $feed = new Feed($config['feed'][$feed_position]['url']);
    $html = '<div class="content2-container-2col-left feedArea">';
    $html .= '<p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="Feed" title="Feed">&nbsp;{feed_' . $feed_position . '}</p>';
    $html .= '<ul class="feedList">';
    for ($i = 0; $i < $config['feed'][$feed_position]['posts'] && $i < $feed->count(); $i++) {
        $author = (!empty($feed->next()->author) && $config['feed'][$feed_position]['showAuthor']) ? ' (' . $feed->current()->author. ')' : '';
        $html .= '<li>' .  date($dateFormat, $feed->current()->date) . ' <a class="links" href="' . $feed->current()->link. '" title="' . $feed->current()->title . '">' . $feed->current()->title . '</a>' . $author . '</li>';
    }
    $html .= "</ul>";
    $html .= '</div>';
    $output_file = fopen($dynstylepath . "feed." . $feed_position . ".html", 'w');
    fwrite($output_file, $html);
    fclose($output_file);
    unset($feed);
}