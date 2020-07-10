<?php

use src\Utils\Uri\Uri;
use src\Utils\View\View;
use src\Models\ChunkModels\InteractiveMap\InteractiveMapModel;

/**
 * This chunk displays interactive map with different kinds of markers.
 * Markers should be passed by $mapModel.
 *
 * OpenLayers chunk should be loaded to header by:
 *  $this->view->addHeaderChunk('openLayers5');
 *
 */
return function (InteractiveMapModel $mapModel, $canvasId)
{
    $publicSrcPath = '/views/chunks/interactiveMap/';

    // load chunk CSS
    View::callChunkInline('loadCssByJs',
        Uri::getLinkWithModificationTime(
            $publicSrcPath . 'interactiveMap.css'
        )
    );
    View::callChunkInline('handlebarsJs');
?>

<script src="<?=Uri::getLinkWithModificationTime(
    $publicSrcPath . 'interactiveMap.js')?>"></script>

<!-- load markers scripts and popup templates -->
<?php
    // shared javascript marker libs to load
    $markerLibs = [
        $publicSrcPath . "markers/ocMarker.js"
    ];

    $markerLibsLoaded = false;
    $markerTypes = array_fill_keys($mapModel->getMarkerTypes(), true);
    $markerTypes["highlightedMarker"] = false;
    foreach ($markerTypes as $markerType => $loadTpl) {
        if (!$markerLibsLoaded) {
            $markerLibsLoaded = true;
            foreach ($markerLibs as $lib) {
?>
<script src="<?=Uri::getLinkWithModificationTime($lib)?>"></script>
<?php
            } //foreach-markerLibs
        } //if-markerLibsLoaded
        try {
            $markerJs = Uri::getLinkWithModificationTime(
                $publicSrcPath . 'markers/' . $mapModel->getMarkersFamily()
                . '/'. $markerType . '.js'
            );
?>
<script type="text/javascript" src="<?=$markerJs?>"></script>
<?php   } catch(Exception $ex) {
            /* ignore if a file does not exist */
        }
        if ($loadTpl) {
?>
<script type="text/x-handlebars-template" class="<?=$markerType?>" >
            <?php include(__DIR__.'/markers/'.$markerType.'Popup.tpl.php'); ?>
</script>
<?php   } //if-loadTpl
} //foreach-markerTypes ?>
<!-- end of load markers scripts popup templates -->

<script>
$(document).ready(function() {
    InteractiveMapServices.getInteractiveMap("<?=$canvasId?>", {
        targetDiv: "<?=$canvasId?>",
        centerOn: <?=$mapModel->getCoords()->getAsOpenLayersFormat()?>,
        mapStartZoom: <?=$mapModel->getZoom()?>,
        forceMapZoom: <?=$mapModel->isZoomForced()?'true':'false'?>,
        startExtent: <?=$mapModel->getStartExtentJson()?>,
        selectedLayerKey: "<?=$mapModel->getSelectedLayerName()?>",
        infoMessage: "<?=$mapModel->getInfoMessage()?>",
        markersData: <?=$mapModel->getMarkersDataJson()?>,
        sectionsProperties: <?=$mapModel->getSectionsPropertiesJson()?>,
        sectionsNames: {
        <?php foreach($mapModel->getSectionsKeys() as $section=>$key) { ?>
            '<?=$section?>': '<?=tr($key)?>',
        <?php } //foreach $sectionsKeys ?>
        },
        markerMgrs: {
        <?php foreach($mapModel->getMarkerTypes() as $markerType) { ?>
            <?=$markerType?>: <?php
                include(__DIR__ . '/markers/' .$markerType .'Mgr.tpl.php');
            ?>,
        <?php } //foreach $markerTypes ?>
        },
        markersFamily: "<?=$mapModel->getMarkersFamily() ?>",
    }).init();
});
</script>

<?php
};
//end of chunk - nothing should be after this line
