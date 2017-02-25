
<link rel="stylesheet" href="tpl/stdstyle/js/lightbox2/dist/css/lightbox.min.css">
<link rel="stylesheet" href="tpl/stdstyle/css/lightTooltip.css">

<script src="tpl/stdstyle/js/lightbox2/dist/js/lightbox-plus-jquery.min.js"></script>

<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>


<script>
    var confirmRmLogTranslation = '{{confirm_remove_log}}';
</script>

<script src="<?=$view->viewcache_js?>"></script>


<input type="hidden" id="cacheid" value="{cacheid}">
<input type="hidden" id="logEnteriesCount" value="<?=$view->logEnteriesCount?>">
<input type="hidden" id="owner_id" value="<?=$view->ownerId?>">
<input type="hidden" id="includeDeletedLogs" value="<?=$view->displayDeletedLogs?>">
<input type="hidden" id="uType" value="<?=($view->isAdminAuthorized)?'1':'0'?>">

<div class="content2-container line-box">
    <div class="">

    <div class="nav4">

<!--
        <?php if(!$view->isUserAuthorized){ ?>
          <span class="notlogged-cacheview"><?=tr('cache_logged_required')?></span>;
        <?php }else{ ?>

        <button class="btn btn-success btn-md">
          <img src="images/actions/new-entry-16.png" />&nbsp;<?=tr('new_log_entry')?>
        </button>
        <button class="btn btn-default btn-md">
          <img src="images/actions/watch-16.png" />&nbsp;<?=$view->watchLabel?>
        </button>
        <button class="btn btn-default btn-md">
          <img src="images/actions/report-problem-16.png" />&nbsp;<?=tr('report_problem')?>
        </button>
        <button class="btn btn-default btn-md">
          <img src="images/actions/print-16.png" />&nbsp;<?=tr('print')?>
        </button>
        <button class="btn btn-default btn-md">
          <img src="<?=$view->printListIcon?>" />&nbsp;<?=$view->printListLabel?>
        </button>
        <button class="btn btn-danger btn-md">
          <img src="images/actions/ignore-16.png" />&nbsp;<?=$view->ignoreLabel?>
        </button>
        <button class="btn btn-primary btn-md">
          <img src="images/actions/edit-16.png" />&nbsp;<?=tr('edit')?>
        </button>
-->


        <?php
            // menu kesza - przyciski - wpis do logu etc... PRZEROBIĆ!

            $clidx = mnu_MainMenuIndexFromPageId($menu, "viewcache_menu");
            if ( $menu[$clidx]['title'] != '' ) {

                $menu[$clidx]['visible'] = false;
        ?>

                <ul id="cachemenu">

                  <li class="title"><?=$menu[$clidx]["title"]?></li>

                  <?php mnu_EchoSubMenu($menu[$clidx]['submenu'], $tplname, 1, false); ?>

                </ul>
            <?php } // if-$menu[$clidx]['title'] != '' ?>

        <?php } //else ?>


    </div>

    <div class="content2-container-2col-left" style="width:60px; clear: left;">
        <div>
          <img src="<?=$view->geoCache->getCacheIcon()?>" class="icon32" id="viewcache-cacheicon" alt="{cachetype}" title="{cachetype}">
        </div>
        <div><img src='<?=$view->geoCache->getDifficultyIcon()?>' class='img-difficulty' width='19' height='16' alt='' title='<?=$view->diffTitle?>'></div>
        <div><img src='<?=$view->geoCache->getTerreinIcon()?>' class='img-difficulty' width='19' height='16' alt='' title='<?=$view->terrainTitle?>'></div>
        <div>

          <?php if( !$view->geoCache->isEvent() ) {
                if (($view->geoCache->getFounds() + $view->geoCache->getNotFounds() + $view->geoCache->getNotesCount()) != 0) { ?>

                  <script type="text/javascript">
                    function cacheStatPopup(){
                      var url = "cache_stats.php?cacheid=<?=$view->geoCache->getCacheId()?>&amp;popup=y";
                      window.open(url,'Cache_Statistics',"width=500,height=750,resizable=yes,scrollbars=1");
                    }
                  </script>
                  <a class="links2 lightTipped" href="#" onclick="cacheStatPopup()">
                     <img src="tpl/stdstyle/images/blue/stat1.png" alt="" title="">
                  </a>
                  <div class="lightTip"><?=tr('show_statictics_cache')?></div>

                <?php } else { ?>
                  <a class="links2 lightTipped" href="#">
                     <img src="tpl/stdstyle/images/blue/stat1.png" alt="" title="">
                  </a>
                  <div class="lightTip"><?=tr('not_stat_cache')?></div>
                <?php }
          } //if-not-event ?>

        </div>
    </div>


        <div class="content2-container-2col-left" id="cache_name_block">
            <table style="width:100%;">
              <tr>
                <td style="width:70%; vertical-align:top;">

                  <span class="content-title-noshade-size5">
                    <?=$view->cachename?> - <?=$view->geoCache->getWaypointId()?>

                    <?php if($view->geoCache->isTitled()) { ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <img src="tpl/stdstyle/images/free_icons/award_star_gold_1.png" class="icon16" alt="<?=$view->titledDesc?>" title="<?=$view->titledDesc?>" />
                    <?php } //if-titled ?>

                  </span><br>

                  <p class="content-title-noshade-size1"><?=$view->geoCacheDesc->getShortDescToDisplay()?></p>

                  <p><?=tr('owner')?>&nbsp;
                      <a class="links" href="viewprofile.php?userid=<?=$view->ownerId?>">{owner_name}</a>

                      <?php if($view->geoCache->isAdopted() ) { ?>
                          <br><?=tr('creator')?>&nbsp;
                          <a class="links" href="viewprofile.php?userid={creator_userid}">{creator_name}</a>
                      <?php } //if-is-adopted ?>

                  </p>
                  <?php if($view->geoCache->isEvent()) { ?>
                      <span class="participants">
                        <img src="tpl/stdstyle/images/blue/meeting.png" width="22" height="22" alt=""/>&nbsp;
                        <a href="#" onclick="javascript:window.open('event_attendance.php?id=<?=$view->geoCache->getCacheId()?>&amp;popup=y','<?=tr('list_of_participants')?>','width=320,height=440,resizable=no,scrollbars=1')">
                          <?=tr('list_of_participants')?>
                        </a>
                      </span>
                  <?php } //if-is-event ?>
                </td>
                <td>
                  <?php if($view->geoPathSectionDisplay) { ?>
                  <div style="text-align:center; border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; border:1px solid #337fed; padding: 5px;">

                    <div style="text-align:center; color: #337fed; border-bottom: 1px solid #337fed;"><?=tr('cache_belongs_to_geopath')?>!</div>
                    <table width="99%">
                      <?php foreach($view->geoPathsList as $geoPath){ ?>
                        <tr>
                          <td width="51">
                            <img width="50" src="<?=$geoPath->img?>">
                          </td>
                          <td align="center">
                            <span style="font-size: 13px;">
                            <a href="powerTrail.php?ptAction=showSerie&ptrail=<?=$geoPath->id?>"><?=$geoPath->name?></a>
                          </td>
                        </tr>
                      <?php } //foreach ?>
                    </table>
                  </div>
                  <?php } //if($view->ptDisplay) ?>
                </td>
              </tr>
           </table>
        </div>

    </div>
