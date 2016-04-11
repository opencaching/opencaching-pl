<link rel="stylesheet" href="tpl/stdstyle/js/lightbox2/dist/css/lightbox.min.css">
<script src="tpl/stdstyle/js/lightbox2/dist/js/lightbox-plus-jquery.min.js"></script>
{body_scripts}

{jQueryPopUpWindowscripts}

<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<script src="{viewcache_js}"></script>

<input type="hidden" id="cacheid" value="{cacheid}" />
<input type="hidden" id="logEnteriesCount" value="{logEnteriesCount}" />
<input type="hidden" id="owner_id" value="{owner_id}" />
<input type="hidden" id="includeDeletedLogs" value="{includeDeletedLogs}" />
<input type="hidden" id="uType" value="{uType}" />

<div id="dialog-message" title="{{GKApi19}}" style="display: {GeoKretyApi_window_display};">
    <p>
        <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
        {GeoKretyApi_windowContent}
    </p><hr>
    <p style="font-size: 8px">
        {{GKApi27}}
    </p>
</div>
<div class="content2-container line-box">
    <div class="">
        <div class="nav4">
            <?php
            if ($usr == false) {
                echo '<span class="notlogged-cacheview">' . tr('cache_logged_required') . '</span>';
            }
            // cachelisting
            $clidx = mnu_MainMenuIndexFromPageId($menu, "cachelisting");
            if ($menu[$clidx]['title'] != '') {
                echo '<ul id="cachemenu">';
                $menu[$clidx]['visible'] = false;
                echo '<li class="title" ';
                echo '>' . $menu[$clidx]["title"] . '</li>';
                mnu_EchoSubMenu($menu[$clidx]['submenu'], $tplname, 1, false);
                echo '</ul>';
            }
            //end cachelisting
            ?>
        </div>
        <div class="content2-container-2col-left" style="width:60px; clear: left;">
            <div><img src="{icon_cache}" class="icon32" id="viewcache-cacheicon" alt="{cachetype}" title="{cachetype}"/></div>
            <div>{difficulty_icon_diff}</div><div>{difficulty_icon_terr}</div>
            <div>{cache_stats}</div>
        </div>
        <div class="content2-container-2col-left" id="cache_name_block">
            <table width="100%"><tr><td valign="top" width="70%">
                        <span class="content-title-noshade-size5">{cachename} - {oc_waypoint}&nbsp;&nbsp;&nbsp;&nbsp;{icon_titled}</span><br />
                        <p class="content-title-noshade-size1">{short_desc}</p>
                        <p>{{owner}}&nbsp; <a class="links" href="viewprofile.php?userid={userid_urlencode}">{owner_name}</a>
                            {creator_name_start}<br/>{{creator}}&nbsp; <a class="links" href="viewprofile.php?userid={creator_userid}">{creator_name}</a>{creator_name_end}</p>
                        {event_attendance_list}
                    </td><td>
                        <div align="center" style="
                             border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;
                             border:1px solid #337fed; padding: 5px;
                             display: {ptSectionDisplay};
                             "><div width="100%" style="color: #337fed; border-bottom: 1px solid #337fed;">{{pt094}}!</div>
                            {ptName}
                        </div>
                    </td>
                </tr></table>
        </div>
    </div>
</div>


