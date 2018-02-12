<?php

use Utils\Debug\Debug;

global $tpl_subtitle;

?>
<!DOCTYPE html>
<html lang="<?=$view->getLang()?>" xml:lang="<?=$view->getLang()?>">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="keywords" content="<?=$view->_keywords?>">
  <meta name="author" content="<?=$view->_siteName?>">

  <title><?=$tpl_subtitle?>{title}</title>

  <link rel="shortcut icon" href="/images/<?=$config['headerFavicon']?>">
  <link rel="apple-touch-icon-precomposed" href="/images/oc_logo_144.png">

  <link rel="stylesheet" type="text/css" media="screen" href="<?=$view->screenCss?>">
  <link rel="stylesheet" type="text/css" media="print" href="<?=$view->printCss?>">

  <?php foreach( $view->getLocalCss() as $css ) { ?>
      <link rel="stylesheet" type="text/css" href="<?=$css?>">
  <?php } //foreach-css ?>

  {htmlheaders}
  {cachemap_header}

  <?php
      if( $view->isGoogleAnalyticsEnabled() ){
          $view->callChunkOnce( 'googleAnalytics', $view->getGoogleAnalyticsKey() );
      }
      if( $view->isjQueryEnabled()){
          $view->callChunk('jQuery');
      }
      if( $view->isjQueryUIEnabled()){
          $view->callChunk('jQueryUI');
      }
      if( $view->isTimepickerEnabled()){
          $view->callChunk('timepicker');
      }
      if( $view->isFancyBoxEnabled()){
          $view->callChunk('fancyBoxLoader', true, false);
      }
      if( $view->isGMapApiEnabled() ){
          if( !isset($GLOBALS['googlemap_key']) || empty($GLOBALS['googlemap_key']) ){
              Debug::errorLog("There is no googlemap_key value in site settings?! Map can't be loaded!");
          } else {
              $callback = isset($view->GMapApiCallback) ? $view->GMapApiCallback : null;
              $view->callChunk('googleMapsApi',
                  $GLOBALS['googlemap_key'], $view->getLang(), $callback);
          }
      }
  ?>

  <?php foreach( $view->getLocalJs() as $js ) { ?>
    <script src="<?=$js?>"></script>
  <?php } //foreach-css ?>
  <script src="/lib/js/CookiesInfo.js" async="async" defer="defer"></script>

