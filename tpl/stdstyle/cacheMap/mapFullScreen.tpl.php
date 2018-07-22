<?php
  use Utils\Uri\SimpleRouter;
?>
<div id="mapCanvasFullScreen"></div>

<div id="mapControlsContainer">

    <div id="layerSwitcher" class="ol-control">
      <select></select>
    </div>

    <div id="mapZoom" class="ol-control">
      <img id="mapZoomIn" src="/images/icons/plus.svg">
      <img id="mapZoomOut" src="/images/icons/minus.svg">
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
      <input id="searchControlInput" type="text" size="10" />
      <input id="searchControlButton" value="<?=$tr('search')?>" type="button" />
       -->

      <a href="<?=SimpleRouter::getLink("CacheMap", "embeded")?>">
        <img id="fullscreenToggle" src="/images/fullscreen-off.png"
           title="<?=$tr('disable_fullscreen')?>" />
      </a>

      <img id="refreshButton" src="/images/refresh.png"
           title="<?=$tr('refresh_map')?>" />

      <img id="gpsPosition" src="/images/map_geolocation_0.png" title="<?=$tr('where_i_am')?>" />

      <img id="filtersToggle" src="/okapi/static/tilemap/legend_other.png"
           title="<?=$tr('toggle_filters')?>" />
    </div>

    <div id="mapFilters" class="noprint ol-control mapFiltersFullScreen">
      <?php if( isset($view->cacheSet) ) { ?>
        <div class="mapFiltersHeader"><?=$tr('gp_mainTitile')?></div>
        <div class="mapFiltersControls">
          <input id="powerTrailSelection" type="checkbox" checked  />
          <label for="powerTrailSelection">
            <a href='<?=$view->cacheSet->getUrl()?>' title='<?=$view->cacheSet->getName()?>' target='_blank'>
              <img width="20" height="20" src="<?=$view->cacheSet->getIcon()?>" alt="<?=$tr('gp_mainTitile')?>" title='<?=$view->cacheSet->getName()?>' />
              <?=$view->cacheSet->getName()?>
            </a>
          </label>
        </div>
      <?php } //if-$view->ptFilterEnabled ?>

      <div class="mapFiltersHeader"><?=$tr('hide_caches_type')?></div>
      <div class="mapFiltersControls">
        <div>
          <input id="h_t" type="checkbox" />
          <label for="h_t"><?=$tr('traditional')?>
            <img src='/okapi/static/tilemap/legend_traditional.png'/>
          </label>

          <input id="h_m" type="checkbox" />
          <label for="h_m"><?=$tr('multicache')?>
            <img src='/okapi/static/tilemap/legend_multi.png'/>
          </label>

          <input id="h_q" type="checkbox" />
          <label for="h_q"><?=$tr('quiz')?>
            <img src='/okapi/static/tilemap/legend_quiz.png'/>
          </label>

          <input id="h_v" type="checkbox" />
          <label for="h_v"><?=$tr('virtual')?>
            <img src='/okapi/static/tilemap/legend_virtual.png'/>
          </label>

          <input id="h_e" type="checkbox" />
          <label for="h_e"><?=$tr('event')?>
            <img src='/okapi/static/tilemap/legend_event.png'/>
          </label>
        </div>
        <div>
          <input id="h_u" type="checkbox" />
          <label for="h_u"><?=$tr('unknown_type')?>
            <img src='/okapi/static/tilemap/legend_unknown.png'/>
          </label>

          <input id="h_w" type="checkbox" />
          <label for="h_w">Webcam
            <img src='/okapi/static/tilemap/legend_webcam.png'/>
          </label>

          <input id="h_o" type="checkbox" />
          <label for="h_o"><?=$tr('moving')?>
            <img src='/okapi/static/tilemap/legend_moving.png'/>
          </label>

          <input id="h_owncache" type="checkbox" />
          <label for="h_owncache"><?=$tr('owncache')?>
            <img src='/okapi/static/tilemap/legend_own.png'/>
          </label>
        </div>
      </div>

      <div class="mapFiltersHeader"><?=$tr('hide_caches')?></div>
      <div class="mapFiltersControls">
          <div>
            <input id="h_ignored" type="checkbox" />
            <label for="h_ignored"><?=$tr('ignored')?></label>
            <input id="h_own" type="checkbox" />
            <label for="h_own"><?=$tr('own')?></label>
            <input id="h_found" type="checkbox" />
            <label for="h_found"><?=$tr('founds')?></label>
            <input id="h_noattempt" type="checkbox" />
            <label for="h_noattempt"><?=$tr('not_yet_found')?></label>
            <input id="h_nogeokret" type="checkbox" />
            <label for="h_nogeokret"><?=$tr('without_geokret')?></label>
          </div>
          <div>
            <input id="h_temp_unavail" type="checkbox" />
            <label for="h_temp_unavail"><?=$tr('temp_unavailables')?></label>
            <input id="h_arch" type="checkbox" />
            <label for="h_arch"><?=$tr('archived_plural')?></label>
            <input id="be_ftf" type="checkbox" />
            <label for="be_ftf"><?=$tr('map_01')?></label>
            <input id="powertrail_only" type="checkbox" />
            <label for="powertrail_only"><?=$tr('map_05')?></label>
          </div>
          <div>
            <?=$tr('map_02')?>
            <select id="min_score">
              <option value="-3"><?=$tr('map_03')?></option>
              <!--<option value="0.5" {min_sel2}>pomiń najsłabsze skrzynki</option>-->
              <option value="1.2"><?=$tr('rating_ge_average')?></option>
              <option value="2"><?=$tr('rating_ge_good')?></option>
              <option value="2.5"><?=$tr('rating_ge_excellent')?></option>
            </select>
          </div>

          <input id="h_noscore" type="checkbox" />
          <label for="h_noscore"><?=$tr('map_04')?></label>

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
  centerOn: null,                     // { lat: 123, lon:123 }
  extMapConfigs: <?=$view->extMapConfigs?>,
};

$(function() {
  var ocMap = null; // global map object
  mapEntryPoint(ocMap, "mapCanvasFullScreen");
})

</script>
