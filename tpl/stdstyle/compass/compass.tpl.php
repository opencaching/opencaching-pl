<?php ?>

<div id="mapid"></div>

<script src="<?=$view->leafletJs?>"></script>
<script src="<?=$view->compassJs?>"></script>
<script>
  cacheCoords = L.latLng(<?=$view->cache->getCoordinates()->getLatitude()?>, <?=$view->cache->getCoordinates()->getLongitude()?>)
  var cacheBaloon = "<img src=\"<?=$view->cache->getCacheIcon()?>\" height=\"16\" width=\"16\" alt=\"<?=tr($view->cache->getCacheTypeTranslationKey())?>\"> ";
    cacheBaloon += "<strong><?=$view->cache->getWaypointId()?></strong> ";
    cacheBaloon += " - <?=tr($view->cache->getSizeTranslationKey())?> ";
    cacheBaloon += "<img src=\"<?=$view->cache->getDifficultyIcon()?>\" alt=\"<?php echo ($view->cache->getDifficulty() / 2) . ' ' . tr('out_of') . ' 5.0';?> \ \" title=\"<?php echo ($view->cache->getDifficulty() / 2) . ' ' . tr('out_of') . ' 5.0';?>\"> ";
    cacheBaloon += "<img src=\"<?=$view->cache->getTerrainIcon()?>\" alt=\"<?php echo ($view->cache->getTerrain() / 2) . ' ' . tr('out_of') . ' 5.0';?> \ \" title=\"<?php echo ($view->cache->getTerrain() / 2) . ' ' . tr('out_of') . ' 5.0';?>\"><br>";
    cacheBaloon += "<?=$view->cache->getCacheName()?>";
  var cacheMarker = L.marker(cacheCoords).addTo(map)
    .bindPopup(cacheBaloon).openPopup();
  translDistance = "{{Distance}}";
  translBearing = "{{notify_lbl_direction}}";
</script>
