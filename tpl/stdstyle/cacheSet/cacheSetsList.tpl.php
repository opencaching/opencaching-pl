

<div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="geoPath" title="cache sets">
    {{gp_mainTitile}}
</div>

<div id="mapContainer">
    <div id="mapCanvas"></div>
</div>

<hr/>

<div>
    <table>
        <tr>
            <th>{{cs_name}}</th>
            <th>{{cs_type}}</th>
            <th>{{cs_publicationDate}}</th>
            <th>{{cs_status}}</th>
            <th>{{cs_cachesNumber}}</th>
            <th>{{cs_gainedCount}}</th>
        </tr>

        <?php foreach($view->cacheSetList as $cs) { ?>
        <tr>
            <td>
                <a href="cacheSetDetails.php?csId=<?=$cs->getId()?>">
                    <?=$cs->getName()?>
                </a>
            </td>
            <td>
                <img src="<?=$cs->getIcon()?>" alt="cacheSetIcon">
                <?=$cs->getTypeTranslation()?>
            </td>
            <td><?=$cs->getStatusTranslation()?></td>
            <td><?=$cs->getCreationDateString()?></td>
            <td><?=$cs->getCacheCount()?></td>
            <td><?=$cs->getGainedCount()?></td>
        </tr>

        <?php } //foreach ?>
    </table>

    <?php $view->callChunk('pagination', $view->paginationModel); ?>

</div>



<script type="text/javascript">

var myLatlng = new google.maps.LatLng(54, 18);

var mapOptions = {
    zoom: 6, //mapZoom,
    //zoomControl: {zoomControl},
    //scrollwheel: {scrollwheel},
    //scaleControl: {scaleControl},
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
    //mapTypeControlOptions: {
    //    mapTypeIds: mapTypeIds
    //}
}

map = new google.maps.Map(document.getElementById('mapCanvas'), mapOptions);

</script>

