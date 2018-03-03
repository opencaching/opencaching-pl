<?php
use Utils\Uri\SimpleRouter;

?>
<div class="content2-container">
  <?=$view->callChunk('infoBar', null, $view->infoMsg, $view->errorMsg)?>
  <div class="btn-group btn-group-md">
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
  </div>

<?php if ($view->coordsOK == 0) {
    if ($view->selectedNbh == 0) { ?>
      <div class="callout callout-warning"><?=tr('myn_intro')?></div>
    <?php } // if selectedNbh == 0 ?>
  <div class="notice"><?=tr('myn_map_info1')?></div>
<?php } // end if coordsOK ?>
  <div class="notice"><?=tr('myn_map_info2')?></div>
  <div id="nbhmapmain" class="nbh-map"></div>
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "nbhmapmain");?>

  <div class="align-center">
    <span id="nbh-coords-lat"></span>
    <span id="nbh-coords-lon"></span> |
    <span id="nbh-radius"></span> km
  </div>

<script>
    let MIN_RADIUS = <?=$view->minRadius?>;
    let MAX_RADIUS = <?=$view->maxRadius?>;

    $(document).ready(function(){
        draw();
        <?php if ($view->coordsOK == 1) { ?>
        $('#nbh-coords-lat').text(latToText($('#input-lat').val()));
        $('#nbh-coords-lon').text(lonToText($('#input-lon').val()));
        $('#nbh-radius').text($('#input-radius').val());
        <?php } // end if coordsOK ?>
    });

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

    function draw(){
      let map = dynamicMapParams_nbhmapmain.map;
      let circleOptionsTemplate = {
            fillColor: '#ffff00',
            fillOpacity: 0.35,
            strokeWeight: 3,
            clickable: true,
            editable: true,
            draggable: false,
        };
    let drawingManager = new google.maps.drawing.DrawingManager({
          drawingMode: 'circle',      // nothing is drown by default
          drawingControl: false,  // hide drawing controls
          circleOptions: circleOptionsTemplate,
          map: map,
    });

    <?php if ($view->coordsOK == 1) { ?>
    drawingManager.setDrawingMode(null);
    let circle = new google.maps.Circle(circleOptionsTemplate);
    circle.setCenter({
        lat: <?=$view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLatitude()?>,
        lng: <?=$view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLongitude()?>
        });
    circle.setRadius(<?=$view->neighbourhoodsList[$view->selectedNbh]->getRadius()?> * 1000);
    circle.setMap(map);
    map.fitBounds(circle.getBounds());
    google.maps.event.addListener(circle, 'radius_changed', function (){
        let newRadius = Math.round(circle.getRadius() / 1000);
        if (newRadius > MAX_RADIUS) {
            newRadius = MAX_RADIUS;
            circle.setRadius(MAX_RADIUS * 1000);
            } else if (newRadius < MIN_RADIUS) {
                newRadius = MIN_RADIUS;
                circle.setRadius(MIN_RADIUS * 1000);
            }
        $('#input-radius').val(newRadius);
        $('#nbh-radius').text(newRadius);
    });
    google.maps.event.addListener(circle, 'center_changed', function (){
        $('#input-lat').val(circle.getCenter().lat());
        $('#input-lon').val(circle.getCenter().lng());
        $('#nbh-coords-lat').text(latToText(circle.getCenter().lat()));
        $('#nbh-coords-lon').text(lonToText(circle.getCenter().lng()));
    });

    <?php } else { ?>
    google.maps.event.addListener(drawingManager, 'circlecomplete', function(circle) {
        drawingManager.setDrawingMode(null);
        let newRadius = Math.round(circle.getRadius() / 1000);
        if (newRadius > MAX_RADIUS) {
            newRadius = MAX_RADIUS;
            circle.setRadius(MAX_RADIUS * 1000);
            } else if (newRadius < MIN_RADIUS) {
                newRadius = MIN_RADIUS;
                circle.setRadius(MIN_RADIUS * 1000);
            }
        $('#input-radius').val(newRadius);
        $('#nbh-radius').text(newRadius);
        $('#input-lat').val(circle.getCenter().lat());
        $('#input-lon').val(circle.getCenter().lng());
        $('#nbh-coords-lat').text(latToText(circle.getCenter().lat()));
        $('#nbh-coords-lon').text(lonToText(circle.getCenter().lng()));
    google.maps.event.addListener(circle, 'radius_changed', function (){
            let newRadius = Math.round(circle.getRadius() / 1000);
            if (newRadius > MAX_RADIUS) {
                newRadius = MAX_RADIUS;
                circle.setRadius(MAX_RADIUS * 1000);
                } else if (newRadius < MIN_RADIUS) {
                    newRadius = MIN_RADIUS;
                    circle.setRadius(MIN_RADIUS * 1000);
                }
            $('#input-radius').val(newRadius);
            $('#nbh-radius').text(newRadius);
        });
        google.maps.event.addListener(circle, 'center_changed', function (){
            $('#input-lat').val(circle.getCenter().lat());
            $('#input-lon').val(circle.getCenter().lng());
            $('#nbh-coords-lat').text(latToText(circle.getCenter().lat()));
            $('#nbh-coords-lon').text(lonToText(circle.getCenter().lng()));
        });
      });
    <?php } // end if coordsOK ?>
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
  <div class="btn-group">
    <button class="btn btn-primary btn-md"><?=tr('save_changes')?></button>
    <a href="/mywatches.php?action=emailSettings" class="btn btn-default btn-md"><?=tr('settings_notifications')?></a>
    <a href="<?=SimpleRouter::getLink('MyNeighbourhood', 'index', $view->selectedNbh) ?>" class="btn btn-default btn-md"><?=tr('exit_config')?></a>
  </div>
<?php } else { // Additional Myneighbourhood area ?>
    <fieldset>
    <legend><?=tr('myn_name')?></legend>
    <input type="text" name="name" id="input-name" class="ui-widget ui-widget-content ui-corner-all" value="<?=($view->selectedNbh == -1) ? '' : $view->neighbourhoodsList[$view->selectedNbh]->getName()?>" maxlength="16" required>
    </fieldset>
  <div class="buffer"></div>
  <div class="btn-group">
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