</div>


<!-- End Text Container -->
<!-- Text container -->


<div class="content2-container">
    <div class="content2-container-2col-left" id="viewcache-baseinfo">
        <p class="content-title-noshade-size3">
            <img src="tpl/stdstyle/images/blue/kompas.png" class="icon32" alt="" title="">
            <b>
              <?php if($view->isUserAuthorized || $view->alwaysShowCoords ) { ?>

                <?php if(!$view->userModifiedCacheCoords) { ?>
                  <?=$view->geoCache->getCoordinates()->getAsText()?>

                <?php } else { // if-userModifiedCacheCoords ?>
                  <?=$view->userModifiedCacheCoords->getAsText()?>

                <?php } // if-userModifiedCacheCoords ?>

              <?php } else { //user-not-authorized ?>
                <?=tr('hidden_coords')?>
              <?php } //else-user-not-authorized ?>
            </b>
            <span class="content-title-noshade-size0">
                (WGS84)
                <?php if($view->userModifiedCacheCoords) { ?>
                  <a href="#coords_mod_section">
                    <img src="tpl/stdstyle/images/blue/signature1-orange.png" class="icon32"
                      alt="<?=tr('orig_coord_modified_info')?><?=$view->geoCache->getCoordinates()->getAsText()?>"
                      title="<?=tr('orig_coord_modified_info')?><?=$view->geoCache->getCoordinates()->getAsText()?>" />
                  </a>
                <?php } //coords modified ?>
            </span><br>

        </p>
        <p style="line-height: 1.6em;">

            <?php if($view->isUserAuthorized || $view->alwaysShowCoords ) { ?>
                <img src="tpl/stdstyle/images/free_icons/map.png" class="icon16" alt="" title="">
                  &nbsp;
                  <a href="#"
                     onclick="javascript:window.open('coordinates.php?lat=<?=$view->geoCache->getCoordinates()->getLatitude()?>&amp;lon=<?=$view->geoCache->getCoordinates()->getLongitude()?>&amp;popup=y&amp;wp=<?=htmlspecialchars($view->geoCache->getWaypointId(), ENT_COMPAT, 'UTF-8')?>','','width=240,height=334,resizable=yes,scrollbars=1')">
                     <?=tr('coords_other')?>
                  </a>
                <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link">

                <br/>
            <?php } //show-other-coords ?>

            <img src="tpl/stdstyle/images/free_icons/mountain.png" class="icon16" width=16 height=16 alt="" title="">
            &nbsp;{{cache_alt}}: {altitude} {{abovesealevel}}

            <br />

            <img src="tpl/stdstyle/images/free_icons/world.png" class="icon16" alt="" title="">&nbsp;{{region}}:
              <b>
                <span style="color: rgb(88,144,168)"><?=$view->geoCache->getCacheLocationObj()->getLocationDesc(' &gt; ')?></span>
              </b>

            <br />

            <?php if($view->displayDistanceToCache) { ?>

              <img src="tpl/stdstyle/images/free_icons/car.png" class="icon16" alt="distance" title="">
              &nbsp;<?=tr('distance_to_cache')?>: <b><?=$view->distanceToCache?> km</b>

              <br />
            <?php } // if-display-distance-to-cache ?>


            <img src="tpl/stdstyle/images/free_icons/box.png" class="icon16" alt="" title="" />
            &nbsp;<?=tr('cache_type')?>: <b><?=tr($view->geoCache->getCacheTypeTranslationKey())?></b>

            <br />

            <img src="tpl/stdstyle/images/free_icons/package_green.png" class="icon16" alt="" title="">
            &nbsp;<?=tr('size')?>: <b><?=tr($view->geoCache->getSizeTranslationKey())?></b>

            <br />

            <img src="tpl/stdstyle/images/free_icons/page.png" class="icon16" alt="" title="">
            &nbsp;{{status_label}}:
            <?php if($view->geoCache->isStatusReady()) { ?>
              <span style="color:green;font-weight:bold;">
            <?php } else { //if-cache-status-not-ready ?>
              <span class="errormsg">
            <?php } // if-cache-status-ready ?>
              <?=tr($view->geoCache->getStatusTranslationKey())?>
              </span>
            <br />


            <?php if($view->geoCache->getWayLenght() || $view->geoCache->getSearchTime()) { ?>

                <img src="tpl/stdstyle/images/free_icons/time.png" class="icon16" alt="" title="">
                &nbsp;{{time}}:
                <?php if($view->geoCache->getSearchTime()) { ?>
                  <?=$view->geoCache->getSearchTimeFormattedString() ?>
                <?php } else { // no-search-time ?>
                  <?=tr('not_available')?>
                <?php } //no-search-time ?>
                &nbsp;&nbsp;

                <img src="tpl/stdstyle/images/free_icons/arrow_switch.png" class="icon16" alt="" title="">
                &nbsp;{{length}}:
                <?php if($view->geoCache->getWayLenght()) { ?>
                  <?=$view->geoCache->getWayLenghtFormattedString() ?>
                <?php } else { // no-way-len ?>
                  <?=tr('not_available')?>
                <?php } //no-way-len ?>

                <br />
            <?php } //if-way-length-and-search-time-present ?>



            <?php if($view->geoCache->isEvent()) { ?>
                <img src="tpl/stdstyle/images/cache/16x16-event.png" class="icon16" alt="" title="">&nbsp;
                <?=tr('date_event_label')?>: <strong> <?=$view->cacheHiddenDate?> </strong>
            <?php } else { // cache-is-not-event ?>
                <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="">&nbsp;
                <?=tr('date_hidden_label')?>: <?=$view->cacheHiddenDate?>
            <?php } // cache-is-not-event ?>

            <br />

            <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="">
            &nbsp;{{date_created_label}}: <?=$view->cacheCreationDate?>

            <br />


            <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="">
            &nbsp;{{last_modified_label}}: <?=$view->cacheLastModifiedDate?>

            <br />


            <?php if(!empty($view->otherSitesListing)){ ?>
              <img src="tpl/stdstyle/images/free_icons/link.png" class="icon16" alt="" title="">
              &nbsp;{{listed_also_on}}:
              <?php foreach ($view->otherSitesListing as $site){ ?>
                <a href=<?=$site->link?> target="_blank"><?=$site->sitename?>(<?=$site->wp?>)</a>
              <?php } //foreach ?>
              <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link">

              <br />

            <?php } //!empty($view->otherSitesListing ?>

        </p>
    </div>


    <div class="content2-container-2col-right" id="viewcache-maptypes">

            <div class="content2-container-2col-left" id="viewcache-numstats">
                <div style="line-height: 1.6em; font-family: arial, sans serif; font-size: 12px;">

                    <br/>

                    <?php if($view->geoCache->isEvent()) { ?>

                      <img src="tpl/stdstyle/images/log/16x16-attend.png" class="icon16" alt="" title=""/>
                      <?=$view->geoCache->getFounds()?> <?=tr('attendends')?>

                      <br />

                      <img src="tpl/stdstyle/images/log/16x16-will_attend.png" class="icon16" alt="" title=""/>
                      <?=$view->geoCache->getNotFounds()?> <?=tr('will_attend')?>

                    <?php } else { //if-not-event ?>
                      <img src="tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="<?=tr('found')?>"/>
                      <?=$view->geoCache->getFounds()?>x <?=tr('found')?>

                      <br />

                      <?php if($view->geoCache->isMovable()) { ?>
                        <img src="tpl/stdstyle/images/log/16x16-moved.png" class="icon16" alt="moved" />
                        <?=$view->geoCache->getMoveCount()?>x <?=tr('moved_text')?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$view->geoCache->getDistance()?>&nbsp;km
                        <br />
                      <?php } //if-mobile-cache ?>

                      <img src="tpl/stdstyle/images/log/16x16-dnf.png" class="icon16" alt="{{not_found}}" />
                      <?=$view->geoCache->getNotFounds()?>x <?=tr('not_found')?>

                    <?php } //if-not-event ?>

                    <br />

                    <img src="tpl/stdstyle/images/log/16x16-note.png" class="icon16" alt="{{log_note}}" />
                    <?=$view->geoCache->getNotesCount()?> <?=tr('log_notes')?>

                    <br />

                    <img src="tpl/stdstyle/images/action/16x16-watch.png" class="icon16" alt="" />
                    <?=$view->geoCache->getWatchingUsersCount()?> <?=tr('watchers')?>

                    <br />

                    <div class="lightTipped" style="display:inline;">
                      <img src="tpl/stdstyle/images/free_icons/vcard.png" class="icon16" alt="" />
                      <?=$view->geoCache->getCacheVisits()?> <?=tr('visitors')?>
                    </div>
                    <?php if($view->displayPrePublicationAccessInfo) {?>
                        <div class="lightTip" >
                          <b><?=tr('prepublication_visits')?>:</b>
                          <?=implode($view->geoCache->getPrePublicationVisits(), '|')?>
                        </div>
                    <?php } //if-displayPrePublicationAccessInfo ?>

                    <br />

                    <img src="tpl/stdstyle/images/free_icons/thumb_up.png" class="icon16" alt="" />
                    <?=$view->geoCache->getRatingVotes()?> x <?=tr('scored')?>

                    <br />

                    <img src="images/cache-rate.png" class="icon16" alt="" />
                    <?=tr('score_label')?>: <b><font color="<?=$view->scoreColor?>"><?=$view->score?></font></b>

                    <br />

                    <?php if($view->geoCache->getRecommendations() > 0) { ?>

                      <a class="links2 lightTipped" href="#">
                          <img src="images/rating-star.png" alt="{{recommended}}" />
                          <?=$view->geoCache->getRecommendations()?> x <?=tr('recommended')?>
                      </a>
                      <div class="lightTip">
                           <b><?=tr('recommended_by')?>:</b>
                           <?=$view->geoCache->getUsersRecomeded()?>
                      </div>

                      <br />
                    <?php } // if-there-are-recommendations ?>

                    <img src="images/gk.png" class="icon16" alt="" title="GeoKrety visited" />
                    <a class="links" href="http://geokrety.org/szukaj.php?wpt=<?=$view->geoCache->getWaypointId()?>" target="_blank">
                      {{history_gk}}
                      <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link">
                    </a>

                    <br />
                </div>
            </div>

            <div id="viewcache-map" class="content2-container-2col-right">
              <div class="img-shadow">
                    <?php if ($view->isUserAuthorized || $view->alwaysShowCoords) { ?>

                        <img src="<?=$view->mapImgLink?>"
                             longdesc="ifr::cachemap-mini.php?cacheId=<?=$view->geoCache->getCacheId()?>::480::345"
                             onclick="enlarge(this);" alt="<?=tr('map')?>" title="<?=tr('map')?>">

                    <?php } else { ?>
                      <?=$view->loginToSeeMapMsg?>

                    <?php } //else $view->isUserAuthorized || $view->alwaysShowCoords ?>
              </div>
            </div>

            <?php if ($view->isUserAuthorized || $view->alwaysShowCoords) { ?>
              <b>{{available_maps}}:</b>
              <?=implode($view->externalMaps, "&nbsp;")?>

            <?php } //else $view->isUserAuthorized || $view->alwaysShowCoords ?>

    </div>
