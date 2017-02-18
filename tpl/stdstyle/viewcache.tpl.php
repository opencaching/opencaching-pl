
<link rel="stylesheet" href="tpl/stdstyle/js/lightbox2/dist/css/lightbox.min.css">
<script src="tpl/stdstyle/js/lightbox2/dist/js/lightbox-plus-jquery.min.js"></script>

<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
<script type="text/javascript" src="lib/js/tip_balloon.js"></script>
<script type="text/javascript" src="lib/js/tip_centerwindow.js"></script>');

<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>


<script>
    var confirmRmLogTranslation = '{{confirm_remove_log}}';
</script>

<script src="{viewcache_js}"></script>


<input type="hidden" id="cacheid" value="{cacheid}">
<input type="hidden" id="logEnteriesCount" value="{logEnteriesCount}">
<input type="hidden" id="owner_id" value="{owner_id}">
<input type="hidden" id="includeDeletedLogs" value="{includeDeletedLogs}">
<input type="hidden" id="uType" value="{uType}">

<div class="content2-container line-box">
    <div class="">
        <div class="nav4">
            <?php if(!$view->isUserAuthorized){ ?>
            <span class="notlogged-cacheview"><?=tr('cache_logged_required')?></span>;
            <?php }else{ ?>

            <?php

            // menu kesza - przyciski - wpis do logu etc... PRZEROBIĆ!

            $clidx = mnu_MainMenuIndexFromPageId($menu, "cachelisting");
            if ($menu[$clidx]['title'] != '') {
                echo '<ul id="cachemenu">';
                $menu[$clidx]['visible'] = false;
                echo '<li class="title" ';
                echo '>' . $menu[$clidx]["title"] . '</li>';
                mnu_EchoSubMenu($menu[$clidx]['submenu'], $tplname, 1, false);
                echo '</ul>';
            }

            ?>
            <?php } //else ?>
        </div>

        <div class="content2-container-2col-left" style="width:60px; clear: left;">
            <div>
              <img src="{icon_cache}" class="icon32" id="viewcache-cacheicon" alt="{cachetype}" title="{cachetype}">
            </div>
            <div>{difficulty_icon_diff}</div>
            <div>{difficulty_icon_terr}</div>
            <div>

              <?php if( $view->geoCache->isEvent() ) {
                    if (($view->geoCache->getFounds() + $view->geoCache->getNotFounds() + $view->geoCache->getNotesCount()) != 0) { ?>

                      <a class="links2" href="javascript:void(0)"
                         onmouseover="Tip('<?=tr('show_statictics_cache')?>', BALLOON, true, ABOVE, false, OFFSETX, -17, PADDING, 8, WIDTH, -240)"
                         onmouseout="UnTip()"
                         onclick="javascript:window.open('cache_stats.php?cacheid=<?=$view->geoCache->getCacheId()?>&amp;popup=y','Cache_Statistics','width=500,height=750,resizable=yes,scrollbars=1')">
                         <img src="tpl/stdstyle/images/blue/stat1.png" alt="Statystyka skrzynki" title="Statystyka skrzynki">
                      </a>

                    <?php } else { ?>
                      <a class="links2" href="javascript:void(0)"
                         onmouseover="Tip('<?=tr('not_stat_cache')?>', BALLOON, true, ABOVE, false, OFFSETX, -17, PADDING, 8, WIDTH, -240)"
                         onmouseout="UnTip()">
                         <img src="tpl/stdstyle/images/blue/stat1.png" alt="" title="">
                      </a>
                    <?php }
              } //if-not-event ?>

            </div>
        </div>


        <div class="content2-container-2col-left" id="cache_name_block">
            <table style="width:100%;">
              <tr>
                <td style="width:70%; vertical-align:top;">

                  <span class="content-title-noshade-size5">{cachename} - <?=$view->geoCache->getWaypointId()?>{icon_titled}</span><br>

                  <p class="content-title-noshade-size1"><?=$view->geoCacheDesc->getShortDescToDisplay()?></p>

                  <p><?=tr('owner')?>&nbsp;
                      <a class="links" href="viewprofile.php?userid={userid_urlencode}">{owner_name}</a>

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
                <?=$view->geoCache->getCoordinates()->getAsText()?>
              <?php } else { //user-not-authorized ?>
                <?=tr('hidden_coords')?>
              <?php } //else-user-not-authorized ?>
            </b>
            <span class="content-title-noshade-size0">
                (WGS84)
                <?php if($view->userModifiedCoords) { ?>
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
                <a href=<?=$site->link?> target="_blank"><?=$site->name?>(<?=$site->wp?>)</a>
              <?php } //foreach ?>
              <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link">

              <br />

            <?php } //!empty($view->otherSitesListing ?>

        </p>
    </div>


    <div class="content2-container-2col-right" id="viewcache-maptypes">

            <div class="content2-container-2col-left" id="viewcache-numstats">
                <p style="line-height: 1.4em;"><br>
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

                      <?php if($view->geoCache->isMobile()) { ?>
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

                    <img src="tpl/stdstyle/images/free_icons/vcard.png" class="icon16" alt="" />
                    <?=$view->geoCache->getCacheVisits()?> <?=tr('visitors')?>

                    <br />

                    <img src="tpl/stdstyle/images/free_icons/thumb_up.png" class="icon16" alt="" />
                    <?=$view->geoCache->getRatingVotes()?> x <?=tr('scored')?>

                    <br />

                    <img src="images/cache-rate.png" class="icon16" alt="" />
                    <?=tr('score_label')?>: <b><font color="<?=$view->scoreColor?>"><?=$view->score?></font></b>

                    <br />

                    <?php if($view->geoCache->getRecommendations() > 0) { ?>

                      <a class ="links2" href="javascript:void(0)"
                         onmouseover="Tip('<b><?=tr('recommended_by')?></b><br><br><?=$view->geoCache->getUsersRecomeded()?><br><br>', BALLOON, true, ABOVE, false, OFFSETY, 20, OFFSETX, -17, PADDING, 8, WIDTH, -240)"
                         onmouseout="UnTip()" >

                          <img src="images/rating-star.png" alt="{{recomendation}}" />
                          <?=$view->geoCache->getRecommendations()?> x <?=tr('recommended')?>
                      </a>

                      <br />
                    <?php } // if-there-are-recommendations ?>

                    <img src="images/gk.png" class="icon16" alt="" title="GeoKrety visited" />
                    <a class="links" href="http://geokrety.org/szukaj.php?wpt=<?=$view->geoCache->getWaypointId()?>" target="_blank">
                      {{history_gk}}
                      <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link">
                    </a>

                    <br />
                </p>
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
    <div class="notice" id="viewcache-attributesend">{{attributes_desc_hint}}  <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></div>

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
          <a href="viewcache.php?cacheid=<?=$view->geoCache->getCacheId()?>&amp;desclang=<?=$descLang?><?=$view->linkargs?>">
          <?php if($view->usedDescLang == $descLang) { ?>
            <i><?=$descLang?></i>

          <?php } else { // available-desc-langs ?>
            <?=$descLang?>

          <?php } // if-current-lang ?>

        <?php } //foreach-available-desc-langs ?>

        <?php if($view->isAdminAuthorized) { ?>
        &nbsp;
        [<a href="add_octeam_comment.php?cacheid=<?=$view->geoCache->getCacheId()?>"><?=tr('add_rr_comment')?></a>]
        &nbsp;
        [<a href="viewcache.php?cacheid=<?=$view->geoCache->getCacheId()?>&amp;rmAdminComment=1"
            onclick="return confirm('<?=tr("confirm_remove_rr_comment")?>');"><?=tr('remove_rr_comment')?></a>]
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
                  <a href="#" onclick="return showHint(event);">
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

<div id="#coords_mod_section" class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">
          <img src="tpl/stdstyle/images/blue/signature1.png" class="icon32" alt="" />
          {{coords_modifier}}
        </p>
</div>


<div class="content2-container">
    {{coordsmod_main}} <br />

    <form action="viewcache.php?cacheid=<?=$view->geoCache->getCacheId()?>" method="post" name="form_coords_mod">
        <?php $view->callChunk('coordsForm', $view->userModifiedCoords, 'userCoords'); ?>

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
            <input id="modCoords" type="submit" name="modCoords" value="{{modify_coords}}" disabled="disabled" class="btn btn-default btn-sm">
            <input id="resetCoords" type="submit" name="resetCoords" value="{{reset_coords}}" disabled="disabled" class="btn btn-default btn-sm">
        </p>

    </form>

    <div class="notice buffer" id="viewcache-mod_coord">
        {{modified_coord_notice}}
    </div>

</div>
<?php } //if-cacheCoordsModificationAllowed ?>

