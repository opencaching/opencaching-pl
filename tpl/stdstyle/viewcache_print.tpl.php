
<script type="text/javascript">
    function hidediv() {
        if (document.getElementById) { // DOM3 = IE5, NS6
            document.getElementById('hideshow').style.visibility = 'hidden';
        }
    }

    function showdiv() {
        if (document.getElementById) { // DOM3 = IE5, NS6
            document.getElementById('hideshow').style.visibility = 'visible';
        }
    }
</script>

<div class="line-box">
    <div class="content-title-noshade-size1">
        <p class="cache-title">
            <img src="<?=$view->geoCache->getCacheIcon()?>" class="icon32" id="viewcache-cacheicon" alt="{cachetype}" title="{cachetype}" align="absmiddle" />{cachename}
        </p>
        <img src="tpl/stdstyle/images/free_icons/arrow_in.png" class="icon16" alt="" title="" />&nbsp;<b><?=$view->geoCache->getWaypointId()?>
        <img src="tpl/stdstyle/images/blue/kompas.png" class="icon32" alt="" title=""  align="absmiddle"/>{coords}</b><br/>

        <img src='<?=$view->geoCache->getDifficultyIcon()?>' class='img-difficulty' width='19' height='16' alt='' title='<?=$view->diffTitle?>'>
        <img src='<?=$view->geoCache->getTerreinIcon()?>' class='img-difficulty' width='19' height='16' alt='' title='<?=$view->terrainTitle?>'>


         <?=$view->geoCacheDesc->getShortDescToDisplay()?>
        {{hidden_by}} <a href="viewprofile.php?userid={userid_urlencode}">{owner_name}</a>

        <img src="tpl/stdstyle/images/free_icons/package.png" class="icon16" alt="" title="" />&nbsp;
        <b><?=tr($view->geoCache->getSizeTranslationKey())?></b>


        <?php if($view->geoCache->getWayLenght() || $view->geoCache->getSearchTime()) { ?>
            <img src="tpl/stdstyle/images/free_icons/time.png" class="icon16" alt="" title="" />&nbsp;
            <?php if($view->geoCache->getSearchTime()) { ?>
              <?=$view->geoCache->getSearchTimeFormattedString() ?>
            <?php } else { // no-search-time ?>
              <?=tr('not_available')?>
            <?php } //no-search-time ?>
            &nbsp;&nbsp;

            <img src="tpl/stdstyle/images/free_icons/arrow_switch.png" class="icon16" alt="" title="" />&nbsp;
            <?php if($view->geoCache->getWayLenght()) { ?>
              <?=$view->geoCache->getWayLenghtFormattedString() ?>
            <?php } else { // no-way-len ?>
              <?=tr('not_available')?>
            <?php } //no-way-len ?>
        <?php } //if-way-length-and-search-time-present ?>

        <img src="images/cache-rate.png" class="icon16" alt="" />
        <?=tr('score_label')?>: <b><font color="<?=$view->scoreColor?>"><?=$view->score?></font></b>

        <?php if(!empty($view->otherSitesListing)){ ?>
          <br />
          <img src="tpl/stdstyle/images/free_icons/link.png" class="icon16" alt="" title="" />&nbsp;{{listed_also_on}}:
          <span class="listed-on">
          <?php foreach ($view->otherSitesListing as $site){ ?>
            <a href=<?=$site->link?> target="_blank"><?=$site->name?>(<?=$site->wp?>)</a>
          <?php } //foreach ?>
          </span>
        <?php } //!empty($view->otherSitesListing ?>

    </div>
</div>

<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="" align="absmiddle" />
        <b>{{descriptions}}</b>&nbsp;<br/>
        <?php foreach($view->geoCache->getCacheAttributesList() as $attr){ ?>
          <img src="<?=$attr->iconLarge?>" title="<?=$attr->text?>" alt="<?=$attr->text?>">&nbsp;
        <?php } // foreach-attrib. ?>
    </p>
</div>

<div class="content2-container">
    <div id="description">
        <div id="viewcache-description">
          <?=$view->geoCacheDesc->getDescToDisplay()?>
        </div>
    </div>
</div>


<?php if( !empty($view->geoCacheDesc->getHint()) ) { ?>

<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="" align="absmiddle" />
        <b>{{additional_hints}}</b>&nbsp;&nbsp;
    </p>
</div>
<div class="content2-container">

    <?php if($view->isUserAuthorized || $view->alwaysShowCoords) { ?>

        <div style="width:200px;align:right;float:right">
          <?php if(!$view->showUnencryptedHint) { ?>
            <font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">A|B|C|D|E|F|G|H|I|J|K|L|M</font><br/>
            <font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>
          <?php } //if-show-unencrypted-hint ?>
        </div>
        <div id="viewcache-hints"><?=$view->hintDecrypted?></div>

    <?php } else { // if-user-not-authorized or showAll-not-set in config ?>

          <span class="notice" style="width:500px;height:44px"  ><?=tr('vc_hint_for_logged_only')?></span>

    <?php } // if-user-authorized or showAll set in config ?>

</div>
<?php } // if-hint-present ?>




