
function removeRecommendation(icon, cacheId) {
  var jQueryIcon = $(icon);

  jQueryIcon.attr("src", "images/loader/spinning-circles.svg");
  jQueryIcon.attr("title", tr.myRecommendations_actionRemove);

  $.ajax({
    type:  "get",
    cache: false,
    url:   "/myRecommendations/removeRecommendation/"+cacheId,
    error: function (xhr) {

        console.log("removeRecommendation resp: " + xhr.responseText);

        jQueryIcon.attr("src", "images/redcross.gif");
        jQueryIcon.attr("title", tr.myRecommendations_recommendationRemovingError);
    },
    success: function (data, status) {

      jQueryIcon.attr("src", "/images/ok.gif");
      jQueryIcon.attr("title", tr.myRecommendations_recommendationRemovingSuccess);
    }
  });
}
