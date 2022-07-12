<?php

use src\Models\GeoCache\GeoCacheDesc;
use src\Models\OcConfig\OcConfig;
use src\Utils\View\View;

/** @var View $view */

/** @var GeoCacheDesc $desc */
$desc = $view->desc;

$view->callChunk('tinyMCE');
?>
<!--  cache description form -->

<div class="content2-container" id="descLangDiv">
    <label for="descLang" class="content-title-noshade"><?= tr('editDesc_langLabel'); ?>:</label>
    <select id="descLang" name="descLang" class="form-control input200" required>
    <?php if (! $desc->getLang()) { ?>
      <option disabled value selected><?= tr('editDesc_langEmptyOpt'); ?></option>
    <?php } //if-!$desc->getLang()?>
    <?php
    foreach ($view->languages as $l) {
        if (! $l->default) {
            continue;
        } ?>
          <option value="<?= $l->code; ?>" <?= ($l->selected) ? 'selected' : ''; ?>>
          <?= $l->localizedName; ?>
          </option>
    <?php
    } //forach languages?>
    </select>

    <button class="btn btn-sm btn-default" type="button" onclick="loadAllLangs()"><?= tr('editDesc_showAllLangs'); ?></button>
</div>

<div class="content2-container">
    <label for="shortDesc" class="content-title-noshade"><?= tr('editDesc_shortDescLabel'); ?>:</label>
    <input type="text" id="shortDesc" name="shortDesc" maxlength="120" value="<?= $desc->getShortDescToDisplay(); ?>"
           class="form-control input400"/>
    <div class="notice"><?= tr('editDesc_shortDescNotice'); ?></div>
</div>


<div class="content2-container">
    <label for="descTxt" class="content-title-noshade"><?= tr('editDesc_fullDesc'); ?></label>
    <textarea id="descTxt" name="descTxt" class="desc tinymce"><?= $desc->getDescriptionRaw(); ?></textarea>

    <div class="notice"><?= tr('editDesc_gcPicNotice'); ?></div>
</div>

<div class="content2-container">
    <label for="hints" class="content-title-noshade"><?= tr('editDesc_hintLabel'); ?>:</label>
    <textarea id="hints" name="hints" class="hint"><?= $desc->getHint(); ?></textarea>
    <div class="notice"><?= tr('editDesc_hintDesc'); ?></div>
    <div class="notice"><?= tr('editDesc_hintChars'); ?></div>
</div>

<?php if (OcConfig::isReactivationRulesEnabled()) { ?>
<div class="content2-container">
    <fieldset class="form-group-sm reactivationRules">
      <legend class="content-title-noshade"><?= tr('editDesc_reactivRulesLabel'); ?></legend>
      <p>
        <?= tr('editDesc_reactivRulesDesc'); ?>
        <div class="notice buffer">
          <?= tr('editDesc_reactivRulesMoreInfo', [OcConfig::getWikiLink('geocacheRactivation')]); ?>
        </div>
      </p>

      <?php
        $reactivRuleChecked = false;
        $firstRuleId = false;

        foreach (OcConfig::getReactivationRulesPredefinedOpts() as $key => $opt) { ?>
        <?php $optTxt = tr($opt);
              $reactivRuleChecked = $reactivRuleChecked || $optTxt == $desc->getReactivationRules(); ?>
        <input type="radio" id="reactivRules<?= $key; ?>" name="reactivRules" value="<?= $optTxt; ?>"
            <?= (! $firstRuleId ? ' required oninvalid="this.setCustomValidity(\'' . tr('editDesc_invalidRactivRule') . '\')" oninput="this.setCustomValidity(\'\')"' : ' oninput="document.getElementById(\'' . $firstRuleId . '\').setCustomValidity(\'\')"'); ?>
            <?= ($optTxt == $desc->getReactivationRules()) ? 'checked' : ''; ?>>
        <label for="reactivRules<?= $key; ?>"><?= $optTxt; ?></label>
        <br/>
        <?php
            if (! $firstRuleId) {
                $firstRuleId = 'reactivRules' . $key;
            }
        } //?>

        <input type="radio" id="reactivRulesCustom" name="reactivRules" value="Custom rulset"
            <?= (! $firstRuleId ? ' required oninvalid="this.setCustomValidity(\'' . tr('editDesc_invalidRactivRule') . '\')" oninput="this.setCustomValidity(\'\')"' : ' oninput="document.getElementById(\'' . $firstRuleId . '\').setCustomValidity(\'\')"'); ?>
            <?= (! $reactivRuleChecked && ! empty($desc->getReactivationRules())) ? 'checked' : ''; ?>
        <label for="reactivRulesCustom"><?= tr('editDesc_reactivRuleCustomDefinition'); ?>:</label>

      <textarea placeholder="<?= tr('editDesc_reactivRuleCustomDefinition'); ?>" id="reactivRulesCustom"
                class="customReactivation" name="reactivRulesCustom"
                maxlength="1000"><?= $desc->getReactivationRules(); ?></textarea>
    </fieldset>
</div>
<?php } // if-OcConfig::isReactivationRulesEnabled()?>


<script>
  // load the full list of availabe languages
  function loadAllLangs ()
  {
    var langs = <?= json_encode($view->languages); ?>;
    var select = $('#descLang');
    // remove all options
    select.find('option:enabled').remove().end();

    // add all possible langs
    $.each(langs, function (key, lang) {
      var opt = $('<option value="'+lang.code+'">'+lang.localizedName+'</option>');
      if (lang.selected) {
        opt.attr("selected","selected");
      }
      select.append(opt);
      console.log(lang);
    });
  }
</script>
