<?php
use Utils\Uri\SimpleRouter;

?>
<div class="content2-container">
  <?=$view->callChunk('infoBar', null, $view->infoMsg, $view->errorMsg)?>
  <div class="nbh-top-config-btns">
    <a class="btn btn-md <?=($view->selectedNbh == 0) ? 'btn-primary' : 'btn-default'?>" href="<?=SimpleRouter::getLink('MyNeighbourhood', 'config', 0)?>"><?=tr('my_neighborhood')?></a>
    <?php foreach ($view->neighbourhoodsList as $nbh) {
      if ($nbh->getSeq() == 0) {
          continue;
      } ?>
    <a class="btn btn-md <?=($view->selectedNbh == $nbh->getSeq()) ? 'btn-primary' : 'btn-default'?>" href="<?=SimpleRouter::getLink('MyNeighbourhood', 'config', $nbh->getSeq())?>"><?=$nbh->getName()?></a>
    <?php } // end foreach neighbourhoodsList ?>
    <?php if ($view->selectedNbh == -1) { ?>
    <a class="btn btn-md btn-primary" href="<?=SimpleRouter::getLink('MyNeighbourhood', 'config', '-1')?>">?</a>
    <?php } // end if selectedNbh = -1
      if (count($view->neighbourhoodsList) <= $view->maxnbh) { ?>
    <a class="btn btn-md btn-success" href="<?=SimpleRouter::getLink('MyNeighbourhood', 'config', '-1')?>" title="<?=tr('myn_addarea_info')?>"><img src="/tpl/stdstyle/images/misc/plus-sign.svg" class="icon16" alt="<?=tr('new')?>">&nbsp;<?=tr('new')?></a>
    <?php } // end if ?>
    <span class="nbh-button-right">
      <a href="<?=SimpleRouter::getLink('MyNeighbourhood', 'index', $view->selectedNbh) ?>" class="btn btn-default btn-md"><?=tr('exit_config')?></a>
    </span>
  </div>

<?php if ($view->coordsOK == 0) {
    if ($view->selectedNbh == 0) { ?>
      <div class="callout callout-warning"><?=tr('myn_intro')?></div>
    <?php } // if selectedNbh == 0 ?>
  <div class="notice"><?=tr('myn_map_info1')?></div>
<?php } elseif ($view->selectedNbh == 0) { ?>
  <div class="notice"><?=tr('myn_name_default')?></div>
<?php } else { ?>
  <div class="notice"><?=tr('myn_name_addition')?></div>
<?php } // end if coordsOK ?>
  <div class="notice"><?=tr('myn_map_info2')?></div>
  <div id="nbhmapmain" class="nbh-map"></div>
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "nbhmapmain");?>

  <div class="align-center">
    <div id="nbh-startdraw-btn" class="btn btn-md btn-success"><?=tr('myn_map_drawbtn')?></div>
    <div id="nbh-coords-line">
      <span id="nbh-coords-lat"></span>
      <span id="nbh-coords-lon"></span> |
      <span id="nbh-radius"></span> km
    </div>
  </div>

<script>
    let configDraw;
    let configDrawHooks = {
        "updateConfig": updateConfig
    };

    $(document).ready(function() {
        configDraw = new ConfigDraw(
            dynamicMapParams_nbhmapmain.map, <?=$view->minRadius?>, <?=$view->maxRadius?>
        );
        configDraw.setHooks(configDrawHooks);
        let initLat, initLon, initRadius;
        <?php if ($view->coordsOK == 1) { ?>
        initLat = <?=$view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLatitude()?>;
        initLon = <?=$view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLongitude()?>;
        initRadius = <?=$view->neighbourhoodsList[$view->selectedNbh]->getRadius()?>;
        $('#nbh-coords-lat').text(latToText($('#input-lat').val()));
        $('#nbh-coords-lon').text(lonToText($('#input-lon').val()));
        $('#nbh-radius').text($('#input-radius').val());
        $('#nbh-startdraw-btn').hide();
        $('#nbh-coords-line').show();
        <?php } else { // else if coordsOK ?>
        $('#nbh-startdraw-btn').click(function() {
            configDraw.startDrawing();
            $('#nbh-startdraw-btn').hide();
            $('#nbh-coords-line').show();
        });
        <?php } // end if coordsOK ?>
        configDraw.init([ initLat, initLon ], initRadius);
    });

    function updateConfig(lat, lon, radius) {
        $('#input-radius').val(radius);
        $('#nbh-radius').text(radius);
        $('#input-lat').val(lat);
        $('#input-lon').val(lon);
        $('#nbh-coords-lat').text(latToText(lat));
        $('#nbh-coords-lon').text(lonToText(lon));
    }

    function latToText(lat) {
        let text = 'N ';
        if (lat < 0) {
            text = 'S ';
        };
        lat = Math.abs(lat);
        let v1 = Math.floor(lat);
        let v2 = Math.round((lat - v1) * 60000) / 1000;
        text += v1 + '° ' + v2 + '\'';
        return text;
    }

    function lonToText(lon) {
        let text = 'E ';
        if (lon < 0) {
            text = 'W ';
        };
        lon = Math.abs(lon);
        let v1 = Math.floor(lon);
        let v2 = Math.round((lon - v1) * 60000) / 1000;
        text += v1 + '° ' + v2 + '\'';
        return text;
    }
  </script>

  <div class="buffer"></div>
  <form method="post" action="<?=SimpleRouter::getLink('MyNeighbourhood', 'save', $view->selectedNbh)?>">
