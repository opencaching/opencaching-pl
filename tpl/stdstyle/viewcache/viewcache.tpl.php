<link rel="stylesheet" href="tpl/stdstyle/css/lightTooltip.css">

<script>
    var confirmRmLogTranslation = '{{confirm_remove_log}}';
</script>

<script src="<?=$view->viewcache_js?>"></script>


<input type="hidden" id="cacheid" value="{cacheid}">
<input type="hidden" id="logEntriesCount" value="<?=$view->logEntriesCount?>">
<input type="hidden" id="owner_id" value="<?=$view->ownerId?>">
<input type="hidden" id="includeDeletedLogs" value="<?=$view->displayDeletedLogs?>">
<input type="hidden" id="uType" value="<?=($view->isAdminAuthorized)?'1':'0'?>">
<?=$view->callChunk('infoBar', null, $view->infoMsg, $view->errorMsg)?>
<!-- cache-menu-buttons -->
<div class="content2-container line-box">

    <div id="cache-menu" class="line-box">
        <?php if(!$view->isUserAuthorized){ ?>
          <span class="notlogged-cacheview"><?=tr('cache_logged_required')?></span>
        <?php }else{ ?>

            <span id="buttons-left">
                <a class="btn btn-primary btn-md" href="log.php?cacheid=<?=$view->geoCache->getCacheId()?>">
                  <img src="images/actions/new-entry-16.png" alt="new-entry"/>&nbsp;<?=tr('new_log_entry')?>
                </a>
                <?php if($view->showWatchButton ){ ?>
                    <label class="btn btn-default btn-md two-state-btn">
                        <input type="checkbox" onclick="watchIt(this)"
                              value="<?=$view->geoCache->getWaypointId()?>"
                              <?=($view->watched)?'checked':''?> />
                        <img src="images/actions/watch-16.png" alt="" />&nbsp;
                        <span class="checkedLabel"><?=tr('watch_not')?></span>
                        <span class="uncheckedLabel"><?=tr('watch')?></span>
                    </label>
                <?php } //if-showWatchButton ?>
                <?php if($view->showIgnoreButton ){ ?>
                    <a class="btn btn-default btn-md" href="<?=$view->ignoreLink?>">
                      <img src="images/actions/ignore-16.png" alt="" />&nbsp;<?=$view->ignoreLabel?>
                    </a>
                <?php } //if-showIgnoreButton ?>
                <a class="btn btn-default btn-md" href="printcache.php?cacheid=<?=$view->geoCache->getCacheId()?>">
                  <img src="images/actions/print-16.png" alt="" />&nbsp;<?=tr('print')?>
                </a>
                <a class="btn btn-default btn-md" href="<?=$view->printListLink?>">
                  <img src="<?=$view->printListIcon?>" alt="" />&nbsp;<?=$view->printListLabel?>
                </a>
            </span>
            <span id="buttons-right">
                <?php if($view->showReportProblemButton) { ?>
                    <a class="btn btn-default btn-md" href="report.php?action=add&amp;cacheid=<?=$view->geoCache->getCacheId()?>">
                      <img src="images/actions/report-problem-18.png" alt="report-problem" />&nbsp;<?=tr('report_problem')?>
                    </a>
                <?php } //if-showReportProblemButton ?>
                <?php if($view->showEditButton ){ ?>
                    <a class="btn btn-success btn-md" href="editcache.php?cacheid=<?=$view->geoCache->getCacheId()?>">
                      <img src="images/actions/edit-16.png" alt="edit" />&nbsp;<?=tr('edit')?>
                    </a>
                <?php } //if-showEditButton ?>
            </span>
        <?php } //else ?>
    </div>

    <div>
        <!-- cache-icons -->
        <div id="cache-title-icons">

            <div class="align-center">
              <img src="<?=$view->cacheMainIcon?>" class="icon32"
                alt="<?=tr($view->geoCache->getCacheTypeTranslationKey())?>" title="<?=tr($view->geoCache->getCacheTypeTranslationKey())?>" />
            </div>
            <div class="align-right">
              <img src='<?=$view->geoCache->getDifficultyIcon()?>' class='img-difficulty' width='19' height='16' alt='difficulty' title='<?=$view->diffTitle?>'>
              <img src='<?=$view->geoCache->getTerrainIcon()?>' class='img-difficulty' width='19' height='16' alt='terrain' title='<?=$view->terrainTitle?>'>
            </div>
            <div class="align-center">

              <?php if( !$view->geoCache->isEvent() ) { ?>

                    <?php if (($view->geoCache->getFounds() + $view->geoCache->getNotFounds() + $view->geoCache->getNotesCount()) != 0) { ?>

                      <script type="text/javascript">
                        function cacheStatPopup(){
                          var url = "cache_stats.php?cacheid=<?=$view->geoCache->getCacheId()?>&popup=y";
                          window.open(url,'Cache_Statistics',"width=500,height=750,resizable=yes,scrollbars=1");
                        }
                      </script>
                      <a class="links2 lightTipped" href="#" onclick="cacheStatPopup()">
                         <img src="tpl/stdstyle/images/blue/stat1.png" alt="stats" title="">
                      </a>
                      <div class="lightTip"><?=tr('show_statictics_cache')?></div>

                    <?php } else { ?>
                      <a class="links2 lightTipped" href="#">
                         <img src="tpl/stdstyle/images/blue/stat1.png" alt="stats" title="">
                      </a>
                      <div class="lightTip"><?=tr('not_stat_cache')?></div>
                    <?php } ?>

              <?php } //if-not-event ?>

            </div>
        </div>

        <!-- cache name block -->
        <div class="content2-container-2col-left" id="cache-name-block">

            <div id="cache-name">
                <div class="content-title-noshade-size5">
                    <?=$view->cachename?> - <?=$view->geoCache->getWaypointId()?>
                    <?php if($view->geoCache->isTitled()) { ?>
                        <img src="tpl/stdstyle/images/free_icons/award_star_gold_1.png" class="icon16" alt="<?=$view->titledDesc?>" title="<?=$view->titledDesc?>" />
                    <?php } //if-titled ?>
                </div>

                <div class="content-title-noshade-size1">
                  <?=$view->geoCacheDesc->getShortDescToDisplay()?>
                </div>

                <div class="common-desc">
                  <?=tr('owner')?>:
                  <a class="links" href="viewprofile.php?userid=<?=$view->ownerId?>"><?=$view->ownerName?></a>
                </div>

                <?php if($view->geoCache->isAdopted() ) { ?>
                  <div class="common-desc">
                    <?=tr('creator')?>:
                    <a class="links" href="viewprofile.php?userid=<?=$view->founderId?>"><?=$view->founderName?></a>
                  </div>
                <?php } //if-is-adopted ?>


                <?php if($view->geoCache->isEvent()) { ?>
                    <div class="common-desc">
                        <img src="tpl/stdstyle/images/blue/meeting.png" width="22" height="22" alt="meeting" />
                        <script type="text/javascript">
                            function eventAttendancePopup(){
                              var url = "event_attendance.php?id=<?=$view->geoCache->getCacheId()?>&popup=y";
                              window.open(url,'<?=tr('list_of_participants')?>',"width=320,height=440,resizable=no,scrollbars=1");
                            }
                        </script>
                        <a href="#" onclick="eventAttendancePopup()"><?=tr('list_of_participants')?></a>
                    </div>
                <?php } //if-is-event ?>
            </div>

            <?php if($view->geoPathSectionDisplay) { ?>
                <div id="geoPath">

                    <div class="align-center" id="geoPath-head">
                      <?=tr('cache_belongs_to_geopath')?>!
                    </div>

                    <?php foreach($view->geoPathsList as $geoPath){ ?>
                      <div class="flex-container test" id="geoPath-content">
                        <img src="<?=$geoPath->img?>" alt="geopath" />
                        <span id="geoPath-link" class="align-center">
                          <a href="powerTrail.php?ptAction=showSerie&ptrail=<?=$geoPath->id?>"><?=$geoPath->name?></a>
                        </span>
                      </div>
                    <?php } //foreach ?>

                </div>
            <?php } //if($view->ptDisplay) ?>

        </div>
    </div>
