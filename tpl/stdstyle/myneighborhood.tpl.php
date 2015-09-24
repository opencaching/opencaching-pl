<?php

?>
<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
<script language="javascript" type="text/javascript">
    <!-- hide script from old browsers
    var map_image_cache;

    //detect browser:
    if ((navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 3) || parseInt(navigator.appVersion) >= 4) {
        rollOvers = 1;
    }
    else {
        if (navigator.appName == "Microsoft Internet Explorer" && parseInt(navigator.appVersion) >= 4) {
            rollOvers = 1;
        }
        else {
            rollOvers = 0;
        }
    }

    window.onload = function () {
        //preload images
        if (rollOvers) {
            map_image_cache = [];
            map_image_cache[-1] = new Image();
            map_image_cache[-1].src = document.getElementById('main-cachemap').getAttribute('basesrc');
            for (i = 0; i < 50; i++) {
                var nc_elem = document.getElementById('mapcache' + i);
                if (nc_elem != null) {
                    map_image_cache[i] = new Image();
                    map_image_cache[i].src = nc_elem.getAttribute('maphref');
                }
            }
        }
    }

    //image swapping function:
    function Lite(nn) {
        if (rollOvers) {
            document.getElementById('main-cachemap').src = map_image_cache[nn].src;
        }
    }

    function Unlite() {
        if (rollOvers) {
            document.getElementById('main-cachemap').src = map_image_cache[-1].src;
        }
    }

    //end hiding -->
</script>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/home.png" class="icon32" alt="" title="" align="middle"/>&nbsp;
    {{my_neighborhood_radius}}
    {distance} km
</div>
<!-- Text container -->
{info}
<div class="content2-container line-box">
    <div class="content2-container-2col-left" id="local-caches-area">
        <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="Cache" align="middle" />&nbsp;
            {{newest_caches}}
        </p>
        <div class="content-txtbox-noshade">
            {new_caches}
            {more_caches}
            <br/><br/>
        </div>
    </div>
    <br />
    <div class="content2-container-2col-right" id="local-cachemap-block">
        <div class="img-shadow">
            {local_cache_map}
        </div>
    </div>
    <br />
    <div class="content2-container-2col-left" id="local-events-area">
        <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="" title="Event" align="middle" />&nbsp;
            {{incomming_events}}
        </p>
        {new_events}
    </div>
    <br />
    <div class="content2-container-2col-left local-logs-area">
        <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="Event" align="middle" />&nbsp;
            {{ftf_awaiting}}
        </p>
        {ftf_caches}
        {more_ftf}
        <br/>
    </div>
    <br />
    <div class="content2-container-2col-left" id="local-logs-area">
        <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="Event" align="middle" />&nbsp;
            {{latest_logs}}
        </p>
        {new_logs}
        {more_logs}
        <br/><br/>
    </div>
    <br />
    
        <div class="content2-container-2col-left" id="cacheTitled" style="display: {ptDisplay}; width: 100%">
        {Title_titledCaches}
        {titledCaches}
        {more_titledCaches}        
        {End_titledCaches}
    </div>

    
    <div class="content2-container-2col-left local-logs-area">
        <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/recommendation.png" class="icon32" alt="" title="Event" align="middle" />&nbsp;
            {{top_recommended}}
        </p>
        {top_caches}
        {more_topcaches}
        <br/><br/>
    </div>
    <br />
</div>
<!-- Chat disabled because of server performance issues
<br/>
<h2><a class="links" href=/chat/>Shoutbox</a></h2>
<?php
// echo getShoutBoxContent();
?>
    <br/>
-->
