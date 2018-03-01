<div class="nbh-block-header">
  <?=tr('map')?>
  <div class="btn-group nbh-sm-buttons">
    <button class="btn btn-xs btn-default nbh-hide-toggle" title="<?=tr('myn_hlp_hide')?>"><span class="nbh-eye"></span></button>
    <button class="btn btn-xs btn-default nbh-size-toggle" title="<?=tr('myn_hlp_resize')?>" id="nbh-map-resize"><span class="ui-icon ui-icon-arrow-2-e-w"></span></button>
  </div>
</div>

<div id="nbhmap" class="nbh-block-content nbh-usermap"></div>
<?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "nbhmap");?>