</div>


<!--  cache details: -->

<div class="content2-container">
    <div class="content2-container-2col-left" id="viewcache-baseinfo">
        <div class="content-title-noshade-size3">
            <a href="compass.php?wp=<?=$view->geoCache->getWaypointId()?>&amp;popup=y" target="_blank">
            <img src="tpl/stdstyle/images/blue/kompas.png" class="icon32" alt="compass" title=""></a>
              <?php if($view->isUserAuthorized || $view->alwaysShowCoords ) { ?>

                <?php if(!$view->userModifiedCacheCoords) { ?>
                  <?=$view->geoCache->getCoordinates()->getAsText()?>

                <?php } else { // if-userModifiedCacheCoords ?>
                  <?=$view->userModifiedCacheCoords->getAsText()?>

                <?php } // if-userModifiedCacheCoords ?>

              <?php } else { //user-not-authorized ?>
                <?=tr('hidden_coords')?>
              <?php } //else-user-not-authorized ?>
            <span class="content-title-noshade-size0">
                (WGS84)
                <?php if($view->userModifiedCacheCoords) { ?>
                  <a href="#coords_mod_section">
                    <img src="tpl/stdstyle/images/blue/signature1-orange.png" class="icon32"
                      alt="<?=tr('orig_coord_modified_info')?><?=$view->geoCache->getCoordinates()->getAsText()?>"
                      title="<?=tr('orig_coord_modified_info')?><?=$view->geoCache->getCoordinates()->getAsText()?>" />
                  </a>
                <?php } //coords modified ?>
            </span>
        </div>

        <div class="list-of-details">

            <?php if($view->isUserAuthorized || $view->alwaysShowCoords ) { ?>
            <div>
                <img src="tpl/stdstyle/images/free_icons/map.png" class="icon16" alt="map" title="" />
                <script type="text/javascript">
                    function coordinatesPopup(){
                      var url = "coordinates.php?lat=<?=$view->geoCache->getCoordinates()->getLatitude()?>"+
                        "&lon=<?=$view->geoCache->getCoordinates()->getLongitude()?>"+
                        "&popup=y&wp=<?=$view->geoCache->getWaypointId()?>";

                      window.open(url,'<?=tr('list_of_participants')?>',"width=240,height=334,resizable=yes,scrollbars=1");
                    }
                </script>
                <span class="no-whitespace">
                    <a href="#" class="links" onclick="coordinatesPopup()"><?=tr('coords_other')?></a>
                    <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link" class="img12" />
                </span>
            </div>
            <?php } //show-other-coords ?>

            <div>
                <img src="tpl/stdstyle/images/free_icons/mountain.png" class="icon16" width=16 height=16 alt="altitude" title="">
                {{cache_alt}}: {altitude} {{abovesealevel}}
            </div>

            <div>
                <img src="tpl/stdstyle/images/free_icons/world.png" class="icon16" alt="location" title="">&nbsp;{{region}}:
                <b>
                  <?=$view->geoCache->getCacheLocationObj()->getLocationDesc(' &gt; ')?>
                </b>
            </div>

            <?php if($view->displayDistanceToCache) { ?>
              <div>
                  <img src="tpl/stdstyle/images/free_icons/car.png" class="icon16" alt="distance" title="">
                  <?=tr('distance_to_cache')?>:<b><?=$view->distanceToCache?>&nbsp;km</b>

              </div>
            <?php } // if-display-distance-to-cache ?>

            <div>
                <img src="tpl/stdstyle/images/free_icons/box.png" class="icon16" alt="type" title="" />
                <?=tr('cache_type')?>: <b><?=tr($view->geoCache->getCacheTypeTranslationKey())?></b>
            </div>

            <div>
                <img src="tpl/stdstyle/images/free_icons/package_green.png" class="icon16" alt="size" title="">
                <?=tr('size')?>: <b><?=tr($view->geoCache->getSizeTranslationKey())?></b>
            </div>

            <div>
                <img src="tpl/stdstyle/images/free_icons/page.png" class="icon16" alt="status" title="">
                {{status_label}}:
                <?php if($view->geoCache->isStatusReady()) { ?>
                  <span style="color:green;font-weight:bold;">
                <?php } else { //if-cache-status-not-ready ?>
                  <span class="errormsg">
                <?php } // if-cache-status-ready ?>
                      <?=tr($view->geoCache->getStatusTranslationKey())?>
                  </span>
            </div>


            <?php if($view->geoCache->getWayLenght() || $view->geoCache->getSearchTime()) { ?>
                <div>
                    <img src="tpl/stdstyle/images/free_icons/time.png" class="icon16" alt="time" title="">
                    {{time}}:
                    <?php if($view->geoCache->getSearchTime()) { ?>
                        <?=$view->geoCache->getSearchTimeFormattedString() ?>
                    <?php } else { // no-search-time ?>
                        <?=tr('not_available')?>
                    <?php } //no-search-time ?>
                    &nbsp;&nbsp;

                    <img src="tpl/stdstyle/images/free_icons/arrow_switch.png" class="icon16" alt="wayTo" title="">
                    {{length}}:
                    <?php if($view->geoCache->getWayLenght()) { ?>
                        <?=$view->geoCache->getWayLenghtFormattedString() ?>
                    <?php } else { // no-way-len ?>
                        <?=tr('not_available')?>
                    <?php } //no-way-len ?>

                </div>
            <?php } //if-way-length-and-search-time-present ?>

            <div>
                <?php if($view->geoCache->isEvent()) { ?>
                    <img src="tpl/stdstyle/images/cache/16x16-event.png" class="icon16" alt="event" title="">
                    <?=tr('date_event_label')?>: <strong> <?=$view->cacheHiddenDate?> </strong>
                <?php } else { // cache-is-not-event ?>
                    <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="hidden" title="">
                    <?=tr('date_hidden_label')?>: <?=$view->cacheHiddenDate?>
                <?php } // cache-is-not-event ?>
            </div>

            <div>
                <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="creation-date" title="">
                {{date_created_label}}: <?=$view->cacheCreationDate?>
            </div>

            <div>
                <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="last-mod" title="">
                {{last_modified_label}}: <?=$view->cacheLastModifiedDate?>
            </div>

            <?php if(!empty($view->otherSitesListing)){ ?>
                <div>
                    <img src="tpl/stdstyle/images/free_icons/link.png" class="icon16" alt="link" title="">
                    {{listed_also_on}}:
                    <?php foreach ($view->otherSitesListing as $site){ ?>
                        <span class="no-whitespace">
                           <a href=<?=$site->link?> target="_blank"><?=$site->sitename?> (<?=$site->wp?>)</a>
                           <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link" class="img12">
                        </span>
                    <?php } //foreach ?>

                </div>
            <?php } //!empty($view->otherSitesListing ?>
        </div>
    </div>

    <div class="content2-container-2col-right" id="viewcache-maptypes">
        <div class="content2-container-2col-left" id="viewcache-numstats">
            <div class="list-of-details">
                <?php if($view->geoCache->isEvent()) { ?>
                    <div>
                        <img src="tpl/stdstyle/images/log/16x16-attend.png" class="icon16" alt="attends" title=""/>
                        <?=$view->geoCache->getFounds()?> <?=tr('attendends')?>
                    </div>

                    <div>
                        <img src="tpl/stdstyle/images/log/16x16-will_attend.png" class="icon16" alt="not-found" title=""/>
                        <?=$view->geoCache->getNotFounds()?> <?=tr('will_attend')?>
                    </div>

                <?php } else { //if-not-event ?>
                    <div>
                        <img src="tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="<?=tr('found')?>"/>
                        <?=$view->geoCache->getFounds()?>x <?=tr('found')?>
                    </div>
                    <div>
                        <?php if($view->geoCache->isMovable()) { ?>
                            <img src="tpl/stdstyle/images/log/16x16-moved.png" class="icon16" alt="moved" />
                            <?=$view->geoCache->getMoveCount()?>x <?=tr('moved_text')?>&nbsp;&nbsp;
                            <?=$view->geoCache->getDistance()?>&nbsp;km

                        <?php } //if-mobile-cache ?>
                    </div>
                    <div>
                        <img src="tpl/stdstyle/images/log/16x16-dnf.png" class="icon16" alt="{{not_found}}" />
                        <?=$view->geoCache->getNotFounds()?>x <?=tr('not_found')?>
                    </div>
                <?php } //if-not-event ?>

                <div>
                    <img src="tpl/stdstyle/images/log/16x16-note.png" class="icon16" alt="{{log_note}}" />
                    <?=$view->geoCache->getNotesCount()?> <?=tr('log_notes')?>
                </div>
                <div>
                    <img src="tpl/stdstyle/images/action/16x16-watch.png" class="icon16" alt="watchers" />
                    <?=$view->geoCache->getWatchingUsersCount()?> <?=tr('watchers')?>
                </div>

                <div class="lightTipped" style="display:inline;">
                    <img src="tpl/stdstyle/images/free_icons/vcard.png" class="icon16" alt="visits" />
                    <?=$view->geoCache->getCacheVisits()?> <?=tr('visitors')?>
                </div>
                <?php if($view->displayPrePublicationAccessInfo) {?>
                    <div class="lightTip" >
                        <b><?=tr('prepublication_visits')?>:</b>
                        <?=implode($view->geoCache->getPrePublicationVisits(), ', ')?>
                    </div>
                <?php } //if-displayPrePublicationAccessInfo ?>

                <div>
                    <img src="tpl/stdstyle/images/free_icons/thumb_up.png" class="icon16" alt="votes" />
                    <?=$view->geoCache->getRatingVotes()?> x <?=tr('scored')?>
                </div>

                <div>
                    <img src="images/cache-rate.png" class="icon16" alt="score" />
                    <?=tr('score_label')?>: <b style="color:<?=$view->scoreColor?>"><?=$view->score?></b>
                </div>

                <?php if($view->geoCache->getRecommendations() > 0) { ?>
                    <div>
                        <a class="links2 lightTipped" href="#">
                            <img src="images/rating-star.png" alt="{{recommended}}" />
                            <?=$view->geoCache->getRecommendations()?> x <?=tr('recommended')?>
                        </a>
                        <div class="lightTip">
                            <b><?=tr('recommended_by')?>:</b>
                            <?=$view->geoCache->getUsersRecomeded()?>
                        </div>
                    </div>
                <?php } // if-there-are-recommendations ?>

                <div>
                    <img src="images/gk.png" class="icon16" alt="geokret" title="GeoKrety visited" />
                    <span class="no-whitespace">
                        <a class="links no-whitespace" href="http://geokrety.org/szukaj.php?wpt=<?=$view->geoCache->getWaypointId()?>" target="_blank">{{history_gk}}</a>
                        <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link" class="img12" >
                    </span>
                </div>
            </div>
        </div>

        <div id="viewcache-map" class="content2-container-2col-right">

            <?php if ($view->isUserAuthorized || $view->alwaysShowCoords) { ?>
              <div class="img-shadow">
                <img src="<?=$view->mapImgLink?>"
                     longdesc="ifr::cachemap-mini.php?cacheId=<?=$view->geoCache->getCacheId()?>::480::345"
                     onclick="enlarge(this);" alt="<?=tr('map')?>" title="<?=tr('map')?>">
              </div>
            <?php } else { ?>
                <?=$view->loginToSeeMapMsg?>

            <?php } //else $view->isUserAuthorized || $view->alwaysShowCoords ?>

        </div>

        <div id="links-to-ext-maps" class="">
            <?php if ($view->isUserAuthorized || $view->alwaysShowCoords) { ?>
                <b>{{available_maps}}:</b>
                <?php foreach($view->externalMaps as $mapName => $url) { ?>
                  <a target="_blank" href="<?=$url?>"><?=$mapName?></a>
                <?php } //foreach ?>
            <?php } //else $view->isUserAuthorized || $view->alwaysShowCoords ?>
        </div>
    </div>
