<?php
use src\Utils\Uri\SimpleRouter;
use src\Controllers\UserWatchedCachesController;

?>
<div class="content2-container">

    <div class="align-right">
      <a href="<?=SimpleRouter::getLink('UserProfile', 'notifySettings')?>" class="btn btn-default btn-sm">
        <?=tr('settings_notifications')?></a>
      <a class="btn btn-default btn-sm"
         href="<?=SimpleRouter::getLink(UserWatchedCachesController::class, 'mapOfWatches')?>">
        <?=tr('map_watched_caches')?></a>
    </div>


    <div class="content2-pagetitle">
      <?=tr('usrWatch_title')?>
    </div>


    <?php if($view->cachesCount > 0) { ?>
        <div class="content2-container">
          <?php $view->callChunk('listOfCaches/listOfCaches', $view->listCacheModel);?>
        </div>

        <div id="downloadMenu">
          <p>
            <?=tr('download')?>:
            <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=gpxgc" title="GPS Exchange Format .gpx">GPX</a>
            <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=loc" title="Waypoint .loc">LOC</a>
            <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=kml" title="Google Earth .kml">KML</a>
            <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=ov2" title="TomTom POI .ov2">OV2</a>
            <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=ovl" title="TOP50-Overlay .ovl">OVL</a>
            <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=txt" title="Tekst .txt">TXT</a>
            <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=wpt" title="Oziexplorer .wpt">WPT</a>
            <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=uam" title="AutoMapa .uam">UAM</a>
            <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=zip" title="Garmin ZIP file (GPX + zdjęcia)  .zip">GARMIN</a>
          </p>
          <p>
            <span class="help"><?=tr('accept_terms_of_use')?></span>
          </p>
        </div>
    <?php } else { //$view->cachesCount == 0 ?>
        <div>
            <br />
            <p><?=tr('usrWatch_nothingWatched')?></p>
        </div>
    <?php } //$view->cachesCount == 0 ?>

    <script>

      function removeFromWatched(icon, cacheWp){

        var jQueryIcon = $(icon);

        jQueryIcon.attr("src", "/images/loader/spinning-circles.svg");
        jQueryIcon.attr("title", "<?=tr('usrWatch_removeWatched')?>");

        $.ajax({
          type:  "get",
          cache: false,
          url:   "/UserWatchedCaches/removeFromWatchesAjax/"+cacheWp,
          error: function (xhr) {

              console.log("removedFromWatched: " + xhr.responseText);

              jQueryIcon.attr("src", "/images/redcross.gif");
              jQueryIcon.attr("title", "<?=tr('usrWatch_removingError')?>");
          },
          success: function (data, status) {

            jQueryIcon.attr("src", "/images/ok.gif");
            jQueryIcon.attr("title", "<?=tr('usrWatch_removingSuccess')?>");
          }
        });

      }
    </script>

</div>