</div>
<!-- End Text Container -->

<!-- Text container -->


<?php if(!empty($view->geoCache->getCacheAttributesList())) { ?>

    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/attributes.png" class="icon32" alt="">
            {{cache_attributes_label}}
        </p>
    </div>

    <div class="content2-container">
        <p>
            <?php foreach($view->geoCache->getCacheAttributesList() as $attr){ ?>
              <img src="<?=$attr->iconLarge?>" title="<?=$attr->text?>" alt="<?=$attr->text?>">&nbsp;

            <?php } // foreach-attrib. ?>
        </p>
    </div>
    <div class="notice" id="viewcache-attributesend">
      {{attributes_desc_hint}}
      <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link">
    </div>

<?php } //cache has attributes ?>

<!-- End Text Container -->


<!-- Text container -->
<?php if(!empty($view->geoCacheDesc->getAdminComment() )) { ?>

    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="">{{rr_comment_label}}
        </p>
    </div>
    <div class="content2-container">
        <p><br><?=$view->geoCacheDesc->getAdminComment()?></p>
    </div>


<?php } // if-admin-comment ?>





<!-- Text container -->
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">

        <img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="">
        {{descriptions}}&nbsp;&nbsp;

        <?php foreach( $view->availableDescLangs as $descLang ){ ?>
          <a class="btn btn-sm btn-default" href="<?=$view->availableDescLangsLinks[$descLang]?>">
              <?php if($view->usedDescLang == $descLang) { ?>
                <i><?=$descLang?></i>

              <?php } else { // available-desc-langs ?>
                <?=$descLang?>

              <?php } // if-current-lang ?>
          </a>
        <?php } //foreach-available-desc-langs ?>

        <?php if($view->isAdminAuthorized) { ?>
        <a class="btn btn-sm btn-default" href="add_octeam_comment.php?cacheid=<?=$view->geoCache->getCacheId()?>">
          <?=tr('add_rr_comment')?>
        </a>
        <a class="btn btn-sm btn-default" href="viewcache.php?cacheid=<?=$view->geoCache->getCacheId()?>&amp;rmAdminComment=1"
            onclick="return confirm('<?=tr("confirm_remove_rr_comment")?>');"><?=tr('remove_rr_comment')?></a>
        <?php } //if-admin-authorized ?>
    </p>
