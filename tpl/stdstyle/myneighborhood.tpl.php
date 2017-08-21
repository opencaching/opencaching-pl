<?php ?>
<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
<script type="text/javascript">
    var map_image_cache;

    window.onload = function () {
        //preload images
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

    //image swapping function:
    function Lite(nn) {
        document.getElementById('main-cachemap').src = map_image_cache[nn].src;
    }

    function Unlite() {
        document.getElementById('main-cachemap').src = map_image_cache[-1].src;
    }
</script>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/home.png" class="icon32" alt="">
    {{my_neighborhood_radius}}
    {distance} {distance_unit}
</div>

<div class="content2-container">
{info}
  <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="">
    {{newest_caches}}
  </p>

  <div class="content2-container-2col-left" id="local-caches-area">
    <div class="content-txtbox-noshade">
      {new_caches}
      {more_caches}
   </div>
  </div>
  <div class="content2-container-2col-right" id="local-cachemap-block">
    <div class="img-shadow">
      {local_cache_map}
    </div>
  </div>
</div>

<div class="content2-container">
  <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="">
    {{incomming_events}}
  </p>
  {new_events}

  <div class="buffer"></div>
  <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="">
    {{ftf_awaiting}}
  </p>
  {ftf_caches}
  {more_ftf}

  <div class="buffer"></div>
  <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="">
    {{latest_logs}}
  </p>
  {new_logs}
  {more_logs}

  <div id="cacheTitled" style="display: {ptDisplay};">
  <div class="buffer"></div>
    {Title_titledCaches}
    {titledCaches}
    {more_titledCaches}
  </div>

  <div class="buffer"></div>
  <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/recommendation.png" class="icon32" alt="">
    {{top_recommended}}
  </p>
  {top_caches}
  {more_topcaches}
  <div class="buffer"></div>
</div>
