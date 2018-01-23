<?php

use lib\Objects\ChunkModels\StaticMap\StaticMapMarker;


/**
 * This chunk represents css generated marekr for staticMap
 */
return function (StaticMapMarker $mx){
?>

  <?php if($mx->markerType == StaticMapMarker::TYPE_CSS_MARKER) { ?>
    <div id="<?=$mx->id?>" class="cssStaticMapMarker lightTipped cssStaticMapMarkerAbs"
         style="left:<?=($mx->left-7)?>px; top:<?=($mx->top-24)?>px;">

      <?php if($mx->link){ ?>
        <a href="<?=$mx->link?>">
      <?php } //if-link-present ?>

      <div class="stmCircle" style="background-color:<?=$mx->color?>"></div>

      <div class="stmTriangleBorder"></div>
      <div class="stmTriangle" style="border-top-color:<?=$mx->color?>"></div>

      <?php if($mx->link){ ?>
        </a>
      <?php } //if-link-present ?>
    </div>
  <?php } // if-markerType == StaticMapMarker::TYPE_CSS_MARKER ?>


  <?php if($mx->markerType == StaticMapMarker::TYPE_CSS_LEGEND_MARKER) { ?>
    <div class="cssStaticMapMarker cssStaticMapMarkerRel">

      <div class="stmCircle" style="background-color:<?=$mx->color?>"></div>

      <div class="stmTriangleBorder"></div>
      <div class="stmTriangle" style="border-top-color:<?=$mx->color?>"></div>

    </div>

  <?php } // if-markerType == StaticMapMarker::TYPE_CSS_LEGEND_MARKER ?>


  <?php if($mx->markerType == StaticMapMarker::TYPE_IMG_MARKER) { ?>
    <img id="<?=$mx->id?>" class="<?=$mx->getClasses()?>"
       style="left:<?=$mx->left?>px; top:<?=$mx->top?>px"
       alt="" src="<?=$mx->markerImg?>" />
  <?php } // if-markerType == StaticMapMarker::TYPE_IMG_MARKER ?>


  <?php if($mx->tooltip) { ?>
    <div class="lightTip" style="left:<?=($mx->left+20)?>px; top:<?=$mx->top?>px">
      <b><?=$mx->tooltip?></b>
    </div>
  <?php } //if-tooltip ?>

<?php
}; //end of chunk
