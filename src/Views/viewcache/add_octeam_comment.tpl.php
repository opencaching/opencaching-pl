<?php
use src\Utils\Uri\SimpleRouter;
use src\Controllers\ViewCacheController;
?>
<div class="content2-pagetitle">
  {{add_rr_comment}}:
  <a href="<?=$v->cacheUrl?>">
    <?=$v->cacheName?>
  </a>
</div>
<div>
    <form method='post'
      action='<?=SimpleRouter::getLink(ViewCacheController::class, 'saveOcTeamComments', $v->cacheId)?>'>
        <textarea name='ocTeamComment' cols='80' rows='15'></textarea>
        <br/>
        <button type="submit" class="btn btn-primary">{{save}}</button>
        <button type="button" class="btn btn-default"
          onclick="window.location.href='<?=$v->cacheUrl?>'">{{cancel}}</button>
    </form>
</div>