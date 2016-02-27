<div id="map_canvas" style="width: 100%; height: 100%; position: absolute; top: 0p; bottom: 0px;">
</div>

<div style='display:none'>
    <input id="h_u" name="h_u" type="checkbox"  />
    <input id="h_t" name="h_t" type="checkbox"  />
    <input id="h_m" name="h_m" type="checkbox"  />
    <input id="h_v" name="h_v" type="checkbox"  />
    <input id="h_w" name="h_w" type="checkbox"  />
    <input id="h_e" name="h_e" type="checkbox"  />
    <input id="h_q" name="h_q" type="checkbox"  />
    <input id="h_o" name="h_o" type="checkbox"  />
    <input id="h_owncache" name="h_owncache" type="checkbox"  />
    <input id="h_ignored" name="h_ignored" type="checkbox"  />
    <input id="h_own" name="h_own" type="checkbox"  />
    <input id="h_found" name="h_found" type="checkbox"  />
    <input id="h_noattempt" name="h_noattempt" type="checkbox"  />
    <input id="h_nogeokret" name="h_nogeokret" type="checkbox"  />
    <input id="h_avail" name="h_avail" type="checkbox"  />
    <input id="h_temp_unavail" name="h_temp_unavail" checked="checked" type="checkbox"  />
    <input id="h_arch" name="h_arch" checked="checked" type="checkbox"  />
    <input id="be_ftf" name="be_ftf" type="checkbox" />
    <input id="powertrail_only" name="powertrail_only" type="checkbox" />
    <input id="min_score" name="min_score" type="hidden" value="-3" />
    <input id="max_score" name="max_score" type="hidden" value="3.0" />
    <input id="h_noscore" name="h_noscore" checked="checked" type="checkbox" />
</div>

<input class="chbox" id="zoom" name="zoom" value="{zoom}" type="hidden" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src="tpl/stdstyle/js/jquery.cookie.js"></script>
<script src="{lib_cachemap3_js}" type="text/javascript"></script>
<script type="text/javascript" language="javascript">

var map_params = { //params for cachemaps3.js
    cachemap_mapper: "{cachemap_mapper}",
    userid: {userid},
    coords: [{coords}],
    zoom: {zoom},
    map_type: {map_type},
    circle: 0, //{circle},
    doopen: {doopen},
    //fromlat: {fromlat}, fromlon: {fromlon},
    //tolat: {tolat}, tolon: {tolon},
    searchdata: "{searchdata}",
    boundsurl: "{boundsurl}",
    extrauserid: "{extrauserid}",
    moremaptypes: false,
    fullscreen: true,
    largemap: false,
    savesettings: false,
    powertrail_ids: "{powertrail_ids}",
    mapCanvasId: 'map_canvas',
    controls: {}
};

window.onload = function() {
    loadOcMap(map_params);
};

</script>
