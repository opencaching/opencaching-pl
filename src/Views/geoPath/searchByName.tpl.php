<?php

use src\Utils\Uri\SimpleRouter;

?>
<div class="content2-container">
  <div class="content2-pagetitle"><?=tr('search')?>: <?=tr('gp_mainTitile')?></div>
  <form class="form form-inline" action="<?=SimpleRouter::getLink('GeoPath', 'searchByName')?>">
    <label for="geoPathName"><?=tr('pt008')?>:</label>
    <input type="text" class="form-control input200" value="<?=$view->searchStr?>" name="name" id="geoPathName">
    <input type="submit" class="form-control btn btn-primary" value="<?=tr('search')?>">
  </form>
  <table class="table table-striped full-width">
    <thead>
      <tr>
        <th><?=tr('cs_name')?></th>
        <th><?=tr('cs_type')?></th>
        <th><?=tr('pt024')?></th>
        <th><?=tr('pt022')?></th>
        <th><?=tr('cs_gainedCount')?></th>
      </tr>
    </thead>
    <?php if (!empty($view->geoPaths)) {?>
    <tbody>
      <?php foreach ($view->geoPaths as $geoPath) {?>
      <tr>
        <td><a href="<?=$geoPath->getUrl()?>" class="links"><?=$geoPath->getName()?></a></td>
        <td>
          <img src="<?=$geoPath->getIcon()?>" alt="">
          <?=$geoPath->getTypeTranslation()?>
        </td>
        <td><?=$geoPath->getCreationDate(true)?>
        <td><?=$geoPath->getCacheCount()?></td>
        <td><?=$geoPath->getGainedCount()?></td>
      </tr>
      <?php } // foreach $view->geoPaths?>
    </tbody>
    <?php } // if !empty geoPaths?>
  </table>
</div>
