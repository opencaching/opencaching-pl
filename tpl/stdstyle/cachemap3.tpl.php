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

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="">
    {{user_map}} {username}
  </div>

  <div class="buffer"></div>

  <table class="cachemap3_header full-width">
    <tr>
      <td style="width: 50%">
        <div id="ext_search">
          <div id="search_control" style="float: left;">
            <div class="form-group">
              <input id="place_search_text" class="form-control input200" type="text" size="10">
              <input id="place_search_button" class="btn btn-default" value="{{search}}" type="button">
            </div>
          </div>
        </div>
      </td>
      <td class="align-right">
        {{current_zoom}}:
        <input type="text" id="zoom" size="2" value="{zoom}" disabled="disabled">
      </td>
      <td class="align-right cachemap3_header_icon">
        <!-- onclick="fullscreen_on();"  -->
        <a id="fullscreen_on" style="cursor: pointer"><img src="images/fullscreen.png" title="{{fullscreen}}" alt="{{fullscreen}}"></a>
      </td>
      <td class="align-right cachemap3_header_icon">
        <a id="refresh_button" style="cursor: pointer"><img src="images/refresh.png" title="{{refresh_map}}" alt="{{refresh_map}}"></a>
      </td>
    </tr>
  </table>

<div id="map_canvas"></div>

