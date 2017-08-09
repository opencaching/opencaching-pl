<?php
use lib\Objects\News\News;

require_once ("./lib/common.inc.php");

const NEWS_ON_PAGE = 5;

if (isset($_GET['page'])) {
    $pagenr = $_GET['page'];
} else {
    $pagenr = 1;
}

$newsCount = News::GetAllNewsCount(isset($_SESSION['user_id']), false);
$pageMax = ceil($newsCount / NEWS_ON_PAGE);
$tpl->assign('pagemax', $pageMax);

if ($pagenr > 1) {
    $tpl->assign('prev_page', $pagenr - 1);
} else {
    $tpl->assign('prev_page', null);
}

if ($pagenr < $pageMax) {
    $tpl->assign('next_page', $pagenr + 1);
} else {
    $tpl->assign('next_page', null);
}

$offset = ($pagenr - 1) * NEWS_ON_PAGE;

$newsList = News::GetAllNews(isset($_SESSION['user_id']), false, $offset, NEWS_ON_PAGE);
$tpl->assign('newslist', $newsList);
$tpl->assign('pagenr', $pagenr);

$tpl->display('tpl/news.tpl');