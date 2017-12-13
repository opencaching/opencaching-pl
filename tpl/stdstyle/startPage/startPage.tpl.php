
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
      <div id="intro">
        <?=$view->introText?>
      </div>
      <!-- /INTRO -->
    <?php } //if-isUserLogged ?>




    <?php if($view->isUserLogged && !empty($view->newsList)){ ?>
    <!-- NEWS -->

    <div id="newsDiv">
        <p class="content-title-noshade-size3">
          <?=tr('news')?>
        </p>

        <?php foreach($view->newsList as $news) { ?>
          <div class="newsItem">
            <div class="news-statusline">
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
    <!-- /NEWS -->
    <?php } //if-!empty($view->newsList) ?>

    <div id="totalStatsDiv">
        <p class="content-title-noshade-size3">
          Nasz liczby (stan zabawy)
        </p>

      <div id="totalStatsCounters">
        <div class="counterWidget" title="XZałożone kiedykolwiek...">
          <div class="counterTitle">XSkrzynek ogółem</div>
          <div class="counterNumber"><?=$view->totalStats->totalCaches?></div>
        </div>

        <div class="counterWidget" title="XRuszaj w teren i znajdź którąś...">
          <div class="counterTitle">XGotowych do szukania</div>
          <div class="counterNumber"><?=$view->totalStats->activeCaches?></div>
        </div>

        <div class="counterWidget" title="XRuszaj w teren i znajdź którąś...">
          <div class="counterTitle">XOcenionych jako "znakomite"</div>
          <div class="counterNumber"><?=$view->totalStats->topRatedCaches?></div>
        </div>

        <div class="counterWidget" title="">
          <div class="counterTitle">XZałożonych w tym miesiacu</div>
          <div class="counterNumber"><?=$view->totalStats->latestCaches?></div>
        </div>

        <div class="counterWidget" title="">
          <div class="counterTitle">XAktywych geoscieżek</div>
          <div class="counterNumber"><?=$view->totalStats->activeCacheSets?></div>
        </div>

        <div class="counterWidget" title="XZnaleźli lub ukryli...">
          <div class="counterTitle">Zarejestrowanych poszukiwaczy</div>
          <div class="counterNumber"><?=$view->totalStats->totalUsers?></div>
        </div>

        <div class="counterWidget" title="XZnaleźli lub ukryli...">
          <div class="counterTitle">
            Nowych poszukiwaczy <?=$view->totalStats->newUsersPeriod?>
          </div>
          <div class="counterNumber"><?=$view->totalStats->newUsers?></div>
        </div>

        <div class="counterWidget" title="">
          <div class="counterTitle">XPoszukiwań ogółem</div>
          <div class="counterNumber"><?=$view->totalStats->totalSearches?></div>
        </div>

        <div class="counterWidget" title="">
          <div class="counterTitle">XZnalezień w tym tygodniu</div>
          <div class="counterNumber"><?=$view->totalStats->latestSearches?></div>
        </div>

        <div class="counterWidget" title="">
          <div class="counterTitle">XRekomendacji w tym tygodniu</div>
          <div class="counterNumber"><?=$view->totalStats->latestRecomendations?></div>
        </div>
      </div>

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

      <ul class="startPageList">
        <?php foreach($view->latestCaches as $c){ ?>
          <li>
            <div>
              (<?=$c['date']?>)
              <span class="content-title-noshade"><?=$c['location']?></span>
            </div>
            <div>
              <a class="links highlite" href="<?=$c['link']?>"
                 onmouseover="showMarker('<?=$c['markerId']?>')"
                 onmouseout="hideMarker('<?=$c['markerId']?>')">

                <img src="<?=$c['icon']?>" class="icon16" alt="CacheIcon" title="">
                <?=$c['cacheName']?>

              </a>
              <?=tr('hidden_by')?>
              <a class="links" href="<?=$c['userUrl']?>"><?=$c['userName']?></a>
            </div>
          </li>
        <?php } //foreach ?>

          <li class="showMoreLink">
            <a href="/newcaches.php" class="btn btn-sm">XPokaż więcej</a>
          </li>
       </ul>
    </div>

    <!-- /newest caches -->

    <!-- incomming events -->
    <div id="newCachesList">
      <p class="content-title-noshade-size3">
        {{incomming_events}}
      </p>

      <ul class="startPageList">
        <?php foreach($view->incomingEvents as $c){ ?>
          <li>
            <div>
              (<?=$c['date']?>)
              <span class="content-title-noshade"><?=$c['location']?></span>
            </div>
            <div>
                <a class="links highlite" href="<?=$c['link']?>"
                   onmouseover="showMarker('<?=$c['markerId']?>')"
                   onmouseout="hideMarker('<?=$c['markerId']?>')">

                  <img src="<?=$c['icon']?>" class="icon16" alt="CacheIcon" title="">
                  <?=$c['cacheName']?>

                </a>
                <?=tr('hidden_by')?>
                <a class="links" href="<?=$c['userUrl']?>"><?=$c['userName']?></a>
            </div>
          </li>
        <?php } //foreach ?>

          <li class="showMoreLink">
            <a href="/newevents.php" class="btn btn-sm">XPokaż więcej</a>
          </li>
       </ul>
    </div>
    <!-- /incomming events -->

    <!-- titled caches -->
    <?php if($view->titledCacheData){ ?>
    <div id="cacheTitled">
      <p class="content-title-noshade-size3">
        XSkrzynki-tygodniaX
      </p>
      <ul class="startPageList">
        <li>
          <div>
            (<?=$view->titledCacheData['date']?>)
            <span class="content-title-noshade">
              <?=$view->titledCacheData['cacheLocation']?>
            </span>
          </div>
          <div>
            <img src="<?=$view->titledCacheData['cacheIcon']?>" class="icon16" alt="Cache" title="Cache">
            <a href="<?=$view->titledCacheData['cacheUrl']?>" class="links highlite">
              <?=$view->titledCacheData['cacheName']?>
            </a>
            <?=tr('hidden_by')?>
            <a href="<?=$view->titledCacheData['cacheOwnerUrl']?>" class="links">
              <?=$view->titledCacheData['cacheOwnerName']?>
            </a>
          </div>


          <div class="cacheTitledLog">
            <img src="images/rating-star.png" alt="Star">
              <a href="<?=$view->titledCacheData['logOwnerUrl']?>" class="links">
                <?=$view->titledCacheData['logOwnerName']?>
              </a>:<br><br>
                    <?=$view->titledCacheData['logText']?>
          </div>
        </li>
        <li class="showMoreLink">
          <a href="/cache_titled.php" class="btn btn-sm">XPokaż więcej</a>
        </li>
      </ul>
    </div>
    <?php } //if-titledCacheData ?>
    <!-- /titled caches -->



    <!-- last-cacheSets -->
    <?php if($view->displayLastCacheSets){ ?>
        <div id="newestCacheSets">
          <p class="content-title-noshade-size3">
            <?=tr('startPage_promotedCacheSet')?>
          </p>
          <ul class="startPageList">
          <?php foreach($view->lastCacheSets AS $cs){ ?>
            <li>
              <div>
                (<?=$cs->getCreationDate(true)?>)
                <span class='content-title-noshade'>
                  <?=$cs->getLocation()->getDescription(' > ')?>
                </span>
              </div>
              <div>
                <a href="<?=$cs->getUrl()?>" class="links highlite">
                  <img src="<?=$cs->getImage()?>" />
                  <?=$cs->getName()?>
                </a>
                <?=tr('hidden_by')?>
                lista autorów...
              </div>
            </li>
          <?php } //foreach-lastCacheSets ?>
            <li class="showMoreLink">
              <a href="/powerTrail.php" class="btn btn-sm">XPokaż więcej</a>
            </li>
          </ul>
        </div>
    <?php } // if-displayGeoPathOfTheDay) ?>
    <!-- /last-cacheSets -->

    <!-- feeds -->
    <?php foreach($view->feeds as $feedName => $feedPosts) { ?>
      <div id="feedArea">
        <p class="content-title-noshade-size3"><?=tr('feed_'.$feedName)?></p>
        <ul id="feedList">
          <?php foreach($feedPosts as $post){ ?>
              <li>
                <?=$post->date?>
                <a class="links" href="<?=$post->link?>">
                  <?=$post->title?>
                </a>
                (<?=$post->author?>)
              </li>
          <?php } //foreach-feedPosts ?>
        </ul>
      </div>
    <?php }//foreach-feeds ?>
    <!-- /feeds -->
</div>
<!-- /CONTENT -->
