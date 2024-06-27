<?php
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheDesc;
use src\Utils\View\View;

/** @var View $view */

/** @var GeoCache $cache */
$cache = $view->cache;

/** @var GeoCacheDesc $desc */
$desc = $view->desc;

$view->callChunk('tinyMCE');
?>

<div class="content2-pagetitle">
  <?= tr('editDesc_title'); ?>
  <a href="<?= $cache->getCacheUrl(); ?>"><?= $cache->getCacheName(); ?></a>
</div>


<form action="/CacheDesc/save/<?= $cache->getWaypointId(); ?>/<?= $desc->getLang(); ?>"
      method="post" enctype="application/x-www-form-urlencoded" id="cacheeditform">

    <?= $view->callSubTpl('/cacheDescEdit/cacheDescEditForm'); ?>

    <div class="content2-container">
        <input type="submit" name="submitform" value="{{submit}}" class="btn btn-primary"/>
    </div>
</form>
