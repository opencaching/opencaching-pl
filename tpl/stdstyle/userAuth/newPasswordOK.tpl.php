<?php
use Utils\Uri\SimpleRouter;

?>

<div class="content2-pagetitle">
  <?=tr('newpw_title')?>
</div>
<div class="content2-container">
  <div class="callout callout-success">
    <?=$view->message?>
  </div>
  <div class="align-center">
    <a href ="<?=SimpleRouter::getLink('StartPage')?>" class="btn btn-md btn-primary"><?=tr('mnu_mainPage')?></a>
    <?php if ($view->notLogged) { ?>
        <a href="<?=SimpleRouter::getLink('UserAuthorization', 'login')?>" class="btn btn-md btn-default"><?=tr('login')?></a>
    <?php }?>
  </div>
</div>