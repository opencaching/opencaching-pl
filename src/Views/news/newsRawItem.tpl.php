
<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="/images/blue/newspaper.png" class="icon22" alt="Newspaper icon">
    <?=$view->news->getTitle()?>
  </div>
  <div>
    <div class="callout-news-status">
      <?=tr('news_lbl_datepub')?>: <strong><?=$view->news->getDatePublication(true)?></strong> |
      <?=tr('news_lbl_author')?>: <strong><?php if ($view->news->isAuthorHidden()) { echo tr('news_OCTeam'); } else { ?><a href="<?=$view->news->getAuthor()->getProfileUrl()?>" class="links"><?=$view->news->getAuthor()->getUserName()?></a><?php } ?></strong>
    </div>
    <div><?=$view->news->getContent()?></div>
  </div>
</div>
