// remove log by ajax
function rmLog(event, logId){
    if(confirm( confirmRmLogTranslation )){
        event.preventDefault();
        $("#rmLogHrefSection-"+logId).hide();
        $("#rmLogLoader-"+logId).show();
        request = $.ajax({
            url: "removelog.php",
            type: "post",
            dataType: 'json',
            data:{
                    logid: logId
            }
        });
        request.done(function (response, textStatus, jqXHR){
            console.log(response);
            if(response.removeLogResult === true){
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
		$("#revertLogHrefSection-" + logId).html('<img src="/tpl/stdstyle/images/free_icons/cancel.png" class="icon16" alt="Cancel icon">');
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
    if($(window).scrollTop() + $(window).height() > $(document).height() - 200) {
        var logEntriesCount = parseInt($('#logEntriesCount').val());
        if(currentLogEntriesOffset < logEntriesCount){
           loadLogEntries(currentLogEntriesOffset,currentLogEntriesLimit);
        }
    }
});

function loadLogEntries(offset, limit){
    if(logEntryUnderExecution === false){
        logEntryUnderExecution = true;
        var geocacheId = $("#cacheid").val();
        var owner_id = $("#owner_id").val();
        request = $.ajax({
            url: "getLogEntries.php",
            type: "post",
            data:{
                    offset: offset,
                    limit: limit,
                    geocacheId: geocacheId,
                    owner_id: owner_id,
                    includeDeletedLogs: $('#includeDeletedLogs').val()
            }
        });
        request.done(function (response, textStatus, jqXHR){
            $("#viewcache-logs").html($("#viewcache-logs").html() + response);
            currentLogEntriesOffset = currentLogEntriesOffset + currentLogEntriesLimit;
            logEntryUnderExecution = false;
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
    var action = 'remove';
  }else{             //not-watched
    var action = 'add';
  }

  $.ajax({
    type:  "get",
    cache: false,
    url:   'mywatches.php?action='+action+'&cacheWp='+$(input).val(),
    error: function (xhr) {
        console.log("watchIt error: " + xhr.responseText);
    },
    success: function (data, status) {
        console.log("watchIt: success!");
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
            + ' src="tpl/stdstyle/images/misc/copy-coords.svg"'
            + ' onclick="copyCoords(event)"'
            + ' class="coords-image"'
            + ' alt="' + tr['copy_coords_prompt'] + '"'
            + ' title="' + tr['copy_coords_prompt'] + '"/>'
        );
    }
});
