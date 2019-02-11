<?php
/**
 * This chunk is used to generate static map (optionally with markers)
 *
 * This chunk needs LightTipped chunk!
 */
use src\Utils\Uri\Uri;
use src\Models\ChunkModels\StaticMap\StaticMapModel;
use src\Utils\View\View;

return function (StaticMapModel $m){
    //start of chunk

    $chunkCSS = Uri::getLinkWithModificationTime(
        '/views/chunks/staticMap/staticMap.css');
    ?>

<script>
    // load pagination chunk css
    var linkElement = document.createElement("link");
    linkElement.rel = "stylesheet";
    linkElement.href = "<?=$chunkCSS?>";
    linkElement.type = "text/css";
    document.head.appendChild(linkElement);
</script>

<div class="staticMapChunk" style="position: relative;">

    <!-- map imgage -->
    <img src="<?=$m->getMapImgSrc()?>" alt="<?=$m->getMapTitle()?>" title="<?=$m->getMapTitle()?>" />

    <!-- markers -->
    <?php foreach($m->getMapMarkers() as $mx) {
        View::callChunkInline('staticMap/staticMapMarker', $mx);
    } //foreach mapMarkers ?>

    <script>
      function highliteStaticMapMarker(id) {
        $('#'+id).toggleClass('hovered');
      }
    </script>

</div>
<?php
}; //end of chunk
