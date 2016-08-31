<?php
/**
 * TODO:
 * @param unknown $v
 */
use lib\Objects\ChunkModels\ListOfCaches;

return function (ListOfCaches $m){
//start of chunk

?>
  <table>
  <?php foreach($m->caches() as $cache) { ?>

    <tr>

      <td class="myneighborhood tab_icon">
        <?php if($cache->ptEnabled) { ?>
          <a href="powerTrail.php?ptAction=showSerie&ptrail=<?=$cache->ptId?>">
            <img src="<?=$cache->ptIcon?>" class="icon16" alt="" title="<?=$cache->ptName?>" />
          </a>
        <?php } //$cache->pt ?>
      </td>

      <td class="myneighborhood tab_icon">
        <img src="<?=$cache->icon?>" class="icon16" alt="Cache" title="Cache" />
      </td>
      <td class="myneighborhood tab_date">
        <?=$cache->date?>
      </td>

      <?php if($m->recoCol()){ ?>
          <td>
            <?=$cache->recoNum?>
          </td>
      <?php } ?>

      <td>
        <?php if($m->logTooltipEnabled()) { ?>
          <a href="viewcache.php?cacheid=<?=$cache->cacheId?>"
             onmouseover="Tip('<?=$cache->logText?>', PADDING,5,WIDTH,280,SHADOW,true)"
             onmouseout="UnTip()" >
            <?=$cache->cacheName?>
          </a>
        <?php }else{ //$cache->logText ?>
          <a href="viewcache.php?cacheid=<?=$cache->cacheId?>">
            <?=$cache->cacheName?>
          </a>
        <?php } //$cache->logText ?>
      </td>

      <?php if($cache->logIcon){ ?>
        <td class="myneighborhood tab_icon">
          <img src="<?=$cache->logIcon?>" class="icon16" alt="Cache" title="Cache" />
        </td>
      <?php }if($cache->logIcon) //?>

      <td>
        <?=$cache->userName?>
      </td>
    </tr>

  <?php } //foreach ?>
  </table>
<?php
}; //end of chunk

