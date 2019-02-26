// remove log by ajax
function rmLog(event, logId){
    if(confirm( confirmRmLogTranslation )){
        event.preventDefault();
        $("#rmLogHrefSection-"+logId).hide();
        $("#rmLogLoader-"+logId).show();
        request = $.ajax({
            url: "/cacheLog/removeLogAjax/"+logId,
            type: "post",
            dataType: 'json',
        });
        request.done(function (response, textStatus, jqXHR){
            console.log(response);
            if(jqXHR.status === 200){
                var uType = $("#uType").val();
                if(uType == 1){
                    $("#log"+logId).addClass('show_deleted');
                } else {
                    $("#log"+logId).remove();
                }
            } else {
               $("#rmLogHrefSection-"+logId).show();
            }
        });

        request.always(function () {
            $("#rmLogLoader-"+logId).hide();
            $("#rmLogHrefSection"+logId).show();
        });
    }
    return false;
}

// revert log by ajax
function revertLog(event, logId) {
	event.preventDefault();
	$("#revertLogHrefSection-" + logId).hide();
	$("#revertLogLoader-" + logId).show();
	$.ajax({
		url : "/CacheLog/revertLogAjax/" + logId,
		type : "get",
	})
	.done(function(response, textStatus, jqXHR) {
			$("#log" + logId).removeClass('show_deleted');
			$("#log-title-" + logId).removeClass('show_deleted');
			$("#log-content-" + logId).removeClass('show_deleted');
	})
	.fail(function(response, textStatus, jqXHR) {
		$("#revertLogHrefSection-" + logId).html('<img src="/images/free_icons/cancel.png" class="icon16" alt="Cancel icon">');
		$("#revertLogHrefSection-" + logId).show();
	});
	$("#revertLogLoader-" + logId).hide();
	return false;
}

var currentLogEntriesOffset = 0;
var currentLogEntriesLimit = 10;
var logEntryUnderExecution = false;

$(window).scroll(function (event) {
  // in most browsers win.scrollTop + win.height == document.height for pages
  // scrolled to bottom of the page but sometimes NOT! (at leas at Chrome Mobile)

  var scrollTop = document.documentElement.scrollTop;
  var widowHeight = window.innerHeight;
  var documentHeight = document.documentElement.offsetHeight;

  if( scrollTop + widowHeight > documentHeight - 200) {
    loadLogEntries();
  }
});

/* contains functions to be called after every part of log entries is loaded */
var logEntriesPostLoadHooks = Array();

function loadLogEntries(){
    if (logEntryUnderExecution === false && $('#logEntriesCount').length) {
        var logEntriesCount = parseInt($('#logEntriesCount').val());
        if (
            isNaN(logEntriesCount)
            || currentLogEntriesOffset >= logEntriesCount
        ) {
            return;
        }
        logEntryUnderExecution = true;
        var geocacheId = $("#cacheid").val();
        var owner_id = $("#owner_id").val();
        request = $.ajax({
            url: "getLogEntries.php",
            type: "post",
            data:{
                    offset: currentLogEntriesOffset,
                    limit: currentLogEntriesLimit,
                    geocacheId: geocacheId,
                    owner_id: owner_id,
                    includeDeletedLogs: $('#includeDeletedLogs').val()
            }
        });
        request.done(function (response, textStatus, jqXHR){
            $("#viewcache-logs").html($("#viewcache-logs").html() + response);
            currentLogEntriesOffset = currentLogEntriesOffset + currentLogEntriesLimit;
            logEntryUnderExecution = false;
            /* executing post load hooks */
            logEntriesPostLoadHooks.forEach(function(element, index) {
                if (typeof element == "function") {
                    element.call(this);
                }
            });
        });
    }
}

function showHint(event)
{
    event.preventDefault();
    $("#hintEncrypted").toggle();
    $("#hintDecrypted").toggle();
    $("#encryptLinkStr").toggle();
    $("#decryptLinkStr").toggle();
    return false;
}

function openCgeoWindow(event, ocWaypoint)
{
    event.preventDefault();
    window.open('https://send2.cgeo.org/add.html?cache='+ocWaypoint,'-','width=240,height=240,resizable=no,scrollbars=0');
    return false;
}

function openGarminWindow(event, latitude, longitude, ocWaypoint, cachename)
{
    event.preventDefault();
    window.open('garmin.php?lat='+latitude+'&long='+longitude+'&wp='+ocWaypoint+'&name='+cachename+'&popup=y',
        'GARMIN','width=450,height=160,resizable=no,scrollbars=0');
    return false;
}

