<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.27&amp;key=<?=$view->gMapKey?>&amp;language=<?=$view->getLang()?>"></script>
<link rel="stylesheet" href="<?=$view->geoPathList_css?>">



<div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="geoPath" title="geoPaths">
    {{gp_mainTitile}}
</div>



<div id="mapContainer">
    <div id="mapCanvas"></div>
</div>

<hr/>

<div>
    <table>
        <tr>
            <th>{{gp_name}}</th>
            <th>{{gp_type}}</th>
            <th>{{gp_publicationDate}}</th>
            <th>{{gp_status}}</th>
            <th>{{gp_cachesNumber}}</th>
            <th>{{gp_gainedCount}}</th>
        </tr>

        <?php foreach($view->geoPathList as $gp) { ?>
        <tr>
            <td>
                <a href="geoPathDetails.php?gpId=<?=$gp->getId()?>">
                    <?=$gp->getName()?>
                </a>
            </td>
            <td>
                <img src="<?=$gp->getIcon()?>" alt="geoPathIcon">
                <?=$gp->getTypeTranslation()?>
            </td>
            <td><?=$gp->getStatusTranslation()?></td>
            <td><?=$gp->getCreationDateString()?></td>
            <td><?=$gp->getCacheCount()?></td>
            <td><?=$gp->getGainedCount()?></td>
        </tr>

        <?php } //foreach ?>
    </table>
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

