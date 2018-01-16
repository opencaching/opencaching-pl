<!--
<div class="content2-pagetitle">
  <?php if($view->isUserLogged) { ?>
    <?=tr('startPage_welcome')?>&nbsp;<?=$view->username?>
  <?php } else { //if-isUserLogged ?>
    <?=tr('startPage_title')?>
  <?php } //if-isUserLogged ?>
</div>
-->

<div class="content2-container">

    <?php if(!$view->isUserLogged) { ?>
      <!-- intro -->
      <div id="intro">
        <?=$view->introText?>
      </div>
      <!-- /intro -->
    <?php } //if-isUserLogged ?>


    <?php if($view->isUserLogged && !empty($view->newsList)){ ?>
    <!-- NEWS -->

    <div id="newsDiv">
        <p class="content-title-noshade-size3">
          <?=tr('news')?>
        </p>

        <?php foreach($view->newsList as $news) { ?>
          <div class="newsItem">
            <div class="newsStatusLine">
              <img src="/tpl/stdstyle/images/free_icons/newspaper.png" alt="">
              <?=$news->getDatePublication(true)?>
                 <span class="newsTitle">
                    <?=$news->getTitle()?>
                    <?php if($news->isAuthorHidden()) { ?>
                      <?=tr('news_OCTeam')?>
                    <?php } else { // if-$news->isAuthorHidden() ?>
                      <a href="<?=$news->getAuthor()->getProfileUrl()?>" class="links">
                        <?=$news->getAuthor()->getUserName()?>
                      </a>
                    <?php } // if-$news->isAuthorHidden() ?>
                  </span>
            </div>
            <?=$news->getContent()?>
          </div>
        <?php } //foreach-newsList ?>
    </div>
    <!-- /news -->
    <?php } //if-!empty($view->newsList) ?>

    <?php if(!$view->isUserLogged) { ?>
    <!-- total Stats -->
    <p class="content-title-noshade-size3"
       title="<?=tr('startPage_validAt')?>: <?=$view->totStsValidAt?>">
      <?=tr('startPage_wholeStatsTitle')?>
    </p>
    <div id="totalStatsDiv">

        <div id="arrowLeft" class="counterArrow" onclick="countersLeft()"></div>
        <div id="totalStatsCounters">
        <?php foreach($view->totStsArr as $key=>$sts) { ?>

            <div class="counterWidget <?=($key>4)?'counterRightHidden':''?>" title="<?=$sts['ldesc']?>">
              <div class="counterInner">
                <div class="counterTitle"><?=$sts['desc']?></div>
                <div class="counterNumber"><?=$sts['val']?></div>
              </div>
            </div>

        <?php } //foreach-totStsArr ?>
        </div>
        <div id="arrowRight" class="counterArrow" onclick="countersRight()"></div>

    </div>
    <script type="text/javascript">
      function countersLeft(){
        // hide left element
        $("#totalStatsDiv .counterWidget:not(.counterLeftHidden):first").toggleClass('counterLeftHidden');

        if( $("#totalStatsDiv .counterRightHidden:first").length == 0 ){
          // move counter from left to right
          el = $("#totalStatsDiv .counterLeftHidden:first");
          el.appendTo($("#totalStatsCounters"));
          el.toggleClass('counterLeftHidden');
          el.toggleClass('counterRightHidden');
        }
        $("#totalStatsDiv .counterRightHidden:first").toggleClass('counterRightHidden');
      }

      function countersRight(){
        // hide left element
        $("#totalStatsDiv .counterWidget:not(.counterRightHidden):last").toggleClass('counterRightHidden');

        if( $("#totalStatsDiv .counterLeftHidden:last").length == 0 ){
          // move counter from right to left
          el = $("#totalStatsDiv .counterRightHidden:last");
          el.prependTo($("#totalStatsCounters"));
          el.toggleClass('counterLeftHidden');
          el.toggleClass('counterRightHidden');
        }
        $("#totalStatsDiv .counterLeftHidden:last").toggleClass('counterLeftHidden');
      }
    </script>
    <!-- /total Stats -->
    <?php } //if-isUserLogged ?>


    <div id="map">
      <?php $view->callChunk('staticMap/staticMap', $view->staticMapModel); ?>
    </div>


    <!-- newest caches -->
    <div id="newCachesList">
      <div>
        <p class="content-title-noshade-size3"
           title="<?=tr('startPage_validAt')?>: <?=$view->newestCachesValidAt?>">
           <?=tr('startPage_latestCachesList')?>
        </p>
        <?php $view->callChunk('staticMap/staticMapMarker', $view->newestCachesLegendMarker); ?>
      </div>

      <ul class="latestCachesList">
        <?php foreach($view->latestCaches as $c){ ?>
          <li>
            <div>
              <a class="links highlite" href="<?=$c['link']?>"
                 onmouseover="highliteStaticMapMarker('<?=$c['markerId']?>')"
                 onmouseout="highliteStaticMapMarker('<?=$c['markerId']?>')">

                <img src="<?=$c['icon']?>" class="icon16" alt="CacheIcon" title="">
                <?=$c['cacheName']?>

              </a>
              <?=tr('hidden_by')?>
              <a class="links" href="<?=$c['userUrl']?>"><?=$c['userName']?></a>
            </div>
            <div class="cacheLocationBox">
              (<?=$c['date']?>)
              <span class="content-title-noshade"><?=$c['location']?></span>
            </div>
          </li>
        <?php } //foreach ?>

          <li class="showMoreLink">
            <a href="/newcaches.php" class="btn btn-sm">
              <?=tr('startPage_showMore')?>
            </a>
          </li>
       </ul>
    </div>
    <!-- /newest caches -->

    <!-- incomming events -->
    <div id="nearestEventsList">
      <p class="content-title-noshade-size3"
         title="<?=tr('startPage_validAt')?>: <?=$view->newestCachesValidAt?>">
        <?=tr('incomming_events')?>
        <?php $view->callChunk('staticMap/staticMapMarker', $view->newestEventsLegendMarker); ?>
      </p>

      <ul class="latestCachesList">
        <?php foreach($view->incomingEvents as $c){ ?>
          <li>
            <div>
                <a class="links highlite" href="<?=$c['link']?>"
                   onmouseover="highliteStaticMapMarker('<?=$c['markerId']?>')"
                   onmouseout="highliteStaticMapMarker('<?=$c['markerId']?>')">

                  <img src="<?=$c['icon']?>" class="icon16" alt="CacheIcon" title="">
                  <?=$c['cacheName']?>

                </a>
                <?=tr('hidden_by')?>
                <a class="links" href="<?=$c['userUrl']?>"><?=$c['userName']?></a>
            </div>
            <div class="cacheLocationBox">
              (<?=$c['date']?>)
              <span class="content-title-noshade"><?=$c['location']?></span>
            </div>
          </li>
        <?php } //foreach ?>

          <li class="showMoreLink">
            <a href="/newevents.php" class="btn btn-sm">
              <?=tr('startPage_showMore')?>
            </a>
          </li>
       </ul>
    </div>
    <!-- /incomming events -->

    <!-- latest-cacheSets -->
    <?php if($view->displayLastCacheSets){ ?>
        <div id="newestCacheSets">
          <p class="content-title-noshade-size3">
            <?=tr('startPage_latestCacheSets')?>
            <?php $view->callChunk('staticMap/staticMapMarker', $view->newestCsLegendMarker); ?>
          </p>
          <ul class="latestCachesList">
          <?php foreach($view->lastCacheSets AS $cs){ ?>
            <li>
                <a href="<?=$cs->getUrl()?>" class="links highlite"
                    onmouseover="highliteStaticMapMarker('<?='cs_'.$cs->getId()?>')"
                    onmouseout="highliteStaticMapMarker('<?='cs_'.$cs->getId()?>')">
                  <div class="csImgBox">
                    <img src="<?=$cs->getImage()?>" />
                  </div>
                </a>
                <div class="csNameBox">
                  <a href="<?=$cs->getUrl()?>" class="links highlite"
                    onmouseover="highliteStaticMapMarker('<?='cs_'.$cs->getId()?>')"
                    onmouseout="highliteStaticMapMarker('<?='cs_'.$cs->getId()?>')">
                    <?=$cs->getName()?>
                  </a>
                  <?=tr('hidden_by')?>
                  <?php foreach($cs->getOwners() as $csOwner) { ?>
                    <a href="<?=$csOwner->getUserProfileUrl()?>" class="links">
                      <?=$csOwner->getUserName()?>
                    </a>
                  <?php } // foreach csOwner?>

                  <br>

                    (<?=$cs->getCreationDate(true)?>)
                    <span class='content-title-noshade'>
                      <?=$cs->getLocation()->getDescription(' > ')?>
                    </span>
                  </div>
                </a>

            </li>
          <?php } //foreach-lastCacheSets ?>
            <li class="showMoreLink">
              <a href="/powerTrail.php" class="btn btn-sm">
                <?=tr('startPage_showMore')?>
              </a>
            </li>
          </ul>
        </div>
    <?php } // if-displayGeoPathOfTheDay) ?>
    <!-- /last-cacheSets -->


    <!-- titled caches -->
    <?php if($view->titledCacheData){ ?>
    <div id="cacheTitled">
      <div>
          <p class="content-title-noshade-size3"
             title="<?=tr('startPage_validAt')?>: <?=$view->titledCacheValidAt?>">
            <?=tr('startPage_latestTitledCaches')?>
          </p>
          <?php $view->callChunk('staticMap/staticMapMarker', $view->newestTitledLegendMarker); ?>
      </div>
      <ul class="latestCachesList">
        <li>
          <div>
            <img src="<?=$view->titledCacheData['cacheIcon']?>" class="icon16" alt="Cache" title="Cache">
            <a href="<?=$view->titledCacheData['cacheUrl']?>" class="links highlite"
                 onmouseover="highliteStaticMapMarker('<?=$view->titledCacheData['markerId']?>')"
                 onmouseout="highliteStaticMapMarker('<?=$view->titledCacheData['markerId']?>')">
              <?=$view->titledCacheData['cacheName']?>
            </a>
            <?=tr('hidden_by')?>
            <a href="<?=$view->titledCacheData['cacheOwnerUrl']?>" class="links">
              <?=$view->titledCacheData['cacheOwnerName']?>
            </a>
          </div>
          <div class="cacheLocationBox">
            (<?=$view->titledCacheData['date']?>)
            <span class="content-title-noshade">
              <?=$view->titledCacheData['cacheLocation']?>
            </span>
          </div>
          <div class="cacheTitledLog">
            <img src="images/rating-star.png" alt="Star">
              <a href="<?=$view->titledCacheData['logOwnerUrl']?>" class="links">
                <?=$view->titledCacheData['logOwnerName']?>:
              </a>
              <div>
                <?=$view->titledCacheData['logText']?>
              </div>
          </div>
        </li>
        <li class="showMoreLink">
          <a href="/cache_titled.php" class="btn btn-sm">
            <?=tr('startPage_showMore')?>
          </a>
        </li>
      </ul>
    </div>
    <?php } //if-titledCacheData ?>
    <!-- /titled caches -->

    <?php if($view->isUserLogged) { ?>
    <!-- total Stats -->
    <p class="content-title-noshade-size3"
       title="<?=tr('startPage_validAt')?>: <?=$view->totStsValidAt?>">
      <?=tr('startPage_wholeStatsTitle')?>
    </p>
    <div id="totalStatsDiv">

        <div id="arrowLeft" class="counterArrow" onclick="countersLeft()"></div>
        <div id="totalStatsCounters">
        <?php foreach($view->totStsArr as $key=>$sts) { ?>

            <div class="counterWidget <?=($key>4)?'counterRightHidden':''?>" title="<?=$sts['ldesc']?>">
              <div class="counterInner">
                <div class="counterTitle"><?=$sts['desc']?></div>
                <div class="counterNumber"><?=$sts['val']?></div>
              </div>
            </div>

        <?php } //foreach-totStsArr ?>
        </div>
        <div id="arrowRight" class="counterArrow" onclick="countersRight()"></div>

    </div>
    <script type="text/javascript">
      function countersLeft(){
        // hide left element
        $("#totalStatsDiv .counterWidget:not(.counterLeftHidden):first").toggleClass('counterLeftHidden');

        if( $("#totalStatsDiv .counterRightHidden:first").length == 0 ){
          // move counter from left to right
          el = $("#totalStatsDiv .counterLeftHidden:first");
          el.appendTo($("#totalStatsCounters"));
          el.toggleClass('counterLeftHidden');
          el.toggleClass('counterRightHidden');
        }
        $("#totalStatsDiv .counterRightHidden:first").toggleClass('counterRightHidden');
      }

      function countersRight(){
        // hide left element
        $("#totalStatsDiv .counterWidget:not(.counterRightHidden):last").toggleClass('counterRightHidden');

        if( $("#totalStatsDiv .counterLeftHidden:last").length == 0 ){
          // move counter from right to left
          el = $("#totalStatsDiv .counterRightHidden:last");
          el.prependTo($("#totalStatsCounters"));
          el.toggleClass('counterLeftHidden');
          el.toggleClass('counterRightHidden');
        }
        $("#totalStatsDiv .counterLeftHidden:last").toggleClass('counterLeftHidden');
      }
    </script>
    <!-- /total Stats -->
    <?php } //if-isUserLogged ?>



    <!-- feeds -->
    <div id="feedsContainer">
      <?php //$view->callChunk('dynamicHtmlLoad', $view->feedsUrl, 'feedsContainer'); ?>
    </div>
    <!-- /feeds -->
</div>

<!-- /CONTENT -->
