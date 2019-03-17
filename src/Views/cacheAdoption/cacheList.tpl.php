<?php
use src\Utils\Uri\SimpleRouter;
use src\Controllers\CacheAdoptionController;
?>

<?php $view->callChunk('infoBar', SimpleRouter::getLink(CacheAdoptionController::class), $view->infoMsg, $view->errorMsg ); ?>

<?php if($view->adoptionOffers) { ?>

<div class="content2-pagetitle">
  <b>{{adopt_10}}</b>
</div>

<div>
  <p>{{adopt_11}}</p>
</div>


<div id="cachesOffered">
  <ul>

    <?php foreach ($view->adoptionOffers as $cache) { ?>

      <li>
        <a href='viewcache.php?cacheid=<?=$cache['cache_id']?>'>
          <?=$cache['name']?>&nbsp;[<?=$cache['offeredFromUserName']?>]:&nbsp;&nbsp;
        </a>
        <a href='<?=SimpleRouter::getLink(CacheAdoptionController::class,'accept',$cache['cache_id'])?>'>
          <span class="btn btn-success btn-sm">{{adopt_12}}</span>
        </a>
        <a href='<?=SimpleRouter::getLink(CacheAdoptionController::class,'refuse',$cache['cache_id'])?>'>
          <span class="btn btn-danger btn-sm">{{adopt_13}}</span>
        </a>
      </li>

    <?php } ?>

  </ul>
</div>
<?php } ?>


<!-- Caches owns by current user -->

<div class="content2-pagetitle">
  <b>{{adopt_00}}</b>
</div>

<?php if( empty($view->userCaches) ) { ?>

    <div>
      <p>{{adopt_03}}</p>
    </div>

<?php } else { ?>

    <div>
      <p>{{adopt_01}}</p>
    </div>

    <div id="cachesOwned">
      <ul>
        <?php foreach ( $view->userCaches as $cache) { ?>
          <li>
              <?php if($cache['adoptionOfferId']) { ?>
                <!-- cache offered for adoption - offer can be aborted -->
                <?=$cache['name']?>
                <a href="<?=SimpleRouter::getLink(CacheAdoptionController::class,'abort',$cache['cache_id'])?>">
                  <span class="btn btn-danger btn-sm">{{adopt_14}} -> <?=$cache['offeredToUserName']?></span>
                </a>

              <?php } else { ?>
                <!-- cache is not offered for adoption - offer can be created -->
                <a href="<?=SimpleRouter::getLink(CacheAdoptionController::class,'selectUser',$cache['cache_id'])?>">
                  <?=$cache['name']?>
                </a>
              <?php } ?>
          </li>
        <?php } ?>
      </ul>
    </div>

<?php } ?>