<!-- koniec sekcji modyfikatora współrzędnych -->










{EditCacheNoteS}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size2">
        <img src="tpl/stdstyle/images/blue/logs.png" style="align: left; margin-right: 10px;" alt="{{personal_cache_note}}"> {{personal_cache_note}}
    </p>
</div>

<div class="content2-container">
    <form action="viewcache.php" method="post" name="cache_note">
        <input type="hidden" name="cacheid" value="{cacheid}">

        <table id="cache_note1" class="table">
            <tr valign="top">
                <td></td>
                <td>
                    <textarea name="note_content" rows="4" cols="85" style="font-size:13px;">{note_content}</textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">
                    <button type="submit" name="save" value="save" class="btn btn-default btn-sm">{{save}}</button>&nbsp;&nbsp;
                    <img src="tpl/stdstyle/images/misc/16x16-info.png" class="icon16" alt="Info">
                    <small>
                        {{cache_note_visible}}</td>
                </small>
                </td>
            </tr>
        </table>
    </form>
</div>
{EditCacheNoteE}


{CacheNoteS}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size2">
        <img src="tpl/stdstyle/images/blue/logs.png" style="align: left; margin-right: 10px;" alt="{{personal_cache_note}}">
        {{personal_cache_note}}
    </p>
</div>

