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


var currentLogEntriesOffset = 0;
var currentLogEntriesLimit = 10;
var logEntryUnderExecution = false;

$(window).scroll(function (event) {
    if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
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