</div>

<?php if(!empty($view->geoCache->getCacheAttributesList())) { ?>
    <!-- cache attributes: -->
    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/attributes.png" class="icon32" alt="attributes">
            {{cache_attributes_label}}
        </span>
    </div>

    <div class="content2-container">
        <p>
            <?php foreach($view->geoCache->getCacheAttributesList() as $attr){ ?>
              <img src="<?=$attr->iconLarge?>" title="<?=$attr->text?>" alt="<?=$attr->text?>">&nbsp;

            <?php } // foreach-attrib. ?>
        </p>
    </div>
    <div class="notice noprint">
      {{attributes_desc_hint}}
      <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link">
    </div>
<?php } //cache has attributes ?>



<?php if(!empty($view->geoCacheDesc->getAdminComment() )) { ?>
    <!-- admin comments: -->
    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="">{{rr_comment_label}}
        </span>
    </div>
    <div class="content2-container">
        <p><?=$view->geoCacheDesc->getAdminComment()?></p>
    </div>
<?php } // if-admin-comment ?>

<!-- cache description header: -->
<div class="content2-container bg-blue02">
    <span class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="description">
        {{descriptions}}
    </span>

    <span id="descLangs-span">
        <?php foreach( $view->availableDescLangs as $descLang ){ ?>
            <a class="btn btn-sm btn-default" href="<?=$view->availableDescLangsLinks[$descLang]?>">
            <?php if($view->usedDescLang == $descLang) { ?>
                <b><?=$descLang?></b>

            <?php } else { // available-desc-langs ?>
                <?=$descLang?>

            <?php } // if-current-lang ?>
            </a>
        <?php } //foreach-available-desc-langs ?>
    </span>
    <?php if($view->isAdminAuthorized) { ?>
        <a class="btn btn-sm btn-default" href="add_octeam_comment.php?cacheid=<?=$view->geoCache->getCacheId()?>">
            <?=tr('add_rr_comment')?>
        </a>
        <a class="btn btn-sm btn-default" href="viewcache.php?cacheid=<?=$view->geoCache->getCacheId()?>&amp;rmAdminComment=1"
            onclick="return confirm('<?=tr("confirm_remove_rr_comment")?>');"><?=tr('remove_rr_comment')?>
        </a>
    <?php } //if-admin-authorized ?>
