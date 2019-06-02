
<div class="content2-container">
  <div class="content2-pagetitle">
    <?=tr('gpCandidates_title')?>:
    <a href="<?=$v->gp->getUrl()?>"><?=$v->gp->getName()?></a>
  </div>

  <?php $v->callChunk('listOfCaches/listOfCaches', $v->listModel);?>

</div>

<script>
  function cancelCandidateOffer(icon, candidateId){

    var jQueryIcon = $(icon);

    jQueryIcon.attr("src", "/images/loader/spinning-circles.svg");
    jQueryIcon.attr("title", "<?=tr('gpCandidates_cancelOfferInProgress')?>");

    $.ajax({
      url:   "/GeoPath/cancelCacheCandidateAjax/"+candidateId,
      type:  "get",
      cache: false,
      error: function (xhr) {

          console.log("removedFromWatched: " + xhr.responseText);

          jQueryIcon.attr("src", "/images/redcross.gif");
          jQueryIcon.attr("title", "<?=tr('gpCandidates_errorOnCancel')?>");
      },
      success: function (data, status) {

        jQueryIcon.attr("src", "/images/ok.gif");
        jQueryIcon.attr("title", "<?=tr('gpCandidates_offerCanceled')?>");
      }
    });
  }
</script>