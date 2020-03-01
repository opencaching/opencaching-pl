<?php
use src\Controllers\PictureController;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\Waypoint;
use src\Utils\Gis\Countries;
use src\Utils\I18n\I18n;
use src\Utils\I18n\Languages;
use src\Utils\Uri\SimpleRouter;
use src\Models\Pictures\OcPicture;

/** @var $v View */
?>
<script>

  function validateBeforeSubmit() {

    // name can't be empty


  }

</script>

<div class="content2-container">

    <div class="content2-pagetitle">
        {{edit_cache}}:
        <a href="<?=$v->cache->getCacheUrl()?>"><?=$v->cache->getCacheName()?></a>
    </div>


    <form action="/cacheEdit/save/<?=$v->cache->getCacheId()?>" method="post" enctype="application/x-www-form-urlencoded"
          name="editcache_form" dir="ltr">

        <!-- BAR: cache base info  -->
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
              <img src="/images/blue/basic2.png" class="icon32" alt=""/>
              {{basic_information}}
            </p>
        </div>

        <!-- cache status -->
        <div class="form-group-sm">
            <label for="cacheStatus">{{status_label}}:</label>
            <select id="cacheStatus" name="status" onChange="yes_change();" class="form-control input200" {disablestatusoption}>
                <?php foreach ($v->allowedNextStatuses as $statusId) { ?>
                <option value="<?=$statusId?>" <?=($v->cache->getStatus() == $statusId) ? 'selected="selected"':''?>>
                    <?=tr(GeoCache::CacheStatusTranslationKey($statusId))?>
                </option>
                <?php } //foreach-allowedNextStatuses ?>
            </select>
        </div>

        <!-- cache name -->
        <div class="form-group-sm">
            <label for="cacheName" class="content-title-noshade">{{name_label}}:</label>
            <input id="cacheName" type="text" name="name" value="<?=$v->cache->getCacheName()?>" maxlength="60"
                   class="form-control input400" onChange="yes_change();" />
        </div>

        <!-- cache type -->
        <div class="form-group-sm">
            <label for="cacheType" class="content-title-noshade">{{cache_type}}:</label>
            <select id="cacheType" name="type" class="form-control input200">
                <?php foreach ($v->allowedTypes as $typeId) { ?>
                <option value="<?=$typeId?>" <?=($v->cache->getCacheType() == $typeId) ? 'selected="selected"':''?>>
                    <?=tr(GeoCache::CacheTypeTranslationKey($typeId))?>
                </option>
                <?php } //foreach-allowedNextStatuses ?>
           </select>
        </div>

        <!-- cache size -->
        <div class="form-group-sm">
            <label for="cacheSize" class="content-title-noshade">{{cache_size}}:</label>
            <select id="cacheSize" name="size" class="form-control input200">
                <?php foreach ($v->allowedSizes as $sizeId) { ?>
                <option value="<?=$sizeId?>" <?=($v->cache->getSizeId() == $sizeId) ? 'selected="selected"':''?>>
                    <?=tr(GeoCache::CacheSizeTranslationKey($sizeId))?>
                </option>
                <?php } //foreach-allowedNextStatuses ?>
           </select>
        </div>

        <!-- cache coordinates -->
        <div class="form-group-sm">
            <span class="content-title-noshade">{{coordinates}}:</span>
            <?php $v->callChunk('coordsForm', $v->cache->getCoordinates(), 'coordsEdit'); ?>
        </div>

        <!-- cache country -->
        <div class="form-group-sm">
            <label for="cacheCountry" class="content-title-noshade">{{country_label}}:</label>
            <select id="cacheCountry" name="size" class="form-control input200">
                <?php foreach ($v->countries as $countryCode) { ?>
                <option value="<?=$countryCode?>" <?=($v->selectedCountryCode == $countryCode) ? 'selected="selected"':''?>>
                    <?=tr($countryCode)?>
                </option>
                <?php } //foreach-countries ?>
           </select>
        </div>

        <!-- cache region -->
        <div class="form-group-sm">
            <label for="cacheRegion" class="content-title-noshade">{{regiononly}}:</label>
            <select id="cacheRegion" name="size" class="form-control input200">
           </select>
           <button class="btn btn-default btn-sm" onclick="return extractregion()">{{region_from_coord}}</button>
        </div>


        <!-- cache difficulties -->
        <div class="form-group-sm">
            <p class="content-title-noshade">{{difficulty_level}}:</p>
            {{task_difficulty}}:
                <select name="difficulty" class="form-control input50">
                    <?php foreach ($v->cacheDifficulties as $cacheDifficulty) { ?>
                        <option value="<?=$cacheDifficulty?>"
                                <?=($v->cache->getDifficulty() == $cacheDifficulty) ? 'selected="selected"':''?>>
                        <?=$cacheDifficulty/2?>
                        </option>
                    <?php } //foreach-countries ?>
                </select>
                &nbsp;&nbsp;
            {{terrain_difficulty}}:
                <select name="terrain" class="form-control input50">
                    <?php foreach ($v->terreinDifficulties as $terreinDifficulty) { ?>
                        <option value="<?=$terreinDifficulty?>"
                        <?=($v->cache->getTerrain() == $terreinDifficulty) ? 'selected="selected"':''?>>
                        <?=$terreinDifficulty/2?>
                        </option>
                    <?php } //foreach-countries ?>
                </select>
        </div>
        <div>
            <div class="notice">
                {{difficulty_problem}}
                <a href="/difficultyForm.php" target="_BLANK">{{rating_system}}</a>.
            </div>
        </div>

        <!-- additional cache info: time, distance -->
        <div class="form-group-sm">
            <p class="content-title-noshade">{{additional_information}} ({{optional}}):</p>
                {{time}}:
                <input type="text" name="search_time" maxlength="10" class="form-control input50"
                       value="<?=$v->cache->getSearchTimeFormattedString(false)?>"/> h
                &nbsp;&nbsp;
                {{length}}:
                <input type="text" name="way_length" maxlength="10" class="form-control input40"
                       value="<?=$v->cache->getWayLenghtFormattedString(false)?>"/> km
                &nbsp;
        </div>
        <div>
            <div class="notice">{{time_distance_hint}}</div>
        </div>


        <!-- foreign waypoints -->
        <div class="form-group-sm">
            <p class="content-title-noshade">{{foreign_waypoint}} ({{optional}}):</p>

            <table class="table compact-horizontal">
                <tr>
                    <td>Geocaching.com:</td>
                    <td><input type="text" name="wp_gc" maxlength="7" size="7" class="form-control input70 uppercase"
                               value="<?=$v->cache->getOtherWaypointIds()['gc']?>" /></td>
                    <td>&nbsp;Navicache.com:</td>
                    <td><input type="text" name="wp_nc" maxlength="6" size="6" class="form-control input70 uppercase"
                               value="<?=$v->cache->getOtherWaypointIds()['nc']?>" /></td>
                </tr>
                <tr>
                    <td>Terracaching.com:</td>
                    <td><input type="text" name="wp_tc" maxlength="7" size="7" class="form-control input70 uppercase"
                               value="<?=$v->cache->getOtherWaypointIds()['tc']?>" /></td>
                    <td>&nbsp;GPSGames.org:</td>
                    <td><input type="text" name="wp_ge" maxlength="6" size="6" class="form-control input70 uppercase"
                               value="<?=$v->cache->getOtherWaypointIds()['ge']?>" /></td>
                </tr>
            </table>
            <div class="notice">{{foreign_waypoint_info}}</div>
        </div>


        <!-- BAR: cache attributes  -->
        <div class="content2-container bg-blue02">
          <p class="content-title-noshade-size1">
            <img src="/images/blue/attributes.png" class="icon32" alt=""/>&nbsp;{{cache_attributes}}
          </p>
        </div>

        <!-- attributes -->
        <div>
          <?php foreach ($v->allAttributes as /** @var $attr GeoCacheAttribute */ $attr) {
              if (in_array($attr->getId(), $v->cache->getCacheAttributesList() )){   // TODO: ache->getCacheAttributesList() doesn't work!!!
                  $icon = $attr->getIconLarge();
              } else {
                  $icon = $attr->getIconUndef();
              } ?>
            <img id="<?=$attr->getId()?>" src="/<?=$icon?>" alt="<?=$attr->getLongText()?>"
                 title="<?=$attr->getLongText()?>" onmousedown="toggleAttr({attrib_id});" />
          <?php } // foreach-attribiutes ?>

          <div class="notice">{{attributes_desc_hint}}</div>
        </div>


        <!-- BAR: cache descriptions  -->
        <div class="content2-container bg-blue02">
          <p class="content-title-noshade-size1">
            <img src="/images/blue/describe.png" class="icon32" alt=""/>
            &nbsp;{{descriptions}}
          </p>
        </div>

        <!-- add new description -->
        <div class="content2-newline">
            <p class="content-title-noshade">
                <a class="btn btn-sm btn-primary"  href="/newdesc.php?cacheid={cacheid_urlencode}" onclick="return check_if_proceed();">
                    <img src="/images/actions/list-add-20.png" align="middle" border="0" alt="" title="Dodaj nowy opis"/>
                    &nbsp;
                    {{add_new_desc}}
                </a>
            </p>
        </div>

        <!-- list of descriptions -->
        <div>
            <?php foreach ($v->descriptions as $descId => $descLang) { ?>
                <div>
                    <img src="<?=Countries::getFlagImg($descLang)?>" class="icon16" alt="">
                    &nbsp;
                    <?=Languages::LanguageNameFromCode($descLang, I18n::getCurrentLang())?>
                    &nbsp;&nbsp;
                    <a class="btn btn-sm btn-success" href="/editdesc.php?descid=<?=$descId?>"
                       onclick="return check_if_proceed();">
                        <img src="/images/actions/edit-16.png" border="0" align="middle" alt="">
                        <?=tr('edit')?>
                    </a>
                    &nbsp;&nbsp;
                    <a class="btn btn-sm" href="/removedesc.php?cacheid=<?=$v->cache->getCacheId()?>&desclang=<?=$descLang?>"
                       onclick="return check_if_proceed();">
                       <img src="/images/log/16x16-trash.png" border="0" align="middle" class="icon16" alt="">
                       <?=tr('delete')?>
                    </a>
                </div>
          <?php } // foreach-attribiutes ?>
        </div>


        <!-- waypoints -->
        <?php if (!$v->skipWaypointsSection) { ?>
            <!-- BAR: waypoints -->
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">
                    <img src="/images/blue/compas.png" class="icon32" alt=""/>
                    &nbsp;{{additional_waypoints}}
                </p>
            </div>

            <div class="content2-newline">
                <p class="content-title-noshade">
                    <a class="btn btn-sm btn-primary" onclick="return check_if_proceed();" href="/newwp.php?cacheid={cacheid}" >
                      <img src="/images/actions/list-add-20.png" align="middle" border="0" alt=""/>
                      &nbsp;
                      {{add_new_waypoint}}
                    </a>
                </p>
            </div>

            <!-- waypoints list -->
            <?php if (!empty($v->allWaypoints)) { ?>
                <div>
                    <table id="gradient" cellpadding="5" width="97%" border="1"
                           style="border-collapse: collapse; font-size: 11px; line-height: 1.6em; color: #000000; ">
                        <tr>
                            <?php if ($v->waypointsHasStages) { ?>
                                <th align="center" valign="middle" width="30"><b><?=tr('stage_wp')?></b></th>
                            <?php } //if-waypointsHasStages ?>
                            <th width="32"><b><?=tr('symbol_wp')?></b></th>
                            <th width="32"><b><?=tr('type_wp')?></b></th>
                            <th width="32"><b><?=tr('coordinates_wp')?></b></th>
                            <th><b><?=tr('describe_wp')?></b></th>
                            <th width="22"><b><?=tr('status_wp')?></b></th>
                            <th width="22"><b><?=tr('edit')?></b></th>
                            <th width="22"><b><?=tr('delete')?></b></th>
                        </tr>

                        <?php foreach ($v->allWaypoints as /** @var $wp Waypoint */ $wp) {
                            $wpTypeName = tr(Waypoint::typeTranslationKey($wp->getType()));
                        ?>
                          <tr>
                              <?php if ($v->waypointsHasStages) { ?>
                              <td align="center" valign="middle"><?=$wp->getStage()?></td>
                              <?php } //if-waypointsHasStages ?>
                              <td align="center" valign="middle">
                                  <img src="/<?=$wp->getIconName()?>" alt="<?=$wpTypeName?>" title="<?=$wpTypeName?>" />
                              </td>
                              <td align="center" valign="middle"><?=$wpTypeName?></td>
                              <td align="center" valign="middle">
                                  <b><span style="color: rgb(88,144,168)"><?=$wp->getCoordinates()->getAsText()?></span></b>
                              </td>
                              <td align="center" valign="middle"><?=$wp->getDesc4Html()?></td>
                              <td align="center" valign="middle">
                                  <img src="<?=Waypoint::getStatusIcon($wp->getStatus())?>"
                                       alt="<?=tr(Waypoint::statusTranslationKy($wp->getStatus()))?>"
                                       title="<?=tr(Waypoint::statusTranslationKy($wp->getStatus()))?>" />
                              </td>
                              <td align="center" valign="middle">       <?php //TODO!!!  JS code  ?>
                                  <a class="links" onclick="return check_if_proceed();"  href="/editwp.php?wpid={wpid}">
                                      <img src="/images/actions/edit-16.png" alt="" />
                                  </a>
                              </td>
                              <td align="center" valign="middle">       <?php //TODO!!!  JS code  ?>
                                  <a class="links" href="/editwp.php?wpid={wpid}&delete"
                                     onclick="if (confirm(\'' . tr('ec_delete_wp') . '\')) {return check_if_proceed();} else {return false;};">
                                     <img src="/images/log/16x16-trash.png" align="middle" class="icon16" alt="" />
                                  </a>
                              </td>
                          </tr>
                        <?php } // foreach-attribiutes ?>
                  </table>
                  <div class="notice">{{waypoints_about_info}}</div>
                </div>

            <?php } else { //if-no-waypoints ?>

                <div class="notice"><?=tr('nowp_notice')?></div>

            <?php } // if-no-waypoints ?>
        <?php } //if-skipWaypointsSection ?>


        <!-- BAR: pictures -->
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
                <img src="/images/blue/picture.png" class="icon32" alt=""/>
                &nbsp;&nbsp;
                {{pictures_label}}
            </p>
        </div>

        <div class="content2-newline">
            <p class="content-title-noshade">
                <a class="btn btn-sm btn-primary" href="/newpic.php?objectid=<?=$v->cache->getCacheId()?>&type=2&def_seq={def_seq}"  <?php //TODO: def_seq = max_def_seq +1...?>
                   onclick="return check_if_proceed();">
                   <img src="/images/actions/list-add-20.png" align="middle" border="0" alt="" />
                   &nbsp;
                    {{add_new_pict}}
                </a>
            </p>
        </div>

        <!-- list of descriptions -->
        <div>
            <?php foreach ($v->pictures as /** @var $pic OcPicture */ $pic) { ?>
                <div class="form-group-sm">
                    [TODO:SEQ]
                    <img src="/images/free_icons/picture.png" class="icon32" alt="" />
                    &nbsp;
                    <a href="<?=$pic->getUrl()?>" target="_blank">
                      <?=$pic->getTitle()?>
                    </a>
                    &nbsp;&nbsp;
                    <a class="btn btn-sm btn-success" href="/editpic.php?uuid=<?=$pic->getUuid()?>" onclick="return check_if_proceed();">
                      <img src="/images/actions/edit-16.png" align="middle" alt="" title="" />
                      <?=tr('edit')?>
                    </a>
                    &nbsp;&nbsp;
                    <a class="btn btn-sm" href="<?=SimpleRouter::getLink(PictureController::class, 'remove',[$pic->getUuid()])?>" onclick="">
                        <img src="/images/log/16x16-trash.png" border="0" align="middle" class="icon16" alt="" title="" />
                        <?=tr('delete')?>
                    </a>
                </div>
            <?php } //foreach- ?>
        </div>


        <!-- BAR: mp3s -->
        <?php if ($v->skipMp3sSection) { ?>
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">
                  <img src="/images/blue/podcache-mp3.png" class="icon32" alt=""/>
                  &nbsp;&nbsp;{{mp3_label}}
                </p>
            </div>

            <div class="content2-newline">
                <p class="content-title-noshade">
                    <a class="btn btn-sm btn-primary" href="/newmp3.php?objectid={cacheid_urlencode}&type=2&def_seq_m={def_seq_m}"
                       onclick="return check_if_proceed();">
                       <img src="/images/actions/list-add-20.png" align="middle" border="0" alt=""/>
                       &nbsp;
                       {{add_new_mp3}}
                    </a>
                </p>
             </div>

            <!-- list of mp3s -->
            <div>
                <?php if (!empty ($v->cache->getMp3List())) { ?>

                    <?php foreach ($v->cache->getMp3List() as $mp3) { ?>
                    <div>
                    {seq_drop_mp3}
                    <img src="/images/free_icons/sound.png" class="icon32" alt="" />
                    &nbsp;
                    <a target="_BLANK" href="{link}">{title}</a>
                    &nbsp;&nbsp;
                    <a class="btn btn-sm btn-success" href="/editmp3.php?uuid={uuid}" onclick="return check_if_proceed();">
                      <img src="/images/actions/edit-16.png" align="middle" alt="" title="" />
                      <?=tr('edit')?>
                    </a>

                    <a class="btn btn-sm"  href="removemp3.php?uuid={uuid}" onclick="">
                      <img src="/images/log/16x16-trash.png" border="0" align="middle" class="icon16" alt="" title="" />
                      <?=tr('delete')?>
                    </a>
                    <?php } // ?>

                <?php } else { // if-empty-mp3List ?>
                  <div class="notice"><?=tr('no_mp3_files')?></div>
                <?php } // if-empty-mp3List ?>
            </div>

        <?php } //if-skipMp3sSection ?>


        <!-- BAR: others -->
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
                <img src="/images/blue/crypt.png" class="icon32"/>{{other}}
            </p>
        </div>

        <div>
            <fieldset class="form-group-sm">
                <legend>&nbsp; <strong>{{date_hidden_label}}</strong> &nbsp;</legend>
                <input class="form-control input30" type="text" name="hidden_day" maxlength="2" value="{date_day}" onChange="yes_change();" />
                -
                <input class="form-control input30" type="text" name="hidden_month" maxlength="2" value="{date_month}" onChange="yes_change();" />
                -
                <input class="form-control input50" type="text" name="hidden_year" maxlength="4" value="{date_year}" onChange="yes_change();" />
            </fieldset>
            <div class="notice">{{event_hidden_hint}}</div>
        </div>

        <!-- activation section -->
        <?php if (!$v->skipActivationSection) { ?>
            <div>
                <fieldset>
                    <legend>&nbsp;<?=tr("submit_new_cache")?>&nbsp;</legend>
                    <input type="radio" onChange="yes_change();" class="radio" name="publish" id="publish_now" value="now" {publish_now_checked}>
                    &nbsp;
                    <label for="publish_now"><?=('publish_now')?></label>
                    <br />
                    <input type="radio" onChange="yes_change();" class="radio" name="publish" id="publish_later" value="later" {publish_later_checked}>
                    &nbsp;
                    <label for="publish_later"><?=tr('publish_date')?>:</label>
                    <input class="input40" type="text" name="activate_year" onChange="yes_change();" maxlength="4" value="{activate_year}"/>
                     -
                    <input class="input20" type="text" name="activate_month" onChange="yes_change();" maxlength="2" value="{activate_month}"/>
                     -
                    <input class="input20" type="text" name="activate_day" onChange="yes_change();" maxlength="2" value="{activate_day}"/>
                    &nbsp;
                    <select name="activate_hour" class="input40" onChange="yes_change();" >
                        {activation_hours}
                    </select>
                    &nbsp;–&nbsp;{activate_on_message}
                    <br />
                    <input type="radio" onChange="yes_change();" class="radio" name="publish" id="publish_notnow" value="notnow" {publish_notnow_checked}>
                    &nbsp;
                    <label for="publish_notnow"><?=tr('dont_publish_yet')?></label>
                </fieldset>
            </div>
        <?php } // if-skipActivationSection ?>

        <!-- password section -->
        <?php if ($v->skipPasswordSection) { ?>
            <div>
                <fieldset>
                    <legend>&nbsp;{{log_password}}&nbsp;</legend>
                    <input class="form-control input120" type="text" name="log_pw" id="log_pw" value="{log_pw}"
                           maxlength="20" onChange="yes_change();" />
                    ({{no_password_label}})
                </fieldset>
                <div class="notice" style="width:500px;">{{please_read}}</div>
            </div>
        <?php } //if-skipPasswordSection ?>


        <div class="callout callout-warning">{{creating_cache}}</div>

        <button type="submit" name="submit" value="{submit}" class="btn btn-primary">{{store}}</button>
    </form>

</div>