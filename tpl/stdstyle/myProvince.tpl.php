
<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>

<div class="content2-pagetitle">
  <img src="tpl/stdstyle/images/blue/home.png" class="icon32" alt="" title="">
  &nbsp;{{myProvince_title}}: <?=$view->provinceName?>
</div>


<div class="content2-container line-box">
    <div class="content2-container-2col-left" id="local-caches-area">
        <p class="content-title-noshade-size3">
          <img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="Cache">
          &nbsp;{{newest_caches}}
        </p>
        <?php $view->listOfCachesChunk($view->newCaches); ?>
    </div>

    <div class="content2-container-2col-left" id="local-events-area">
        <p class="content-title-noshade-size3">
          <img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="" title="Event">
          &nbsp;{{incomming_events}}
        </p>
        <?php $view->listOfCachesChunk($view->incommingEvents); ?>
    </div>

    <div class="content2-container-2col-left local-logs-area">
        <p class="content-title-noshade-size3">
          <img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="Event">
          &nbsp;{{ftf_awaiting}}
        </p>
        <?php $view->listOfCachesChunk($view->ftfs); ?>
    </div>

    <div class="content2-container-2col-left" id="local-logs-area">
        <p class="content-title-noshade-size3">
          <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="Event">
          &nbsp;{{latest_logs}}
        </p>
        <?php $view->listOfCachesChunk($view->lastLogs); ?>
    </div>

    <?php if($view->enableTitleCaches) { ?>
        <div class="content2-container-2col-left" id="local-logs-area">
            <p class="content-title-noshade-size3">
              <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="Event">
              &nbsp;{titledCaches_title}
            </p>
            <?php $view->listOfCachesChunk($view->titledCaches); ?>
        </div>
    <?php } //$view->enableTitleCaches ?>

    <div class="content2-container-2col-left local-logs-area">
        <p class="content-title-noshade-size3">
          <img src="tpl/stdstyle/images/blue/recommendation.png" class="icon32" alt="" title="Event">
          &nbsp;{{top_recommended}}
        </p>
        <?php $view->listOfCachesChunk($view->topRecos); ?>
    </div>

</div>
