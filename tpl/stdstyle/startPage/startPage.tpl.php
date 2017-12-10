
<div class="content2-pagetitle">
  <?php if($view->isUserLogged) { ?>
    <?=tr('startPage_welcome')?>&nbsp;<?=$view->username?>
  <?php } else { //if-isUserLogged ?>
    <?=tr('startPage_title')?>
  <?php } //if-isUserLogged ?>
</div>

<div class="content2-container">

    <?php if(!$view->isUserLogged) { ?>
      <!-- INTRO -->
      <div class="intro"><?=$view->introText?></div>
      <!-- /INTRO -->
    <?php } //if-isUserLogged ?>


    <?php if($view->isUserLogged && !empty($view->newsList)){ ?>
    <!-- NEWS -->

    <?php //TODO: https://www.w3schools.com/w3css/w3css_slideshow.asp ?>

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
    <!-- /NEWS -->
    <?php } //if-!empty($view->newsList) ?>

    <div>
      <p class="main-totalstats">
        <?=tr('startPage_stsAllCaches')?>:<span class="content-title-noshade"><?=$view->totalStats->totalCaches?></span>
        <?=tr('startPage_stsActiveCaches')?>: <span class="content-title-noshade"><?=$view->totalStats->activeCaches?></span>
      | <?=tr('startPage_stsFounds')?>: <span class="content-title-noshade"><?=$view->totalStats->founds?></span>
      | <?=tr('startPage_stsUsers')?>: <span class="content-title-noshade"><?=$view->totalStats->activeUsers?></span>
      </p>
    </div>

    <!-- newest caches -->
    <div id="map">
      <div style="position: relative;">

        <img src="<?=$view->staticMapUrl?>" id="main-cachemap" alt="<?=tr('map')?>" />
        <?php foreach($view->mapMarkers as $m) { ?>

          <img id="<?=$m['id']?>" class="mapMarker lightTipped" style="left:<?=$m['left']?>px; top:<?=$m['top']?>px"
              alt="" src="<?=$m['img']?>">
          <div class="lightTip" style="left:<?=($m['left']+20)?>px; top:<?=$m['top']?>px">
            <b><?=$m['toolTip']?></b>
          </div>

        <?php } //foreach mapMarkers ?>
        <script type="text/javascript">
            function showMarker(id) {
              $('#'+id).toggleClass('hovered');
            }

            function hideMarker(id) {
              $('#'+id).toggleClass('hovered');
            }
        </script>
      </div>
    </div>

    <div id="newCachesList">
      <p class="content-title-noshade-size3">
        {{newest_caches}}
      </p>

      <ul>
        <?php foreach($view->latestCaches as $c){ ?>
          <li>
            <span class="content-title-noshade"><?=$c['location']?></span>
            (<?=$c['date']?>):
            <br/>

            <a class="links" href="<?=$c['link']?>"
               onmouseover="showMarker('<?=$c['markerId']?>')"
               onmouseout="hideMarker('<?=$c['markerId']?>')">

              <img src="<?=$c['icon']?>" class="icon16" alt="CacheIcon" title="">
              <?=$c['cacheName']?>

            </a>
            &nbsp;<?=tr('hidden_by')?>&nbsp;
            <a class="links" href="<?=$c['userUrl']?>"><?=$c['userName']?></a>

          </li>
        <?php } //foreach ?>

    </div>

    <!-- /newest caches -->

    <!-- incomming events -->
    <div id="newCachesList">
      <p class="content-title-noshade-size3">
        {{incomming_events}}
      </p>

      <ul>
        <?php foreach($view->incomingEvents as $c){ ?>
          <li>
            <span class="content-title-noshade"><?=$c['location']?></span>
            (<?=$c['date']?>):
            <br/>

            <a class="links" href="<?=$c['link']?>"
               onmouseover="showMarker('<?=$c['markerId']?>')"
               onmouseout="hideMarker('<?=$c['markerId']?>')">

              <img src="<?=$c['icon']?>" class="icon16" alt="CacheIcon" title="">
              <?=$c['cacheName']?>

            </a>
            &nbsp;<?=tr('hidden_by')?>&nbsp;
            <a class="links" href="<?=$c['userUrl']?>"><?=$c['userName']?></a>

          </li>
        <?php } //foreach ?>

    </div>
    <!-- /incomming events -->

    <!-- titled caches -->
    <?php if($view->titledCacheData){ ?>
    <div id="cacheTitled">
      <p class="content-title-noshade-size3">
        XSkrzynki-tygodniaX
      </p>

      <img src='<?=$view->titledCacheData['cacheIcon']?>' class='icon16' alt='Cache' title='Cache'>
      <a href='<?=$view->titledCacheData['cacheUrl']?>' class='links'>
        <?=$view->titledCacheData['cacheName']?>
      </a>
      &nbsp;<?=tr('hidden_by')?>
      <a href='<?=$view->titledCacheData['cacheOwnerUrl']?>' class='links'>
        <?=$view->titledCacheData['cacheOwnerName']?>
      </a>
      <br>

      <p class='content-title-noshade'><?=$view->titledCacheData['cacheLocation']?></p>
      <div class='CacheTitledLog'>
        <img src='images/rating-star.png' alt='Star'>
          &nbsp;
          <a href='<?=$view->titledCacheData['logOwnerUrl']?>' class='links'>
            <?=$view->titledCacheData['logOwnerName']?>
          </a>:<br><br>
                <?=$view->titledCacheData['logText']?>
      </div>

    </div>
    <?php } //if-titledCacheData ?>
    <!-- /titled caches -->

    <!-- last-cacheSets -->
    <?php if($view->displayLastCacheSets){ ?>
        <div id="newestCacheSets">
          <p class="content-title-noshade-size3">
            <?=tr('startPage_promotedCacheSet')?>
          </p>
          <ul>
          <?php foreach($view->lastCacheSets AS $cs){ ?>
            <li>
              <p class='content-title-noshade'>
                <?=$cs->getLocation()->getDescription(' > ')?>
              </p>
              <a href="<?=$cs->getUrl()?>">
                <img src="<?=$cs->getImage()?>" />
                <?=$cs->getName()?>
              </a>
            </li>
          <?php } //foreach-lastCacheSets ?>
          </ul>
        </div>
    <?php } // if-displayGeoPathOfTheDay) ?>
    <!-- /last-cacheSets -->

    <!-- feeds -->
    {Feeds}
    <!-- /feeds -->
</div>
<!-- /CONTENT -->
