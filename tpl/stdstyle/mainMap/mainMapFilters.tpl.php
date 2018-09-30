

<div class="mapFilterContainer">
    <div class="mapFiltersHeader"><?=tr('hide_caches_type')?></div>
    <div class="mapFiltersControls">
        <div class="col">
          <div>
            <input id="exTypeTraditional" type="checkbox" class="filterParam">
            <label for="exTypeTraditional">
              <?=tr('traditional')?>
              <img src="/okapi/static/tilemap/legend_traditional.png" alt="<?=tr('traditional')?>">
            </label>
          </div>
          <div>
            <input id="exTypeMulti" type="checkbox" class="filterParam">
            <label for="exTypeMulti">
              <?=tr('multicache')?>
              <img src="/okapi/static/tilemap/legend_multi.png" alt="<?=tr('multicache')?>">
            </label>
          </div>
          <div>
            <input id="exTypeQuiz" type="checkbox" class="filterParam">
            <label for="exTypeQuiz">
              <?=tr('quiz')?>
              <img src="/okapi/static/tilemap/legend_quiz.png" alt="<?=tr('quiz')?>">
            </label>
          </div>
        </div>
        <div class="col">
          <div>
            <input id="exTypeVirtual" type="checkbox" class="filterParam">
            <label for="exTypeVirtual">
              <?=tr('virtual')?>
              <img src="/okapi/static/tilemap/legend_virtual.png" alt="<?=tr('virtual')?>">
            </label>
          </div>
          <div>
            <input id="exTypeEvent" type="checkbox" class="filterParam">
            <label for="exTypeEvent">
              <?=tr('event')?>
              <img src="/okapi/static/tilemap/legend_event.png" alt="<?=tr('event')?>">
            </label>
          </div>
          <div>
            <input id="exTypeOther" type="checkbox" class="filterParam">
            <label for="exTypeOther">
              <?=tr('unknown_type')?>
              <img src="/okapi/static/tilemap/legend_unknown.png" alt="<?=tr('unknown_type')?>">
            </label>
          </div>
        </div>
        <div class="col">
          <div>
            <input id="exTypeWebcam" type="checkbox" class="filterParam">
            <label for="exTypeWebcam">
              <?=tr('webcam')?>
              <img src="/okapi/static/tilemap/legend_webcam.png" alt="<?=tr('webcam')?>">
            </label>
          </div>
          <div>
            <input id="exTypeMoving" type="checkbox" class="filterParam">
            <label for="exTypeMoving">
              <?=tr('moving')?>
              <img src="/okapi/static/tilemap/legend_moving.png" alt="<?=tr('moving')?>">
            </label>
          </div>
          <div>
            <input id="exTypeOwn" type="checkbox" class="filterParam">
            <label for="exTypeOwn">
              <?=tr('owncache')?>
              <img src="/okapi/static/tilemap/legend_own.png" alt="<?=tr('owncache')?>">
            </label>
          </div>
        </div>
    </div>
</div>

<div class="mapFilterContainer">
    <div class="mapFiltersHeader"><?=tr('hide_caches')?></div>
    <div class="mapFiltersControls">
        <div class="col">
          <div>
            <input id="exIgnored" type="checkbox" class="filterParam">
            <label for="exIgnored"><?=tr('ignored')?></label>
          </div>
          <div>
            <input id="exMyOwn" type="checkbox" class="filterParam">
            <label for="exMyOwn"><?=tr('own')?></label>
          </div>
          <div>
            <input id="exFound" type="checkbox" class="filterParam">
            <label for="exFound"><?=tr('founds')?></label>
          </div>
          <div>
            <input id="exNoYetFound" type="checkbox" class="filterParam">
            <label for="exNoYetFound"><?=tr('not_yet_found')?></label>
          </div>

          <div>
            <input id="exNoGeokret" type="checkbox" class="filterParam">
            <label for="exNoGeokret"><?=tr('without_geokret')?></label>
          </div>
        </div>

        <div class="col">
          <div>
            <input id="exTempUnavail" type="checkbox" class="filterParam">
            <label for="exTempUnavail"><?=tr('temp_unavailables')?></label>
          </div>
          <div>
            <input id="exArchived" type="checkbox" class="filterParam">
            <label for="exArchived"><?=tr('archived_plural')?></label>
          </div>
        </div>
    </div>
</div>

<div class="mapFilterContainer">
    <div class="mapFiltersHeader"><?=tr('hide_caches')?></div>
    <div class="mapFiltersControls">

        <div class="col">
            <div>
              <input id="ftfHunter" type="checkbox" class="filterParam">
              <label for="ftfHunter"><?=tr('map_01')?></label>
            </div>
            <div>
              <input id="powertrailOnly" type="checkbox" class="filterParam">
              <label for="powertrailOnly"><?=tr('map_05')?></label>
            </div>

            <?=tr('map_02')?>
            <select id="rating" class="filterParam">
                <option value="1-5|X"><?=tr('map_03')?></option>
                <option value="2-5|X"><?=tr('rating_ge_average')?></option>
                <option value="3-5|X"><?=tr('rating_ge_good')?></option>
                <option value="4-5|X"><?=tr('rating_ge_excellent')?></option>
            </select>
        </div>
    </div>
</div>

<?php if( isset($view->cacheSet) ) { ?>
<div class="mapFilterContainer">
    <div class="mapFiltersHeader"><?=tr('gp_mainTitile')?></div>
    <div class="mapFiltersControls">
      <div class="col">
        <div>
          <input id="csId" type="hidden" value="<?=$view->cacheSet->getId()?>">
          <input id="csEnabled" type="checkbox" checked="checked">
          <label for="csEnabled">
            <a href='<?=$view->cacheSet->getUrl()?>'
              title='<?=$view->cacheSet->getName()?>' target='_blank'>
              <img width="20px" height="20px" src="<?=$view->cacheSet->getIcon()?>"
                   alt="<?=tr('gp_mainTitile')?>"
                   title="<?=$view->cacheSet->getName()?>">
              <?=$view->cacheSet->getName()?>
            </a>
          </label>
        </div>
      </div>
    </div>
</div>
<?php } //if-$view->ptFilterEnabled ?>

