<!DOCTYPE html>
<html lang="<?=$view->getLang()?>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="keywords" content="<?=$view->_keywords?>">
    <meta name="author" content="<?=$view->_siteName?>">

    <title><?=$view->_title?></title>

    <link rel="shortcut icon" href="<?=$view->_favicon?>">
    <link rel="apple-touch-icon-precomposed" href="<?=$view->_appleLogo?>">

    <?php foreach( $view->getLocalCss() as $css ) { ?>
      <link rel="stylesheet" type="text/css" href="<?=$css?>">
    <?php } //foreach-css ?>


    <?php

        $view->callChunk('bootstrapCss'); // always load bootstrap

        if( $view->isGoogleAnalyticsEnabled() ){
            $view->callChunkOnce( 'googleAnalytics', $view->getGoogleAnalyticsKey() );
        }
        if( $view->isjQueryUIEnabled()){
            $view->callChunk('jQueryUI');
        }
        if( $view->isTimepickerEnabled()){
            $view->callChunk('timepicker');
        }
        if( $view->isLightBoxEnabled()){
            $view->callChunk('lightBoxLoader', true, false);
        }
        if( $view->isGMapApiEnabled() ){
            if( !isset($GLOBALS['googlemap_key']) || empty($GLOBALS['googlemap_key']) ){
                $view->errorLog("There is no googlemap_key value in site settings?!".
                            "Map can't be loaded!");
            }else{
                $callback = isset($view->GMapApiCallback)?$view->GMapApiCallback:null;
                $view->callChunk('googleMapsApi',
                    $GLOBALS['googlemap_key'], $view->getLang(), $callback);
            }
        }
    ?>


<!-- (C) The Opencaching Project 2017 -->

</head>

<body class="<?=$view->_backgroundSeason?>">

      <div class="page-wrapper">
        <div class="d-none d-lg-block left seasonalBackground <?=$view->_backgroundSeason?>"></div>
        <div class="d-none d-lg-block right seasonalBackground <?=$view->_backgroundSeason?>"></div>

        <div class="container-fluid">

          <div class="row">
            <div id="header" class="col">

              <div class="row">
<!-- logo -->
                <div id="headerOcLogo" class="col-5">
                    <a href="/index.php">
                      <img src="<?=$view->_mainLogo?>" alt="Opencaching logo" />
                      <div class="title"><?=$view->_logoTitle?></div>
                      <div class="subtitle"><?=$view->_logoSubtitle?></div>
                    </a>
                </div>
<!-- / logo -->
                <div class="col-7 text-right">
<!-- flags -->
                  <div id="headerFlags" class="my-2 d-print-none">
                    <?php foreach($view->_languageFlags as $langFlag){ ?>
                      <a rel="nofollow" href="<?=$langFlag['link']?>">
                        <img src="<?=$langFlag['img']?>"
                             alt="<?=$langFlag['name']?> version"
                             title="<?=$langFlag['name']?> version">
                      </a>
                    <?php } //forach-lang-flags ?>
                  </div>
<!-- / flags -->
<!-- login-box -->
                  <div id="headerLoginBox d-print-none">
                    <?php if($view->_isUserLogged){ //if-user-logged ?>
                      <?=$tr('logged_as')?>
                      <a href="/viewprofile.php"><?=$view->_username?></a>
                      <a href="/login.php?action=logout"
                         class="btn btn-outline-primary btn-sm ml-1">
                        <?=tr('logout')?>
                      </a>

                    <?php } else { //user-not-logged ?>

                      <form action="login.php?action=login" method="post"
                            class="form-inline justify-content-end">

                        <input name="email" type="text"
                          class="form-control form-control-sm mr-1" value=""
                          placeholder="<?=$tr('user_or_email')?>" />

                        <input name="password" type="password"
                          class="form-control form-control-sm mr-1" value=""
                          placeholder="<?=$tr('password')?>" />

                        <input type="hidden" name="target" value="<?=$view->_target?>" />
                        <input type="submit" value="<?=$tr('login')?>"
                               class="btn btn-primary btn-sm" />

                      </form>

                    <?php } //user-not-logged ?>
                  </div>
<!-- / login-box -->
                </div><!-- col -->
              </div><!-- row -->


              <div id="headerBanner" class="row d-print-none justify-content-end">

                <div class="col py-2 pr-0 text-right">