<!-- End Text Container -->
<!-- Text container -->
<div class="content2-container">
    <div class="content2-container-2col-left" id="viewcache-baseinfo">
        <p class="content-title-noshade-size3">
            <img src="tpl/stdstyle/images/blue/kompas.png" class="icon32" alt="" title="" />
            <b>{coords}</b> <span class="content-title-noshade-size0">(WGS84){mod_cord_info}</span><br />
        </p>
        <p style="line-height: 1.6em;">
            <img src="tpl/stdstyle/images/free_icons/mountain.png" class="icon16" width=16 height=16 alt="" title="" align="middle" />&nbsp;{{cache_alt}}: {altitude} {{abovesealevel}}<br />
            <img src="tpl/stdstyle/images/free_icons/map.png" class="icon16" alt="" title="" align="middle" />&nbsp;{coords_other} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"><br />
            <img src="tpl/stdstyle/images/free_icons/world.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{region}}:<b><span style="color: rgb(88,144,168)"> {kraj} {dziubek1} {woj} {dziubek2} {miasto}</span></b><br />
            {distance_cache}
            <img src="tpl/stdstyle/images/free_icons/box.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{cache_type}}: <b>{cachetype}</b><br />
            <img src="tpl/stdstyle/images/free_icons/package_green.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{size}}: <b>{cachesize}</b><br />
            <img src="tpl/stdstyle/images/free_icons/page.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{status_label}}: {status}<br />
            {hidetime_start}<img src="tpl/stdstyle/images/free_icons/time.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{time}}: {search_time}&nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/arrow_switch.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{length}}: {way_length}<br />{hidetime_end}
            <?php
            if (tpl_get_var('typeLetter') == 'e') {
                echo '<img src="tpl/stdstyle/images/cache/16x16-event.png" class="icon16" alt="" title="" align="middle" />&nbsp;';
                echo tr('date_event_label') . ': <strong>' . tpl_get_var('hidden_date') . '</strong>';
            } else {
                echo '<img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;';
                echo tr('date_hidden_label') . ': ' . tpl_get_var('hidden_date');
            }
            ?><br />
            <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{date_created_label}}: {date_created}<br />
            <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{last_modified_label}}: {last_modified}<br />
            <img src="tpl/stdstyle/images/free_icons/arrow_in.png" class="icon16" alt="" title="" align="middle" />&nbsp;Waypoint: <b><a href="{absolute_server_URI}{oc_waypoint}">{oc_waypoint}</a></b><br />
            {hidelistingsites_start}<img src="tpl/stdstyle/images/free_icons/link.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{listed_also_on}}: {listed_on} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"><br />{hidelistingsites_end}
        </p>
    </div>
    <div class="content2-container-2col-right" id="viewcache-maptypes">
        <?php
        if ($usr == false && $hide_coords) {
            ?>
            {map_msg}
            <?php
        } else {
            ?>
            <div class="content2-container-2col-left" id="viewcache-numstats">
                <p style="line-height: 1.4em;"><br />
                    {found_icon} {founds} {found_text}<br />
                    {hidemobile_start}{moved_icon} {moved} x {{moved_text}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{dystans}&nbsp;km<br/>{hidemobile_end}
                    {notfound_icon} {notfounds} {notfound_text}<br />
                    {note_icon} {notes} {{log_notes}}<br />
                    {watch_icon} {watcher} {{watchers}}<br />
                    {visit_icon} {visits} {{visitors}}<br />
                    {vote_icon} {votes_count} x {{scored}}<br />
                    {score_icon} {{score_label}}: <b><font color="{scorecolor}">{score}</font></b><br />
                    {list_of_rating_begin}{rating_stat}{list_of_rating_end}
                    {gk_icon} <a class="links" href="//geokrety.org/szukaj.php?wpt={oc_waypoint}" target="_blank">{{history_gk}}  <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a><br />
                </p>
            </div>
            <div id="viewcache-map" class="content2-container-2col-right"><div class="img-shadow">
                    <?php
                        printf(
                        '<img src="//maps.googleapis.com/maps/api/staticmap?center=%1$f,%2$f&amp;zoom=%6$s&amp;size=170x170&amp;maptype=%5$s&amp;key=%4$s&amp;sensor=false&amp;markers=color:%7$s%%7Clabel:%%7C%1$f,%2$f&amp;format=png"
                         longdesc="ifr::cachemap-mini.php?inputZoom=%8$s&amp;lat=%1$f&amp;lon=%2$f&amp;cacheid=%3$s::%9$s::%10$s"
                         onclick="enlarge(this);" alt="%11$s" title="%11$s" />',
                            tpl_get_var('latitude'),
                            tpl_get_var('longitude'),
                            tpl_get_var('cacheid'),
                            tpl_get_var('googlemap_key'),
                            $config['maps']['cache_page_map']['layer'],
                            $config['maps']['cache_page_map']['zoom'],
                            $config['maps']['cache_page_map']['marker_color'],
                            $config['maps']['cache_mini_map']['zoom'],
                            $config['maps']['cache_mini_map']['width'],
                            $config['maps']['cache_mini_map']['height'],
                            tr('map')
                        );
                    ?>     
                </div>
            </div>
            <?php
        }
        if ($usr == false && $hide_coords) {
            echo "";
        } else {
            echo "<b>{{available_maps}}:</b>&nbsp;\n";
            
            foreach($config['maps']['external'] as $key => $value){
                if ( $value == 1 ) {
                    printf($config['maps']['external'][$key.'_URL'],tpl_get_var('latitude'),tpl_get_var('longitude'),tpl_get_var('cacheid'),tpl_get_var('oc_waypoint'),urlencode($vars['cachename']),$key);
                    echo "&nbsp;\n";
                }    
            } 
        }
        ?>
    </div>
</div>
<!-- End Text Container -->

<!-- Text container -->
{cache_attributes_start}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/attributes.png" class="icon32" alt="" />
        {{cache_attributes_label}}
    </p>
</div>
<div class="content2-container">
    <p>
        {cache_attributes}{password_req}
    </p>
</div>
<div class="notice" id="viewcache-attributesend">{{attributes_desc_hint}}  <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></div>
{cache_attributes_end}
<!-- End Text Container -->
<!-- Text container -->
{start_rr_comment}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">

        <img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="" />
        {{rr_comment_label}}
    </p>
</div>
<div class="content2-container">
    <p><br/>
        {rr_comment}
    </p>
</div>
{end_rr_comment}
<!-- End Text Container -->
<!-- Text container -->
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="" />
        {{descriptions}}&nbsp;&nbsp;
        {desc_langs}&nbsp;{add_rr_comment}&nbsp;{remove_rr_comment}
    </p></div>
<div class="content2-container">
    <div id="description">
        <div id="viewcache-description">
            {desc}
        </div>
    </div>
</div>
<!-- End Text Container -->
<!-- Text container -->

<!-- sekcja opensprawdzacza -->
{opensprawdzacz_start}

<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/opensprawdzacz32x32.png" class="icon32" alt="" />
        {{Open_Sprawdzacz}}
    </p></div>
<p>
    {{opensprawdzacz_main}}<br/><br/>
    <a href="opensprawdzacz.php?op_keszynki={oc_waypoint}">{{os_sprawdz}}</a><br/><br/>
</p>
<p>{{statistics}}:
    {{os_pr}}: {proby} {{os_times}}, {{os_sukc}}: {sukcesy} {{os_times}}.
    {opensprawdzacz_end}
    <!-- koniec sekcji opensprawdzacza -->

    {waypoints_start}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="" />
        {{additional_waypoints}}
    </p>
</div>
<p>
    {waypoints_content}
</p>
<br />
<div class="notice" id="viewcache-attributesend"><a class="links" href="{wiki_link_additionalWaypoints}" target="_blank">{{show_info_about_wp}} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a></div>
{waypoints_end}
<!-- End Text Container -->
<!-- Text container -->
{hidehint_start}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="" />
        <b>{{additional_hints}}</b>&nbsp;&nbsp; <span id="decrypt-info"> {decrypt_link_start}
            {decrypt_icon}

            {decrypt_link}
            {decrypt_link_end} </span>
        <br/>

    </p>
</div>
<div class="content2-container">
    <p id="decrypt-hints">
        {hints}
    </p>
    <p id="hintEncrypted" style="display: none">
        {hintEncrypted}
    </p>

    <div style="width:200px;text-align:right;float:right">
        {decrypt_table_start}
        {decrypt_table}

        {decrypt_table_end}
    </div>
</div>

{hidehint_end}
<!-- End Text Container -->

<!-- sekcja modyfikatora współrzędnych -->
{coordsmod_start}
<div  class="content2-container bg-blue02">
    <a id="coords_mod">
        <p class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/signature1.png" class="icon32" alt="" /></a>
    {{coords_modifier}}
</p></div>
<p>
    {{coordsmod_main}}<br/>
<form action="viewcache.php?cacheid={cacheid}" method="post" name="form_coords_mod">
    <fieldset style="border: 1px solid black; width: 200px; background-color: #FAFBDF; margin-left: 50px;">
        <legend>
            &nbsp; <strong>WGS-84</strong> &nbsp;
        </legend>
        &nbsp;&nbsp;&nbsp;
        <select name="coordmod_latNS" class="input40">
            <option value="N" {N_selected}>N</option>
            <option value="S" {S_selected}>S</option>
        </select>
        &nbsp;
        <input name="coordmod_lat_degree" type="text" maxlength="2" class="input30" value="{coordmod_lat_h}" />
        &deg;&nbsp;
        <input type="text" name="coordmod_lat" value="{coordmod_lat}" maxlength="6" class="input50" />
        &nbsp;'&nbsp;
        <br />
        &nbsp;&nbsp;&nbsp;
        <select name="coordmod_lonEW" class="input40">
            <option value="E" {E_selected}>E</option>
            <option value="W" {W_selected}>W</option>
        </select>
        &nbsp;
        <input name="coordmod_lon_degree" type="text" maxlength="3" value="{coordmod_lon_h}" class="input30"/>
        &deg;&nbsp;
        <input type="text" name="coordmod_lon" maxlength="6" value="{coordmod_lon}" class="input50" />
        &nbsp;'&nbsp;
    </fieldset>
    {coords_message}
</p>
<p>
    <input type="submit" name="modCoords" value="{{modify_coords}}" />
    <input type="submit" name="resetCoords" value="{{reset_coords}}" />
</p>
</form>
<div class="notice buffer" id="viewcache-mod_coord">
    {{modified_coord_notice}}
</div>
{coordsmod_end}
<!-- koniec sekcji modyfikatora współrzędnych -->


{EditCacheNoteS}
<div class="content2-container bg-blue02">
    <p class="content-title-noshade-size2">
        <img src="tpl/stdstyle/images/blue/logs.png" style="align: left; margin-right: 10px;" alt="{{personal_cache_note}}" /> {{personal_cache_note}}
    </p>
</div>

<div class="content2-container">
    <form action="viewcache.php" method="post" name="cache_note">
        <input type="hidden" name="cacheid" value="{cacheid}" />

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
                    <button type="submit" name="save" value="save" style="width:100px">{{save}}</button>&nbsp;&nbsp;
                    <img src="tpl/stdstyle/images/misc/16x16-info.png" class="icon16" alt="Info" />
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
        <img src="tpl/stdstyle/images/blue/logs.png" style="align: left; margin-right: 10px;" alt="{{personal_cache_note}}" />
        {{personal_cache_note}}
    </p>
</div>

<div class="content2-container">
    <form action="viewcache.php?cacheid={cacheid}#cache_note1" method="post" name="cache_note">
        <input type="hidden" name="cacheid" value="{cacheid}" />

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
                    <button type="submit" name="edit" value="edit" style="width:100px">{{Edit}}</button>&nbsp;&nbsp;
                    <button type="submit" name="remove" value="remove" style="width:100px">{{delete}}</button>&nbsp;&nbsp;
                    <img src="tpl/stdstyle/images/misc/16x16-info.png" class="icon16" alt="Info" />
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

        <img src="tpl/stdstyle/images/blue/npav1.png" class="icon32" alt="" />
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
        <img src="tpl/stdstyle/images/blue/travelbug.png" class="icon32" alt="" />
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
        <img src="tpl/stdstyle/images/blue/podcache-mp3.png" class="icon32" alt="" />
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
        <img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="" />
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
        <img src="tpl/stdstyle/images/blue/tools.png" class="icon32" alt="" />&nbsp;{{utilities}}
    </p>
</div>
<div class="content2-container">
    <div id="viewcache-utility">
        <div>
            {search_icon} {{search_geocaches_nearby}}
            <?php echo ":
            <a href=\"search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;latNS="; ?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h="; ?>{lon_h}<?php echo "&amp;lon_min="; ?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">"; ?>{{all_geocaches}}<?php echo "</a>&nbsp;
            <a href=\"search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=1&amp;f_userfound=1&amp;f_inactive=1&amp;latNS="; ?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h="; ?>{lon_h}<?php echo "&amp;lon_min="; ?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">"; ?>{{searchable}}<?php echo "</a><br />";
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
        </div><hr style="color: blue;"/>
        <?php
        if ($usr == false && $hide_coords) { // hide downloading gpx etc if user is not logged
            echo "";
        } else {
            ?>
        <div>{save_icon}<b> {{download_as_file}}</b><br/>
            <table class="content" style="font-size: 12px; line-height: 1.6em;">
                <tr>
                    <td  width="350" align="left" style="padding-left:5px;">
                        <div class="searchdiv">
                            <span class="content-title-noshade txt-blue08">{{format_GPX}}</span>:<br/>
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=gpxgc" title="GPS Exchange Format .gpx">GPX</a>&nbsp|&nbsp
                            <a class="links" href="search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid={cacheid_urlencode}&amp;output=zip" title="Garmin ZIP file ({{format_pict}})  .zip">GARMIN ({{format_pict}})</a>
                        </div>
                    </td>
                    <td width="350" align="left" style="padding-left:5px;">
                        <div class="searchdiv">
                            <span class="content-title-noshade txt-blue08">{{format_other}}</span>:<br/>
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
                            <span class="content-title-noshade txt-blue08">{{send_to}}:</span><br/>
                            <a class="links" href="#" onclick="openCgeoWindow(event, '{oc_waypoint}')" title="c:geo">{{send_to_cgeo}}</a> |
                            <a class="links" href="#" onclick="openGarminWindow(event, '{latitude}','{longitude}',
                            '{oc_waypoint}','{cachename}')" title="{{send_to_gps}}">{{send_to_gps}}</a>
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
            <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt=""/>
            {{log_entries}}
            &nbsp;&nbsp;
            {found_icon} {founds}x
            {notfound_icon} {notfounds}x
            {note_icon} {notes}x
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
