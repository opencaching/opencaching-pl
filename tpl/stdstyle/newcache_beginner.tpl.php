<?php
use lib\Objects\GeoCache\GeoCacheCommons;

?>

<div class="content2-pagetitle">
  <?=tr('nc_begin_title')?>
</div>

<div class="content2-container">
  <div class="callout callout-info">
    <p>{{nc_begin_01}} <strong><?=$view->need_find_limit ?></strong> {{nc_begin_02}}</p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_TRADITIONAL, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('traditional')?>" class="icon32"> <?=tr('traditional')?></p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_MULTICACHE, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('multicache')?>" class="icon32"> <?=tr('multicache')?></p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_QUIZ, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('quiz')?>" class="icon32"> <?=tr('quiz')?></p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_MOVING, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('moving')?>" class="icon32"> <?=tr('moving')?></p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_OTHERTYPE, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('unknown_type')?>" class="icon32"> <?=tr('unknown_type')?></p>
    <div class="buffer"></div>
    <p>
      {{nc_begin_03}}
      <strong><?=$view->caches_find ?></strong>
      <progress value="<?=$view->caches_find ?>" max="<?=$view->need_find_limit ?>"></progress>
    </p>
  </div>
</div>