</div>

<!-- cache description: -->
<div class="content2-container">
    <div id="viewcache-description">
        <?=$view->geoCacheDesc->getDescToDisplay()?>
    </div>
</div>

<?php if( !is_null($view->openChecker) ) { ?>
<!-- openChecker container -->

    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/openchecker_32x32.png" class="icon32" alt="openchecker">
            {{openchecker_name}}
        </span>
    </div>

    <div class="content2-container">
        <div class="common-desc">
            {{openchecker_enabled}}
        </div>
        <div id="openchecker-btn">
            <a class="btn btn-default" href="openchecker.php?wp=<?=$view->geoCache->getWaypointId()?>">
                {{openchecker_check}}!
            </a>
        </div>
        <div class="common-desc">
            {{statistics}}:
            {{openchecker_tries}}: <?=$view->openChecker->getTries()?> {{openchecker_times}},
            {{openchecker_hits}}: <?=$view->openChecker->getHits()?> {{openchecker_times}}.
        </div>
    </div>

<?php } // if-openchacker-present ?>

<?php if( !empty($view->waypointsList) ) { ?>
    <!-- waypoints: -->
    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="waypoints">
            {{additional_waypoints}}
        </span>
    </div>
    <div class="content2-container">
        <table id="waypoints-table">
            <tr>
                <?php if($view->cacheWithStages) { ?>
                    <th style="width: 10%"><?=tr('stage_wp')?></th>
                <?php } //if-cache-with-stages ?>
                <th style="width: 10%"><?=tr('symbol_wp')?></th>
                <th style="width: 10%"><?=tr('type_wp')?></th>
                <th style="width: 20%"><?=tr('coordinates_wp')?></th>
                <th><?=tr('describe_wp')?></th>
            </tr>
            <?php foreach( $view->waypointsList as $wp ) { ?>
                <tr>
                    <?php if($view->cacheWithStages) { ?>
                        <td>
                            <strong>
                            <?php if($wp->getStage() != 0) { ?>
                                <?=$wp->getStage() ?>
                            <?php } ?>
                            </strong>
                        </td>
                    <?php } // if-cacheWithStages ?>

                    <td>
                        <img src="<?=$wp->getIconName()?>" alt="waypoint-icon" title="<?=tr($wp->getTypeTranslationKey())?>" />
                    </td>
                    <td>
                        <?=tr($wp->getTypeTranslationKey())?>
                    </td>
                    <td>
                        <b style="color: rgb(88,144,168)">
                            <?php if($wp->areCoordsHidden()) { ?>
                                N ?? ??????<br/>E ?? ??????
                            <?php } else { // if-coords-visible?>
                                <script type="text/javascript">
                                    function wpCoordinatesPopup<?=$wp->getId()?>(){
                                      var url = "coordinates.php?lat=<?=$wp->getCoordinates()->getLatitude()?>&"+
                                                "lon=<?=$wp->getCoordinates()->getLongitude()?>&popup=y&"+
                                                "wp=<?=$view->geoCache->getWaypointId()?>";
                                      window.open(url,"","width=240,height=334,resizable=yes,scrollbars=1");
                                    }
                                </script>
                                <a class="links4" href="#" onclick="wpCoordinatesPopup<?=$wp->getId()?>()">
                                    <?=$wp->getCoordinates()->getLatitudeString() ?><br/><?=$wp->getCoordinates()->getLongitudeString() ?>
                                </a>
                            <?php } // if-coords-visible ?>
                         </b>
                    </td>
                    <td>
                        <?=$wp->getDesc4Html()?>
                    </td>
                </tr>
            <?php } // foreach-waypoints ?>
        </table>
    </div>

    <div class="notice noprint">
        <a class="links" href="{wiki_link_additionalWaypoints}" target="_blank">
            {{show_info_about_wp}}
            <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link">
        </a>
    </div>
<?php } //if-waypoints-present ?>