</div>
<div class="content2-container">
    <div id="description">
        <div id="viewcache-description">
            <?=$view->geoCacheDesc->getDescToDisplay()?>
        </div>
    </div>
</div>
<!-- End Text Container -->
<!-- Text container -->





<!-- OpenChecker container -->
<?php if( !is_null($view->openChecker) ) { ?>

    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/openchecker_32x32.png" class="icon32" alt="">
            {{openchecker_name}}
        </p>
    </div>

    <div class="content2-container">
        <p>
            {{openchecker_enabled}}<br \><br \>
            <form method="get" action="openchecker.php">
                <button name="wp" value="<?=$view->geoCache->getWaypointId()?>" class="btn btn-default">{{openchecker_check}}</button>
            </form>
            <br><br>
        </p>
        <p>{{statistics}}:
            {{openchecker_tries}}: <?=$view->openChecker->getTries()?> {{openchecker_times}},
            {{openchecker_hits}}: <?=$view->openChecker->getHits()?> {{openchecker_times}}.
        </p>
    </div>

<?php } // if-openchacker-present ?>

<!-- END OpenChecker container -->




<?php if( !empty($view->waypointsList) ) { ?>

    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="">
            {{additional_waypoints}}
        </p>
    </div>
    <p>
        <table id="gradient" cellpadding="5" width="97%" border="1" style="border-collapse: collapse; font-size: 12px; line-height: 1.6em">
          <tr>
            <?php if($view->cacheWithStages) { ?>
              <th align="center" valign="middle" width="30"><b><?=tr('stage_wp')?></b></th>
            <?php } //if-cache-with-stages ?>

            <th align="center" valign="middle" width="40">&nbsp;<b><?=tr('symbol_wp')?></b>&nbsp;</th>
            <th align="center" valign="middle" width="40">&nbsp;<b><?=tr('type_wp')?></b>&nbsp;</th>
            <th width="90" align="center" valign="middle">&nbsp;<b><?=tr('coordinates_wp')?></b>&nbsp;</th>
            <th align="center" valign="middle"><b><?=tr('describe_wp')?></b></th>
          </tr>

        <?php foreach( $view->waypointsList as $wp ) { ?>
          <tr>

              <?php if($view->cacheWithStages) { ?>
                <td align="center" valign="middle"><center><strong>
                    <?php if($wp->getStage() != 0) { ?>
                      <?=$wp->getStage() ?>
                    <?php } ?>
                </strong></center></td>
              <?php } // if-cacheWithStages ?>

              <td align="center" valign="middle"><center><img src="<?=$wp->getIconName()?>" alt="" title="<?=tr($wp->getTypeTranslationKey())?>" /></center></td>
              <td style="text-align: left; vertical-align: middle;"><?=tr($wp->getTypeTranslationKey())?></td>

              <td align="left" valign="middle">
                <b><span style="color: rgb(88,144,168)">
                  <?php if($wp->areCoordsHidden()) { ?>
                    N ?? ?????? <br/> E ?? ??????

                  <?php } else { // if-coords-visible?>
                    <a class="links4" href="#"
                       onclick="javascript:window.open('coordinates.php?lat=<?=$wp->getCoordinates()->getLatitude()?>&amp;lon=<?=$wp->getCoordinates()->getLongitude()?>&amp;popup=y&amp;wp=<?=$view->geoCache->getWaypointId()?>, ENT_COMPAT, 'UTF-8')','','width=240,height=334,resizable=yes,scrollbars=1'); return event.returnValue=false">

                      <?=$wp->getCoordinates()->getLatitudeString() ?> <br/> <?=$wp->getCoordinates()->getLongitudeString() ?>

                    </a>
                  <?php } // if-coords-visible ?>
                </span></b>
              </td>

              <td align="left" valign="middle"><?=$wp->getDesc4Html()?></td>
          </tr>
        <?php } // foreach-waypoints ?>
        </table>
    </p>
    <br>

  <div class="notice" id="viewcache-attributesend">
    <a class="links" href="{wiki_link_additionalWaypoints}" target="_blank">
      {{show_info_about_wp}}
      <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link">
    </a>
  </div>

<?php } //if-waypoints-present ?>

