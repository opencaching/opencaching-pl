<?php
use src\Utils\Uri\SimpleRouter;

?>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="/images/blue/newspaper.png" class="icon22" alt="Newspaper icon">
    <?=tr('news')?>
  </div>
  <div class="callout callout-news callout-<?=$view->news->getStatusBootstrapName()?>">
    <div class="callout-news-status">
      <a href="<?=$view->news->getNewsUrl() ?>">Link</a> |
      <a href="<?=$view->news->getNewsUrl(true) ?>">Raw Link</a> |
      <?=tr('news_lbl_datepub')?>: <strong><?=$view->news->getDatePublication(true)?></strong> |
      <?=tr('news_lbl_author')?>: <strong><?php if ($view->news->isAuthorHidden()) { echo tr('news_OCTeam'); } else { ?><a href="<?=$view->news->getAuthor()->getProfileUrl()?>" class="links"><?=$view->news->getAuthor()->getUserName()?></a><?php } ?></strong>
    </div>
    <div class="callout-news-title"><?=$view->news->getTitle()?></div>
    <div class="callout-news-content"><?=$view->news->getContent()?></div>
  </div>
  <div class="align-center">
    <a href="<?=SimpleRouter::getLink('News.NewsList')?>" class="btn btn-primary btn-md"><?=tr('news')?></a>
  </div>
</div>
