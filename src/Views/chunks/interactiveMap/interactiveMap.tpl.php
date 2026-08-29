<?php

declare(strict_types=1);

use src\Models\ChunkModels\InteractiveMap\InteractiveMapModel;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLogCommons;
use src\Utils\Uri\Uri;
use src\Utils\View\View;

/**
 * This chunk displays interactive map with different kinds of markers.
 * Markers should be passed by $mapModel.
 *
 * OpenLayers chunk should be loaded to header by:
 *  $this->view->addHeaderChunk('openLayers5');
 */
return function (InteractiveMapModel $mapModel, string $canvasId) {
    $publicSrcPath = '/views/chunks/interactiveMap/';

    // load chunk CSS
    View::callChunkInline(
        'loadCssByJs',
        Uri::getLinkWithModificationTime(
            $publicSrcPath . 'interactiveMap.css'
        )
    );

    if ($mapModel->getMarkersFamily() === 'okapi') {
        // load css for okapi  markers
        View::callChunkInline(
            'loadCssByJs',
            Uri::getLinkWithModificationTime(
                $publicSrcPath . '/markers/okapi/okapiBasedMarker.css'
            )
        );
    }
    View::callChunkInline('handlebarsJs'); ?>
<script>
var cacheStatusList = <?= GeoCacheCommons::getCacheStatusListJson(); ?>;
var logTypeList = <?= GeoCacheLogCommons::getLogTypeListJson(); ?>;
</script>
<script src="<?= Uri::getLinkWithModificationTime(
        $publicSrcPath . 'interactiveMap.js'
    ); ?>"></script>

<!-- load markers scripts and popup templates -->
<?php
    // shared javascript marker libs to load
    $markerLibs = [
        $publicSrcPath . 'markers/ocMarker.js',
    ];

    if ($mapModel->getMarkersFamily() === 'okapi') {
        // shared js necessary for okapi markers
        array_push(
            $markerLibs,
            $publicSrcPath . 'markers/ocZoomCachedMarker.js'
        );
        array_push(
            $markerLibs,
            $publicSrcPath . 'markers/okapi/okapiBasedMarker.js'
        );
    }
    $markerLibsLoaded = false;
    $markerTypes = array_fill_keys($mapModel->getMarkerTypes(), true);
    $markerTypes['highlightedMarker'] = false;

    foreach ($markerTypes as $markerType => $loadTpl) {
        if (! $markerLibsLoaded) {
            $markerLibsLoaded = true;

            foreach ($markerLibs as $lib) {
                ?>
<script src="<?= Uri::getLinkWithModificationTime($lib); ?>"></script>
<?php
            } //foreach-markerLibs
        } //if-markerLibsLoaded

        try {
            $markerJs = Uri::getLinkWithModificationTime(
                $publicSrcPath . 'markers/' . $mapModel->getMarkersFamily()
                . '/' . $markerType . '.js'
            ); ?>
<script type="text/javascript" src="<?= $markerJs; ?>"></script>
<?php
        } catch (Exception $ex) {
            // ignore if a file does not exist
        }

        if ($loadTpl) {
            ?>
<script type="text/x-handlebars-template" class="<?= $markerType; ?>" >
            <?php
                include __DIR__ . '/markers/' . $markerType . 'Popup.tpl.php'; ?>
</script>
<?php
        } //if-loadTpl
    } //foreach-markerTypes?>
<!-- end of load markers scripts popup templates -->

<script>
$(document).ready(function() {
    InteractiveMapServices.getInteractiveMap("<?= $canvasId; ?>", {
        targetDiv: "<?= $canvasId; ?>",
        centerOn: <?= $mapModel->getCoords()->getAsOpenLayersFormat(); ?>,
        mapStartZoom: <?= $mapModel->getZoom(); ?>,
        forceMapZoom: <?= $mapModel->isZoomForced() ? 'true' : 'false'; ?>,
        startExtent: <?= $mapModel->getStartExtentJson(); ?>,
        selectedLayerKey: "<?= $mapModel->getSelectedLayerName(); ?>",
        infoMessage: "<?= $mapModel->getInfoMessage(); ?>",
        markersData: <?= $mapModel->getMarkersDataJson(); ?>,
        sectionsProperties: <?= $mapModel->getSectionsPropertiesJson(); ?>,
        sectionsNames: {
        <?php foreach ($mapModel->getSectionsKeys() as $section => $key) { ?>
            '<?= $section; ?>': '<?= tr($key); ?>',
        <?php
        } //foreach $sectionsKeys?>
        },
        markerMgrs: {
        <?php foreach ($mapModel->getMarkerTypes() as $markerType) { ?>
            <?= $markerType; ?>: <?php
            include __DIR__ . '/markers/' . $markerType . 'Mgr.tpl.php';
            ?>,
        <?php } //foreach $markerTypes?>
        },
        markersFamily: "<?= $mapModel->getMarkersFamily(); ?>",
        enableBackgroundLayer: <?= $mapModel->getMarkersFamily() == 'okapi' ? 'true' : 'false'; ?>,
    }).init();
});
</script>

<?php
};
//end of chunk - nothing should be after this line
