<?php
use src\Models\GeoCache\GeoCache;
?>

<div class="mapFilterContainer">
    <div class="mapFiltersHeader"><?=tr('map_excludeCachesByType')?></div>
    <div class="mapFiltersControls">
        <div class="col">
          <div>
            <input id="exTypeTraditional" type="checkbox" class="filterParam">
            <label for="exTypeTraditional">
              <?=tr('cacheType_2')?>
              <img src="/okapi/static/tilemap/legend_traditional.png"
                   alt="<?=tr('cacheType_2')?>">
            </label>
          </div>
          <div>
            <input id="exTypeMulti" type="checkbox" class="filterParam">
            <label for="exTypeMulti">
              <?=tr('cacheType_3')?>
              <img src="/okapi/static/tilemap/legend_multi.png"
                   alt="<?=tr('cacheType_3')?>">
            </label>
          </div>
          <div>
            <input id="exTypeQuiz" type="checkbox" class="filterParam">
            <label for="exTypeQuiz">
              <?=tr('cacheType_7')?>
              <img src="/okapi/static/tilemap/legend_quiz.png"
                   alt="<?=tr('cacheType_7')?>">
            </label>
          </div>
          <div>
            <input id="exTypeGuestbook" type="checkbox" class="filterParam">
            <label for="exTypeGuestbook">
              <?=tr('cacheType_13')?>
              <img src="/okapi/static/tilemap/legend_guestbook.png"
                   alt="<?=tr('cacheType_13')?>">
            </label>
          </div>
          <div>
            <input id="exTypeBitcache" type="checkbox" class="filterParam">
            <label for="exTypeBitcache">
              <?=tr('cacheType_1')?>
              <img src="/okapi/static/tilemap/legend_bitcache.png"
                   alt="<?=tr('cacheType_12')?>">
            </label>
          </div>
        </div>
        <div class="col">
          <div>
            <input id="exTypeVirtual" type="checkbox" class="filterParam">
            <label for="exTypeVirtual">
              <?=tr('cacheType_4')?>
              <img src="/okapi/static/tilemap/legend_virtual.png"
                   alt="<?=tr('cacheType_4')?>">
            </label>
          </div>
          <div>
            <input id="exTypeWebcam" type="checkbox" class="filterParam">
            <label for="exTypeWebcam">
              <?=tr('cacheType_5')?>
              <img src="/okapi/static/tilemap/legend_webcam.png"
                   alt="<?=tr('cacheType_5')?>">
            </label>
          </div>
          <div>
            <input id="exTypeBenchmark" type="checkbox" class="filterParam">
            <label for="exTypeBenchmark">
              <?=tr('cacheType_12')?>
              <img src="/okapi/static/tilemap/legend_benchmark.png"
                   alt="<?=tr('cacheType_12')?>">
            </label>
          </div>
          <div>
            <input id="exTypeEvent" type="checkbox" class="filterParam">
            <label for="exTypeEvent">
              <?=tr('cacheType_6')?>
              <img src="/okapi/static/tilemap/legend_event.png"
                   alt="<?=tr('cacheType_6')?>">
            </label>
          </div>
        </div>
        <div class="col">
          <div>
            <input id="exTypeOther" type="checkbox" class="filterParam">
            <label for="exTypeOther">
              <?=tr('cacheType_1')?>
              <img src="/okapi/static/tilemap/legend_unknown.png"
                   alt="<?=tr('cacheType_1')?>">
            </label>
          </div>
          <div>
            <input id="exTypeMoving" type="checkbox" class="filterParam">
            <label for="exTypeMoving">
              <?=tr('cacheType_9')?>
              <img src="/okapi/static/tilemap/legend_moving.png"
                   alt="<?=tr('cacheType_9')?>">
            </label>
          </div>
          <div>
            <input id="exTypeChallenge" type="checkbox" class="filterParam">
            <label for="exTypeChallenge">
              <?=tr('cacheType_15')?>
              <img src="/okapi/static/tilemap/legend_challenge.png"
                   alt="<?=tr('cacheType_15')?>">
            </label>
          </div>
          <div>
            <input id="exTypeOwn" type="checkbox" class="filterParam">
            <label for="exTypeOwn">
              <?=tr('cacheType_11')?>
              <img src="/okapi/static/tilemap/legend_own.png"
                   alt="<?=tr('cacheType_11')?>">
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