function watchIt(input){

  if(!input.checked){ // watched
    var action = 'removeFromWatchesAjax';
  }else{              // not-watched
    var action = 'addToWatchesAjax';
  }

  $.ajax({
    type:  "get",
    cache: false,
    url:   '/UserWatchedCaches/'+action+'/'+$(input).val(),
    error: function (xhr) {
        console.log("watchIt error: " + xhr.responseText);
    },
    success: function (result) {
        $('#watchersCount').html(result['message']);
        console.log("watchIt: success!");
    }
  });
}

function ignoreIt(input){

  if(!input.checked){ // ignored
    var action = 'removeFromIgnoredAjax';
  }else{              // not-ignored
    var action = 'addToIgnoredAjax';
  }

  console.log(action);

  $.ajax({
    type:  "get",
    cache: false,
    url:   '/UserIgnoredCaches/'+action+'/'+$(input).val(),
    error: function (xhr) {
        console.log("ignoreIt error: " + xhr.responseText);
    },
    success: function (result) {
        console.log("ignoreIt: success!");
    }
  });
}

/**
 * This function switch coords format
 */
var currentCordsFormat = 'CoordsDegMin';
function changeCoordsFormat(){
  switch(currentCordsFormat){
    case 'CoordsDegMin':
      $('.CoordsDegMin').hide();
      $('.CoordsDegMinSec').show();
      currentCordsFormat = 'CoordsDegMinSec';
      break;

    case 'CoordsDegMinSec':
      $('.CoordsDegMinSec').hide();
      $('.CoordsDecimal').show();
      currentCordsFormat = 'CoordsDecimal';
      break;

    case 'CoordsDecimal':
      $('.CoordsDecimal').hide();
      $('.CoordsDegMin').show();
      currentCordsFormat = 'CoordsDegMin';
      break;
  }
}

var closeDialog = 0;
var dialogElement;

function copyCoords(e) {
    var statusText = tr['copy_coords_failure'];
    var statusClass = "copy-coords-failure";
    var coordsElem = $('.' + currentCordsFormat);
    if (coordsElem != null) {
        var coords = coordsElem.text().trim();
        var temp = $("<input>");
        $("body").append(temp);
        temp.val(coords).select();
        if (document.queryCommandEnabled("copy")) {
            var result = document.execCommand("copy", false, null);
            temp.remove();
            if (result) {
                statusClass = "";
                statusText =
                    tr['copy_coords_success_prefix']
                    + "<br><span class=\"copy-coords-values\">"
                    + coords
                    + "</span><br>"
                    + tr['copy_coords_success_suffix']
            }
        }
    }
    var copyStatus = $("#copy-coords-status");
    if (copyStatus.length) {
        copyStatus.html(statusText);
        copyStatus.dialog({
            autoOpen: true,
            minHeight: 20,
            minWidth : 50,
            width: "auto",
            position: {
                my: "center",
                at: "center",
                of: e.target
            },
            classes: {
                "ui-dialog": "copy-coords-status ui-corner-all",
                "ui-dialog-content": "copy-coords-status " + statusClass,
                "ui-dialog-titlebar-close": "copy-coords-status",
            },
            open: function() {
                closeDialog = 1;
                $(document).bind('click', documentClickCloseDialog);
            },
            focus: function() {
                closeDialog = 0;
            },
            close: function() {
                $(document).unbind('click', documentClickCloseDialog);
                dialogElement = null;
            }
        });
        dialogElement = copyStatus;
        closeDialog = 0;
    }
}

function documentClickCloseDialog() {
    if (closeDialog && dialogElement != null) {
        dialogElement.dialog('close');
    }
    closeDialog = 1;
}

$(document).ready(function() {
    if (
        $("#cacheCoordinates").length
        && document.queryCommandSupported("copy")
    ) {
        $("#cacheCoordinates").after(
            ''
            + '<img id="copy-coords"'
            + ' src="images/misc/copy-coords.svg"'
            + ' onclick="copyCoords(event)"'
            + ' class="coords-image"'
            + ' alt="' + tr['copy_coords_prompt'] + '"'
            + ' title="' + tr['copy_coords_prompt'] + '"/>'
        );
    }
    // load initial logs if viewcache-logs element is visible
    // (little fix for logs not showing if page cannot be scrolled, because
    //  it fully fits window height [android and short cache description f.ex.])
    if ($("#viewcache-logs").length && $('#logEntriesCount').length) {
        var el = $("#viewcache-logs").get(0);
        var visibleBottom = ($(window).scrollTop() + $(window).height());
        if (el.getBoundingClientRect().bottom < visibleBottom) {
            var logEntriesCount = parseInt($('#logEntriesCount').val());
            if (logEntriesCount > 0 && $(".logs").length == 0) {
                loadLogEntries();
            }
        }
    }
});
