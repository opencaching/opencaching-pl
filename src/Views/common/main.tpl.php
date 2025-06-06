<?php
// Warning: possible names config is temple load the same class - that's why classes here uses "_" prefix
use src\Models\OcConfig\OcConfig as _OcConfig;
use src\Utils\Uri\SimpleRouter as _SimpleRouter;

$view->addHeaderChunk('darkmodeJS');

?>
<!DOCTYPE html>
<html lang="<?= $view->getLang(); ?>" <?= $view->responsiveModeEnabled() ? 'class="responsive-enabled"' : '' ?>>
<head>
  <meta charset="utf-8">

  <title><?= $view->getSubtitle(); ?>{title}</title>

  <link rel="shortcut icon" href="<?= _OcConfig::getSiteMainViewIcon('shortcutIcon'); ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= _OcConfig::getSiteMainViewIcon('appleTouch'); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= _OcConfig::getSiteMainViewIcon('icon32'); ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= _OcConfig::getSiteMainViewIcon('icon16'); ?>">
  <link rel="manifest" href="<?= _OcConfig::getSiteMainViewIcon('webmanifest'); ?>">
  <link rel="mask-icon" href="<?= _OcConfig::getSiteMainViewIcon('maskIcon'); ?>" color="#5bbad5">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="apple-mobile-web-app-title" content="Opencaching">
  <meta name="application-name" content="Opencaching">
  <meta name="msapplication-TileColor" content="#04bd00">
  <meta name="msapplication-config" content="/images/icons/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">

  <link rel="stylesheet" type="text/css" media="screen" href="<?= $view->screenCss; ?>">
  <link rel="stylesheet" type="text/css" media="screen" href="<?= $view->responsiveCss; ?>">
  <link rel="stylesheet" type="text/css" media="print" href="<?= $view->printCss; ?>">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.0/normalize.min.css">
  <link rel="stylesheet" type="text/css" href="/css/typography.css">

  <?php
      //load jQuery when responsive mode is enabled
      if (isset($_COOKIE[$config['cookie']['name'] . '_responsive_mode'])) {
          $view->loadJQuery();
      }
  ?>

  <?php if ($view->_showVideoBanner) {
      foreach ($view->_topBannerVideo as $key => $videoPath) {
          if ($key !== 0) { ?>
              <link rel="prefetch" href="<?= $videoPath; ?>">
          <?php }
      }
  } ?>

  <?php foreach ($view->getLocalCss() as $css) { ?>
      <link rel="stylesheet" type="text/css" href="<?= $css; ?>">
  <?php } //foreach-css?>

  <?php foreach ($view->getHeaderChunks() as $chunkName => $args) {?>
    <!-- load chunk $chunkName -->
    <?php $view->callChunk($chunkName, ...$args); ?>
  <?php } //foreach getHeaderChunks?>

  <?php
      if ($view->isGoogleAnalyticsEnabled()) {
          $view->callChunkOnce('googleAnalytics', $view->getGoogleAnalyticsKey());
      }

      if ($view->isjQueryEnabled()) {
          $view->callChunk('jQuery');
      }

      if ($view->isjQueryUIEnabled()) {
          $view->callChunk('jQueryUI');
      }

      if ($view->isTimepickerEnabled()) {
          $view->callChunk('timepicker');
      }

      if ($view->isFancyBoxEnabled()) {
          $view->callChunk('fancyBoxLoader', true, false);
      }

      foreach ($view->getLocalJs() as $js) {
          if (! $js['defer']) {?>
            <script src="<?= $js['url']; ?>"<?= $js['async'] ? ' async' : ''; ?>></script>
  <?php }
      } //foreach-js?>
  <script src="/js/CookiesInfo.js" async defer></script>

</head>

