<div class="content2-container">

    <div class="content2-pagetitle">
      <?=tr('usrIgnore_ignored_caches')?>
    </div>


    <?php if($view->cachesCount > 0) { ?>
        <div class="content2-container">
          <?php $view->callChunk('listOfCaches/listOfCaches', $view->listCacheModel);?>
        </div>

    <?php } else { //$view->cachesCount == 0 ?>
        <div>
            <br />
            <p><?=tr('usrIgnore_no_ignores')?></p>
        </div>
    <?php } //$view->cachesCount == 0 ?>

    <script>

      function removeFromIgnored (icon, cacheWp){

        var jQueryIcon = $(icon);

        jQueryIcon.attr("src", "/images/loader/spinning-circles.svg");
        jQueryIcon.attr("title", "<?=tr('usrIgnore_removeFromIgnored')?>");

        $.ajax({
          type:  "get",
          cache: false,
          url:   "/UserIgnoredCaches/removeFromIgnoredAjax/"+cacheWp,
          error: function (xhr) {

              console.log("removedFromIgnored: " + xhr.responseText);

              jQueryIcon.attr("src", "/images/redcross.gif");
              jQueryIcon.attr("title", "<?=tr('usrIgnore_removingError')?>");
          },
          success: function (data, status) {

            jQueryIcon.attr("src", "/images/ok.gif");
            jQueryIcon.attr("title", "<?=tr('usrIgnore_removingSuccess')?>");
          }
        });
      }
    </script>
</div>