<?php if( !empty($view->geoCacheDesc->getHint()) ) { ?>
    <!-- cache hint: -->
    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="">
            {{additional_hints}}
        </span>
    </div>

    <div class="content2-container">
        <div id="userNote-div">
        <?php if($view->isUserAuthorized || $view->alwaysShowCoords) { ?>

            <p id="hintEncrypted"><?=$view->hintDecrypted?></p>
            <p id="hintDecrypted" style="display: none"><?=$view->hintEncrypted?></p>

            <div id="hintEncTable">
                <?php if(!$view->showUnencryptedHint) { ?>
                    A|B|C|D|E|F|G|H|I|J|K|L|M<br/>
                    N|O|P|Q|R|S|T|U|V|W|X|Y|Z
                <?php } //if-show-unencrypted-hint ?>
            </div>
        <?php } else { // if-user-not-authorized or showAll-not-set in config ?>
            <span class="notice"><?=tr('vc_hint_for_logged_only')?></span>
        <?php } // if-user-authorized or showAll set in config ?>

        <?php if($view->isUserAuthorized || $view->alwaysShowCoords) { ?>
            <?php if(!$view->showUnencryptedHint) { ?>
                <a class="btn btn-default btn-sm" href="#" onclick="return showHint(event);">
                    <span id="decryptLinkStr"><?=tr('decrypt')?></span>
                    <span id="encryptLinkStr" style="display:none"><?=tr('encrypt')?></span>
                </a>
            <?php } //if-show-unencrypted-hint ?>
        <?php } // if-user-authorized or showAll set in config ?>

        </div>
    </div>
<?php } // if-hint-present ?>


