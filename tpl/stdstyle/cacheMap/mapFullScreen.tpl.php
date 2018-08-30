<?php
  use Utils\Uri\SimpleRouter;
?>
<div id="mapCanvasFullScreen"></div>

<div id="mapControlsContainer">

    <div id="layerSwitcher" class="ol-control">
      <select></select>
    </div>

    <div id="mapZoom" class="ol-control">
      <img id="mapZoomIn" src="/images/icons/plus.svg" alt="+">
      <img id="mapZoomOut" src="/images/icons/minus.svg" alt="-">
    </div>

    <div id="mousePosition" class="ol-control"></div>

    <div id="mapScale" class="ol-control"></div>

    <div id="mapClickMarker"></div>

    <div id="ocAttribution" class="ol-control"></div>

    <div id="mapPopup">
      <a href="#" id="mapPopup-closer"></a>
      <div id="mapPopup-content"></div>
    </div>

    <div id="cacheInforBallonTpl">
        <?=$view->_callTemplate("/cacheMap/cacheInfoBalloon")?>
    </div>

    <div id="controlCombo" class="noprint ol-control">
      <!--
        // search temporary disabled
      <input id="searchControlInput" type="text" size="10">
      <input id="searchControlButton" value="<?=$tr('search')?>" type="button">
       -->

        <img id="fullscreenToggle" src="/images/fullscreen-off.png"
           title="<?=$tr('disable_fullscreen')?>" alt="<?=$tr('disable_fullscreen')?>">

      <img id="refreshButton" src="/images/refresh.png"
           title="<?=$tr('refresh_map')?>" alt="<?=$tr('refresh_map')?>">

      <img id="gpsPosition" src="/images/map_geolocation_0.png" title="<?=$tr('where_i_am')?>" alt="<?=$tr('where_i_am')?>">

      <img id="filtersToggle" src="/okapi/static/tilemap/legend_other.png"
           title="<?=$tr('toggle_filters')?>" alt="<?=$tr('toggle_filters')?>">
    </div>

    <div id="mapFilters" class="noprint ol-control mapFiltersFullScreen">
      <?php if( isset($view->cacheSet) ) { ?>
        <div class="mapFiltersHeader"><?=$tr('gp_mainTitile')?></div>

        <div class="mapFiltersControls">
          <input id="powerTrailSelection" type="checkbox" checked >
          <label for="powerTrailSelection">
            <a href='<?=$view->cacheSet->getUrl()?>' title='<?=$view->cacheSet->getName()?>'
               target='_blank'>
              <img width="20px" height="20px" src="<?=$view->cacheSet->getIcon()?>"
                   alt="<?=$tr('gp_mainTitile')?>" title="<?=$view->cacheSet->getName()?>">
              <?=$view->cacheSet->getName()?>
            </a>
          </label>
        </div>

      <?php } //if-$view->ptFilterEnabled ?>

      <div class="mapFiltersHeader"><?=$tr('hide_caches_type')?></div>
      <div class="mapFiltersControls">
        <div class="row">
            <div class="col">
              <div>
                <input id="h_t" type="checkbox">
                <label for="h_t">
                  <?=$tr('traditional')?>
                  <img src="/okapi/static/tilemap/legend_traditional.png" alt="<?=$tr('traditional')?>">
                </label>
              </div>
              <div>
                <input id="h_m" type="checkbox">
                <label for="h_m">
                  <?=$tr('multicache')?>
                  <img src="/okapi/static/tilemap/legend_multi.png" alt="<?=$tr('multicache')?>">
                  </label>
              </div>
              <div>
                <input id="h_q" type="checkbox">
                <label for="h_q">
                  <?=$tr('quiz')?>
                  <img src="/okapi/static/tilemap/legend_quiz.png" alt="<?=$tr('quiz')?>">
                </label>
              </div>
              <div>
                <input id="h_v" type="checkbox">
                <label for="h_v">
                  <?=$tr('virtual')?>
                  <img src="/okapi/static/tilemap/legend_virtual.png" alt="<?=$tr('virtual')?>">
                </label>
              </div>
              <div>
                <input id="h_e" type="checkbox">
                <label for="h_e">
                  <?=$tr('event')?>
                  <img src="/okapi/static/tilemap/legend_event.png" alt="<?=$tr('event')?>">
                </label>
              </div>
            </div>

            <div class="col">
              <div>
                <input id="h_u" type="checkbox">
                <label for="h_u">
                  <?=$tr('unknown_type')?>
                  <img src="/okapi/static/tilemap/legend_unknown.png" alt="<?=$tr('unknown_type')?>">
                  </label>
              </div>
              <div>
                <input id="h_w" type="checkbox">
                <label for="h_w">
                  <?=$tr('webcam')?>
                  <img src="/okapi/static/tilemap/legend_webcam.png" alt="<?=$tr('webcam')?>">
                </label>
              </div>
              <div>
                <input id="h_o" type="checkbox">
                <label for="h_o">
                  <?=$tr('moving')?>
                  <img src="/okapi/static/tilemap/legend_moving.png" alt="<?=$tr('moving')?>">
                </label>
              </div>
              <div>
                <input id="h_owncache" type="checkbox">
                <label for="h_owncache">
                  <?=$tr('owncache')?>
                  <img src="/okapi/static/tilemap/legend_own.png" alt="<?=$tr('owncache')?>">
                </label>
              </div>
            </div>
        </div>
      </div>

      <div class="mapFiltersHeader"><?=$tr('hide_caches')?></div>
      <div class="mapFiltersControls">
        <div class="row">
          <div class="col">
            <div>
              <input id="h_ignored" type="checkbox">
              <label for="h_ignored"><?=$tr('ignored')?></label>
            </div>
            <div>
              <input id="h_own" type="checkbox">
              <label for="h_own"><?=$tr('own')?></label>
            </div>
            <div>
              <input id="h_found" type="checkbox">
              <label for="h_found"><?=$tr('founds')?></label>
            </div>
            <div>
              <input id="h_noattempt" type="checkbox">
              <label for="h_noattempt"><?=$tr('not_yet_found')?></label>
            </div>
            <div>
              <input id="h_nogeokret" type="checkbox">
              <label for="h_nogeokret"><?=$tr('without_geokret')?></label>
            </div>
          </div>
          <div class="col">
            <div>
                <input id="h_temp_unavail" type="checkbox">
                <label for="h_temp_unavail"><?=$tr('temp_unavailables')?></label>
            </div>
            <div>
                <input id="h_arch" type="checkbox">
                <label for="h_arch"><?=$tr('archived_plural')?></label>
            </div>
            <div>
                <input id="be_ftf" type="checkbox">
                <label for="be_ftf"><?=$tr('map_01')?></label>
            </div>
            <div>
                <input id="powertrail_only" type="checkbox">
                <label for="powertrail_only"><?=$tr('map_05')?></label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <?=$tr('map_02')?>
            <select id="min_score">
              <option value="-3"><?=$tr('map_03')?></option>
              <!--<option value="0.5" {min_sel2}>pomiń najsłabsze skrzynki</option>-->
              <option value="1.2"><?=$tr('rating_ge_average')?></option>
              <option value="2"><?=$tr('rating_ge_good')?></option>
              <option value="2.5"><?=$tr('rating_ge_excellent')?></option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <div>
                <input id="h_noscore" type="checkbox">
                <label for="h_noscore"><?=$tr('map_04')?></label>
            </div>
          </div>
        </div>

      </div>
    </div>
</div>

<script>

/* map params */
var ocMapInputParams = {
  userId:     <?=$view->mapUserId?>,
  searchData: <?= isset($view->searchData)?$view->searchData:"null"?>,
  powertrailIds: null,                //TODO
  userSettings: <?=$view->filterVal?>,
  fitToBounds: null,                  // { minLat: 123, maxLat: 123, minLon: 123, maxLon: 123 }
  centerOn: <?=$view->centerOn?>,                     // { lat: 123, lon:123 }
  mapStartZoom: <?=$view->mapStartZoom?>,
  extMapConfigs: <?=$view->extMapConfigs?>,
  fullScreenToggleUri: '<?=SimpleRouter::getLink('CacheMap','embeded')?>',
};

$(function() {
  var ocMap = null; // global map object
  mapEntryPoint(ocMap, "mapCanvasFullScreen");
})

</script>
