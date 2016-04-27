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


var currentLogEnteriessOffset = 0;
var currentLogEnteriessLimit = 10;
var logEnteryUnderExecution = false;

$(window).scroll(function (event) {
    if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
        var logEnteriesCount = parseInt($('#logEnteriesCount').val());
        if(currentLogEnteriessOffset < logEnteriesCount){
           loadLogEnteries(currentLogEnteriessOffset,currentLogEnteriessLimit);
        }
    }
});

function loadLogEnteries(offset, limit){
    if(logEnteryUnderExecution === false){
        logEnteryUnderExecution = true;
        var geocacheId = $("#cacheid").val();
        var owner_id = $("#owner_id").val();
        request = $.ajax({
            url: "getLogEnteries.php",
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
            currentLogEnteriessOffset = currentLogEnteriessOffset + currentLogEnteriessLimit;
            logEnteryUnderExecution = false;
        });
    }
}

function showHint(event)
{
    event.preventDefault();
        $("#decrypt-hints").toggle();
        $("#hintEncrypted").toggle();
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