<div class="content2-container">
    <form action="viewcache.php?cacheid={cacheid}#cache_note1" method="post" name="cache_note">
        <input type="hidden" name="cacheid" value="{cacheid}">

        <table id="cache_note2" class="table">
            <tr valign="top">
                <td></td>
                <td>
                    <div class="searchdiv" style="width: 710px;">
                        <span style="font-size:13px;">{notes_content}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">&nbsp;
                    <button type="submit" name="edit" value="edit" class="btn btn-default btn-sm">{{Edit}}</button>&nbsp;&nbsp;
                    <button type="submit" name="remove" value="remove" class="btn btn-default btn-sm">{{delete}}</button>&nbsp;&nbsp;
                    <img src="tpl/stdstyle/images/misc/16x16-info.png" class="icon16" alt="Info">
                    <small>
                        {{cache_note_visible}}</td>
                </small>
                </td>
            </tr>
        </table>
    </form>
</div>
{CacheNoteE}

<!-- Text container -->
{hidenpa_start}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">

        <img src="tpl/stdstyle/images/blue/npav1.png" class="icon32" alt="">
        {{obszary_ochrony_przyrody}}
    </p>
</div>
<div class="content2-container">
    <center>
        {npa_content}
    </center>
</div>
{hidenpa_end}
<!-- End Text Container -->
<!-- Text container -->
{geokrety_begin}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/travelbug.png" class="icon32" alt="">
        Geokrety
    </p>
</div>
<div class="content2-container">
    <div id="geoKretySection">
        <p>
            {geokrety_content}
        </p>
    </div>
</div>
{geokrety_end}
<!-- End Text Container -->
<!-- Text container -->
{hidemp3_start}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/podcache-mp3.png" class="icon32" alt="">
        {{mp3_files_info}}
    </p>
</div>
<div class="content2-container">
    <div id="viewcache-mp3s">
        {mp3_files}
    </div>
</div>
{hidemp3_end}
<!-- End Text Container -->

<!-- Text container -->
{hidepictures_start}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="">
        {{images}}
    </p>
</div>
<div class="content2-container">
    <div id="viewcache-pictures">
        {pictures}
    </div>
</div>
{hidepictures_end}
<!-- End Text Container -->
<!-- Text container -->
{hidesearchdownloadsection_start}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/tools.png" class="icon32" alt="">&nbsp;{{utilities}}
    </p>