<div id="map_filters">

  <?php //disable this part of template if pwoerTrail filter is not supported
      if( {pt_filter_enabled} ) {
  ?>
  <table id="powertrail_filter" class="cachemap3_pt full-width">
    <tr>
      <th colspan='2'>{{gp_mainTitile}}</th>
    </tr>
    <tr>
      <td>
        <input class="chbox" id="pt_selection" name="pt_selection" value="1"  type="checkbox" checked="checked" onclick="reload()" />&nbsp;
        <label for="pt_selection" style="display:inline-block; vertical-align:sub; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px;">
          <a href="{pt_url}" title="{pt_name}" target="_blank" class="links">
          <img width="20" height="20" src="{pt_icon}" alt="{pt_name}" title="{pt_name}"> {pt_name}</a>
        </label>
      </td>
    </tr>
  </table>
  <?php } //if-pt_filter_enabled ?>

  <table id="cache_types" class="cachemap3_filters" style="float: left;">
    <tr>
      <th colspan='2'>{{hide_caches_type}}</th>
    </tr>
    <tr>
      <td class="h_t">
        <input class="chbox" id="h_t" name="h_t" value="1" type="checkbox" {h_t_checked} onclick="reload()">
        <label for="h_t"><img src="okapi/static/tilemap/legend_traditional.png" alt="{{traditional}}"> {{traditional}}</label>
      </td>
      <td class="h_u">
        <input class="chbox" id="h_u" name="h_u" value="1" type="checkbox" {h_u_checked} onclick="reload()">
        <label for="h_u"><img src="okapi/static/tilemap/legend_unknown.png" alt="{{unknown_type}}"> {{unknown_type}}</label>
     </td>
    </tr>
    <tr>
      <td class="h_m">
        <input class="chbox" id="h_m" name="h_m" value="1" type="checkbox" {h_m_checked} onclick="reload()">
          <label for="h_m"><img src="okapi/static/tilemap/legend_multi.png" alt="{{multicache}}"> {{multicache}}</label>
      </td>
      <td class="h_w">
        <input class="chbox" id="h_w" name="h_w" value="1" type="checkbox" {h_w_checked} onclick="reload()">
        <label for="h_w"><img src="okapi/static/tilemap/legend_webcam.png" alt="{{webcam}}"> {{webcam}}</label>
      </td>
    </tr>
    <tr>
      <td class="h_q">
        <input class="chbox" id="h_q" name="h_q" value="1" type="checkbox" {h_q_checked} onclick="reload()">
        <label for="h_q"><img src="okapi/static/tilemap/legend_quiz.png" alt="{{quiz}}"> {{quiz}}</label>
      </td>
      <td class="h_o">
        <input class="chbox" id="h_o" name="h_o" value="1" type="checkbox" {h_o_checked} onclick="reload()">
        <label for="h_o"><img src="okapi/static/tilemap/legend_moving.png" alt="{{moving}}"> {{moving}}</label>
      </td>
    </tr>
    <tr>
      <td class="h_v">
        <input class="chbox" id="h_v" name="h_v" value="1" type="checkbox" {h_v_checked} onclick="reload()">
        <label for="h_v"><img src="okapi/static/tilemap/legend_virtual.png" alt="{{virtual}}"> {{virtual}}</label>
      </td>
      <td class="h_owncache">
        <input class="chbox" id="h_owncache" name="h_owncache" value="1" type="checkbox" {h_owncache_checked} onclick="reload()">
        <label for="h_owncache"><img src="okapi/static/tilemap/legend_own.png" alt="{{owncache}}"> {{owncache}}</label>
      </td>
    </tr>
    <tr>
      <td class="h_e">
        <input class="chbox" id="h_e" name="h_e" value="1" type="checkbox" {h_e_checked} onclick="reload()">
        <label for="h_e"><img src="okapi/static/tilemap/legend_event.png" alt="{{event}}"> {{event}}</label>
      </td>
      <td></td>
    </tr>
  </table>

  <table id="other_options" class="cachemap3_filters" style="float: right;">
    <tr>
      <th colspan="2">{{hide_caches}}</th>
    </tr>
    <tr>
      <td class="h_ignored">
        <input class="chbox" id="h_ignored" name="h_ignored" value="1" type="checkbox" {h_ignored_checked} onclick="reload()">
        <label for="h_ignored">{{ignored}}</label>
      </td>
      <td class="h_temp_unavail">
        <input class="chbox" id="h_temp_unavail" name="h_temp_unavail" value="1" type="checkbox" {h_temp_unavail_checked} onclick="reload()">
        <label for="h_temp_unavail">{{temp_unavailables}}</label>
      </td>
    </tr>
    <tr>
      <td class="h_own">
        <input class="chbox" id="h_own" name="h_own" value="1" type="checkbox" {h_own_checked} onclick="reload()">
        <label for="h_own">{{own}}</label>
      </td>
      <td class="h_arch">
        <input class="chbox" id="h_arch" name="h_arch" value="1" type="checkbox" {h_arch_checked} onclick="reload()">
        <label for="h_arch">{{archived_plural}}</label>
      </td>
    </tr>
    <tr>
      <td class="h_found">
        <input class="chbox" id="h_found" name="h_found" value="1" type="checkbox" {h_found_checked} onclick="reload()">
        <label for="h_found">{{founds}}</label>
      </td>
      <td><hr></td>
    </tr>
    <tr>
      <td class="h_noattempt">
        <input class="chbox" id="h_noattempt" name="h_noattempt" value="1" type="checkbox" {h_noattempt_checked} onclick="reload()">
        <label for="h_noattempt">{{not_yet_found}}</label>
      </td>
      <td>
        <input class="chbox" id="be_ftf" name="be_ftf" value="1" type="checkbox" {be_ftf_checked} onclick="reload(); check_field()">
        <label for="be_ftf">{{map_01}}</label>
      </td>
    </tr>
    <tr>
      <td class="h_nogeokret">
        <input class="chbox" id="h_nogeokret" name="h_nogeokret" value="1" type="checkbox" {h_nogeokret_checked} onclick="reload()">
        <label for="h_nogeokret">{{without_geokret}}</label>
      </td>
      <td style="{powerTrails_display}">
        <input class="chbox" id="powertrail_only" name="powertrail_only" value="1" type="checkbox" {powertrail_only_checked} onclick="reload()">
        <label for="powertrail_only">{{map_05}}</label>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="align-center">
        {{map_02}}
        <select id="min_score" name="min_score" class="form-control input200" onchange="reload()">
          <option value="-3" {min_sel1}>{{map_03}}</option>
          <!--<option value="0.5" {min_sel2}>pomiń najsłabsze skrzynki</option>-->
          <option value="1.2" {min_sel3}>{{rating_ge_average}}</option>
          <option value="2" {min_sel4}>{{rating_ge_good}}</option>
          <option value="2.5" {min_sel5}>{{rating_ge_excellent}}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="align-center">
        <input class="chbox" id="h_noscore" name="h_noscore" value="1" type="checkbox" {h_noscore_checked} onclick="reload()">
        <label for="h_noscore">{{map_04}}</label>
      </td>
    </tr>
  </table>
  </div>
</div>
<script src="{lib_cachemap3_js}" type="text/javascript"></script>
<script type="text/javascript">

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
