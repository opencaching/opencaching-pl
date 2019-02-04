<?php
use lib\Objects\GeoCache\GeoCache;
?>

<div class="mapFilterContainer">
    <div class="mapFiltersHeader"><?=tr('map_excludeCachesByType')?></div>
    <div class="mapFiltersControls">
        <div class="col">
          <div>
            <input id="exTypeTraditional" type="checkbox" class="filterParam">
            <label for="exTypeTraditional">
              <?=tr('map_traditionalType')?>
              <img src="/okapi/static/tilemap/legend_traditional.png"
                   alt="<?=tr('map_traditionalType')?>">
            </label>
          </div>
          <div>
            <input id="exTypeMulti" type="checkbox" class="filterParam">
            <label for="exTypeMulti">
              <?=tr('map_multicacheType')?>
              <img src="/okapi/static/tilemap/legend_multi.png"
                   alt="<?=tr('map_multicacheType')?>">
            </label>
          </div>
          <div>
            <input id="exTypeQuiz" type="checkbox" class="filterParam">
            <label for="exTypeQuiz">
              <?=tr('map_quizType')?>
              <img src="/okapi/static/tilemap/legend_quiz.png"
                   alt="<?=tr('map_quizType')?>">
            </label>
          </div>
        </div>
        <div class="col">
          <div>
            <input id="exTypeVirtual" type="checkbox" class="filterParam">
            <label for="exTypeVirtual">
              <?=tr('map_virtualType')?>
              <img src="/okapi/static/tilemap/legend_virtual.png"
                   alt="<?=tr('map_virtualType')?>">
            </label>
          </div>
          <div>
            <input id="exTypeEvent" type="checkbox" class="filterParam">
            <label for="exTypeEvent">
              <?=tr('map_eventType')?>
              <img src="/okapi/static/tilemap/legend_event.png"
                   alt="<?=tr('map_eventType')?>">
            </label>
          </div>
          <div>
            <input id="exTypeOther" type="checkbox" class="filterParam">
            <label for="exTypeOther">
              <?=tr('map_otherType')?>
              <img src="/okapi/static/tilemap/legend_unknown.png"
                   alt="<?=tr('map_otherType')?>">
            </label>
          </div>
        </div>
        <div class="col">
          <div>
            <input id="exTypeWebcam" type="checkbox" class="filterParam">
            <label for="exTypeWebcam">
              <?=tr('map_webcamType')?>
              <img src="/okapi/static/tilemap/legend_webcam.png"
                   alt="<?=tr('map_webcamType')?>">
            </label>
          </div>
          <div>
            <input id="exTypeMoving" type="checkbox" class="filterParam">
            <label for="exTypeMoving">
              <?=tr('map_movingType')?>
              <img src="/okapi/static/tilemap/legend_moving.png"
                   alt="<?=tr('map_movingType')?>">
            </label>
          </div>
          <div>
            <input id="exTypeOwn" type="checkbox" class="filterParam">
            <label for="exTypeOwn">
              <?=tr('map_owncacheType')?>
              <img src="/okapi/static/tilemap/legend_own.png"
                   alt="<?=tr('map_owncacheType')?>">
            </label>
          </div>
        </div>
    </div>
</div>