<?php if ($view->selectedNbh == 0) { // Main MyNeighbourhood area ?>
  <fieldset>
    <legend>&nbsp;<?=tr('myn_style_lbl')?>&nbsp;</legend>
    <label for="radio-1"><?=tr('myn_style_full')?></label>
    <input type="radio" name="style" value="full" id="radio-1" class="nbh-radio" <?=$view->preferences['style']['name'] == 'full' ? 'checked' : ''?>>
    <label for="radio-2"><?=tr('myn_style_min')?></label>
    <input type="radio" name="style" value="min" id="radio-2" class="nbh-radio" <?=$view->preferences['style']['name'] == 'min' ? 'checked' : ''?>>
  </fieldset>
  <div class="buffer"></div>
  <fieldset id="cacheitems-slider-fset">
    <legend>&nbsp;<?=tr('myn_cacheitems_lbl')?>&nbsp;</legend>
    <div id="nbh-slider">
      <div id="nbh-custom-handle" class="ui-slider-handle"></div>
    </div>
  </fieldset>
  <div class="buffer"></div>
  <input type="hidden" name="caches-perpage" id="input-caches" value="<?=$view->preferences['style']['caches-count']?>">
  <div class="align-center">
    <button class="btn btn-primary btn-md"><?=tr('save_changes')?></button>
    <a href="<?=SimpleRouter::getLink('UserProfile', 'notifySettings')?>" class="btn btn-default btn-md"><?=tr('settings_notifications')?></a>
    <a href="<?=SimpleRouter::getLink('MyNeighbourhood', 'index', $view->selectedNbh) ?>" class="btn btn-default btn-md"><?=tr('exit_config')?></a>
  </div>
<?php } else { // Additional Myneighbourhood area ?>
    <fieldset>
    <legend><?=tr('myn_name')?></legend>
    <input type="text" name="name" id="input-name" class="ui-widget ui-widget-content ui-corner-all" value="<?=($view->selectedNbh == -1) ? '' : $view->neighbourhoodsList[$view->selectedNbh]->getName()?>" maxlength="16" required>
    </fieldset>
  <div class="buffer"></div>
  <div class="align-center">
    <button class="btn btn-primary btn-md"><?=tr('save_changes')?></button>
    <button class="btn btn-danger btn-md" id="nbh-delete-btn"><?=tr('myn_delete')?></button>
    <a href="<?=SimpleRouter::getLink('MyNeighbourhood', 'index', $view->selectedNbh) ?>" class="btn btn-default btn-md"><?=tr('exit_config')?></a>
  </div>
  <div class="nbh-nodisplay">
    <div id="nbh-delete-dialog-confirm" title="<?=tr('myn_delete')?>">
      <p><span class="ui-icon ui-icon-alert" id="nbh-delete-dialog-icon"></span><?=tr('myn_delete_confirm')?></p>
    </div>
  </div>
<?php } // end if-else ?>
  <input type="hidden" name="lon" id="input-lon" value="<?=$view->coordsOK == 1 ? $view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLongitude() : '' ?>">
  <input type="hidden" name="lat" id="input-lat" value="<?=$view->coordsOK == 1 ? $view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLatitude() : '' ?>">
  <input type="hidden" name="radius" id="input-radius" value="<?=$view->coordsOK == 1 ? $view->neighbourhoodsList[$view->selectedNbh]->getRadius() : '' ?>">
  </form>
<script>
    let cancelButton = "<?=tr('cancel')?>";
    let deleteButton = "<?=tr('delete')?>";
    let deleteLink = "<?=SimpleRouter::getLink('MyNeighbourhood', 'delete', $view->selectedNbh)?>";
    let cachesCount = <?=$view->preferences['style']['caches-count']?>;
    let minCaches = <?=$view->minCaches?>;
    let maxCaches = <?=$view->maxCaches?>;
</script>
</div>
