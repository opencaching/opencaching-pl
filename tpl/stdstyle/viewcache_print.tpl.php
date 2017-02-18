
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
            <img src="{icon_cache}" class="icon32" id="viewcache-cacheicon" alt="{cachetype}" title="{cachetype}" align="absmiddle" />{cachename}
        </p>
        <img src="tpl/stdstyle/images/free_icons/arrow_in.png" class="icon16" alt="" title="" />&nbsp;<b><?=$view->geoCache->getWaypointId()?>
        <img src="tpl/stdstyle/images/blue/kompas.png" class="icon32" alt="" title=""  align="absmiddle"/>{coords}</b><br/>

        {difficulty_icon_diff} {difficulty_icon_terr} <?=$view->geoCacheDesc->getShortDescToDisplay()?>
        {{hidden_by}} <a href="viewprofile.php?userid={userid_urlencode}">{owner_name}</a>

        <img src="tpl/stdstyle/images/free_icons/package.png" class="icon16" alt="" title="" />&nbsp;<b>{cachesize}</b>


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

<?php
global $usr, $lang, $hide_coords;
?>
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
            {desc}
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




{CacheNoteS}
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
                    <span style="font-size:16px;">{notes_content}</span>
                </div>
            </td>
        </tr>
    </table>
</div>
{CacheNoteE}

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



{hidenpa_start}
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
                <td align="center" valign="middle">&nbsp;</td><td align="center" valign="middle">{npa_content}</td><td align="center" valign="middle"><a class="links" href="http://www.natura2000.pl/" target="_blank"><img src="tpl/stdstyle/images/misc/natura2000.png" alt="" title="" /></a></td>
            </tr>
        </table>
    </center>
</div>
{hidenpa_end}

<!-- sekcja modyfikatora współrzędnych -->
<?php if($view->cacheCoordsModificationAllowed) { ?>

<div  class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/signature1.png" class="icon32" alt="" align="absmiddle" />
        <b>{{coords_modifier}}</b>
    </p>
</div>
<div class="content2-container">
    <p>
        {{srch_Coord_have_been_modified}}:<BR/>
        {coordmod_lat_h} {coordmod_lat}<BR/>
        {coordmod_lon_h} {coordmod_lon}<BR/>
    </p>
</div>

<?php } //if-cacheCoordsModificationAllowed ?>

<!-- koniec sekcji modyfikatora współrzędnych -->

{geokrety_begin}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/travelbug.png" class="icon32" alt="" align="absmiddle" />
        <b>Geokrety</b>
    </p>
</div>
<div class="content2-container">
    <p>
        {geokrety_content}
    </p>
</div>
{geokrety_end}
{hidepictures_start}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="" align="absmiddle" />
        <b>{{images}}</b>
    </p>
</div>
<div class="content2-container">
    <div id="viewcache-pictures">
        <div id="hideshow">
            {pictures}
        </div>
    </div>
</div>
{hidepictures_end}

<!-- Text container -->
{hidelogbook_start}
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
{hidelogbook_end}
<!-- End Text Container -->