<!-- End Text Container -->






<!-- Text container -->

<?php if( !empty($view->geoCacheDesc->getHint()) ) { ?>

    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="">
            <b>{{additional_hints}}</b>&nbsp;&nbsp;
            <span id="decrypt-info">

            <?php if($view->isUserAuthorized || $view->alwaysShowCoords) { ?>

                <img src="tpl/stdstyle/images/blue/decrypt.png" class="icon32" alt="" />

                <?php if(!$view->showUnencryptedHint) { ?>

                <span style="font-weight:400">
                  <a class="btn btn-default btn-sm" href="#" onclick="return showHint(event);">
                      <span id="decryptLinkStr"><?=tr('decrypt')?></span>
                      <span id="encryptLinkStr" style="display:none"><?=tr('encrypt')?></span>
                  </a>
                </span>

                <?php } //if-show-unencrypted-hint ?>

            <?php } // if-user-authorized or showAll set in config ?>
            </span>
            <br />
        </p>
    </div>

    <div class="content2-container">
        <?php if($view->isUserAuthorized || $view->alwaysShowCoords) { ?>

            <p id="decrypt-hints"><?=$view->hintDecrypted?></p>
            <p id="hintEncrypted" style="display: none"><?=$view->hintEncrypted?></p>

            <div style="width:200px;text-align:right;float:right">
                <?php if(!$view->showUnencryptedHint) { ?>

                    <font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">A|B|C|D|E|F|G|H|I|J|K|L|M</font>
                    <font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>

                <?php } //if-show-unencrypted-hint ?>
            </div>

        <?php } else { // if-user-not-authorized or showAll-not-set in config ?>

          <span class="notice" style="width:500px;height:44px"  ><?=tr('vc_hint_for_logged_only')?></span>

        <?php } // if-user-authorized or showAll set in config ?>

    </div>

<?php } // if-hint-present ?>








<!-- sekcja modyfikatora współrzędnych -->

