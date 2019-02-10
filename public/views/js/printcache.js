 $( document ).ready(function() {
    var showlogs = $("#showlogs").val();
    if(showlogs == ''){
    } else if(showlogs == '&showlogs=4') {
        loadLogEntries(0,4);
    } else if (showlogs == '&showlogsall=y'){
        loadLogEntries(0,9999);
    }
});

function loadLogEntries(offset, limit){
    var owner_id = $("#owner_id").val();
    var geocacheId = $("#cacheid").val();

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
    });
}