<div class="mapFilterContainer">
    <div class="mapFiltersHeader"><?=tr('map_excludeCaches')?></div>
    <div class="mapFiltersControls">
        <div class="col">
          <div>
            <input id="exIgnored" type="checkbox" class="filterParam">
            <label for="exIgnored"><?=tr('map_excludeIgnored')?></label>
          </div>
          <div>
            <input id="exMyOwn" type="checkbox" class="filterParam">
            <label for="exMyOwn"><?=tr('map_excludeOwned')?></label>
          </div>
          <div>
            <input id="exFound" type="checkbox" class="filterParam">
            <label for="exFound"><?=tr('map_excludeFound')?></label>
          </div>
          <div>
            <input id="exNoYetFound" type="checkbox" class="filterParam">
            <label for="exNoYetFound"><?=tr('map_excludeNotYetFound')?></label>
          </div>

          <div>
            <input id="exNoGeokret" type="checkbox" class="filterParam">
            <label for="exNoGeokret"><?=tr('map_excludeWithoutGeokret')?></label>
          </div>
        </div>

        <div class="col">
          <div>
            <input id="exTempUnavail" type="checkbox" class="filterParam">
            <label for="exTempUnavail"><?=tr('map_excludeTempUnavailable')?></label>
          </div>
          <div>
            <input id="exArchived" type="checkbox" class="filterParam">
            <label for="exArchived"><?=tr('map_excludeArchived_plural')?></label>
          </div>
          <div>
            <input id="exWithoutRecommendation" type="checkbox" class="filterParam">
            <label for="exWithoutRecommendation"><?=tr('map_exWithoutRecomendation')?></label>
          </div>



        </div>
    </div>
</div>

<div class="mapFilterContainer">
    <div class="mapFiltersHeader"><?=tr('map_displayOnlyCaches')?></div>
    <div class="mapFiltersControls">

        <div class="col">
            <div>
              <input id="ftfHunter" type="checkbox" class="filterParam">
              <label for="ftfHunter"><?=tr('map_onlyFtfs')?></label>
            </div>
            <div>
              <input id="powertrailOnly" type="checkbox" class="filterParam">
              <label for="powertrailOnly"><?=tr('map_onlyCacheFromGeopaths')?></label>
            </div>
        </div>
        <div class="col">

            <div>
                <label for="rating"><?=tr('map_onlyWithMinScore')?></label>
                <select id="rating" class="filterParam">
                    <option value="1-5|X"><?=tr('map_scoreAny')?></option>
                    <option value="2-5|X"><?=tr('map_scoreGeAverange')?></option>
                    <option value="3-5|X"><?=tr('map_scoreGeGood')?></option>
                    <option value="4-5|X"><?=tr('map_scoreGeExcellent')?></option>
                </select>
            </div>

          <div>
            <label for="size2"><?=tr('map_biggerSizeThan')?>:</label>
            <select id="size2" class="filterParam">
              <option value="any"><?=tr('map_sizeAny')?></option>
              <?php if ($view->nanoFilterEnabled) { ?>
                  <option value="nano"><?=lcfirst(tr(GeoCache::SIZE_NANO_TR_KEY))?></option>
              <?php } ?>
              <option value="micro"><?=lcfirst(tr(GeoCache::SIZE_MICRO_TR_KEY))?></option>
              <option value="small"><?=lcfirst(tr(GeoCache::SIZE_SMALL_TR_KEY))?></option>
              <option value="regular"><?=lcfirst(tr(GeoCache::SIZE_REGULAR_TR_KEY))?></option>
            </select>
          </div>

        </div>
    </div>
</div>

<?php if( isset($view->cacheSet) ) { ?>
<div class="mapFilterContainer">
    <div class="mapFiltersHeader"><?=tr('map_csFilter')?></div>
    <div class="mapFiltersControls">
      <div class="col">
        <div>
          <input id="csId" type="hidden" value="<?=$view->cacheSet->getId()?>">
          <input id="csEnabled" type="checkbox" checked="checked">
          <label for="csEnabled">
            <a href='<?=$view->cacheSet->getUrl()?>'
              title='<?=$view->cacheSet->getName()?>' target='_blank'>
              <img width="20px" height="20px" src="<?=$view->cacheSet->getIcon()?>"
                   alt="<?=tr('map_csFilter')?>"
                   title="<?=$view->cacheSet->getName()?>">
              <?=$view->cacheSet->getName()?>
            </a>
          </label>
        </div>
      </div>
    </div>
</div>
<?php } //if-$view->ptFilterEnabled ?>
