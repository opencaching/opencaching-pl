// remove log by ajax
function rmLog(logId){
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
            $("#log"+logId).remove();
        } else {
           $("#rmLogHrefSection-"+logId).show();
        }
    });

    request.always(function () {
        $("#rmLogLoader-"+logId).hide();
        $("#rmLogHrefSection"+logId).show();
    });

    return false;
}


var currentLogEnteriessOffset = 0;
var currentLogEnteriessLimit = 10;

$(window).scroll(function (event) {
    var scroll = $(window).scrollTop();
    var height = $(document).height();
    if((height - scroll) < 800 ){
        var logEnteriesCount = parseInt($('#logEnteriesCount').val());
        if(currentLogEnteriessOffset < logEnteriesCount){
           loadLogEnteries(currentLogEnteriessOffset,currentLogEnteriessLimit);
        }
    }
});

function loadLogEnteries(offset, limit){
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
    });
}