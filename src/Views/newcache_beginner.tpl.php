<?php
use src\Models\GeoCache\GeoCacheCommons;

?>

<div class="content2-pagetitle">
  <?=tr('nc_begin_title')?>
</div>

<div class="content2-container">
  <div class="callout callout-info">
    <p>{{nc_begin_01}} <strong><?=$view->need_find_limit ?></strong> {{nc_begin_02}}</p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_TRADITIONAL, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('cacheType_2')?>" class="icon32"> <?=tr('cacheType_2')?></p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_MULTICACHE, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('cacheType_3')?>" class="icon32"> <?=tr('cacheType_2')?></p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_QUIZ, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('cacheType_7')?>" class="icon32"> <?=tr('cacheType_7')?></p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_MOVING, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('cacheType_9')?>" class="icon32"> <?=tr('cacheType_9')?></p>
    <p><img src="<?=GeoCacheCommons::CacheIconByType(GeoCacheCommons::TYPE_OTHERTYPE, GeoCacheCommons::STATUS_READY)?>" alt="<?=tr('cacheType_1')?>" class="icon32"> <?=tr('cacheType_1')?></p>
    <div class="buffer"></div>
    <p>
      {{nc_begin_03}}
      <strong><?=$view->caches_find ?></strong>
      <progress value="<?=$view->caches_find ?>" max="<?=$view->need_find_limit ?>"></progress>
    </p>
  </div>
</div>
