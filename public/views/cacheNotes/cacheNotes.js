
function removeNote(icon, cacheId){

  var jQueryIcon = $(icon);

  jQueryIcon.attr("src", "tpl/stdstyle/images/loader/spinning-circles.svg");
  jQueryIcon.attr("title", "<?=tr('usrWatch_removeWatched')?>");

  $.ajax({
    type:  "get",
    cache: false,
    url:   "/cacheNotes/removeNote/"+cacheId,
    error: function (xhr) {

        console.log("removeNote resp: " + xhr.responseText);

        jQueryIcon.attr("src", "images/redcross.gif");
        jQueryIcon.attr("title", "<?=tr('myNotes_noteRemovingError')?>");
    },
    success: function (data, status) {

      jQueryIcon.attr("src", "/images/ok.gif");
      jQueryIcon.attr("title", "<?=tr('myNotes_noteRemovingSuccess')?>");
    }
  });


}

function removeCoords(icon, cacheId){

  var jQueryIcon = $(icon);

  jQueryIcon.attr("src", "tpl/stdstyle/images/loader/spinning-circles.svg");
  jQueryIcon.attr("title", "<?=tr('usrWatch_removeWatched')?>");

  $.ajax({
    type:  "get",
    cache: false,
    url:   "/cacheNotes/removeCoords/"+cacheId,
    error: function (xhr) {

        console.log("removedModCoords: " + xhr.responseText);

        jQueryIcon.attr("src", "images/redcross.gif");
        jQueryIcon.attr("title", "<?=tr('myNotes_coordsRemovingError')?>");
    },
    success: function (data, status) {

      jQueryIcon.attr("src", "/images/ok.gif");
      jQueryIcon.attr("title", "<?=tr('myNotes_coordsRemovingSuccess')?>");
    }
  });
}
