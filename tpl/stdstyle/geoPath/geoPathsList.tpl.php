<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>

<div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="geoPath" title="geoPaths">
    {{gp_mainTitile}}
</div>



<div id="mapContainer">

    <a id="mapFullScreen" href="cachemap-full.php?pt={powerTrailId}&lat={mapCenterLat}&lon={mapCenterLon}&calledFromPt=1" >
      <img src="images/fullscreen.png" alt="Pełny ekran">
    </a>

    <div id="map-canvas"></div>
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
                <a href="powerTrail.php?ptAction=showSerie&ptrail=<?=$gp->getId()?>">
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
