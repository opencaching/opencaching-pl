
<div class="content2-pagetitle"><?=tr('register_pageTitle')?></div>

<div class="content2-container">

    <div id="leftBox">
      <div id="wecomeMessage" class="callout callout-info">
        <?=tr('register_welcomeMessage')?>
      </div>
    </div>

    <div id="rightBox">

        <div id="externalServices">

          <a class="btn" href="">XXXRejestracja przez Google</a>

          <?php if($view->fbLoginEnabled) { ?>
          <a class="btn" href="<?=$view->fbRedirectUrl?>">XXXRejestracja przez Facebooka</a>
          <?php } // if-fbLoginEnabled ?>

        </div>

        <div id="ocRegistrationBox">
          Rejestracja przez FB
        </div>

    </div>

</div>
<!-- /content -->