<?php if($view->isUserAuthorized) { ?>
    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size2">
            <img src="tpl/stdstyle/images/blue/logs.png" style="align: left; margin-right: 10px;" alt="{{personal_cache_note}}" />
            <b>{{personal_cache_note}}</b>
        </p>
    </div>
    <div class="content2-container">
        <table>
            <tr valign="top">
                <td></td>
                <td>
                    <div>
                        <span style="font-size:16px;"><?=$view->userNoteText?></span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
<?php } //if-isUserAuthorized ?>

<?php if( !empty($view->waypointsList) ) { ?>

<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/compas.png" alt="" />
        <b>{{additional_waypoints}}</b>
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
<br/>

<?php } //if-waypoints-present ?>



<?php if( !empty($view->geoCache->getNatureRegions() ) || !empty($view->geoCache->getNatura2000Sites())) { ?>

<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/npav1.png" class="icon32" alt="" align="absmiddle" />
        <b>{{natura2000}}</b>
    </p>
</div>
<div class="content2-container">
    <center>
        <table width="90%" border="0" style="border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em">
            <tr>
                <td align="center" valign="middle">&nbsp;</td><td align="center" valign="middle">

                        <?php if( !empty($view->geoCache->getNatureRegions() ) ){ ?>

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

                         <?php if(  !empty($view->geoCache->getNatura2000Sites()) ) { ?>

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

                </td><td align="center" valign="middle"><a class="links" href="http://www.natura2000.pl/" target="_blank"><img src="tpl/stdstyle/images/misc/natura2000.png" alt="" title="" /></a></td>
            </tr>
        </table>
    </center>
</div>

<?php } //if-natureRegions-present ?>



<!-- sekcja modyfikatora współrzędnych -->
<?php if($view->cacheCoordsModificationAllowed && $view->userModifiedCacheCoords) { ?>

<div  class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/signature1.png" class="icon32" alt="" align="absmiddle" />
        <b>{{coords_modifier}}</b>
    </p>
</div>
<div class="content2-container">
    <p>
        {{srch_Coord_have_been_modified}}:<br />
        <?=$view->userModifiedCacheCoords->getAsText()?>
    </p>
</div>

<?php } //if-cacheCoordsModificationAllowed ?>

<!-- koniec sekcji modyfikatora współrzędnych -->

<?php if( !empty($view->geoCache->getGeokretsHosted())) { ?>
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/travelbug.png" class="icon32" alt="" align="absmiddle" />
        <b>Geokrety</b>
    </p>
</div>
<div class="content2-container">
    <p>
        <?php foreach ($view->geoCache->getGeokretsHosted() as $gk) { ?>

            <img src="/images/geokret.gif" alt="">&nbsp;
            <a href='https://geokrety.org/konkret.php?id=<?=$gk['id']?>'><?=$gk['name']?></a>
            - <?=tr('total_distance')?>: <?=$gk['distance']?> km <br/>

        <?php } ?>
    </p>
</div>
<?php } //if-geokrety-inside ?>


<?php if( !empty($view->picturesToDisplay) ) { ?>
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="" align="absmiddle" />
        <b>{{images}}</b>
    </p>
</div>
<div class="content2-container">
    <div id="viewcache-pictures">
        <div id="hideshow">


            <?php foreach ($view->picturesToDisplay as $pic) { ?>

              <?php if(!$view->displayBigPictures) { ?>

                <!-- <br style="clear: left;" /> at every 4 pic... TODO -->
                <div class="viewcache-pictureblock">

                    <?php if( !$pic->spoiler || $view->isUserAuthorized || $view->alwaysShowCoords ) { ?>
                      <div class="img-shadow">
                        <a href="<?=$pic->url?>" title="<?=$pic->title?>" >
                          <img src="<?=$pic->thumbUrl?>" alt="<?=$pic->title?>" title="<?=$pic->title?>" />

                    <?php } else { //if-no-spoiler-or-display-all ?>

                      <div class="img-shadow">
                        <a href="<?=$pic->url?>" title="<?=$pic->title?>" >
                    <?php } //if-no-spoiler-or-display-all ?>

                        </a>
                      </div>

                    <span class="title"><?=$pic->title?></span>
                </div>

              <?php } else { //if-display-big-pics ?>

                <div style="display: block; float: left; margin: 3px;">
                <div style=""><p><?=$pic->title?></p></div>
                <img style="max-width: 600px;" src="<?=$pic->url?>" alt="<?=$pic->title?>" title="<?=$pic->title?>" />
                </div>

              <?php } //if-display-big-pics ?>

            <?php } //foreach ?>


        </div>
    </div>
</div>
<?php } //if-pictures-to-display-present ?>

<!-- Text container -->
<?php if($view->hideLogbook) { ?>

    <div class="content2-container bg-blue02 logs">
        <p class="content-title-noshade-size1">
            <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" align="absmiddle" />
            <b>{{log_entries}}</b>
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

        </p>
    </div>
    <div class="content2-container" id="viewcache-logs">

    </div>
<?php } // if-hideLogbook ?>

<!-- End Text Container -->
