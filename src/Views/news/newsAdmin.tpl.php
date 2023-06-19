<?php
use src\Utils\Uri\SimpleRouter;
use src\Utils\View\View;
use src\Models\News\News;

/** @var View $view */
?>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="/images/blue/newspaper.png" class="icon22" alt="Newspaper icon">
    <?=tr('news_menu_OCTeam')?>
  </div>

  <div class="align-right">
    <a href="<?=SimpleRouter::getLink('News.NewsAdmin','createNews')?>" class="btn btn-primary btn-md">
      <?=tr('news_btn_add')?>
    </a>
    <select onchange="refreshCategory(this)" class="form-control input200">
      <option value='<?=News::CATEGORY_ANY?>' <?=$view->selectedCategory == '' ?'selected':''?>>
        <?=tr('news_allCategories')?>
      </option>
      <?php foreach($view->allCategories as $cat) { ?>
        <option value="<?=$cat?>" <?=$view->selectedCategory == $cat?'selected':''?>>
        <?=ltrim($cat, '_')?>
        </option>
      <?php } //foreach ?>
    </select>
  </div>

  <?php if(empty($view->newsList)) { ?>
    <div class="callout callout-news">
      <p><?=tr('news_noNews')?></p>
    </div>
  <?php } // if(empty($view->newsList)) ?>

  <?php foreach($view->newsList as /** @var News $news */ $news) { ?>
    <div class="callout callout-news callout-<?=$news->getStatusBootstrapName()?>">
      <div class="callout-news-status">
        <span class="news-right">
          <a href="<?=SimpleRouter::getLink('News.NewsAdmin', 'editNews', $news->getId())?>" class="btn btn-md btn-default"><?=tr('edit')?></a>
        </span>
        <a href="<?=$news->getNewsUrl() ?>">Link</a> |
        <a href="<?=$news->getNewsUrl(true) ?>">Raw Link</a> |
        <?=tr('news_lbl_status')?>: <span class="text-color-<?=$news->getStatusBootstrapName()?>"><strong><?=tr('news_status_' . $news->getStatus());?></strong></span> |
        <?=tr('news_lbl_show_on_mainp')?>: <strong><?php if ($news->getShowOnMainpage()) { echo tr('yes'); } else { echo tr('no'); } ?></strong> |
        <?=tr('news_lbl_show_notlogged')?>: <strong><?php if ($news->getShowNotLogged()) { echo tr('yes'); } else { echo tr('no'); } ?></strong><br>
        <?=tr('news_lbl_author')?>: <strong><?php if (is_null($news->getAuthor())) { echo tr('news_OCTeam'); } else { ?><a href="<?=$news->getAuthor()->getProfileUrl()?>" class="links"><?=$news->getAuthor()->getUserName()?></a><?php } ?></strong> <?php if ($news->isAuthorHidden()) { echo tr('news_lbl_author_disp');?> <strong><?=tr('news_OCTeam')?></strong><?php } ?> |
        <?=tr('news_lbl_lastmod')?>: <strong><?=$news->getDateLastModified(true)?></strong> <?php if (! is_null($news->getLastEditor())) { echo tr('news_lbl_lastmoduser'); ?>: <a href="<?=$news->getLastEditor()->getProfileUrl()?>" class="links"><?=$news->getLastEditor()->getUserName()?></a><?php }?><br>
        <?=tr('news_lbl_datepub')?>: <strong><?=$news->getDatePublication(true)?></strong> |
        <?=tr('news_lbl_dateexp')?>: <strong><?php if (is_null($news->getDateExpiration())) echo tr('news_no_limit'); else echo $news->getDateExpiration(true);?></strong> |
        <?=tr('news_lbl_datemainpexp')?>: <strong><?php if (is_null($news->getDateMainPageExpiration())) echo tr('news_no_limit'); else echo $news->getDateMainPageExpiration(true);?></strong><br>
      </div>
      <div class="callout-news-title"><?=$news->getTitle()?></div>
      <div class="callout-news-content"><?=$news->getContent()?></div>
    </div>
  <?php } //foreach ?>
  <?php $view->callChunk('pagination', $view->paginationModel); ?>

  <div class="align-center">
    <a href="<?=SimpleRouter::getLink('News.NewsAdmin','createNews')?>" class="btn btn-primary btn-md"><?=tr('news_btn_add')?></a>
  </div>

</div>

<script>
  function refreshCategory(select) {
    var selectedVal = $(select).val();
    window.location.href = "/News.NewsAdmin/showCategory/"+selectedVal;
  }
</script>