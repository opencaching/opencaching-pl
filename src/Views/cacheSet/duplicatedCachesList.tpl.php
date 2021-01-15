<?php
use src\Models\GeoCache\GeoCache;
use src\Models\PowerTrail\PowerTrail;
?>
<style>
table {
    margin-right: 5px;
}

table, th, td {
  border: 1px solid black;
  padding: 5px;
}
table.center {
  margin-left: auto;
  margin-right: auto;
}
.gpdups {
  list-style-type: none; /* Remove bullets */
  padding: 5px;
}

</style>

<table >
  <tr>
    <th>cacheId</th>
    <th>waypoint</th>
    <th>cacheName</th>
    <th>owner</th>
    <th>geopaths</th>
  </tr>

<?php /* @var $c GeoCache */ ?>
<?php foreach($v->caches as $c) { ?>
<tr>
  <td><?=$c->getCacheId()?></td>
  <td><?=$c->getGeocacheWaypointId()?></td>
  <td><a href="<?=$c->getCacheUrl()?>"><?=$c->getCacheName()?></a></td>
  <td><?=$c->getOwner()->getUserName()?></td>
  <td>
    <ul>
    <?php /* @var $pt PowerTrail */ ?>
    <?php foreach ($v->pts[$c->getCacheId()] as $pt) { ?>
      <li class="gpdups">
        <button onclick="removeCacheFromGp(<?=$c->getCacheId()?>, <?=$pt->getId()?>)">
          Remove from [<?=$pt->getName()?>]
        </button>
        &nbsp(<a href="<?=$pt->getPowerTrailUrl()?>"><?=$pt->getName()?></a>)
      </li>
    <?php } ?>
    </ul>
  </td>

</tr>
<?php } ?>
</table>

<script type="text/javascript">

function removeCacheFromGp (cacheId, gpId) {
  $.ajax({
    type:  "get",
    cache: false,
    url:   "/Admin.CacheSetAdmin/removeDuplicatedCachesAjax/" + gpId + "/" + cacheId,
    error: function (xhr) {

        console.debug("removeDuplicatedCachesAjax: " + xhr.responseText);

    },
    success: function (data, status) {
      console.debug(data);

    }
  });
}
</script>
