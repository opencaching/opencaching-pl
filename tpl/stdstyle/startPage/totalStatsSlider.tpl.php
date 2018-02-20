<p class="content-title-noshade-size3"
   title="<?=tr('startPage_validAt')?>: <?=$view->totStsValidAt?>">
  <?=tr('startPage_wholeStatsTitle')?>
</p>

<div class="totalStatsSlider">
    <?php foreach($view->totStsArr as $key=>$sts) { ?>
        <div class="totalStatsFrame" title="<?=$sts['ldesc']?>">
            <div class="totalStatsTitle"><?=$sts['desc']?></div>
            <div class="totalStatsNumber"><?=$sts['val']?></div>
        </div>
    <?php } //foreach-totStsArr ?>
</div>