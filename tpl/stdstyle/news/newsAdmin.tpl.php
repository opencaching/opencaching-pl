<?php
?>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/newspaper.png" width="22" height="22" alt="">
    {{news_menu_OCTeam}}
  </div>

  <?php foreach($view->newsList as $news) { ?>
    <div class="callout callout-news callout-<?=$news->getStatusBootstrapName()?>">
      <div class="callout-news-status">
        <span class="news-right"><a href="admin_news.php?action=edit&id=<?=$news->getId()?>" class="btn btn-md">{{edit}}</a></span>
        {{news_lbl_status}}: <span class="text-color-<?=$news->getStatusBootstrapName()?>"><strong><?php echo tr('news_status_' . $news->getStatus());?></strong></span> |
        {{news_lbl_show_on_mainp}}: <strong><?php if ($news->getShowOnMainpage()) { ?>{{yes}}<?php } else { ?>{{no}}<?php } ?></strong> |
        {{news_lbl_show_notlogged}}: <strong><?php if ($news->getShowNotLogged()) {?>{{yes}}<?php } else { ?>{{no}}<?php } ?></strong><br>
        {{news_lbl_author}}: <strong><?php if (is_null($news->getAuthor())) {?>{{news_OCTeam}}<?php } else { ?><a href="viewprofile.php?userid=<?=$news->getAuthor()->getUserId()?>" class="links"><?=$news->getAuthor()->getUserName()?></a><?php } ?></strong> <?php if ($news->isAuthorHidden()) { ?>{{news_lbl_author_disp}} <strong>{{news_OCTeam}}</strong><?php } ?> |
        {{news_lbl_lastmod}}: <strong><?=$news->getDateLastModified(true)?></strong><?php if (! is_null($news->getLastEditor())) {?> {{news_lbl_lastmoduser}}: <a href="viewprofile.php?userid=<?=$news->getLastEditor()->getUserId()?>" class="links"><?=$news->getLastEditor()->getUserName()?></a><?php }?><br>
        {{news_lbl_datepub}}: <strong><?=$news->getDatePublication(true)?></strong> |
        {{news_lbl_dateexp}}: <strong><?php if (is_null($news->getDateExpiration())) echo tr('news_no_limit'); else echo $news->getDateExpiration(true);?></strong> |
        {{news_lbl_datemainpexp}}: <strong><?php if (is_null($news->getDateMainPageExpiration())) echo tr('news_no_limit'); else echo $news->getDateMainPageExpiration(true);?></strong><br>
      </div>
      <div class="callout-news-title"><?=$news->getTitle()?></div>
      <div class="callout-news-content"><?=$news->getContent()?></div>
    </div>
  <?php } //foreach ?>
  <?php $view->callChunk('pagination', $view->paginationModel); ?>
  <div class="align-center"><a href="admin_news.php?action=create" class="btn btn-primary">{{news_btn_add}}</a></div>
</div>
