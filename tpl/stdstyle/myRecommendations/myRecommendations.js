
function removeRecommendation(icon, cacheId) {
alert("ojojoj "+cacheId)
  var jQueryIcon = $(icon);

  jQueryIcon.attr("src", "tpl/stdstyle/images/loader/spinning-circles.svg");
  jQueryIcon.attr("title", "<?=tr('usrWatch_removeWatched')?>");

  $.ajax({
    type:  "get",
    cache: false,
    url:   "/cacheRecommendation/removeRecommendation/"+cacheId,
    error: function (xhr) {

        console.log("removeRecommendation resp: " + xhr.responseText);

        jQueryIcon.attr("src", "images/redcross.gif");
        jQueryIcon.attr("title", "<?=tr('myNotes_noteRemovingError')?>");
    },
    success: function (data, status) {

      jQueryIcon.attr("src", "/images/ok.gif");
      jQueryIcon.attr("title", "<?=tr('myNotes_noteRemovingSuccess')?>");
    }
  });
}