<?php if($view->cacheCoordsModificationAllowed) { ?>
    <!-- coords user modification: -->
    <div id="coords_mod_section" class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/signature1.png" class="icon32" alt="" />
            {{coords_modifier}}
        </span>
    </div>

    <div class="content2-container">
        <div class="common-desc">{{coordsmod_main}}</div>

        <form id="form-coords-mod" action="viewcache.php?cacheid=<?=$view->geoCache->getCacheId()?>" method="post" name="form_coords_mod">
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

            <div id="submitBtns" >
                <input id="userModifiedCoordsSubmited" type="submit" name="userModifiedCoordsSubmited" value="{{modify_coords}}" disabled="disabled" class="btn btn-default btn-sm">
                <input id="deleteUserModifiedCoords" type="submit" name="deleteUserModifiedCoords" value="{{reset_coords}}" disabled="disabled" class="btn btn-default btn-sm">
            </div>
        </form>
        <div class="notice">
            {{modified_coord_notice}}
        </div>
    </div>
<?php } //if-cacheCoordsModificationAllowed ?>


<?php if($view->isUserAuthorized) { ?>
    <!-- user-note:  -->
    <div class="content2-container bg-blue02" id="userNotes">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/logs.png" alt="{{personal_cache_note}}"> {{personal_cache_note}}
        </span>
    </div>

    <div class="content2-container">
        <form action="viewcache.php?cacheid=<?=$view->geoCache->getCacheId()?>#userNotes" method="post" name="cache_note" id="cacheNoteForm">
            <textarea class="userNoteEdit userNoteTextarea" name="userNoteText" rows="4" style="display:none"><?=$view->userNoteText?></textarea>
            <div class="userNoteDisplay userNoteTextarea">
                <?=nl2br($view->userNoteText)?>
            </div>

            <div>
                <input type="submit" name="saveUserNote" value="{{save}}" class="btn btn-default btn-sm userNoteEdit" style="display:none" />
                <button id="editNoteBtn" type="button" class="btn btn-default btn-sm userNoteDisplay" style="display:none">{{Edit}}</button>
                <input type="submit" name="removeUserNote" value="{{delete}}" class="btn btn-default btn-sm userNoteDisplay" style="display:none">

                <span class="notice">
                    {{cache_note_visible}}
                </span>
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

<?php if( !empty($view->geoCache->getNatureRegions() ) || !empty($view->geoCache->getNatura2000Sites())) { ?>
    <!-- natura2000 etc. -->
    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/npav1.png" class="icon32" alt="">
            {{obszary_ochrony_przyrody}}
        </span>
    </div>

    <div class="content2-container align-center">
        <?php if( !empty($view->geoCache->getNatureRegions() ) ) { ?>
            <table class="naturaTable">
                <tr>
                    <td>
                      <b><?=tr('npa_info')?></b>:
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <?php foreach ($view->geoCache->getNatureRegions() as $npa) { ?>
                    <tr>
                      <td>
                          <a target="_blank" href="http://<?=$npa['npalink']?>">
                              <?=$npa['npaname']?>
                          </a>
                      </td>
                      <td>
                        <img src="tpl/stdstyle/images/pnk/<?=$npa['npalogo']?>" alt="" />
                      </td>
                    </tr>
                <?php } //foreach ?>
            </table>

        <?php } //if-NatureRegions-presents ?>
        <?php if( !empty($view->geoCache->getNatura2000Sites())) { ?>
            <table class="naturaTable">
                <tr>
                    <td>
                        <b>
                            <?=tr('npa_info')?>
                            <span style="color:green">NATURA 2000</span>
                        </b>:
                        <br />
                        <?php foreach ($view->geoCache->getNatura2000Sites() as $npa) {
                                $npa_item = $config['nature2000link'];
                                $npa_item = mb_ereg_replace('{linkid}', $npa['linkid'], $npa_item);
                                $npa_item = mb_ereg_replace('{sitename}', $npa['npaSitename'], $npa_item);
                                $npa_item = mb_ereg_replace('{sitecode}', $npa['npaSitecode'], $npa_item);
                                echo $npa_item; ?>
                                <br />
                        <?php } //foreach ?>
                    </td>
                    <td>
                      <img src="tpl/stdstyle/images/misc/natura2000.png" alt="natura2000" />
                    </td>
                </tr>
            </table>
        <?php } //if-Natura2000-presents ?>
    </div>
<?php } //if-natureRegions-present ?>

<?php if( !empty($view->geoCache->getGeokretsHosted())) { ?>
    <!-- geokrety: -->
    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/travelbug.png" class="icon32" alt="travelbug">
            Geokrety
        </span>
    </div>
    <div class="content2-container">
        <div id="geoKretySection">
            <p>
                <?php foreach ($view->geoCache->getGeokretsHosted() as $gk) { ?>
                    <img src="/images/geokret.gif" alt="geokret">&nbsp;
                    <a href='https://geokrety.org/konkret.php?id=<?=$gk['id']?>'><?=$gk['name']?></a>
                    - <?=tr('total_distance')?>: <?=$gk['distance']?> km <br/>
                <?php } ?>
            </p>
        </div>
    </div>
<?php } //if-geokrety-inside ?>


<?php if( !empty($view->geoCache->getMp3List() )) { ?>
    <!-- mp3-list -->
    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/podcache-mp3.png" class="icon32" alt="mp3">
            {{mp3_files_info}}
        </span>
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

<?php if( !empty($view->picturesToDisplay) ) { ?>
    <!-- pics-list: -->
    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="">
            {{images}}
        </span>
    </div>
    <div class="content2-container">
        <div id="viewcache-pictures">
            <?php foreach ($view->picturesToDisplay as $key => $pic) { ?>

                <div class="viewcache-pictureblock">
                    <div class="img-shadow">
                        <a href="<?=$pic->url?>" data-lightbox="cache-pics" data-title="<?=$pic->title?>">
                            <img src="<?=$pic->thumbUrl?>" alt="<?=$pic->title?>" />
                        </a>
                    </div>
                    <span class="title"><?=$pic->title?></span>
                </div>
                <?php if($key%4 == 3){ //add after every 4 pics ?>
                <div style="clear:both"></div>
                <?php } ?>
            <?php } //foreach ?>
        </div>
    </div>
<?php } //if-pictures-to-display-present ?>

<?php if($view->isUserAuthorized) { ?>
    <!-- utils-section: -->
    <div class="content2-container bg-blue02">
        <span class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/tools.png" class="icon32" alt="">&nbsp;{{utilities}}
        </span>
    </div>

    <div class="content2-container">
        <div id="viewcache-utility">
            <div>
                <img src="tpl/stdstyle/images/action/16x16-search.png" class="icon16" alt="" />
                {{search_geocaches_nearby}}:


                <a href="search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;latNS={latNS}&amp;lat_h={lat_h}&amp;lat_min={lat_min}&amp;lonEW={lonEW}&amp;lon_h={lon_h}&amp;lon_min={lon_min}&amp;distance=20&amp;unit=km">
                    {{all_geocaches}}
                </a> |
                <a href="search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=1&amp;f_userfound=1&amp;f_inactive=1&amp;latNS={latNS}&amp;lat_h={lat_h}&amp;lat_min={lat_min}&amp;lonEW={lonEW}&amp;lon_h={lon_h}&amp;lon_min={lon_min}&amp;distance=20&amp;unit=km">
                    {{searchable}}
                </a>
            </div>

            <?php if($view->searchAtOtherSites) { ?>
                <div>
                    <img src="tpl/stdstyle/images/action/16x16-search.png" class="icon16" alt="" />
                        {{find_geocaches_on}}:

                        <a target="_blank" href="//www.geocaching.com/seek/nearest.aspx?origin_lat={latitude}&amp;origin_long={longitude}&amp;dist=100&amp;submit8=Submit\">geocaching.com</a> |
                        <a target="_blank" href="http://www.terracaching.com/gmap.cgi#center_lat={latitude}&amp;center_lon={longitude}&amp;center_zoom=7&cselect=all&ctselect=all">terracaching.com</a> |
                        <a target="_blank" href="http://www.navicache.com/cgi-bin/db/distancedp.pl?latNS={latNS}&amp;latHours={latitude}&amp;longWE={lonEW}&amp;longHours={longitudeNC}&amp;Distance=100&amp;Units=M">navicache.com</a> |
                        <a target="_blank" href="http://geocaching.gpsgames.org/cgi-bin/ge.pl?basic=yes&amp;download=Google+Maps&amp;zoom=8&amp;lat_1={latitude}&amp;lon_1={longitude}">gpsgames.org</a> |
                        <a href="http://www.opencaching.cz/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS={latNS}&amp;lat_h={lat_h}&amp;lat_min={lat_min}&amp;lonEW={lonEW}&amp;lon_h={lon_h}&amp;lon_min={lon_min}&amp;distance=100&amp;unit=km">oc.cz</a> |
                        <a href="http://www.opencaching.de/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS={latNS}&amp;lat_h={lat_h}&amp;lat_min={lat_min}&amp;lonEW={lonEW}&amp;lon_h={lon_h}&amp;lon_min={lon_min}&amp;distance=100&amp;unit=km">oc.de</a>
                </div>
            <?php } //if-searchAtOtherSites ?>

            <hr style="color: blue;">

            <div>
                <img src="tpl/stdstyle/images/action/16x16-save.png" class="icon16" alt="" />
                <b>{{download_as_file}}</b>

                <table>
                    <tr>
                        <td>
                            <div class="searchdiv">
                                <span class="content-title-noshade txt-blue08">{{format_GPX}}</span>:<br>
                                <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=gpxgc" title="GPS Exchange Format .gpx">GPX</a> |
                                <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=<?=$view->geoCache->getCacheId()?>&amp;output=zip" title="Garmin ZIP file ({{format_pict}})  .zip">GARMIN ({{format_pict}})</a>
                            </div>
                        </td>
                        <td>
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
                        <td>
                            <div class="searchdiv">
                                <span class="content-title-noshade txt-blue08">{{send_to}}:</span><br>
                                <a class="links" href="#" onclick="openCgeoWindow(event, '<?=$view->geoCache->getWaypointId()?>')" title="c:geo">{{send_to_cgeo}}</a> |
                                <a class="links" href="#" onclick="openGarminWindow(event, '{latitude}','{longitude}',
                                '<?=$view->geoCache->getWaypointId()?>','<?=$view->cachename?>')" title="{{send_to_gps}}">{{send_to_gps}}</a>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                </table>

                <div class="notice">
                  {{accept_terms_of_use}}
                </div>
            </div>
        </div>
    </div>
<?php } // if-isUserAuthorized ?>

<div class="content2-container bg-blue02">
    <span class="content-title-noshade-size1" id="log_start">
        <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="">
        {{log_entries}}:

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
            <?=$view->geoCache->getPicsInLogsCount()?>x

            <a href="gallery_cache.php?cacheid=<?=$view->geoCache->getCacheId()?>">
                 <?=tr('gallery_short')?>
            </a>
        <?php } //if-getNumberOfPicsInLogs > 0 ?>

    </span>
    <span id="log-start-buttons">

        <?php if($view->isUserAuthorized) { ?>
            <a class="btn btn-sm btn-primary" href="log.php?cacheid=<?=$view->geoCache->getCacheId()?>" title="<?=tr('new_log_entry')?>">
              <!-- img src="images/actions/new-entry-18.png" title="<?=tr('new_log_entry')?>" alt="<?=tr('new_log_entry')?>"-->
              <?=tr('new_log_entry_short')?>
            </a>
        <?php } //if-isUserAuthorized ?>

        <?php if($view->displayAllLogsLink) { ?>

            <a class="btn btn-sm btn-default" href="viewlogs.php?cacheid=<?=$view->geoCache->getCacheId()?>" >
              <!-- img src="tpl/stdstyle/images/action/16x16-showall.png" class="icon16" alt="<?=tr('show_all_log_entries')?>"
                   title="<?=tr('show_all_log_entries')?>" / -->
              <?=tr("show_all_log_entries_short")?>
            </a>
        <?php } //if-logEntriesCount ?>


        <?php if($view->showDeletedLogsDisplayLink) { ?>
            <span style="white-space: nowrap;">
                <a class="btn btn-sm btn-default" href="<?=$view->deletedLogsDisplayLink?>" title="<?=$view->deletedLogsDisplayText?>">
                    <!-- img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="<?=$view->deletedLogsDisplayText?>" title="<?=$view->deletedLogsDisplayText?>" /-->
                    <?=$view->deletedLogsDisplayText?>
                </a>
            </span>
        <?php } //if-showDeletedLogsDisplayLink ?>
    </span>
</div>

<div class="content2-container" id="viewcache-logs">
    <!-- log entries - to be loaded dynamicly by ajax -->
</div>


<?php if($view->badgesPopUp) { ?>

<script type='text/javascript'>
        $( function() {
            $( '#dialog' ).dialog({
                autoOpen: true,
                width : 550,
                show: {
                    effect: 'fade',
                    duration: 1000
                },
                hide: {
                    effect: 'fade',
                    duration: 1000
                }
            });
        });
</script>

<div id="dialog" title="{{merit_badge_gain_next_level}}">
  <?=$view->badgesPopupHtml?>
</div>

<?php } //if($view->badgesPopUp ?>
