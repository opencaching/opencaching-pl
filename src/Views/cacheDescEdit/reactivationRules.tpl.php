<?php
use src\Models\OcConfig\OcConfig;
use src\Utils\View\View;
/** @var $view View */
?>
<!-- Reactivation rules template -->
<div>

    <!--  p class="content-title-noshade"><?=tr('editDesc_reactivRulesTitle')?></p-->
    <fieldset class="form-group-sm reactivationRules">
      <legend><strong>&nbsp;<?=tr('editDesc_reactivRulesLabel')?>&nbsp;</strong></legend>
      <p>
        <?=tr('editDesc_reactivRulesDesc')?>
        <div class="notice buffer">
          <?=tr('editDesc_reactivRulesMoreInfo', [OcConfig::getWikiLink('geocacheRactivation')])?>
        </div>
      </p>


      <?php foreach(OcConfig::getReactivationRulesPredefinedOpts() as $key => $opt) { ?>
        <?php $optTxt = tr($opt); ?>
        <input type="radio" id="reactivRules<?=$key?>" name="reactivRules" value="<?=$optTxt?>"
        <?=($optTxt == $view->desc->getReactivationRules())?"checked":""?>>
        <label for="reactivRules<?=$key?>"><?=$optTxt?></label>
        <br/>
      <?php } // ?>

      <input type="radio" id="reactivRulesCustom" name="reactivRules" value="Custom rulset"
      <?=(!empty($view->desc->getReactivationRules()))?"checked":""?>>
      <label for="reactivRulesCustom"><?=tr('editDesc_reactivRuleCustomDefinition')?>:</label>

      <textarea placeholder="<?=tr('editDesc_reactivRuleCustomDefinition')?>"
                class="customReactivation" name="reactivRulesCustom"><?=$view->desc->getReactivationRules()?></textarea>
    </fieldset>
</div>
<!-- Reactivation rules template ends-->
