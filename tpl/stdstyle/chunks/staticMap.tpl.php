<?php
/**
 * This chunk is used to generate static map (optionally with markers)
 *
 * This chunk needs LightTipped chunk!
 */
use Utils\Uri\Uri;
use lib\Objects\ChunkModels\StaticMapModel;
use lib\Objects\ChunkModels\StaticMapMarker;

return function (StaticMapModel $m){
    //start of chunk

    $chunkCSS = Uri::getLinkWithModificationTime('/tpl/stdstyle/chunks/staticMap.css');
    ?>

<script type='text/javascript'>
    // load pagination chunk css
    var linkElement = document.createElement("link");
    linkElement.rel = "stylesheet";
    linkElement.href = "<?=$chunkCSS?>";
    linkElement.type = "text/css";
    document.head.appendChild(linkElement);
</script>

<div style="position: relative;">

    <img src="<?=$m->getMapImgSrc()?>" alt="<?=$m->getMapTitle()?>" title="<?=$m->getMapTitle()?>" />

    <?php foreach($m->getMapMarkers() as $mx) { ?>

      <?php if($mx->markerType == StaticMapMarker::TYPE_CSS_MARKER) { ?>

        <div id="<?=$mx->id?>" class="cssStaticMapMarker lightTipped"
              style="left:<?=($mx->left-7)?>px; top:<?=($mx->top-24)?>px;">

              <div class="circleBorder"></div>
              <div class="circle" style="background-color:<?=$mx->color?>"></div>

              <div class="triangleBorder"></div>
              <div class="triangle" style="border-top-color:<?=$mx->color?>"></div>
        </div>

      <?php } else { // if-markerType ?>

        <img id="<?=$mx->id?>" class="<?=$mx->getClasses()?>"
           style="left:<?=$mx->left?>px; top:<?=$mx->top?>px"
           alt="" src="<?=$mx->markerImg?>" />

      <?php } // if-markerType ?>


      <?php if($mx->tooltip) { ?>

        <div class="lightTip" style="left:<?=($mx->left+20)?>px; top:<?=$mx->top?>px">
          <b><?=$mx->tooltip?></b>
        </div>

      <?php } //if-tooltip ?>

    <?php } //foreach mapMarkers ?>

</div>
<?php
}; //end of chunk