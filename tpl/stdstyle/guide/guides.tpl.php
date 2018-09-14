
<div class="content2-pagetitle">
  <?=tr('guides_title')?>
</div>

<div>
<p><?=tr('guides_intro')?></p>

<p><?=tr('guides_listHeader')?></p>

<p>
    <ul>
      <li><?=tr('guides_toKnowMore')?></li>
      <li><?=tr('guides_howToRegisterAndFound')?></li>
      <li><?=tr('guides_visitOutdoor')?></li>
    </ul>
</p>

<p><?=tr('guides_howToContact')?></p>

<p>
  <?=tr('guides_mapHeader')?>&nbsp;
  <?=$view->guidesNumber?>&nbsp;
  <?=tr('guides_mapHeaderEnd')?>
</p>

</div>

<div id="mapContainer">
    <div id="mapCanvas"></div>
</div>

<p>
  <img src="/images/rating-star.png" alt="rekomendacje" title="rekomendacje">
  <?=tr('guru_09')?>
</p>

<p>
  <?=tr('guides_howToBecomeGuide')?>
  <ul>
    <li><?=tr('guides_becomingGuideCond')?></li>
  </ul>
</p>

<p>
  <?=tr('guides_guideSetInProfile')?>
  <a class="links" href="/myprofile.php?action=change"><?=tr('guides_profileLink')?></a>.
  <?=tr('guides_thanks')?>
</p>

<?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvas");?>




