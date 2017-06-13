<?php

/**
 * This chunk allow to display list of caches.
 * Data should be stored in ListOfCaches object param
 *
 * Check myProvince.php for example of use it.
 * Note that this code is used in many places
 * so be carefull before changes.
 *
 * @param ListOfCaches $m
 *
 */
use lib\Objects\ChunkModels\ListOfCaches;

return function (ListOfCaches $m){
//start of chunk

?>
  <table class="bs-table table-striped">
      <thead></thead>
      <tbody>
  <?php foreach($m->caches() as $cache) { ?>
        <tr>

          <td>
            <?php if($cache->ptEnabled) { ?>
              <a href="powerTrail.php?ptAction=showSerie&ptrail=<?=$cache->ptId?>">
                <img src="<?=$cache->ptIcon?>" class="icon16" alt="" title="<?=$cache->ptName?>" />
              </a>
            <?php } //$cache->pt ?>
          </td>

          <td>
            <img src="<?=$cache->icon?>" class="icon16" alt="Cache" title="Cache" />
          </td>
          <td>
            <?=$cache->date?>
          </td>

          <?php if($m->recoCol()){ ?>
              <td class="cell-favorite">
                <?=$cache->recoNum?>
              </td>
          <?php } ?>

          <th>
              <a href="viewcache.php?cacheid=<?=$cache->cacheId?>" title="<?=$cache->cacheName ?>" class="truncated">
                <?=$cache->cacheName?>
              </a>
          </th>

          <?php if($cache->logIcon){ ?>
            <td>
              <img src="<?=$cache->logIcon?>" class="icon16" alt="Cache" title="Cache" />
            </td>
          <?php }if($cache->logIcon) //?>

          <td>
            <?php if($m->logTooltipEnabled()) { ?>
              <a href="viewprofile.php?userid=<?=$cache->userId?>"
                 onmouseover="Tip('<?=$cache->logText?>', PADDING,5,WIDTH,280,SHADOW,true)"
                 onmouseout="UnTip()" >
                <?=$cache->userName?>
            </a>
            <?php }else{ //$cache->userName ?>
              <a href="viewprofile.php?userid=<?=$cache->userId?>">
                <?=$cache->userName?>
              </a>
            <?php } //$cache->userName ?>
          </td>
        </tr>
  <?php } //foreach ?>
  </table>
<?php
}; //end of chunk

