<?php
?>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/newspaper.png" width="22" height="22" alt="">
    {{news}}
  </div>

  <?php foreach($view->newsList as $news) { ?>
  
      <div class="callout callout-news callout-<?=$news->getStatusBootstrapName()?>">
      <div class="callout-news-status">
        {{news_lbl_datepub}}: <strong><?=$news->getDatePublication(true)?></strong> |
        {{news_lbl_author}}: <strong><?php if ($news->isAuthorHidden()) { ?>{{news_OCTeam}}<?php } else { ?><a href="viewprofile.php?userid=<?=$news->getAuthor()->getUserId()?>" class="links"><?=$news->getAuthor()->getUserName()?></a><?php } ?></strong>
      </div>
      <div class="callout-news-title"><?=$news->getTitle()?></div>
      <div class="callout-news-content"><?=$news->getContent()?></div>
    </div>
  <?php } //foreach ?>

  <?php $view->callChunk('pagination', $view->paginationModel); ?>
</div>