<?php if($view->cacheCoordsModificationAllowed) { ?>

<div id="coords_mod_section" class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
          <img src="tpl/stdstyle/images/blue/signature1.png" class="icon32" alt="" />
          {{coords_modifier}}
        </p>
</div>


<div class="content2-container">
    {{coordsmod_main}} <br />

    <form action="viewcache.php?cacheid=<?=$view->geoCache->getCacheId()?>" method="post" name="form_coords_mod">
        <?php $view->callChunk('coordsForm', $view->userModifiedCacheCoords, 'userCoords'); ?>

        <script type="text/javascript">
          // disable subit button if coords are not set

          $('#userCoordsFinalCoordsReady').change(function(){
              if( $('#userCoordsFinalCoordsReady').val() ){
                $("#submitBtns > input[type=submit]").attr('disabled', false);
              }else{
                $("#submitBtns > input[type=submit]").attr('disabled', true);
              }
          });
        </script>

        <p id="submitBtns" >
            <input id="userModifiedCoordsSubmited" type="submit" name="userModifiedCoordsSubmited" value="{{modify_coords}}" disabled="disabled" class="btn btn-default btn-sm">
            <input id="deleteUserModifiedCoords" type="submit" name="deleteUserModifiedCoords" value="{{reset_coords}}" disabled="disabled" class="btn btn-default btn-sm">
        </p>
    </form>
    <div class="notice buffer" id="viewcache-mod_coord">{{modified_coord_notice}}</div>

</div>
<?php } //if-cacheCoordsModificationAllowed ?>

<!-- koniec sekcji modyfikatora współrzędnych -->








<?php if($view->isUserAuthorized) { ?>

    <div class="content2-container bg-blue02" id="userNotes">
        <p class="content-title-noshade-size2">
            <img src="tpl/stdstyle/images/blue/logs.png" style="align: left; margin-right: 10px;" alt="{{personal_cache_note}}"> {{personal_cache_note}}
        </p>
    </div>

    <div class="content2-container">
      <form action="viewcache.php?cacheid=<?=$view->geoCache->getCacheId()?>#userNotes" method="post" name="cache_note" id="cacheNoteForm">

            <textarea class="userNoteEdit" name="userNoteText" rows="4" cols="85" style="font-size:13px; display:none"><?=$view->userNoteText?></textarea>
            <div class="searchdiv userNoteDisplay" style="width: 710px;"><span style="font-size:13px;"><?=$view->userNoteText?></span></div>


          <div>
            <input type="submit" name="saveUserNote" value="{{save}}" class="btn btn-default btn-sm userNoteEdit" style="display:none" />
            <button id="editNoteBtn" type="button" class="btn btn-default btn-sm userNoteDisplay" style="display:none">{{Edit}}</button>
            <input type="submit" name="removeUserNote" value="{{delete}}" class="btn btn-default btn-sm userNoteDisplay" style="display:none">

            <img src="tpl/stdstyle/images/misc/16x16-info.png" class="icon16" alt="Info">
            <small>{{cache_note_visible}}</small>
          </div>
        </form>
    </div>

    <script type="text/javascript">
      <?php if(empty($view->userNoteText)) { ?>
        //empty note - enable userNoteEdit
        $('#cacheNoteForm .userNoteDisplay').hide();
        $('#cacheNoteForm .userNoteEdit').show();
      <?php } else { // if-empty-userNoteText ?>
        //there is something to display
        $('#cacheNoteForm .userNoteDisplay').show();
        $('#cacheNoteForm .userNoteEdit').hide();
      <?php } // if-empty-userNoteText ?>

      $('#editNoteBtn').click( function(){
        $('#cacheNoteForm .userNoteDisplay').toggle();
        $('#cacheNoteForm .userNoteEdit').toggle();
      });

    </script>

<?php } //if-isUserAuthorized ?>




<!-- Text container -->
<?php if( !empty($view->geoCache->getNatureRegions() ) || !empty($view->geoCache->getNatura2000Sites())) { ?>

    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">

            <img src="tpl/stdstyle/images/blue/npav1.png" class="icon32" alt="">
            {{obszary_ochrony_przyrody}}
        </p>
    </div>

    <div class="content2-container">
        <center>
        <?php if( !empty($view->geoCache->getNatureRegions() ) ) { ?>

            <table width="90%" border="0" style="border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em">
              <tr>
                <td align="center" valign="middle">
                  <b><?=tr('npa_info')?></b>:
                </td>
                <td align="center" valign="middle">&nbsp;</td>
              </tr>

            <?php foreach ($view->geoCache->getNatureRegions() as $npa) { ?>

                <tr>
                  <td align="center" valign="middle">
                    <font color="blue">
                      <a target="_blank" href="http://<?=$npa['npalink']?>"><?=$npa['npaname']?></a>
                    </font>
                    <br />
                  </td>
                  <td align="center" valign="middle">
                    <img src="tpl/stdstyle/images/pnk/"<?=$npa['npalogo']?>">
                  </td>
                </tr>
            <?php } //foreach ?>
            </table>

         <?php } //if-NatureRegions-presents ?>

         <?php if( !empty($view->geoCache->getNatura2000Sites())) { ?>

            <table width="90%" border="0" style="border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em\">
              <tr>
              <td width=90% align="center" valign="middle"><b><?=tr('npa_info')?><font color="green">NATURA 2000</font></b>:<br>
                <?php foreach ($view->geoCache->getNatura2000Sites() as $npa) {
                            $npa_item = $config['nature2000link'];
                            $npa_item = mb_ereg_replace('{linkid}', $npa['linkid'], $npa_item);
                            $npa_item = mb_ereg_replace('{sitename}', $npa['npaSitename'], $npa_item);
                            $npa_item = mb_ereg_replace('{sitecode}', $npa['npaSitecode'], $npa_item);
                            echo $npa_item; ?>
                            <br />

                <?php } //foreach ?>

              </td>
              <td align="center" valign="middle"><img src="tpl/stdstyle/images/misc/natura2000.png\"></td>
            </tr>
            </table>


         <?php } //if-Natura2000-presents ?>

        </center>
    </div>

<?php } //if-natureRegions-present ?>
<!-- End Text Container -->




<!-- Text container -->

