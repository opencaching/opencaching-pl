<p class="content-title-noshade-size3"
   title="<?=tr('startPage_validAt')?>: <?=$view->totStsValidAt?>">
  <?=tr('startPage_wholeStatsTitle')?>
</p>

<div id="totalStatsDiv">

    <div id="arrowLeft" class="counterArrow" onclick="countersRight()"></div>
    <div id="totalStatsCounters">
    <?php foreach($view->totStsArr as $key=>$sts) { ?>

        <div class="counterWidget <?=($key>4)?'counterRightHidden':''?>" title="<?=$sts['ldesc']?>">
            <div class="counterTitle"><?=$sts['desc']?></div>
            <div class="counterNumber"><?=$sts['val']?></div>
        </div>

    <?php } //foreach-totStsArr ?>
    </div>
    <div id="arrowRight" class="counterArrow" onclick="countersLeft()"></div>

</div>
