<?php
use Utils\Uri\Uri;

?>
<div class="content2-container">
  <div class="content2-pagetitle">
    <?=tr('rss_pageTitle')?>
  </div>
  <?=$view->callChunk('infoBar', null, $view->infoMsg, $view->errorMsg)?>
  <div class="buffer"></div>
  <p><img src="/tpl/stdstyle/images/misc/rss.svg" class="icon22" alt="RSS icon"> <a class="links" href="/rss/newcaches.xml"><?=tr('rss_latestCaches')?></a> - <?=Uri::getAbsUri('rss/newcaches.xml')?></p>
  <div class="buffer"></div>
  <p><img src="/tpl/stdstyle/images/misc/rss.svg" class="icon22" alt="RSS icon"> <a class="links" href="/rss/newlogs.xml"><?=tr('rss_latestLogs')?></a> - <?=Uri::getAbsUri('rss/newlogs.xml')?></p>
  <div class="buffer"></div>
  <p><img src="/tpl/stdstyle/images/misc/rss.svg" class="icon22" alt="RSS icon"> <a class="links" href="/rss/newnews.xml"><?=tr('rss_latestNews')?></a> - <?=Uri::getAbsUri('rss/newnews.xml')?></p>
</div>