<?php if( !empty($view->geoCache->getGeokretsHosted())) { ?>
    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/travelbug.png" class="icon32" alt="">
            Geokrety
        </p>
    </div>
    <div class="content2-container">
        <div id="geoKretySection">
            <p>
              <?php foreach ($view->geoCache->getGeokretsHosted() as $gk) { ?>

                <img src="/images/geokret.gif" alt="">&nbsp;
                <a href='https://geokrety.org/konkret.php?id=<?=$gk['id']?>'><?=$gk['name']?></a>
                - <?=tr('total_distance')?>: <?=$gk['distance']?> km <br/>

              <?php } ?>
            </p>
        </div>
    </div>
<?php } //if-geokrety-inside ?>




<!-- End Text Container -->
<!-- Text container -->



<?php if( !empty($view->geoCache->getMp3List() )) { ?>

    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/podcache-mp3.png" class="icon32" alt="">
            {{mp3_files_info}}
        </p>
    </div>
    <div class="content2-container">
        <div id="viewcache-mp3s">
          <?php foreach ($view->geoCache->getMp3List() as $mp3 ) { ?>
              <div class="viewcache-pictureblock">
              <div class="img-shadow">
                <a href="<?=$mp3['url']?>" target="_blank">
                  <img src="tpl/stdstyle/images/blue/32x32-get-mp3.png" alt="" title="" />
                </a>
              </div>
              <span class="title"><?=$mp3['title']?></span>
              </div>
          <?php } //foreach ?>
        </div>
    </div>

<?php } // if-mp3-presents ?>


<!-- End Text Container -->





<!-- Text container -->
<?php if( !empty($view->picturesToDisplay) ) { ?>


    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="">
            {{images}}
        </p>
    </div>
    <div class="content2-container">
        <div id="viewcache-pictures">

            <?php foreach ($view->picturesToDisplay as $pic) { ?>

                <!-- <br style="clear: left;" /> at every 4 pic... TODO -->
                <div class="viewcache-pictureblock">
                    <div class="img-shadow">
                        <a class="example-image-link" href="<?=$pic->url?>" data-lightbox="example-1" data-title="<?=$pic->title?>">

                          <img class="example-image" src="<?=$pic->thumbUrl?>" alt="<?=$pic->title?>" />

                        </a>

                    </div>
                <span class="title"><?=$pic->title?></span>
              </div>
            <?php } //foreach ?>

        </div>
    </div>

<?php } //if-pictures-to-display-present ?>

<!-- End Text Container -->







<!-- Text container -->
<?php if($view->isUserAuthorized) { ?>


<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/tools.png" class="icon32" alt="">&nbsp;{{utilities}}
    </p>
</div>
<div class="content2-container">
    <div id="viewcache-utility">
        <div>
            <img src="tpl/stdstyle/images/action/16x16-search.png" class="icon16" alt="" />
            {{search_geocaches_nearby}}:




            <a href="search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;latNS={latNS}&amp;lat_h={lat_h}&amp;lat_min={lat_min}&amp;lonEW={lonEW}&amp;lon_h={lon_h}&amp;lon_min={lon_min}&amp;distance=100&amp;unit=km">
            {{all_geocaches}}
            </a>

            &nbsp;

            <a href="search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=1&amp;f_userfound=1&amp;f_inactive=1&amp;latNS={latNS}&amp;lat_h={lat_h}&amp;lat_min={lat_min}&amp;lonEW={lonEW}&amp;lon_h={lon_h}&amp;lon_min={lon_min}&amp;distance=100&amp;unit=km">
            {{searchable}}
            </a>

            <br />

            <span>
              <img src="tpl/stdstyle/images/action/16x16-search.png" class="icon16" alt="" />

            {{find_geocaches_on}}:&nbsp;

                <?php if($view->searchAtOtherSites) { ?>
                <b>

                <a target="_blank" href="//www.geocaching.com/seek/nearest.aspx?origin_lat={latitude}&amp;origin_long={longitude}&amp;dist=100&amp;submit8=Submit\">Geocaching.com</a>
                &nbsp;&nbsp;&nbsp;
                <a target="_blank" href="http://www.terracaching.com/gmap.cgi#center_lat={latitude}&amp;center_lon={longitude}&amp;center_zoom=7&cselect=all&ctselect=all">TerraCaching.com</a>
                &nbsp;&nbsp;
                <a target="_blank" href="http://www.navicache.com/cgi-bin/db/distancedp.pl?latNS={latNS}&amp;latHours={latitude}&amp;longWE={lonEW}&amp;longHours={longitudeNC}&amp;Distance=100&amp;Units=M">Navicache.com</a>
                &nbsp;&nbsp;&nbsp;

                <a target="_blank" href="http://geocaching.gpsgames.org/cgi-bin/ge.pl?basic=yes&amp;download=Google+Maps&amp;zoom=8&amp;lat_1={latitude}&amp;lon_1={longitude}">GPSgames.org</a>
                &nbsp;

                <a href="http://www.opencaching.cz/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS={latNS}&amp;lat_h={lat_h}&amp;lat_min={lat_min}&amp;lonEW={lonEW}&amp;lon_h={lon_h}&amp;lon_min={lon_min}&amp;distance=100&amp;unit=km">OC CZ</a>
                &nbsp;&nbsp;&nbsp;

                <a href="http://www.opencaching.de/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS={latNS}&amp;lat_h={lat_h}&amp;lat_min={lat_min}&amp;lonEW={lonEW}&amp;lon_h={lon_h}&amp;lon_min={lon_min}&amp;distance=100&amp;unit=km">OC DE</a>

                </b>&nbsp;&nbsp;

                <?php } //if-searchAtOtherSites ?>


            </span>
        </div>

        <hr style="color: blue;">

        <div>
            <img src="tpl/stdstyle/images/action/16x16-save.png" class="icon16" alt="" /><b> {{download_as_file}}</b>
            <br>
            <table class="content" style="font-size: 12px; line-height: 1.6em;">
                <tr>
                    <td  width="350" align="left" style="padding-left:5px;">
                        <div class="searchdiv">
                            <span class="content-title-noshade txt-blue08">{{format_GPX}}</span>:<br>
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=gpxgc" title="GPS Exchange Format .gpx">GPX</a>&nbsp|&nbsp
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=zip" title="Garmin ZIP file ({{format_pict}})  .zip">GARMIN ({{format_pict}})</a>
                        </div>
                    </td>
                    <td width="350" align="left" style="padding-left:5px;">
                        <div class="searchdiv">
                            <span class="content-title-noshade txt-blue08">{{format_other}}</span>:<br>
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=loc" title="Waypoint .loc">LOC</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=kml" title="Google Earth .kml">KML</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=ov2" title="TomTom POI .ov2">OV2</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=ovl" title="TOP50-Overlay .ovl">OVL</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=txt" title="Tekst .txt">TXT</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=wpt" title="Oziexplorer .wpt">WPT</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=uam" title="AutoMapa .uam">UAM</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=xml" title="XML">XML</a>
                        </div>
                    </td>
                </tr>
                <tr>
                     <td  width="350" align="left" style="padding-left:5px;">
                        <div class="searchdiv">
                            <span class="content-title-noshade txt-blue08">{{send_to}}:</span><br>
                            <a class="links" href="#" onclick="openCgeoWindow(event, '<?=$view->geoCache->getWaypointId()?>')" title="c:geo">{{send_to_cgeo}}</a> |
                            <a class="links" href="#" onclick="openGarminWindow(event, '{latitude}','{longitude}',
                            '<?=$view->geoCache->getWaypointId()?>','<?=$view->cachename?>')" title="{{send_to_gps}}">{{send_to_gps}}</a>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="notice buffer" id="viewcache-termsofuse"> {{accept_terms_of_use}} </div>
        </div>

        </div>
    </div>

