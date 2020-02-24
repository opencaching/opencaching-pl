<script>
function removeFromList(icon, cacheId){

  var jQueryIcon = $(icon);

  jQueryIcon.attr("src", "images/loader/spinning-circles.svg");
  jQueryIcon.attr("title", "<?=tr('usrWatch_removeWatched')?>");

  $.ajax({
    type:  "get",
    cache: false,
    url:   "/printList/removeFromListAjax/"+cacheId,
    error: function (xhr) {

        console.log("removedFromList: " + xhr.responseText);

        jQueryIcon.attr("src", "images/redcross.gif");
        jQueryIcon.attr("title", "<?=tr('myNotes_coordsRemovingError')?>");
    },
    success: function (data, status) {

      jQueryIcon.attr("src", "/images/ok.gif");
      jQueryIcon.attr("title", "<?=tr('myNotes_coordsRemovingSuccess')?>");
    }
  });
}
</script>

<div class="content2-container">

    <div class="content2-pagetitle">
      <?=tr('mnu_clipboard')?>
    </div>

    <?php if($view->rowCount > 0) { ?>
        <div class="content2-container">
          <?php $view->callChunk('listOfCaches/listOfCaches', $view->listCacheModel);?>
        </div>

        <div class="align-right">
          <a class="btn btn-sm" href="/printcache.php?source=mylist"><?=tr('mylist_03')?></a>
          <a class="btn btn-sm" href="/printList/clearListAjax"><?=tr('mylist_04')?></a>
        </div>

        <div>
          <?=tr('mylist_05')?>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=gpx" title="GPS Exchange Format .gpx">GPX</a>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=zip" title="Garmin (GPX+Photos)">Garmin</a>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=gpxgc" title="GPS Exchange Format (Groundspeak) .gpx">GPX GC</a>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=loc" title="Waypoint .loc">LOC</a>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=kml" title="Google Earth .kml">KML</a>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=ov2" title="TomTom POI .ov2">OV2</a>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=ovl" title="TOP50-Overlay .ovl">OVL</a>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=txt" title="Tekst .txt">TXT</a>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=wpt" title="Oziexplorer .wpt">WPT</a>
          <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=uam" title="AutoMapa .uam">UAM</a>
          <br/>
          <span class="help"><?=tr('accept_terms_of_use')?></span>
        </div>

    <?php } else { //$view->cachesCount == 0 ?>
        <div>
            <br />
            <p><?=tr('mylist_01')?></p>
        </div>
    <?php } //$view->cachesCount == 0 ?>

</div>
