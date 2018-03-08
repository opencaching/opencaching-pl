<?php
use lib\Objects\Neighbourhood\Neighbourhood;

?>
<div class="nbh-block-header">
  <?=tr('map')?>
  <div class="btn-group nbh-sm-buttons">
    <button class="btn btn-xs btn-default nbh-hide-toggle" title="<?=tr('myn_hlp_hide')?>" id="nbh-map-hide"><span class="nbh-eye"></span></button>
    <button class="btn btn-xs btn-default nbh-size-toggle" title="<?=tr('myn_hlp_resize')?>" id="nbh-map-resize"><span class="ui-icon ui-icon-arrow-2-e-w"></span></button>
  </div>
</div>

<div id="nbhmap" class="nbh-block-content nbh-usermap<?=$view->preferences['items'][Neighbourhood::ITEM_MAP]['show'] == true ? '' : ' nbh-nodisplay'?>"></div>
<?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "nbhmap");?>