<?php } // if-isUserAuthorized ?>




    <!-- Text container -->
    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1" id="log_start">
            <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="">
            {{log_entries}}
            &nbsp;&nbsp;

            <?php if($view->geoCache->isEvent()) { ?>
              <img src="tpl/stdstyle/images/log/16x16-attend.png" class="icon16" alt="" title=""/>
              <?=$view->geoCache->getFounds()?>

              <img src="tpl/stdstyle/images/log/16x16-will_attend.png" class="icon16" alt="" title=""/>
              <?=$view->geoCache->getNotFounds()?>

            <?php } else { //if-not-event ?>

              <img src="tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="<?=tr('found')?>"/>
              <?=$view->geoCache->getFounds()?>x

              <img src="tpl/stdstyle/images/log/16x16-dnf.png" class="icon16" alt="{{not_found}}" />
              <?=$view->geoCache->getNotFounds()?>x

            <?php } //if-not-event ?>

            <img src="tpl/stdstyle/images/log/16x16-note.png" class="icon16" alt="{{log_note}}" />
            <?=$view->geoCache->getNotesCount()?>x



            <?php if( $view->geoCache->getPicsInLogsCount() > 0 ) { ?>

              <img src="tpl/stdstyle/images/free_icons/photo.png" alt="Photo" class="icon16"/>
              &nbsp;
              <?=$view->geoCache->getPicsInLogsCount()?>x
              &nbsp;
              <a href="gallery_cache.php?cacheid=<?=$view->geoCache->getCacheId()?>"><?=tr('gallery_short')?></a>

            <?php } //if-getNumberOfPicsInLogs > 0 ?>

            &nbsp;

            <a class="btn btn-sm btn-primary" href="log.php?cacheid=<?=$view->geoCache->getCacheId()?>" title="<?=tr('new_log_entry')?>">
              <img src="images/actions/new-entry-18.png" title="<?=tr('new_log_entry')?>" alt="<?=tr('new_log_entry')?>">
              <?=tr('new_log_entry_short')?>
            </a>

            <?php if($view->displayAllLogsLink) { ?>

                <a class="btn btn-sm btn-default" href="viewlogs.php?cacheid=<?=$view->geoCache->getCacheId()?>" >
                  <img src="tpl/stdstyle/images/action/16x16-showall.png" class="icon16" alt="<?=tr('show_all_log_entries')?>"
                       title="<?=tr('show_all_log_entries')?>" />
                  &nbsp;
                  <?=tr("show_all_log_entries_short")?>
                </a>
            <?php } //if-logEnteriesCount ?>



            <?php if($view->showDeletedLogsDisplayLink) { ?>

                <span style="white-space: nowrap;">
                  <a class="btn btn-sm btn-default" href="<?=$view->deletedLogsDisplayLink?>" title="<?=$view->deletedLogsDisplayText?>">
                    <img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="<?=$view->deletedLogsDisplayText?>" title="<?=$view->deletedLogsDisplayText?>" />
                    <?=$view->deletedLogsDisplayText?>
                  </a>
                </span>

            <?php } //if-showDeletedLogsDisplayLink ?>


        </p>
    </div>

    <div class="content2-container" id="viewcache-logs">
        <!-- log enteries - to be loaded dynamicly by ajax -->
    </div>
    <!-- End Text Container -->