</head>
<body {bodyMod} class="<?=$view->backgroundSeason?>">
  <div id="overall">
    <div class="page-container-1">
      <div class="seasonalBackground left <?=$view->backgroundSeason?>">&nbsp;</div>
      <div class="seasonalBackground right <?=$view->backgroundSeason?>">&nbsp;</div>

      <!-- HEADER -->
      <!-- OC-Logo -->
      <img src="<?=$view->_mainLogo?>" alt="OC logo" class="oc-logo">

      <!-- Sitename -->
      <div class="site-name">
        <p class="title"><a href="/index.php"><?=$view->_logoTitle?></a></p>
        <p class="subtitle"><a href="/index.php"><?=$view->_logoSubtitle?></a></p>
      </div>

      <!-- Flag navigations -->
      <div class="navflag-container">
        <div class="navflag">
          <ul>
            <?php foreach($view->_languageFlags as $langFlag){ ?>
                <li>
                  <a rel="nofollow" href="<?=$langFlag['link']?>">
                    <img class="img-navflag" src="<?=$langFlag['img']?>" alt="<?=$langFlag['name']?> version" title="<?=$langFlag['name']?> version">
                  </a>
                </li>
            <?php } //forach-lang-flags ?>
          </ul>
        </div>
      </div>

      <!-- Site slogan -->
                <div class="site-slogan-container">
                    <form method="get" action="search.php" name="search_form" id="search_form">
                        <div class="site-slogan">
                            <div>
                                <p class="search">
                                    <input type="radio" onclick="chname('waypointname','search.php');" name="searchto" id="st_1" value="searchbywaypointname" class="radio" checked="checked">
                                    <label for="st_1">{{waypointname_label}}</label>&nbsp;&nbsp;
                                    <?php if ($config['quick_search']['byowner']) { ?>
                                      <input type="radio" onclick="chname('owner','search.php');" name="searchto" id="st_2" value="searchbyowner" class="radio">
                                      <label for="st_2">{{owner_label}}</label>&nbsp;&nbsp;
                                    <?php } ?>
                                    <?php if ($config['quick_search']['byfinder']) { ?>
                                      <input type="radio" onclick="chname('finder','search.php');" name="searchto" id="st_3" value="searchbyfinder" class="radio">
                                      <label for="st_3">{{finder_label}}</label>&nbsp;&nbsp;
                                    <?php } ?>
                                    <?php if ($config['quick_search']['byuser']) { ?>
                                      <input type="radio" onclick="chname('username','searchuser.php');" name="searchto" id="st_4" value="searchbyuser" class="radio">
                                      <label for="st_4">{{user}}</label>&nbsp;&nbsp;
                                    <?php } ?>
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
                                </p>
                            </div>
                            <div class="form-group-xs">
                                <input id="search_input" type="text" name="waypointname" class="form-control input100" style="color:gray;">
                                <input type="submit" name="submit" value="{{search}}" class="btn btn-default btn-xs">
                            </div>
                        </div>
                    </form>
                </div>

                <div id="loginbox-container">
                    <div id="loginbox">
                        <?php if($view->_isUserLogged){ //if-user-logged ?>
                            <?=tr('logged_as')?>
                            <a href="/viewprofile.php"><?=$view->_username?></a> -
                            <a href="/login.php?action=logout"><?=tr('logout')?></a>

                        <?php } else { //user-not-logged ?>
                            <form action="login.php?action=login" method="post" name="login"
                                  style="display: inline;" class="form-group-sm">
                                  <?=tr('loginForm_userOrEmail')?>:&nbsp;
                                  <input name="email" size="10" type="text" class="form-control input100" value="" autocomplete="username">
                                  &nbsp;<?=tr('loginForm_password')?>:&nbsp;
                                  <input name="password" size="10" type="password" class="form-control input100" value="" autocomplete="current-password">
                                  &nbsp;
                                  <input type="hidden" name="target" value="<?=$view->_target?>" />
                                  <input type="submit" value="<?=tr('login')?>" class="btn btn-primary btn-sm" />
                            </form>
                        <?php } //user-not-logged ?>
                    </div>
                </div>

                <!-- Header banner     -->
                <div class="header">
                  <img src="/images/head/rotator.php" alt="Banner">
                </div>

                <!-- Navigation - horizontal menu bar -->
                <div id="nav2">
                    <ul>
                        <?php foreach($view->_menuBar as $key=>$url) { ?>
                          <?php if(is_array($url)) { //array="open in new window" ?>
                            <li><a href="<?=$url[0]?>" target="_blank" rel="noopener"><?=$key?></a>
                          <?php } else { ?>
                            <li><a href="<?=$url?>" rel="noopener"><?=$key?></a>
                          <?php } ?>
                        <?php } //foreach _menuBar?>
                    </ul>
                </div>

                <!-- Buffer after header -->
                <div class="buffer" style="height:20px;"></div>

                <!-- NAVIGATION -->
                <!-- Navigation Left menu -->

                <div id="nav3">
                    <?php if(!$view->_isUserLogged) { ?>
                    <!-- non-authorized user menu -->
                    <ul>
                      <li class="title"><?=tr('main_menu')?></li>

                      <?php foreach($view->_nonAuthUserMenu as $key => $url){ ?>
                        <li class="group">
                            <?php if(is_array($url)) { //array="open in new window" ?>
                              <a href="<?=$url[0]?>" target="_blank" rel="noopener"><?=$key?></a>
                            <?php } else { // !is_array($url) ?>
                              <a href="<?=$url?>" rel="noopener"><?=$key?></a>
                            <?php } // if-is_array($url) ?>
                        </li>
                      <?php } //foreach ?>

                    </ul>

                <?php } else { //if-_isUserLogged ?>

                    <!-- authorized menu -->
                    <ul>
                      <li class="title"><?=tr('main_menu')?></li>
                      <?php foreach($view->_authUserMenu as $key => $url){ ?>
                        <li class="group">
                            <?php if(is_array($url)) { //array="open in new window" ?>
                              <a href="<?=$url[0]?>" target="_blank" rel="noopener"><?=$key?></a>
                            <?php } else { // !is_array($url) ?>
                              <a href="<?=$url?>" rel="noopener"><?=$key?></a>
                            <?php } // if-is_array($url) ?>
                        </li>
                      <?php } //foreach ?>
                    </ul>

                    <!-- custom user menu -->
                    <ul>
                      <li class="title"><?=tr('user_menu')?></li>
                      <?php foreach($view->_customUserMenu as $key => $url){ ?>
                        <li class="group">
                            <a href="<?=$url?>">
                              <?=$key?>
                            </a>
                        </li>
                      <?php } //foreach ?>
                    </ul>


                    <!-- additional menu -->
                    <ul>
                      <li class="title"><?=tr('mnu_additionalMenu')?></li>
                      <?php foreach($view->_additionalMenu as $key => $url){ ?>
                        <li class="group">
                            <?php if(is_array($url)) { //array="open in new window" ?>
                              <a href="<?=$url[0]?>" target="_blank" rel="noopener"><?=$key?></a>
                            <?php } else { // !is_array($url) ?>
                              <a href="<?=$url?>" rel="noopener"><?=$key?></a>
                            <?php } // if-is_array($url) ?>
                        </li>
                      <?php } //foreach ?>
                    </ul>

                    <?php if ($view->_isAdmin) { ?>
                      <!-- admin menu -->
                      <ul>
                          <li class="title"><?=tr('administration')?></li>
                          <?php foreach($view->_adminMenu as $key => $url){ ?>
                            <li class="group">
                                <?php if(is_array($url)) { //array="open in new window" ?>
                                  <a href="<?=$url[0]?>" target="_blank" rel="noopener"><?=$key?></a>
                                <?php } else { // !is_array($url) ?>
                                  <a href="<?=$url?>" rel="noopener"><?=$key?></a>
                                <?php } // if-is_array($url) ?>
                            </li>
                          <?php } //foreach ?>
                      </ul>
                    <?php } //admin ?>

                <?php } //if-_isUserLogged ?>

                    <!-- Main title -->
                </div>

      <!--     CONTENT -->
      <div class="templateContainer">
        {template}
      </div>

      <!-- FOOTER -->
      <div id="footer">
        <?php if ($view->_isUserLogged && $view->_displayOnlineUsers) { ?>
            <p>
              <span class="txt-black">{{online_users}}: </span>
              <span class="txt-white">
                <?php foreach($view->_onlineUsers as $userId=>$username){ ?>
                    <a class="links-onlusers" href="/viewprofile.php?userid=<?=$userId?>"><?=$username?></a>&ensp;
                <?php } //foreach ?>
              </span>
              <span class="txt-black"> ({{online_users_info}})</span>
            </p>
            <div class="spacer">&nbsp;</div>
        <?php } // user-logged && displayOnlineUsers ?>

        <p>
          <?php foreach($view->_footerMenu as $key=>$url){ ?>
              <?php if(is_array($url)) { //array="open in new window" ?>
                  <a href="<?=$url[0]?>" target="_blank" rel="noopener"><?=$key?></a>
              <?php } else { // !is_array($url) ?>
                  <a href="<?=$url?>" rel="noopener"><?=$key?></a>
              <?php } // if-is_array($url) ?>
          <?php } //foreach _footerMenu ?>
        </p>

        <p><br><?=$view->licenseHtml?></p>

      </div>
    </div>
    <!-- Cookies info -->
    <div class="cookies-message" id="cookies-message-div" style="display: none;" hidden="hidden">
      <p class="align-center">{{cookiesInfo}}
        <a href="javascript:WHCloseCookiesWindow();" class="btn btn-sm btn-success">&nbsp;X&nbsp;</a>
      </p>
    </div>
  </div>
  <?php
      // fancyBox js should be loaded at th end of page
      if( $view->isFancyBoxEnabled()){
          $view->callChunk('fancyBoxLoader', false, true);
      }
  ?>
  <script>
    // this is used by search widget
    function chname(newName,searchPage) {
      document.getElementById("search_input").name = newName;
      document.getElementById("search_form").action = searchPage;
      return false;
    }
  </script>
  <!-- (C) The Opencaching Project 2018 -->
</body>
</html>
