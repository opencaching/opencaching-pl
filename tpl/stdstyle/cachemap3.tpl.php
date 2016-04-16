<script type='text/javascript'>

    //On touch-screen devices use full-screen map by default
    //Check for touch device below should be kept in sync with analogous check in lib/cachemap3.js

    if (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0)){
        //check cookie to allow user to come back to non-full screen mode
        if( document.cookie.indexOf("forceFullScreenMap=off") == -1){
            //touch device + cookie not set => redirect to full screen map
            window.location = 'cachemap-full.php'+window.location.search;
        }
    }
</script>
<div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/world.png" class="icon32" style='margin: 0 4px 3px 6px'/>
    {{user_map}} <b style='color: #000'>{username}</b>
</div>

<style>
    #shortcut_icons { position: relative }
    #shortcut_icons img { position: absolute; top: -23px; cursor: pointer; }
    .opt_table input { border: 0; }
    .opt_table { background: #eee; border: 1px solid #ccc; }
    .opt_table th { background: #888; padding: 3px 8px 5px 8px; font-family: Tahoma; font-size: 13px; font-weight: bold; color: #fff; }
    .opt_table td { padding: 6px; font-family: Tahoma; font-size: 13px; vertical-align: top; }
    .opt_table select { padding: 1px; font-family: Tahoma; font-size: 13px; border: 1px solid #888; }
    .opt_table td.i { position: relative; width: 35px; display: block; }
    .opt_table td.i img { position: absolute; top: 0; }
    .opt_table .dim { color: #888; }
    .opt_table .dim img { opacity: .3; }
    img.dim { opacity: .3; }
</style>

<div style='margin: 0px auto; width:98%'>
    <div id='shortcut_icons'></div>
    <table style='border: 1px solid #ccc; background: #eee; padding: 3px 6px 3px 8px; width: 100%; margin-bottom: 5px;'>
        <tr>
            <td>
                <div id="ext_search"><div id="search_control" style="float: left;">
                        <table cellspacing="0" cellpadding="0"><tr>
                                <td><input id="place_search_text" class="gsc-input" type="text" size="10"></td>
                                <td><input id="place_search_button" class="gsc-search-button" value="{{search}}" type="button"></td>
                            </tr></table>
                    </div></div>
            </td>
            <td>
                <table style='float: right;'><tr>
                        <td style='font-size: 13px;'>
                            {{current_zoom}}:
                            <input type="text" id="zoom" size="2" value="{zoom}" disabled="disabled" style='border: 0; font-weight: bold; font-size: 13px; background: transparent'/>
                        </td>
                        <!-- onclick="fullscreen_on();"  -->
                        <td><a id="fullscreen_on" style='cursor: pointer'><img src="images/fullscreen.png" title="{{fullscreen}}"/></a></td>
                        <td><a id="refresh_button" style='cursor: pointer'><img src="images/refresh.png" title="{{refresh_map}}"/></a></td>
                    </tr></table>
            </td>
        </tr>
    </table>
</div>

<div id="map_canvas" style="width: 97.5%; margin: 0px auto; height: 512px; border: 1px solid #888;"></div>

<div id="map_filters" style='margin: 10px 0px'>

    <?php //disable this part of template if pwoerTrail filter is not supported
        if( {pt_filter_enabled} ) {
    ?>
    <table id='powertrail_filter' class='opt_table' cellspacing="0" style='clear: both; margin:5px auto; width:98%'>
        <tr>
            <th colspan='2'>{{pt001}}</th>
        </tr>
        <tr>
            <td>
                <input class="chbox" id="pt_selection" name="pt_selection" value="1"
                        type="checkbox" checked="checked" onclick="reload()" />&nbsp;
                <label for="pt_selection" style='display:inline-block; vertical-align:sub; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px;'>
                    <a  href='{pt_url}' title='{pt_name}' target='_blank' style="text-decoration: none">
                        <img width="20" height="20" src="{pt_icon}" alt="{{pt001}}" title='{pt_name}' />
                        {pt_name}
                    </a>
                </label>
            </td>
        </tr>
    </table>
    <?php } //if-pt_filter_enabled ?>



    <table id='cache_types' class='opt_table' cellspacing="0" style='float: left; width:48%; margin:0px 1%'>
        <tr>
            <th colspan='2'>{{hide_caches_type}}:</th>
        </tr>
        <tr>
            <td>
                <table>
                    <tr class='h_t'>
                        <td><input class="chbox" id="h_t" name="h_t" value="1" type="checkbox" {h_t_checked} onclick="reload()"/>&nbsp;<label for="h_t">{{traditional}}</label></td>
                        <td class='i'><img src='okapi/static/tilemap/legend_traditional.png'/></td>
                    </tr>
                    <tr class='h_m'>
                        <td><input class="chbox" id="h_m" name="h_m" value="1" type="checkbox" {h_m_checked} onclick="reload()"/><label for="h_m">&nbsp;{{multicache}}</label></td>
                        <td class='i'><img src='okapi/static/tilemap/legend_multi.png'/></td>
                    </tr>
                    <tr class='h_q'>
                        <td><input class="chbox" id="h_q" name="h_q" value="1" type="checkbox" {h_q_checked} onclick="reload()"/><label for="h_q">&nbsp;{{quiz}}</label></td>
                        <td class='i'><img src='okapi/static/tilemap/legend_quiz.png'/></td>
                    </tr>
                    <tr class='h_v'>
                        <td><input class="chbox" id="h_v" name="h_v" value="1" type="checkbox" {h_v_checked} onclick="reload()"/><label for="h_v">&nbsp;{{virtual}}</label></td>
                        <td class='i'><img src='okapi/static/tilemap/legend_virtual.png'/></td>
                    </tr>
                    <tr class='h_e'>
                        <td><input class="chbox" id="h_e" name="h_e" value="1" type="checkbox" {h_e_checked} onclick="reload()"/><label for="h_e">&nbsp;{{event}}</label></td>
                        <td class='i'><img src='okapi/static/tilemap/legend_event.png'/></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr class='h_u'>
                        <td><input class="chbox" id="h_u" name="h_u" value="1" type="checkbox" {h_u_checked} onclick="reload()"/><label for="h_u">&nbsp;{{unknown_type}}</label></td>
                        <td class='i'><img src='okapi/static/tilemap/legend_unknown.png'/></td>
                    </tr>
                    <tr class='h_w'>
                        <td><input class="chbox" id="h_w" name="h_w" value="1" type="checkbox" {h_w_checked} onclick="reload()"/><label for="h_w">&nbsp;Webcam</label></td>
                        <td class='i'><img src='okapi/static/tilemap/legend_webcam.png'/></td>
                    </tr>
                    <tr class='h_o'>
                        <td><input class="chbox" id="h_o" name="h_o" value="1" type="checkbox" {h_o_checked} onclick="reload()"/><label for="h_o">&nbsp;{{moving}}</label></td>
                        <td class='i'><img src='okapi/static/tilemap/legend_moving.png'/></td>
                    </tr>
                    <tr class='h_owncache'>
                        <td><input class="chbox" id="h_owncache" name="h_owncache" value="1" type="checkbox" {h_owncache_checked} onclick="reload()"/><label for="h_owncache">&nbsp;{{owncache}}</label></td>
                        <td class='i'><img src='okapi/static/tilemap/legend_own.png'/></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table id='other_options' class='opt_table' cellspacing="0" style='float: right; width:48%; margin:0px 1%'>
        <tr>
            <th colspan='2'>{{hide_caches}}:</th>
        </tr>
        <tr>
            <td>
                <div class='h_ignored'>
                    <input class="chbox" id="h_ignored" name="h_ignored" value="1" type="checkbox" {h_ignored_checked} onclick="reload()"/><label for="h_ignored">&nbsp;{{ignored}}</label>
                </div>
                <div class='h_own'>
                    <input class="chbox" id="h_own" name="h_own" value="1" type="checkbox" {h_own_checked} onclick="reload()"/><label for="h_own">&nbsp;{{own}}</label>
                </div>
                <div class='h_found'>
                    <input class="chbox" id="h_found" name="h_found" value="1" type="checkbox" {h_found_checked} onclick="reload()"/><label for="h_found">&nbsp;{{founds}}</label>
                </div>
                <div class='h_noattempt'>
                    <input class="chbox" id="h_noattempt" name="h_noattempt" value="1" type="checkbox" {h_noattempt_checked} onclick="reload()"/><label for="h_noattempt">&nbsp;{{not_yet_found}}</label>
                </div>
                <div class='h_nogeokret'>
                    <input class="chbox" id="h_nogeokret" name="h_nogeokret" value="1" type="checkbox" {h_nogeokret_checked} onclick="reload()"/><label for="h_nogeokret">&nbsp;{{without_geokret}}</label>
                </div>
            </td>
            <td>
                <div class='h_temp_unavail'>
                    <input class="chbox" id="h_temp_unavail" name="h_temp_unavail" value="1" type="checkbox" {h_temp_unavail_checked} onclick="reload()"/><label for="h_temp_unavail">&nbsp;{{temp_unavailables}}</label>
                </div>
                <div class='h_arch'>
                    <input class="chbox" id="h_arch" name="h_arch" value="1" type="checkbox" {h_arch_checked} onclick="reload()"/><label for="h_arch">&nbsp;{{archived_plural}}</label>
                </div>
                <hr>
                <div>
                    <input class="chbox" id="be_ftf" name="be_ftf" value="1" type="checkbox" {be_ftf_checked} onclick="reload(); check_field()"/><label for="be_ftf">&nbsp;{{map_01}}</label>
                </div>

                <div style="{powerTrails_display}">
                    <input class="chbox" id="powertrail_only" name="powertrail_only" value="1" type="checkbox" {powertrail_only_checked} onclick="reload()"/><label for="powertrail_only">&nbsp;{{map_05}}</label>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <div>
                    <center>
                        {{map_02}}
                        <select id="min_score" name="min_score" onchange="reload()">
                            <option value="-3" {min_sel1}>{{map_03}}</option>
                            <!--<option value="0.5" {min_sel2}>pomiń najsłabsze skrzynki</option>-->
                            <option value="1.2" {min_sel3}>{{rating_ge_average}}</option>
                            <option value="2" {min_sel4}>{{rating_ge_good}}</option>
                            <option value="2.5" {min_sel5}>{{rating_ge_excellent}}</option>
                        </select>
                    </center>
                </div>
                <div style='margin-top: 5px'>
                    <center>
                        <input class="chbox" id="h_noscore" name="h_noscore" value="1" type="checkbox" {h_noscore_checked} onclick="reload()"/><label for="h_noscore">&nbsp;{{map_04}}</label>
                    </center>
                 </div>
            </td>
        </tr>
    </table>
    <div style='clear: both'></div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src="{lib_cachemap3_js}" type="text/javascript"></script>
<script type="text/javascript" language="javascript">

var map_params = { //params for cachemaps3.js
    cachemap_mapper: "{cachemap_mapper}",
    userid: {userid},
    coords: [{coords}],
    zoom: {zoom},
    map_type: {map_type},
    circle: {circle},
    doopen: {doopen},
    fromlat: {fromlat}, fromlon: {fromlon},
    tolat: {tolat}, tolon: {tolon},
    searchdata: "{searchdata}",
    boundsurl: "{boundsurl}",
    extrauserid: "{extrauserid}",
    fullscreen: false,
    savesettings: true,
    powertrail_ids: "{powertrail_ids}",
    mapCanvasId: 'map_canvas',
    reload_func: 'reload', //function name to reload oc map
    mapTypeControl: {
        pos: google.maps.ControlPosition.TOP_RIGHT,
        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
    },
    customControls: {
        fullscreenButton: {
            id: "fullscreen_on"
        },
        refreshButton: {
            id: "refresh_button"
        },
        search: {
            input_id: "place_search_text",
            but_id: "place_search_button"
        },
        zoom_display: {
            id: "zoom"
        },
        coordsUnderCursor: {
            pos: google.maps.ControlPosition.TOP_LEFT
        },
        ocFilters:{
            boxId: "map_filters"
        }
    }
};

window.onload = function() {
    loadOcMap( map_params );
}

</script>
