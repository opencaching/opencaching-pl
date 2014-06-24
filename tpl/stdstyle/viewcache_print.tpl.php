<?php

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/
?>
<script language=javascript type='text/javascript'>
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
                    <img src="{icon_cache}" class="icon32" id="viewcache-cacheicon" alt="{cachetype}" title="{cachetype}"/>{mod_suffix}{cachename}
                    <img src="tpl/stdstyle/images/free_icons/arrow_in.png" class="icon16" alt="" title="" align="middle" />&nbsp;<b>{oc_waypoint}
                    <img src="tpl/stdstyle/images/blue/kompas.png" class="icon16" alt="" title="" />{coords}</b><br/>

                {difficulty_icon_diff} {difficulty_icon_terr} {short_desc} {{hidden_by}} <a href="viewprofile.php?userid={userid_urlencode}">{owner_name}</a>

                <img src="tpl/stdstyle/images/free_icons/package.png" class="icon16" alt="" title="" align="middle" />&nbsp;<b>{cachesize}</b>
                {hidetime_start}<img src="tpl/stdstyle/images/free_icons/time.png" class="icon16" alt="" title="" align="middle" />&nbsp; {search_time}&nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/arrow_switch.png" class="icon16" alt="" title="" align="middle" />&nbsp; {way_length} {hidetime_end}
                {score_icon}<b><font color="{scorecolor}">{score}</font></b>
                {hidelistingsites_start}<br /><img src="tpl/stdstyle/images/free_icons/link.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{listed_also_on}}: {listed_on}{hidelistingsites_end}

            </div>

        </div>


<?php
global $usr, $lang, $hide_coords;
?>
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">
                    <img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="" />
                    {{descriptions}}&nbsp;<br/>{cache_attributes}{password_req}
                </p></div>

        <div class="content2-container">

                <div id='branding'>{branding}</div>

                <div id="description">

                <div id="viewcache-description">

                    {desc}

                </div>
                </div>
            </div>
{hidehint_start}
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">
                    <img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="" />
                    <b>{{additional_hints}}</b>&nbsp;&nbsp;
                </p>
            </div>
                    <div class="content2-container">
                        <div id="viewcache-hints">
                            {hints}
                        </div>

                    <div style="width:200px;align:right;float:right">
                        {decrypt_table_start}
                        <font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">A|B|C|D|E|F|G|H|I|J|K|L|M</font><br/>
                        <font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>
                        {decrypt_table_end}
                    </div>
                </div>
{hidehint_end}
{CacheNoteS}
    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size2">
            <img src="tpl/stdstyle/images/blue/logs.png" style="align: left; margin-right: 10px;" alt="{{personal_cache_note}}" />
            {{personal_cache_note}}
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
{waypoints_start}
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">
                    <img src="tpl/stdstyle/images/blue/compas.png" alt="" />
                    {{additional_waypoints}}
                </p></div>
                <p>
                    {waypoints_content}
                </p><br/>
{waypoints_end}
{hidenpa_start}
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">

                    <img src="tpl/stdstyle/images/blue/npav1.png" class="icon32" alt="" />
                    NATURA 2000 obszar
                </p>
                </div>
                <div class="content2-container"><center>
<table width="90%" border="0" style="border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em">
<tr>
<td align="center" valign="middle">&nbsp;</td><td align="center" valign="middle">{npa_content}</td><td align="center" valign="middle"><a class="links" href="http://www.natura2000.pl/" target="_blank"><img src="tpl/stdstyle/images/misc/natura2000.png" alt="" title="" /></a></td>
</tr>
</table></center>
            </div>
{hidenpa_end}

<!-- sekcja modyfikatora współrzędnych -->
{coordsmod_start}
<div  class="content2-container bg-blue02">
<p class="content-title-noshade-size1">
<img src="tpl/stdstyle/images/blue/signature1.png" class="icon32" alt="" />
{{coords_modifier}}
</p>
</div>
<div class="content2-container">
<p>
{{srch_Coord_have_been_modified}}:<BR/>
    {coordmod_lat_h} {coordmod_lat}<BR/>
    {coordmod_lon_h} {coordmod_lon}<BR/>
</p>
</div>
{coordsmod_end}
<!-- koniec sekcji modyfikatora współrzędnych -->

{geokrety_begin}
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">
                    <img src="tpl/stdstyle/images/blue/travelbug.png" class="icon32" alt="" />
                    Geokrety
                </p></div>
                <div class="content2-container">
                <p>
                    {geokrety_content}
                </p>
            </div>
{geokrety_end}
{hidepictures_start}
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">
                    <img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="" />
                    {{images}}
                </p></div>
                <div class="content2-container">
                <div id="viewcache-pictures">
                <div id="hideshow">
                    {pictures}
                    </div>
                </div>
            </div>
{hidepictures_end}


<!-- Text container -->
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">
                    <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt=""/>
                    {{log_entries}}
                    &nbsp;&nbsp;
                    {found_icon} {founds}x
                    {notfound_icon} {notfounds}x
                    {note_icon} {notes}x
                </p>
            </div>
            <div class="content2-container" id="viewcache-logs">
                    {logs}
            </div>
<!-- End Text Container -->

