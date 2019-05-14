<?php
  use src\Utils\Uri\SimpleRouter;
  use src\Controllers\CacheLogController;
?>
<div class="content2-container">

  <div class="content2-pagetitle">
    <?=tr('lastLogList_pageTitle')?>
    <a href="/rss/newlogs.xml">
      <img src="/images/misc/rss.svg" class="icon16" alt="latest logs RSS" alt="latest logs RSS">
    </a>
  </div>

  <div class="align-right">
    <a class="btn btn-default btn-sm"
       href="<?=SimpleRouter::getLink(CacheLogController::class, 'lastLogsMap')?>">
       <?=tr('lastLogMap_pageName')?>
    </a>
  </div>

  <!-- listOfLogs-chunk start -->
  <?php $v->callChunk('listOfCaches/listOfCaches', $v->listOfLogsModel);?>
  <!-- listOfLogs-chunk end -->


</div>