</div>
<div class="content2-container">
    <div id="viewcache-utility">
        <div>
            {search_icon} {{search_geocaches_nearby}}
            <?php echo ":
            <a href=\"search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;latNS="; ?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h="; ?>{lon_h}<?php echo "&amp;lon_min="; ?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">"; ?>{{all_geocaches}}<?php echo "</a>&nbsp;
            <a href=\"search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=1&amp;f_userfound=1&amp;f_inactive=1&amp;latNS="; ?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h="; ?>{lon_h}<?php echo "&amp;lon_min="; ?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">"; ?>{{searchable}}<?php echo "</a><br>";
            ?>
            <span style="display: {userLogged}">{search_icon} {{find_geocaches_on}}:&nbsp;
                <?php
                if ($usr == !false && $usr['userFounds'] > 99) {
                    echo
                    "<b>
                        <a target=\"_blank\" href=\"//www.geocaching.com/seek/nearest.aspx?origin_lat=";
                    ?>{latitude}<?php echo "&amp;origin_long="; ?>{longitude}<?php echo "&amp;dist=100&amp;submit8=Submit\">Geocaching.com</a>&nbsp;&nbsp;&nbsp;
                        <a target=\"_blank\" href=\"http://www.terracaching.com/gmap.cgi#center_lat="; ?>{latitude}<?php echo "&amp;center_lon="; ?>{longitude}<?php echo "&amp;&center_zoom=7&cselect=all&ctselect=all\">TerraCaching.com</a>&nbsp;&nbsp;
                        <a target=\"_blank\" href=\"http://www.navicache.com/cgi-bin/db/distancedp.pl?latNS="; ?>{latNS}<?php echo "&amp;latHours="; ?>{latitude}<?php echo "&amp;longWE="; ?>{lonEW}<?php echo "&amp;longHours="; ?>{longitudeNC}<?php echo "&amp;Distance=100&amp;Units=M\">Navicache.com</a>&nbsp;&nbsp;&nbsp;
                        <a target=\"_blank\" href=\"http://geocaching.gpsgames.org/cgi-bin/ge.pl?basic=yes&amp;download=Google+Maps&amp;zoom=8&amp;lat_1="; ?>{latitude}<?php echo "&amp;lon_1="; ?>{longitude}<?php echo "\">GPSgames.org</a>&nbsp;
                        <a href=\"http://www.opencaching.cz/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS="; ?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h="; ?>{lon_h}<?php echo "&amp;lon_min="; ?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">OC CZ</a>&nbsp;&nbsp;&nbsp;
                        <a href=\"http://www.opencaching.de/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS="; ?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h="; ?>{lon_h}<?php echo "&amp;lon_min="; ?>{lon_min}<?php
                    echo "&amp;distance=100&amp;unit=km\">OC DE</a></b>&nbsp;&nbsp;
                    ";
                }
                ?>
            </span>
        </div><hr style="color: blue;">
        <?php
        global $hide_coords;
        if ($usr == false && $hide_coords) { // hide downloading gpx etc if user is not logged
            echo "";
        } else {
            ?>
        <div>{save_icon}<b> {{download_as_file}}</b><br>
            <table class="content" style="font-size: 12px; line-height: 1.6em;">
                <tr>
                    <td  width="350" align="left" style="padding-left:5px;">
                        <div class="searchdiv">
                            <span class="content-title-noshade txt-blue08">{{format_GPX}}</span>:<br>
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=gpxgc" title="GPS Exchange Format .gpx">GPX</a>&nbsp|&nbsp
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=zip" title="Garmin ZIP file ({{format_pict}})  .zip">GARMIN ({{format_pict}})</a>
                        </div>
                    </td>
                    <td width="350" align="left" style="padding-left:5px;">
                        <div class="searchdiv">
                            <span class="content-title-noshade txt-blue08">{{format_other}}</span>:<br>
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=loc" title="Waypoint .loc">LOC</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=kml" title="Google Earth .kml">KML</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=ov2" title="TomTom POI .ov2">OV2</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=ovl" title="TOP50-Overlay .ovl">OVL</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=txt" title="Tekst .txt">TXT</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=wpt" title="Oziexplorer .wpt">WPT</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=uam" title="AutoMapa .uam">UAM</a> |
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=xml" title="XML">XML</a>
                        </div>
                    </td>
                </tr>
                <tr>
                     <td  width="350" align="left" style="padding-left:5px;">
                        <div class="searchdiv">
                            <span class="content-title-noshade txt-blue08">{{send_to}}:</span><br>
                            <a class="links" href="#" onclick="openCgeoWindow(event, '<?=$view->geoCache->getWaypointId()?>')" title="c:geo">{{send_to_cgeo}}</a> |
                            <a class="links" href="#" onclick="openGarminWindow(event, '{latitude}','{longitude}',
                            '<?=$view->geoCache->getWaypointId()?>','{cachename}')" title="{{send_to_gps}}">{{send_to_gps}}</a>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="notice buffer" id="viewcache-termsofuse"> {{accept_terms_of_use}} </div>
        </div>
         <?php
            }
            ?>
        </div>
    </div>
    {hidesearchdownloadsection_end}
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



            {gallery}
            &nbsp;
            {viewlogs}
            &nbsp;
            {new_log_entry_link}
            &nbsp;
            {showhidedel_link}
        </p>
    </div>
    <div class="content2-container" id="viewcache-logs">
        <!-- log enteries - to be loaded dynamicly by ajax -->
    </div>
    <!-- End Text Container -->
