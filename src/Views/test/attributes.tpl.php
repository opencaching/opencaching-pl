<?php

use src\Models\GeoCache\CacheAttribute;

?>
<style>
.atContainer {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    width: 514px;
}

.atDiv {
    position: relative;
    width: 35px;
    height: 34px;
    margin: 1px 2px;
}

.atImg {
    width: 35px;
    height: 34px;
    padding: 0px 2px;
}

.withOpacity {
    opacity: 0.3;
}

.atCross {
  position: absolute;
  right: 0px;
  top: 0px;
  width: 35px;
  height: 34px;
  opacity: 0.8;
  display: none;
}

.atCross:before, .atCross:after {
  position: absolute;
  left: 16px;
  content: ' ';
  height: 34px;
  width: 5px;
  background-color: red;
  opacity: 1;
}
.atCross:before {
  transform: rotate(45deg);
}
.atCross:after {
  transform: rotate(-45deg);
}

</style>

<h1>Attributes tester</h1>

<?php foreach(['pl', 'nl', 'ro','uk', 'us'] as $node) { ?>
    <hr>
    <h2>oc<?=$node?></h2>
    <div>
      <p>Screenshot from the search view:</p>
      <img src="/images/cacheAttributes/test/oc<?=$node?>.png">
    </div>
    <p></p>
    <p>3-states icons generated from new code:</p>
    <div class="atContainer">
    <?php foreach ($view->attrList[$node] as $key=>$at) { ?>
        <div class="atDiv" onclick="changeAtIcon(this)" state="selected">
          <img class="atImg" src="<?=CacheAttribute::getIcon($at, $node)?>"
               title="<?=tr(CacheAttribute::getTrKey($at))?>"
               alt="<?=tr(CacheAttribute::getTrKey($at))?>">
          <div class="atCross"></div>
        </div>
    <?php } ?>
    </div>
    <p><br/></p>
    <p>All attributes from CacheAttribute list (displays <img src="/images/blue/atten-red.png"> if icon not found):</p>
    <div class="atContainer">
    <?php foreach (CacheAttribute::getGpxAttrIds() as $at) { ?>
        <div class="atDiv">
            <img class="atImg atImgList" src="<?=CacheAttribute::getIcon($at, $node)?>"
               title="<?= $at; ?>; <?= CacheAttribute::getTrKey($at); ?>; <?= tr(CacheAttribute::getTrKey($at)); ?>"
               alt="<?= $at; ?>; <?= CacheAttribute::getTrKey($at); ?>; <?= tr(CacheAttribute::getTrKey($at)); ?>">
        </div>
    <?php } ?>
    </div>
<?php } ?>

<script>

function changeAtIcon(obj) {
  var iconDiv = $(obj);

  switch (iconDiv.attr('state')) {
  case 'selected': // -> negselected
    iconDiv.removeClass('withOpacity');
    iconDiv.children(".atCross").show();
    iconDiv.attr('state','negselected');
    break;
  case 'notselected': // -> selected
    iconDiv.removeClass('withOpacity');
    iconDiv.children(".atCross").hide();
    iconDiv.attr('state','selected');
    break;
  case 'negsel': // -> notselected
  default:
    iconDiv.addClass('withOpacity');
    iconDiv.children(".atCross").hide();
    iconDiv.attr('state','notselected');
  }
}

$('.atImgList').on("error", function() {
    $(this).attr('src', '/images/blue/atten-red.png');
});
</script>
