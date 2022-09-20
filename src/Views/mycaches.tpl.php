<?php
/** @noinspection PhpUndefinedFieldInspection */

use src\Models\GeoCache\GeoCacheCommons;

$view = tpl_getView();

?>
<script src="/js/wz_tooltip.js"></script>
<div class="content2-pagetitle">
  <img src="/images/blue/cache.png" class="icon32" alt="" title="">
    <?= tr('my_caches_status'); ?>: <?= tr($view->cacheStatusTr); ?>
</div>

<div class="content2-container">
  <div class="btn-group btn-group-justified">
    <a class="btn btn-sm <?= ($view->cacheStatus == GeoCacheCommons::STATUS_READY) ? 'btn-primary' : 'btn-default'; ?>"
       href="/mycaches.php?status=1">
        <?= tr('active'); ?>
      (<?= $view->cachesNo[GeoCacheCommons::STATUS_READY]; ?>)
    </a>
    <a class="btn btn-sm <?= ($view->cacheStatus == GeoCacheCommons::STATUS_UNAVAILABLE) ? 'btn-primary' : 'btn-default'; ?>"
       href="/mycaches.php?status=2">
        <?= tr('temp_unavailable'); ?>
      (<?= $view->cachesNo[GeoCacheCommons::STATUS_UNAVAILABLE]; ?>)
    </a>
    <a class="btn btn-sm <?= ($view->cacheStatus == GeoCacheCommons::STATUS_ARCHIVED) ? 'btn-primary' : 'btn-default'; ?>"
       href="/mycaches.php?status=3">
        <?= tr('archived'); ?>
      (<?= $view->cachesNo[GeoCacheCommons::STATUS_ARCHIVED]; ?>)
    </a>
    <a class="btn btn-sm <?= ($view->cacheStatus == GeoCacheCommons::STATUS_NOTYETAVAILABLE) ? 'btn-primary' : 'btn-default'; ?>"
       href="/mycaches.php?status=5">
        <?= tr('not_published'); ?>
      (<?= $view->cachesNo[GeoCacheCommons::STATUS_NOTYETAVAILABLE]; ?>)
    </a>
    <a class="btn btn-sm <?= ($view->cacheStatus == GeoCacheCommons::STATUS_WAITAPPROVERS) ? 'btn-primary' : 'btn-default'; ?>"
       href="/mycaches.php?status=4">
        <?= tr('for_approval'); ?>
      (<?= $view->cachesNo[GeoCacheCommons::STATUS_WAITAPPROVERS]; ?>)
    </a>
    <a class="btn btn-sm <?= ($view->cacheStatus == GeoCacheCommons::STATUS_BLOCKED) ? 'btn-primary' : 'btn-default'; ?>"
       href="/mycaches.php?status=6">
        <?= tr('blocked'); ?>
      (<?= $view->cachesNo[GeoCacheCommons::STATUS_BLOCKED]; ?>)
    </a>
  </div>
</div>

<div class="content2-container">
  <table class="full-width" style="line-height: 1.4em; font-size: 13px;">
    <tr>
      <td colspan="2">
        <a class="links" href="/mycaches.php?col=1<?= $view->myCacheSort; ?>">
            <?= tr('date_hidden_label'); ?>
        </a>
      </td>
      <td></td>
      <td>
        <a class="links" href="/mycaches.php?col=2<?= $view->myCacheSort; ?>">
            <?= tr('geocache'); ?>
        </a>
      </td>
      <td>
        <a class="links" href="/mycaches.php?col=3<?= $view->myCacheSort; ?>">
          <img src="/images/log/16x16-found.png" alt="<?= tr('mc_by_founds'); ?>" title="<?= tr('mc_by_founds'); ?>">
        </a>
      </td>
      <td>
        <a class="links" href="/mycaches.php?col=9<?= $view->myCacheSort; ?>">
          <img src="/images/log/16x16-dnf.png" alt="<?= tr('mc_by_not_founds'); ?>" title="<?= tr('mc_by_not_founds'); ?>">
        </a>
      </td>
      <td>
        <a class="links" href="/mycaches.php?col=4<?= $view->myCacheSort; ?>">
          <img src="/images/rating-star.png" alt="<?= tr('mc_by_reco'); ?>" title="<?= tr('mc_by_reco'); ?>">
        </a>
      </td>
      <td>
        <a class="links" href="/mycaches.php?col=6<?= $view->myCacheSort; ?>">
          <img src="/images/gk.png" alt="<?= tr('mc_by_gk'); ?>" title="<?= tr('mc_by_gk'); ?>">
        </a>
      </td>
      <td>
        <a class="links" href="/mycaches.php?col=8<?= $view->myCacheSort; ?>">
          <img src="/images/action/16x16-watch.png" alt="<?= tr('mc_by_watchers'); ?>" title="<?= tr('mc_by_watchers'); ?>">
        </a>
      </td>
      <td>
        <a class="links" href="/mycaches.php?col=7<?= $view->myCacheSort; ?>">
          <img src="images/free_icons/vcard.png" alt="<?= tr('mc_by_visits'); ?>" title="<?= tr('mc_by_visits'); ?>">
        </a>
      </td>
      <td>
        <a class="links" href="/mycaches.php?col=5<?= $view->myCacheSort; ?>">
            <?= tr($view->col5Header); ?>
        </a>
      </td>
      <td>
        <strong><?= tr('latest_logs'); ?></strong>
      </td>
    </tr>
    <tr>
      <td colspan="12">
        <hr>
      </td>
    </tr>
      <?= $view->fileContent; ?>
    <tr>
      <td colspan="12">
        <hr>
      </td>
    </tr>
  </table>
</div>
<div class="content2-container">
  <p>
      <?= $view->pages; ?>
  </p>
</div>
