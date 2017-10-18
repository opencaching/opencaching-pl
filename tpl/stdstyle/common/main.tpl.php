<?php

use Utils\Database\OcDb;
use lib\Objects\GeoCache\PrintList;
use Utils\DateTime\Year;
use Utils\Debug\Debug;
use lib\Objects\Admin\ReportCommons as ReportCtl;

// load menu
global $mnu_selmenuitem, $tpl_subtitle, $absolute_server_URI, $mnu_siteid /* which menu item should be highlighted */, $site_name;

require_once $stylepath . '/lib/menu.php';


// decide which menu item should be selected
if ( isset($mnu_siteid) ) {
    // dla wybranych stron (viewprofile i ustatsg1|2) nie utawia sie menu na template
    // tylko na start...
    $menu_item_siteid = $mnu_siteid;
}else{
    // dla pozostałych templateName
    $menu_item_siteid = $tplname;
}

// znajdz pageIdx dla bieżacego templatu
$pageidx = mnu_MainMenuIndexFromPageId($menu, $menu_item_siteid);

// add selected menu item as a apendix to site title (tpl_subtitle) (?)
if ($tplname != 'start'){
    $tpl_subtitle .= htmlspecialchars($mnu_selmenuitem['title'] . ' - ', ENT_COMPAT, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="<?=$view->getLang()?>" xml:lang="<?=$view->getLang()?>">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">

        <meta name="keywords" content="geocaching, opencaching, skarby, poszukiwania, geocashing, longitude, latitude, utm, coordinates, treasure hunting, treasure, GPS, global positioning system, garmin, magellan, mapping, geo, hiking, outdoors, sport, hunt, stash, cache, geocaching, geocache, cache, treasure, hunting, satellite, navigation, tracking, bugs, travel bugs">
        <meta name="author" content="{site_name}">

        <link rel="stylesheet" type="text/css" media="screen" href="<?=$view->screenCss?>">
        <link rel="stylesheet" type="text/css" media="print" href="<?=$view->printCss?>">


        <link rel="shortcut icon" href="/images/<?=$config['headerFavicon']?>">
        <link rel="apple-touch-icon-precomposed" href="/images/oc_logo_144.png">


        <?php foreach( $view->getLocalCss() as $css ) { ?>
          <link rel="stylesheet" type="text/css" href="<?=$css?>">
        <?php } //foreach-css ?>

        <title><?=$tpl_subtitle?>{title}</title>

        <script type="text/javascript" src="/lib/enlargeit/enlargeit.js"></script>


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
            if( $view->isLightBoxEnabled()){
                $view->callChunk('lightBoxLoader', true, false);
            }
            if( $view->isGMapApiEnabled() ){
                if( !isset($GLOBALS['googlemap_key']) || empty($GLOBALS['googlemap_key']) ){
                    Debug::errorLog("There is no googlemap_key value in site settings?!".
                                "Map can't be loaded!");
                }else{
                    $callback = isset($view->GMapApiCallback)?$view->GMapApiCallback:null;
                    $view->callChunk('googleMapsApi',
                        $GLOBALS['googlemap_key'], $view->getLang(), $callback);
                }
            }
        ?>

        <script type='text/javascript' src='/lib/js/CookiesInfo.js'></script>
        <script type='text/javascript'>WHSetText('{{cookiesInfo}}');</script>

        <script type="text/javascript">
            // this is used by search widget
            function chname(newName,searchPage) {
                document.getElementById("search_input").name = newName;
                document.getElementById("search_form").action = searchPage;
                return false;
            }
        </script>

    </head>
    <body {bodyMod} class="<?=$view->backgroundSeason?>">

        <div id="overall">
            <div class="page-container-1" style="position: relative;">

                <div class="seasonalBackground left <?=$view->backgroundSeason?>">&nbsp;</div>
                <div class="seasonalBackground right <?=$view->backgroundSeason?>">&nbsp;</div>

                <!-- HEADER -->
                <!-- OC-Logo -->
                <div><img src="<?=$view->_mainLogo?>" alt="OC logo" style="margin-top:5px; margin-left:3px;"></div>

                <!-- Sitename -->
                <div class="site-name">
                    <p class="title"><a href="index.php"><?=$view->_logoTitle?></a></p>
                    <p class="subtitle"><a href="index.php"><?=$view->_logoSubtitle?></a></p>
                </div>

                <!-- Flag navigations -->
                <div class="navflag-container">
                    <div class="navflag">
                        <ul>
                            <?php foreach($view->_languageFlags as $langFlag){ ?>
                                <li>
                                    <a rel="nofollow" href="<?=$langFlag['link']?>">
                                        <img class="img-navflag" src="<?=$langFlag['img']?>" alt="<?=$langFlag['name']?> version"
                                             title="<?=$langFlag['name']?> version">
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
                                  <input name="email" size="10" type="text" class="form-control input100" value="" />
                                  &nbsp;<?=tr('loginForm_password')?>:&nbsp;
                                  <input name="password" size="10" type="password" class="form-control input100" value="" />
                                  &nbsp;
                                  <input type="hidden" name="target" value="<?=$view->_target?>" />
                                  <input type="submit" value="<?=tr('login')?>" class="btn btn-primary btn-sm" />
                            </form>
                        <?php } //user-not-logged ?>
                    </div>
                </div>

                <!-- Header banner     -->
                <div class="header">
                    <div style="width:970px; padding-top:1px;">
                      <img src="/images/head/rotator.php" alt="Banner" style="border:0px;">
                    </div>
                </div>

                <!-- Navigation - horizontal menu bar -->
                <div id="nav2">
                    <ul>
                        <?php
                        // znajdz idx dla 'mylist'
                        $dowydrukuidx = mnu_MainMenuIndexFromPageId($menu, "mylist");

                        // dodaj liczbę pozycji printlisty do opsiu menu 'mylist'
                        if ( !empty(PrintList::GetContent()) ) {
                            $menu[$dowydrukuidx]['visible'] = true;
                            $menu[$dowydrukuidx]['menustring'] .=
                                " (" . count(PrintList::GetContent()) . ")";
                        }


                        if (isset($menu[$pageidx])) {
                            mnu_EchoMainMenu($menu[$pageidx]['siteid']);
                        }else{
                            mnu_EchoMainMenu(null);
                        }
                        ?>
                    </ul>
                </div>

                <!-- Buffer after header -->
                <div class="buffer" style="height:30px;"></div>

                <!-- NAVIGATION -->
                <!-- Navigation Left menu -->

                <div id="nav3">
                    <?php
                    //Main menu
                    $mainmenuidx = mnu_MainMenuIndexFromPageId($menu, "start");

                    if (isset($menu[$mainmenuidx]['submenu'])) {

                        $registeridx = mnu_MainMenuIndexFromPageId($menu[$mainmenuidx]["submenu"], "register");

                        if ($usr) {
                            $menu[$mainmenuidx]['submenu'][$registeridx]['visible'] = false;
                        } else {
                            $menu[$mainmenuidx]['submenu'][$registeridx]['visible'] = true;
                        }
                        echo '<ul>';
                        echo '<li class="title">' . tr('main_menu') . '</li>';
                        mnu_EchoSubMenu($menu[$mainmenuidx]['submenu'], $menu_item_siteid, 1, false);
                        echo '</ul>';
                    }

                    if ($usr && isset($_SESSION['user_id'])) {
                        $myhomeidx = mnu_MainMenuIndexFromPageId($menu, "myhome");
                        $myprofileidx = mnu_MainMenuIndexFromPageId($menu[$myhomeidx]["submenu"], "myprofile");
                        // [fixme] Have to do the menu unrolling... in not such a crappy way
                        // ^ agreed, but it's 1:30 AM
                        if ($menu_item_siteid == "myprofile" || $menu_item_siteid == "myprofile_change" || $menu_item_siteid == "newemail" || $menu_item_siteid == "newpw" || $menu_item_siteid == "change_statpic") {
                            for ($i = 0; $i < count($menu[$myhomeidx]["submenu"][$myprofileidx]['submenu']); $i++) {
                                $menu[$myhomeidx]["submenu"][$myprofileidx]['submenu'][$i]['visible'] = true;
                            }
                        }
                        echo '<ul>';
                        echo '<li class="title">' . $menu[$myhomeidx]["title"] . '</li>';
                        mnu_EchoSubMenu($menu[$myhomeidx]['submenu'], $menu_item_siteid, 1, false);
                        echo '</ul>';
                    }

                    if (isset($usr['admin']) && $usr['admin']) {
                        $new_reports = ReportCtl::getReportsCountByStatus(ReportCtl::STATUS_NEW);
                        $active_reports = ReportCtl::getReportsCountByStatus(ReportCtl::STATUS_OPEN);

                        $db = OcDb::instance();
                        $new_pendings = $db->simpleQueryValue("SELECT COUNT(status) FROM caches WHERE status = 4", 0);
                        $in_review_count = $db->simpleQueryValue(
                            "SELECT COUNT(*) FROM caches JOIN approval_status ON approval_status.cache_id = caches.cache_id
                            WHERE caches.status = 4", 0);

                        $adminidx = mnu_MainMenuIndexFromPageId($menu, "admin/reports_list");
                        $menu[$adminidx]['visible'] = false;
                        $zgloszeniaidx = mnu_MainMenuIndexFromPageId($menu[$adminidx]["submenu"], "admin/reports_list");
                        if ($active_reports > 0){
                            $menu[$adminidx]["submenu"][$zgloszeniaidx]['menustring'] .= " (" . $new_reports . "/" . $active_reports . ")";
                        }
                        $zgloszeniaidx = mnu_MainMenuIndexFromPageId($menu[$adminidx]["submenu"], "viewpendings");
                        if ($new_pendings > 0){
                            $waitingForAssigne = $new_pendings - $in_review_count;
                        } else {
                            $waitingForAssigne = 0;
                        }
                        $menu[$adminidx]["submenu"][$zgloszeniaidx]['menustring'] .= " (" . $waitingForAssigne . "/" . $new_pendings .  ")";
                        ?>

                        <ul>
                          <li class="title"><?=$menu[$adminidx]["title"]?></li>
                          <?php mnu_EchoSubMenu($menu[$adminidx]['submenu'], $menu_item_siteid, 1, false); ?>
                        </ul>

                    <?php } //admin ?>

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
                                <a class="links-onlusers" href="/viewprofile.php?userid=<?=$userId?>">
                                  <?=$username?>
                                </a>&nbsp;
                              <?php } //foreach ?>

                          </span>
                          <span class="txt-black"> ({{online_users_info}})</span>
                        </p>
                        <div class="spacer">&nbsp;</div>
                    <?php } // user-logged && displayOnlineUsers ?>


                    <?php
                    $bottomMenuResult = buildBottomMenu($config['bottom_menu']);
                    echo $bottomMenuResult;
                    ?>

                </div>
                <!-- (C) The Opencaching Project 2017 -->
            </div>
        </div>
        <?php
            // lightbox js should be loaded at th end of page
            if( $view->isLightBoxEnabled()){
                $view->callChunk('lightBoxLoader', false, true);
            }
        ?>
    </body>
</html>
