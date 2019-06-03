
<div class="content2-container">
  <div class="content2-pagetitle">
    <?=tr('gpMyCandidates_title')?>:
    <a href="<?=$v->user->getProfileUrl()?>"><?=$v->user->getUserName()?></a>
  </div>

  <?php $view->callChunk('listOfCaches/listOfCaches', $v->listModel);?>

</div>

<script>
  function acceptOffer(btn, candidateId){

    var btnContainer = $(btn).parent();
    btnContainer.empty();

    var img = $('<img class="icon16" title="<?=tr('gpCandidates_cancelOfferInProgress')?>">');
    img.attr("src", "/images/loader/spinning-circles.svg");
    img.appendTo(btnContainer);

    $.ajax({
      url:   '/GeoPath/acceptCacheCandidateAjax/'+candidateId,
      type:  "get",
      cache: false,

      error: function (xhr) {
        console.log("candidate accepted: " + xhr.responseText);
        img.attr("src", "/images/redcross.gif");
        img.attr("title", "<?=tr('gpMyCandidates_errorOnAccept')?>");
      },

      success: function (data, status) {
        img.attr("src", "/images/ok.gif");
        img.attr("title", "<?=tr('gpMyCandidates_offerAccepted')?>");
      }
    });
  }

  function refuseOffer(btn, candidateId){

    var btnContainer = $(btn).parent();
    btnContainer.empty();

    var img = $('<img class="icon16" title="<?=tr('gpCandidates_cancelOfferInProgress')?>">');
    img.attr("src", "/images/loader/spinning-circles.svg");
    img.appendTo(btnContainer);

    $.ajax({
      url:   '/GeoPath/refuseCacheCandidateAjax/'+candidateId,
      type:  "get",
      cache: false,

      error: function (xhr) {
        console.log("candidate refused: " + xhr.responseText);
        img.attr("src", "/images/redcross.gif");
        img.attr("title", "<?=tr('gpMyCandidates_errorOnRefuse')?>");
      },

      success: function (data, status) {
        img.attr("src", "/images/ok.gif");
        img.attr("title", "<?=tr('gpMyCandidates_offerRefused')?>");
      }
    });
  }
</script>
