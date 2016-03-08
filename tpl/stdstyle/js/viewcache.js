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
        }
    });

    request.always(function () {
        $("#rmLogLoader-"+logId).hide();
        $("#rmLogHrefSection"+logId).show();
    });

    return false;
}

