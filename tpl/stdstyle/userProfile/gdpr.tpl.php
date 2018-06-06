<?php

use Utils\Uri\SimpleRouter;

?>
<div class="content2-container align-center">
  <div class="buffer"></div>
  <h2>
    <?=tr('gdpr_text')?>
    <a href="<?=$view->_wikiLinkRules?>" target="_blank" class="btn btn-default btn-md"><?=tr('rules')?></a>
  <?php
      if (! empty($view->_wikiLinkPrivacyPolicy)) {
      ?>
        <a href="<?=$view->_wikiLinkPrivacyPolicy?>" target="_blank" class="btn btn-default btn-md"><?=tr('mnu_privacyPolicy')?></a>
      <?php
      }
  ?>
  </h2>
  <h2><?=str_replace('{OCTeamEmail}', $view->_ocTeamEmail, tr('gdpr_text2'))?></h2>
  <form action="<?=SimpleRouter::getLink('UserProfile', 'confirmRules')?>" method="post">
    <input type="hidden" name="url" value="<?=$view->_currentUri?>">
    <input type="submit" class="btn btn-primary" value="<?=tr('gdpr_acceptBtn')?>">
  </form>
</div>