<body {bodyMod} class="<?= $view->backgroundSeason; ?>">
  <?php if (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') {
    echo '<div id="dark-mode-mask"></div>';
  }?>
  <div id="overall">
    <div class="page-container-1">
      <div class="seasonalBackground left <?= $view->backgroundSeason; ?>">&nbsp;</div>
      <div class="seasonalBackground right <?= $view->backgroundSeason; ?>">&nbsp;</div>
      <div class="topline-container">
        <div class="topline-logo">
          <a href="/" class="transparent"><img src="<?= $view->_mainLogo; ?>" alt="OC logo"></a>
        </div>
        <div class="topline-sitename">
          <a href="/" class="transparent"><?= $view->_logoTitle; ?></a>
        </div>
        <div class="topline-buffer"></div>
        <button class="responsive-toggle btn btn-sm btn-default" type="button" onclick="responsiveToggle()">
            <img src="/images/misc/responsive-mode-disabled.svg" class="icon16 mobile-off" alt="<?= tr('responsiveModeToggle_disabled'); ?>" title="<?= tr('responsiveModeToggle_disabled'); ?>">
            <img src="/images/misc/responsive-mode-enabled.svg" class="icon16 mobile-friendly" alt="<?= tr('responsiveModeToggle_enabled'); ?>" title="<?= tr('responsiveModeToggle_enabled'); ?>">
        </button>
        <button class="theme-toggle btn btn-sm btn-default" type="button" id="theme-toggle-btn">
            <theme-switch></theme-switch>
        </button>
        <div class="topline-buttons">
          <?php if ($view->_isUserLogged) { //if-user-logged?>
            <form method="get" action="/search.php" name="search_form" id="search_form" class="form-group-sm">
              <input type="hidden" name="showresult" value="1">
              <input type="hidden" name="expert" value="0">
              <input type="hidden" name="output" value="HTML">
              <input type="hidden" name="sort" value="bydistance">
              <input type="hidden" name="f_inactive" value="0">
              <input type="hidden" name="f_ignored" value="0">
              <input type="hidden" name="f_userfound" value="0">
              <input type="hidden" name="f_userowner" value="0">
              <input type="hidden" name="f_watched" value="0">
              <input type="hidden" name="f_geokret" value="0">
              <input type="hidden" name="searchto" value="searchbywaypointname" id="search_by">
              <input id="search_input" type="text" name="waypointname" placeholder="<?= tr('search'); ?>: <?= tr('waypointname_label'); ?>" class="form-control input200 btn-right-straight">
                <button class="btn btn-sm btn-default btn-left-straight btn-right-straight topline-btn-wide" type="submit">
                  <img class="icon16" src="/images/misc/magnifying-glass.svg" alt="<?= tr('search'); ?>" title="<?= tr('search'); ?>">
                </button>
              <button class="btn btn-sm btn-default btn-left-straight" type="button" onclick="topmenuTogle()">
                <img class="topline-dropdown-icon" src="/images/misc/dropdown.svg" alt="<?= tr('search'); ?>" title="<?= tr('search'); ?>">
              </button>
              <div id="topline-search-dropdown" class="topline-dropdown-content">
                <div class="" onclick="chname('waypointname', '<?= tr('search'); ?>: <?= tr('waypointname_label'); ?>' , 'searchbywaypointname', '/search.php');"><?= tr('waypointname_label'); ?></div>
                <?php if ($config['quick_search']['geopath']) { ?>
                  <div class="" onclick="chname('name', '<?= tr('search'); ?>: <?= tr('pt000'); ?>', 'name', '<?= _SimpleRouter::getLink('GeoPath', 'searchByName'); ?>');"><?= tr('pt000'); ?></div>
                <?php } ?>
                <?php if ($config['quick_search']['byuser']) { ?>
                  <div class="" onclick="chname('username', '<?= tr('search'); ?>: <?= tr('user'); ?>', 'searchbyuser', '/searchuser.php');"><?= tr('user'); ?></div>
                <?php } ?>
                <?php if ($config['quick_search']['byowner']) { ?>
                  <div class="" onclick="chname('owner', '<?= tr('search'); ?>: <?= tr('owner_label'); ?>', 'searchbyowner', '/search.php');"><?= tr('owner_label'); ?></div>
                <?php } ?>
                <?php if ($config['quick_search']['byfinder']) { ?>
                  <div class="" onclick="chname('finder', '<?= tr('search'); ?>: <?= tr('finder_label'); ?>', 'searchbyfinder', '/search.php');"><?= tr('finder_label'); ?></div>
                <?php } ?>
                </div>
            </form>

            <div class="topline-buffer"></div>

            <div class="btn-group btn-group-sm">
              <a href="/viewprofile.php" class="btn btn-default btn-sm topline-btn-wide">
                <img src="/images/misc/user.svg" class="icon16" alt="<?= tr('user_profile'); ?>" title="<?= tr('user_profile'); ?>">
                <?= $view->_username; ?>
              </a>
              <a href="<?= _SimpleRouter::getLink('UserAuthorization', 'logout'); ?>" class="btn btn-default btn-sm topline-btn-wide">
                <img src="/images/misc/exit.svg" class="icon16" alt="<?= tr('logout'); ?>" title="<?= tr('logout'); ?>"> <?= tr('logout'); ?>
              </a>
            </div>
          <?php } else { //user-not-logged?>
            <?php if (! $view->_hideTopLoginForm) { ?>
                <form action="<?= _SimpleRouter::getLink('UserAuthorization', 'login'); ?>" method="post" name="login" class="form-group-sm">
                  <label for="top-form-email" class="btn btn-sm btn-default btn-right-straight">
                    <img src="/images/misc/user.svg" class="icon16" alt="<?= tr('loginForm_userOrEmail'); ?>" title="<?= tr('loginForm_userOrEmail'); ?>">
                  </label>
                  <input name="email" id="top-form-email" type="text" class="form-control input120 btn-left-straight" value="" autocomplete="username" placeholder="<?= tr('loginForm_userOrEmail'); ?>" required>
                  <label for="top-form-password" class="btn btn-sm btn-default btn-right-straight">
                    <img src="/images/misc/key.svg" class="icon16" alt="<?= tr('loginForm_password'); ?>" title="<?= tr('loginForm_password'); ?>">
                  </label>
                  <input name="password" id="top-form-password" type="password" class="form-control input120 btn-left-straight" value="" autocomplete="current-password" placeholder="<?= tr('loginForm_password'); ?>" required>
                  <input type="hidden" name="target" value="<?= $view->_target; ?>">
                  <input type="submit" value="<?= tr('login'); ?>" class="btn btn-primary btn-sm">
                  <a href="<?= _SimpleRouter::getLink('UserRegistration'); ?>" class="btn btn-success btn-sm"><?= tr('registration'); ?></a>
                </form>
            <?php } ?>
          <?php } //user-not-logged?>

        </div>
      </div>

      <?php if ($view->_showVideoBanner) { ?>
        <div class="top-video-container">
          <video width="970" height="180" autoplay muted preload="auto" id="topline-video-player">
            <source src="<?= $view->_topBannerVideo[0]; ?>" type="video/mp4">
          </video>
        </div>
        <div class="top-video-slider">
        <?php foreach ($view->_topBannerTxt as $bannerTxt) { ?>
          <div><?= $bannerTxt; ?></div>
        <?php } //foreach videoBannerTxt?>
        </div>

        <script>
            let topVideoSource = [];
            <?php foreach ($view->_topBannerVideo as $key => $val) { ?>
                topVideoSource[<?= $key; ?>]='<?= $val; ?>';
            <?php } // foreach topBannerVideo?>
            let topVideoIndex = 0;
            let topVideoPlayer = document.getElementById("topline-video-player");
            topVideoPlayer.setAttribute("src",topVideoSource[0]);

       function videoPlay(videoNum) {
           topVideoPlayer.setAttribute("src", topVideoSource[videoNum]);
           topVideoPlayer.load();
           topVideoPlayer.play();
       }

       topVideoPlayer.addEventListener('ended', toplineVideoHandler, false);

       function toplineVideoHandler() {
         topVideoIndex++;
         if (topVideoIndex === topVideoSource.length) {
           topVideoIndex = 0;
         }
         videoPlay(topVideoIndex);
       }

        $('.top-video-slider').slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          autoplay: true,
          autoplaySpeed: 5000,
          arrows: false,
        });
        </script>
      <?php } // if - showVideoBanner?>

      <!-- HEADER -->

                <!-- Navigation - horizontal menu bar -->
                <?php if (!$view->hideTopNavAndMainMenu()) { ?>
                  <div id="nav2">
                    <ul class="rythm_nav2">
                      <?php foreach ($view->_menuBar as $key => $url) { ?>
                        <?php if (is_array($url)) { //array="open in new window"?>
                        <li><a href="<?= $url[0]; ?>" target="_blank" rel="noopener"><?= $key; ?></a></li>
                        <?php } else { ?>
                        <li><a href="<?= $url; ?>" rel="noopener"><?= $key; ?></a></li>
                        <?php } ?>
                      <?php } //foreach _menuBar?>
                    </ul>
                  </div>
                <?php } ?>

                <!-- Buffer after header -->
                <div class="buffer" style="height:20px;"></div>

                <!-- NAVIGATION -->
                <!-- Navigation Left menu -->
                <?php if(! $view->hideTopNavAndMainMenu()){ ?>
                  <div id="nav3">
                    <?php if (!$view->_isUserLogged) { ?>
                      <!-- non-authorized user menu -->
                      <ul class="rythm_nav3MainMenu">
                        <li class="title"><?= tr('main_menu'); ?></li>

                          <?php foreach ($view->_nonAuthUserMenu as $key => $url) { ?>
                            <li class="group">
                                <?php if (is_array($url)) { //array="open in new window"?>
                                  <a href="<?= $url[0]; ?>" target="_blank" rel="noopener"><?= $key; ?></a>
                                <?php } else { // !is_array($url)?>
                                  <a href="<?= $url; ?>" rel="noopener"><?= $key; ?></a>
                                <?php } // if-is_array($url)?>
                            </li>
                          <?php } //foreach?>

                      </ul>

                    <?php } else { //if-_isUserLogged?>

                      <!-- authorized menu -->
                      <ul class="rythm_nav3MainMenu">
                        <li class="title"><?= tr('main_menu'); ?></li>
                          <?php foreach ($view->_authUserMenu as $key => $url) { ?>
                            <li class="group">
                                <?php if (is_array($url)) { //array="open in new window"?>
                                  <a href="<?= $url[0]; ?>" target="_blank" rel="noopener"><?= $key; ?></a>
                                <?php } else { // !is_array($url)?>
                                  <a href="<?= $url; ?>" rel="noopener"><?= $key; ?></a>
                                <?php } // if-is_array($url)?>
                            </li>
                          <?php } //foreach?>
                      </ul>

                      <!-- custom user menu -->
                      <ul class="rythm_nav3UserMenu">
                        <li class="title"><?= tr('user_menu'); ?></li>
                          <?php foreach ($view->_customUserMenu as $key => $url) { ?>
                            <li class="group">
                              <a href="<?= $url; ?>">
                                  <?= $key; ?>
                              </a>
                            </li>
                          <?php } //foreach?>
                      </ul>


                      <!-- additional menu -->
                      <ul class="rythm_nav3AddsMenu">
                        <li class="title"><?= tr('mnu_additionalMenu'); ?></li>
                          <?php foreach ($view->_additionalMenu as $key => $url) { ?>
                            <li class="group">
                                <?php if (is_array($url)) { //array="open in new window"?>
                                  <a href="<?= $url[0]; ?>" target="_blank" rel="noopener"><?= $key; ?></a>
                                <?php } else { // !is_array($url)?>
                                  <a href="<?= $url; ?>" rel="noopener"><?= $key; ?></a>
                                <?php } // if-is_array($url)?>
                            </li>
                          <?php } //foreach?>
                      </ul>

                        <?php if ($view->_isAdmin) { ?>
                        <!-- admin menu -->
                        <ul>
                          <li class="title"><?= tr('administration'); ?></li>
                            <?php foreach ($view->_adminMenu as $key => $url) { ?>
                              <li class="group">
                                  <?php if (is_array($url)) { //array="open in new window"?>
                                    <a href="<?= $url[0]; ?>" target="_blank" rel="noopener"><?= $key; ?></a>
                                  <?php } else { // !is_array($url)?>
                                    <a href="<?= $url; ?>" rel="noopener"><?= $key; ?></a>
                                  <?php } // if-is_array($url)?>
                              </li>
                            <?php } //foreach?>
                        </ul>
                        <?php } //admin?>

                    <?php } //if-_isUserLogged?>

                    <!-- Main title -->
                  </div>
                <?php } ?>

      <!--     CONTENT -->
      <div class="templateContainer">
        {template}
      </div>

      <!-- FOOTER -->
      <div id="footer">
        <?php if ($view->_isUserLogged && $view->_displayOnlineUsers) { ?>
            <p>
              <span class="txt-black">{{online_users}}:</span>
              <span class="txt-white">
                <?php foreach ($view->_onlineUsers as $userId => $username) { ?>
                    <a class="links-onlusers" href="/viewprofile.php?userid=<?= $userId; ?>"><?= $username; ?></a>&nbsp;
                <?php } //foreach?>
              </span>
              <span class="txt-black">({{online_users_info}})</span>
            </p>
            <div class="spacer">&nbsp;</div>
        <?php } // user-logged && displayOnlineUsers?>

        <p>
          <?php foreach ($view->_footerMenu as $key => $url) { ?>
              <?php if (is_array($url)) { //array="open in new window"?>
                  <a href="<?= $url[0]; ?>" target="_blank" rel="noopener"><?= $key; ?></a> &nbsp;
              <?php } else { // !is_array($url)?>
                  <a href="<?= $url; ?>" rel="noopener"><?= $key; ?></a> &nbsp;
              <?php } // if-is_array($url)?>
          <?php } //foreach _footerMenu?>
        </p>

        <div class="bottom-page-container">
          <?= $view->licenseHtml; ?>

          <?php if (! $view->_crowdinInContextEnabled) { ?>
              <span class="bottom-flags">
                <?php foreach ($view->_languageFlags as $langFlag) { ?>
                  <a rel="nofollow" href="<?= $langFlag['link']; ?>">
                    <img class="img-navflag" src="<?= $langFlag['img']; ?>"
                         alt="<?= $langFlag['name']; ?> version" title="<?= $langFlag['name']; ?> version">
                  </a>
                <?php } //foreach-lang-flags?>
              </span>
          <?php } //$view->_crowdinInContextEnabled?>

          <?php if ($view->_crowdinInContextEnabled) { ?>
              <span>
                <a href="<?= $view->_crowdinInContextActionUrl; ?>">
                  <?= tr('common_disableCrowdinInContext'); ?>
                </a>
              </span>
          <?php } else { //if-_crowdinInContextEnabled?>
              <?php if ($view->_crowdinInContextAllowed) { ?>
                  <span>
                      <a href="<?= $view->_crowdinInContextActionUrl; ?>">
                          <?= tr('common_enableCrowdinInContext'); ?>
                      </a>
                  </span>
              <?php } // $v->_crowdinInContextAllowed?>
          <?php } // $v->_crowdinInContextEnabled?>
        </div>

      </div>
    </div>
    <!-- Cookies info -->
    <div class="cookies-message" id="cookies-message-div" style="display: none;" hidden="hidden">
      <p class="align-center">{{cookiesInfo}}
        <a href="javascript:WHCloseCookiesWindow();" class="btn btn-sm btn-success">&nbsp;X&nbsp;</a>
      </p>
    </div>
  </div>
  <script>
    // this is used by search widget
    function chname(newName, newHint, newSearchBy, searchPage) {
      document.getElementById("search_input").name = newName;
      document.getElementById("search_input").placeholder = newHint;
      document.getElementById("search_form").action = searchPage;
      document.getElementById("search_by").value = newSearchBy;
      topmenuTogle();
    }

    function topmenuTogle() {
        document.getElementById("topline-search-dropdown").classList.toggle("topline-dropdown-show");;
    }
  </script>
  <script>

      //responsive mode - toggle and cookie
      var cookie_name = "<?php echo $config['cookie']['name']; ?>_responsive_mode";

      var x = document.cookie,
          html = document.getElementsByTagName("html")[0];

      var re = new RegExp(cookie_name+'=1');

      if (!x.match(re)) {
          // html.classList.remove("responsive-enabled");
      } else {
          html.classList.add("responsive-enabled");
      }

      function responsiveToggle() {
          html.classList.toggle("responsive-enabled");

          if(html.classList.contains("responsive-enabled")){
              var expiryDate = new Date();
              expiryDate.setMonth(expiryDate.getMonth() + 1);
              var expires = "; expires=" + expiryDate.toGMTString();
              document.cookie = cookie_name+"=1" + expires + "; path=/";
              if (window.jQuery) {
                  maybe_change_navigation();
              }
          }else{
              document.cookie = cookie_name+"=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
              if (window.jQuery) {
                  $('#nav3 ul .group').slideDown();
              }
          }
      }

      //responsive mode - left nav
      function maybe_change_navigation(){
          if (screen.width <= 768) {
              $('#nav3 ul .group').slideUp();
          }
          else {
              $('#nav3 ul .group').slideDown();
          }
      }

      if (window.jQuery) {
          if(html.classList.contains("responsive-enabled")){
              maybe_change_navigation();
              $(window).on('resize', function(){
                  if (screen.width > 768) {
                      $('#nav3 ul .group').slideDown();
                  }
              });
          }

          $('#nav3').on("click", ".title", function(e){
              e.preventDefault();

              if($(this).parent().hasClass("opened")){
                  $(this).parent().removeClass("opened");
                  $(this).parent().find('.group').each(function(){
                      $(this).slideUp();
                  });
              }else{
                  $(this).parent().addClass("opened");
                  $(this).parent().find('.group').each(function(){
                      $(this).slideDown();
                  });
              }

          });
      }

  </script>
  <script src="/js/public.js"></script>
  <?php
      // fancyBox js should be loaded at the end of page
      if ($view->isFancyBoxEnabled()) {
          $view->callChunk('fancyBoxLoader', false, true);
      }
      // load defer JS at the end
      foreach ($view->getLocalJs() as $js) {
          if ($js['defer']) {?>
            <script src="<?= $js['url']; ?>"<?= $js['async'] ? ' async' : ''; ?> defer></script>
  <?php } //if
      } //foreach-js?>
  <!-- (C) The Opencaching Project 2020 -->
</body>
</html>
