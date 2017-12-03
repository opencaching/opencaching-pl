<div class="content2-pagetitle"><?=tr('startPage_title')?></div>

<div class="content2-container">
    <div class="intro"><?=$view->introText?></div>

    <!-- NEWS -->
    <div class="intro"><?=$view->introText?></div>

    <?php if(!empty($view->newsList)){ ?>
    <div class="news">
        <p class="content-title-noshade-size3">
          <img src="tpl/stdstyle/images/blue/newspaper.png" width="32" alt="">
          <?=tr('news')?>
        </p>

        <?php foreach($view->newsList as $news) { ?>
          <div class="news-item">
            <div class="news-statusline">
              <img src="/tpl/stdstyle/images/free_icons/newspaper.png" width="16" height="16" alt="">
              <?=$news->getDatePublication(true)?>
                 <span class="newsTitle">
                    <?=$news->getTitle()?>
                    <?php if($news->isAuthorHidden()) { ?>
                      <?=tr('news_OCTeam')?>
                    <?php } else { // if-$news->isAuthorHidden() ?>
                      <a href="viewprofile.php?userid=<?=$news->getAuthor()->getUserId()?>" class="links">
                        <?=$news->getAuthor()->getUserName()?>
                      </a>
                    <?php } // if-$news->isAuthorHidden() ?>
                  </span>
            </div>
            <?=$news->getContent()?>
          </div>
        <?php } //foreach-newsList ?>

    </div>
    <?php } //if-!empty($view->newsList) ?>
    <!-- /NEWS -->

    <div>
      <p class="main-totalstats">
        <?=tr('startPage_stsAllCaches')?>:<span class="content-title-noshade"><?=$view->totalStats->totalCaches?></span>
        <?=tr('startPage_stsActiveCaches')?>: <span class="content-title-noshade"><?=$view->totalStats->activeCaches?></span>
      | <?=tr('startPage_stsFounds')?>: <span class="content-title-noshade"><?=$view->totalStats->founds?></span>
      | <?=tr('startPage_stsUsers')?>: <span class="content-title-noshade"><?=$view->totalStats->activeUsers?></span>
      </p>
    </div>

    <!-- newest caches -->
    <div>
      <p class="content-title-noshade-size3">
        <img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Cache" title="Cache">
        {{newest_caches}}
      </p>

      <?php
        global $dynstylepath;
        $tmpTxt = file_get_contents($dynstylepath . "start_newcaches.inc.php");
        $tmpTxt = str_replace('hidden_by', tr('hidden_by'), $tmpTxt);
        echo $tmpTxt;
        unset($tmpTxt);
      ?>
    </div>

    <div style="position: relative">
      <?php
        global $dynstylepath;
        include ($dynstylepath . "main_cachemap.inc.php");
      ?>
    </div>

    <!-- /newest caches -->

    <!-- incomming events -->
    <div>
        <p class="content-title-noshade-size3">
          <img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="Event" title="Event">
          &nbsp;{{incomming_events}}
        </p>
        <?php
            global $dynstylepath;
            $tmpTxt = file_get_contents($dynstylepath . "nextevents.inc.php");
            if ($tmpTxt == '') {
                $tmpTxt = tr('list_of_events_is_empty');
            }
            $tmpTxt = str_replace('hidden_by', tr('org1'), $tmpTxt);
            echo $tmpTxt;
            unset($tmpTxt);
        ?>
    </div>
    <!-- /incomming events -->

    <!-- titled caches -->
    <div class="content2-container-2col-left" id="cacheTitled" style="display: {ptDisplay};">
      <?php
        global $is_titled, $titled_cache_period_prefix;

        if ($is_titled == '1'){
            $ntitled_cache = $titled_cache_period_prefix.'_titled_cache';
            $tmpTxt = '<p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/TitledCache.png" class="icon32" alt="Titled Cache" title="Titled Cache">&nbsp;'.tr($ntitled_cache).'</p>';
            $tmpTxt .= '<div class="cache-titled-content">';
            echo $tmpTxt;
        }
      ?>

      {TitledCaches}

      <?php
        global $is_titled;

        if ($is_titled == '1'){
            $tmpTxt = '<p class="show-more"><a href="cache_titled.php" class="links">' . tr("show_more_titled_caches") . '...</a></p>';
            $tmpTxt .= '</div><br>';
            echo $tmpTxt;
        }
      ?>
    </div>
    <!-- /titled caches -->

    <!-- pathOfTheDay -->
    <?php if($view->displayGeoPathOfTheDay){ ?>
        <div style="width: 100%">
          <p class="content-title-noshade-size3">
            <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="GeoPath" title="GeoPath">
            &nbsp;{{pt137}}
          </p>
          <?php
            if (file_exists($dynstylepath . 'ptPromo.inc-' . $lang . '.php')){
                include ($dynstylepath . 'ptPromo.inc-' . $lang . '.php');
            }
          ?>
        </div>
    <?php } // if-displayGeoPathOfTheDay) ?>
    <!-- /pathOfTheDay -->

    <!-- feeds -->
    {Feeds}
    <!-- /feeds -->
</div>
<!-- /CONTENT -->


<script type="text/javascript">

    //image swapping function:
    function Lite(nn) {
        document.getElementById('smallmark' + nn).style.visibility = 'hidden';
        document.getElementById('bigmark' + nn).style.visibility = 'visible';
    }

    function Unlite(nn) {
        document.getElementById('bigmark' + nn).style.visibility = 'hidden';
        document.getElementById('smallmark' + nn).style.visibility = 'visible';
    }

</script>