<!-- quick search -->
                    <script type="text/javascript">
                      // this is used by search widget below
                      function qSearch_setMode(newName,searchPage) {
                        $("#qSearchText").attr("name", newName);
                        $("#qSearchForm").attr("action",searchPage);
                        return false;
                      }
                    </script>

                    <form method="get" action="/search.php" name="search_form" id="qSearchForm"
                          class="d-inline-block px-3 py-2 text-light">

                      <div class="form-row">
                        <div class="col text-right">
                          <div class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input class="form-check-input" type="radio" name="searchto"
                                     value="searchbywaypointname" checked
                                     onclick="qSearch_setMode('waypointname','search.php');">

                              <?=$tr('waypointname_label')?>
                            </label>
                          </div>

                          <?php if ($view->_qSearchByOwnerEnabled) { ?>
                          <div class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input class="form-check-input" type="radio" name="searchto"
                                     value="searchbyowner"
                                     onclick="qSearch_setMode('owner','search.php');">

                              <?=$tr('owner_label')?>
                            </label>
                          </div>
                          <?php } ?>

                          <?php if ($view->_qSearchByFinderEnabled) { ?>
                          <div class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input class="form-check-input" type="radio" name="searchto"
                                     value="searchbyfinder"
                                     onclick="qSearch_setMode('finder','search.php');">

                              <?=$tr('finder_label')?>
                            </label>
                          </div>
                          <?php } ?>

                          <?php if ($view->_qSearchByUserEnabled) { ?>
                          <div class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input class="form-check-input" type="radio" name="searchto"
                                     value="searchbyuser"
                                     onclick="qSearch_setMode('username','searchuser.php');">

                              <?=$tr('user')?>
                            </label>
                          </div>
                          <?php } ?>

                        </div>
                      </div>

                      <div class="form-row justify-content-end text-right">
                          <div class="col">
                            <input id="qSearchText" type="text" name="waypointname"
                                   class="form-control-sm d-inline-block">

                            <input type="submit" name="submit" value="<?=$tr('search')?>"
                                   class="btn btn-light btn-sm d-inline-block">
                          </div>
                      </div>

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

                    </form>
<!-- / quick search -->
                </div><!-- col -->
              </div><!-- row-banner -->

              <div class="row"><!-- horiznotal-nav -->
<!-- horizontal nav-bar -->

                <ul class="nav nav-pills">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>
                  <li class="nav-item">
                    <a class="nav-link active" href="#">Active</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                  </li>
                </ul>

<!-- / horizontal nav-bar -->
              </div><!-- row-horizontal-nav -->
            </div>
          </div>

          <div class="row">
            <div class="col-lg-2 d-none d-lg-block sidebar">


                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                  <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-expanded="true">Home</a>
                  <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-expanded="true">Profile</a>
                  <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-expanded="true">Messages</a>
                  <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Settings</a>
                </div>
                <div class="tab-content" id="v-pills-tabContent">
                  <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">...</div>
                  <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">...</div>
                  <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">...</div>
                  <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">...</div>
                </div>


            </div>
            <div class="col-lg-10 content">
              <?php $view-> _callTemplate(); ?>
            </div>
          </div> <!-- row-mainArea -->


          <div class="row">
            <div class="col footer text-center">

              <?php if($view->_isUserLogged && $view->_displayOnlineUsers){ ?>
                <h6>
                <?=$tr('online_users')?>:
                <?=count($view->_onlineUsers)?>

                <?=$tr('online_users_info')?>:
                <?php foreach($view->_onlineUsers as $userId=>$username){ ?>
                <a href="/viewprofile.php?userid=<?=$userId?>"><?=$username?></a>
                <?php } //foreach ?>
                </h6>
              <?php } // user-logged && displayOnlineUsers ?>

              <div class="m-2">
                  <?php foreach($view->footerMenu as $menuEntry){ ?>
                    <a class="btn btn-outline-primary btn-sm" href="<?=$menuEntry[1]?>"><?=$tr($menuEntry[0])?></a>
                  <?php } //oreach footerMenu ?>
              </div>

              <div class="m-2">
                <?=$view->licenseHtml?>
              </div>
            </div>
          </div><!-- row-container -->

        </div><!--/ container -->

      </div><!-- / page-wrapper -->




    <?php
        // JS scripts to loaded at the end
        $view->callChunk('jQuery');      // always load jQuery
        $view->callChunk('bootstrapJs'); // always load bootstrap

        if( $view->isLightBoxEnabled()){
            $view->callChunk('lightBoxLoader', false, true);
        }
    ?>
</body>

</html>

