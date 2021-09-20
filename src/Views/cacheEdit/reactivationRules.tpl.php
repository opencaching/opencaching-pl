<?php
use src\Models\OcConfig\OcConfig;
?>
<!-- Reactivation rules template -->
<div>

    <!--  p class="content-title-noshade"><?=tr('editCache_reactivRulesTitle')?></p-->
    <fieldset style="border: 1px solid black; width: 80%; class="form-group-sm">
      <legend><strong>&nbsp;<?=tr('editCache_reactivRulesLabel')?>&nbsp;</strong></legend>
      <p>
        <?=tr('editCache_reactivRulesDesc')?>
        <div class="notice buffer"><?=tr('editCache_reactivRulesMoreInfo')?></div>
      </p>


      <?php foreach(OcConfig::getReactivationRulesPredefinedOpts() as $key => $opt) { ?>
        <?php $optTxt = tr($opt); ?>
        <input type="radio" id="reactivRules<?=$key?>" name="reactivRules" value="<?=$optTxt?>"
            <?=($optTxt == $view->reactivRulesRadio)?"checked":""?>>
        <label for="reactivRules<?=$key?>"><?=$optTxt?></label>
        <br/>
      <?php } // ?>

      <input type="radio" id="reactivRulesCustom" name="reactivRules" value="Custom rulset"
            <?=("Custom rulset" == $view->reactivRulesRadio)?"checked":""?>>
      <label for="reactivRulesCustom"><?=tr('editCache_reactivRuleCustomDefinition')?>:</label>

      <textarea placeholder="<?=tr('editCache_reactivRuleCustomDefinition')?>"
                class="customReactivation" name="reactivRulesCustom"><?=$view->reactivRulesCustom?></textarea>


    </fieldset>
</div>
<!-- Reactivation rules template ends-->
