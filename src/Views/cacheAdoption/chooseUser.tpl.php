<?php
use src\Utils\Uri\SimpleRouter;
use src\Controllers\CacheAdoptionController;
?>

<div class="content2-pagetitle">
    {{adopt_04}}
    <a href="<?=$view->cacheObj->getCacheUrl()?>"><?=$view->cacheObj->getCacheName()?></a>
</div>

<form
  action="<?=SimpleRouter::getLink(
      CacheAdoptionController::class,
      'addAdoptionOffer',
      $view->cacheObj->getCacheId())?>"
  method="post">
    <div>
      <p><?=tr('adopt_05',[$view->listOfCachesUrl])?></p>
    </div>

    <div class="alertMsg">
      <p>{{adopt_06}}</p>
    </div>

    <div>
        <label for="username">{{adopt_07}}</label>
        <input id="username" type="text" size="25" name="username" />
        <input type="submit" class="btn btn-sm btn-primary" value="{{adopt_08}}" />
    </div>

</form>
