<div class="content2-container bg-blue02">
  <span class="content-title-noshade-size1">
    <img src="<?=$view->cacheicon?>" class="icon32" alt="">
    {{gallery_of_cache}} <?=$view->cache->getCacheName() ?>
  </span>
  <span id="log-start-buttons">
    <a href="<?=$view->cache->getCacheUrl() ?>" class="btn btn-sm btn-default">{{back_to_the_geocache_listing}}</a>
  </span>
</div>

<?php if (count($view->cachepictures) > 0) { ?>
  <div class="content2-container">
    <p class="content-title-noshade-size1">{{images_cache}}</p>
    <?php foreach ($view->cachepictures as $picture) { 
        if (!($picture->spoiler && $view->hidespoilers)) {?>
        <div class="viewcache-pictureblock">
          <div class="img-shadow">
            <a href="<?=$picture->url ?>" data-fancybox="picture-cache" data-caption="<?=$picture->title ?>">
              <img src="<?=$picture->thumbUrl ?>" alt="<?=$picture->title ?>">
            </a>
          </div>
          <span class="title"><?=$picture->title ?></span>
        </div>
    <?php } // if !$view->hidespoilers
    } // foreach ?>
  </div>
<?php } // if count() ?>

<?php if (count($view->logpictures) > 0) { ?>
  <div class="content2-container">
    <p class="content-title-noshade-size1">{{images_logs}}</p>
    <?php foreach ($view->logpictures as $picture) { 
        if (!($picture['spoiler'] == '1' && $view->hidespoilers)) {?>
        <div class="viewcache-pictureblock">
          <div class="img-shadow">
            <a href="<?=$picture['url'] ?>" data-fancybox="picture-logs" data-caption="<?=$picture['title'] ?>">
              <img src="<?=$picture['thumbUrl'] ?>" alt="<?=$picture['title'] ?>">
            </a>
          </div>
          <span class="title"><a href="/viewlogs.php?logid=<?=$picture['object_id'] ?>"><?=$picture['title'] ?></a></span>
        </div>
    <?php } // if !$view->hidespoilers
    } // foreach ?>
  </div>
<?php } // if count() ?>
