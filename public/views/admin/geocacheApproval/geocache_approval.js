/**
 * Removes html tags from given input string
 */
function strip_tags(input) {
    return input !== null ? input.replace(/<[\s\S]*?>/g, '') : "";
}

/**
 * Determines what style class to use for caches list row.
 *
 * rowData - a row resulted from API,
 * daysBeforeAlert - maximum days between now and cache submission when alert is
 *                   not set.
 */
function getApprovalTableRowStyle(rowData, daysBeforeAlert) {
    result = "";

    if (
        rowData["assigned_user_id"] === null
        && moment(rowData["date_created"]).isBefore(moment().subtract(5, 'd'))
    ) {
        result = "alert";
    } else if (
        rowData["assigned_user_id"] !== null
        && currentUserId == rowData["assigned_user_id"]
    ) {
        result = "highlighted";
    }

    return result;
}

/**
 * Returns predefined, translated string if given cache name is empty.
 */
function getNonEmptyCacheName(name) {
    return name.replace(/\s/g,'') == '' ? cacheNonamedTrans : name;
}

/**
 * Fills up html row contents with row data resulted from API.
 *
 * row - jQuery row (tr) object
 * rowData - one row from API "data" result"
 * bgClass - row background class name, applied to cells
 */
function fillRowWithData(row, rowData, bgClass) {
    row.attr("cache_id", rowData['cache_id']);
    row.find("td").addClass(bgClass);
    row.find(".geocacheApproval-cacheName")
        .attr("href", viewCacheLink + "?cacheid=" + rowData['cache_id'])
        .text(getNonEmptyCacheName(rowData["cachename"]));
    row.find(".geocacheApproval-userName")
        .attr("href", "viewprofile.php?userid=" + rowData['user_id'])
        .text(rowData["username"]);
    row.find(".geocacheApproval-region")
        .text(rowData["adm3"]);
    row.find(".alertable")
        .text(rowData["date_created"]);
    row.find(".geocacheApproval-lastLogDate")
        .text(rowData["last_log_date"]);
    if (rowData['last_log_author'] !== null) {
        row.find(".geocacheApproval-lastLogUserName")
            .attr(
                "href",
                "viewprofile.php?userid=" + rowData['last_log_author']
            )
            .text(rowData["last_log_username"]);
    }
    if (rowData['last_log_id'] !== null) {
        row.find(".geocacheApproval-lastLogText")
            .attr("href", "viewlogs.php?logid=" + rowData['last_log_id'])
            .attr("title", strip_tags(rowData['last_log_text']))
            .text(strip_tags(rowData['last_log_text']));
    }
    row.find(".geocacheApproval-assignedUser")
        .attr("href", "viewprofile.php?userid=" + rowData['assigned_user_id'])
        .text(rowData['assigned_user_name']);
}

function assignCurrentUser(element) {
    cacheAction(element, "assign");
}

function acceptCache(element) {
    cacheAction(element, "accept");
}

function rejectCache(element) {
    cacheAction(element, "reject");
}

/**
 * Displays general error information on Api failure
 */
function showApiError(jqXHR, textStatus, errorThrown) {
    console.log(textStatus);
    console.log(errorThrown);
    var tmpl = Handlebars.compile(
        $("#geocacheApproval_generalErrorTemplate").html()
    );
    var templateData = {
        error_thrown: errorThrown,
        text_status: textStatus
    };
    $("#geocacheApproval_info").html(tmpl(templateData));
}

/**
 * Performs chosen action on cache waiting for approval.
 *
 * element - jQuery "clicked" element (a)
 * actionType - one of "assign", "accept", "reject"
 */
function cacheAction(element, actionType) {
    var params = {
        action: null,
        successTpl: null,
        failureTpl: null
    };
    if (actionType == "assign") {
        params.action = "assign";
        params.successTpl = "assigned";
        params.failureTpl = "assign";
    } else if (actionType == "accept") {
        params.action = "accept";
        params.successTpl = "accepted";
        params.failureTpl = "accept";
    } else if (actionType == "reject") {
        params.action = "reject";
        params.successTpl = "rejected";
        params.failureTpl = "reject";
    }
    var parentRow = element.closest("tr");
    var cacheId = parentRow.attr("cache_id");
    $.get("/Admin.GeoCacheApprovalAdminApi/" + params.action + "/" + cacheId)
    .done(function(result) {
        if ("status" in result && result["status"] === "OK") {
            var tmpl = Handlebars.compile(
                $("#geocacheApproval_" + params.successTpl + "Template").html()
            );
            $("#geocacheApproval_info").html(tmpl(result));
        } else if (
            params.failureTpl != null
            && "status" in result
            && result["status"] === "ERROR"
        ) {
            var tmpl = Handlebars.compile(
                $(
                    "#geocacheApproval_" + params.failureTpl + "ErrorTemplate"
                ).html()
            );
            $("#geocacheApproval_info").html(tmpl(result));
        }
    })
    .fail(showApiError)
    .always(function() {
        refreshWaitingTable();
    });
}

/**
 * Shows jQuery UI dialog with confirmation of accept/reject actions
 *
 * element - jQuery "clicked" element (a)
 * templateId - id of template to create dialog from
 * actionButtonText - text of "confirm" button
 * actionOnConfirm - function/method to call on confirm button click
 */
function showConfirmDialog(
    element, templateId, actionButtonText, actionButtonClass, actionOnConfirm
) {
    var tmpl = Handlebars.compile($("#" + templateId).html());
    var parentRow = element.closest("tr");
    var templateData = {
        cache_id: parentRow.attr("cache_id"),
        cache_name: parentRow.find(".geocacheApproval-cacheName").text(),
        cache_owner: parentRow.find(".geocacheApproval-userName").text()
    };

    $("#geocacheApproval_confirmDialogTemplate").dialog({
        dialogClass: "geocacheApproval-confirmDialog"
            + " geocacheApproval-confirmDialog-noTitlebar",
        resizable: false,
        height: "auto",
        minHeight: 10,
        minWidth: 600,
        modal: true,
        buttons: [
            {
                text: actionButtonText,
                class: actionButtonClass,
                click: function() {
                    $(this).dialog("close");
                    actionOnConfirm();
                }
            },
            {
                text: cancelButtonText,
                class: "geocacheApproval-confirmDialog-cancelButton",
                click: function() {
                    $(this).dialog("close");
                }
            }
        ],
        open: function() {
            $(this).html(tmpl(templateData));
        }
    });
}

/**
 * Regenerates list of waiting caches, clearing existing and assigning new
 * actions for corresponding links.
 */
function refreshWaitingTable() {
    $.get("/Admin.GeoCacheApprovalAdminApi/getWaiting")
    .done(function(result) {
        var apprTable = $("#geocacheApproval_waitingTable")

        // clear table and click handlers
        apprTable.find(".geocacheApproval-row").remove();
        $(".geocacheApproval-actionAccept").off('click');
        $(".geocacheApproval-actionBlock").off('click');
        $(".geocacheApproval-actionAssign").off('click');

        if (result["data"].length > 0) {
            var inReviewCount = 0;
            $.each(result["data"], function(index, rowData) {
                var bgClass = "bgcolor" + (2 - (index) % 2);
                row = $(
                    "#geocacheApproval_rowTemplate .geocacheApproval-row"
                ).clone(
                    true, true
                );
                row.addClass(getApprovalTableRowStyle(rowData));
                fillRowWithData(row, rowData, bgClass);
                apprTable.append(row);
                if (rowData["assigned_user_id"] !== null) {
                    inReviewCount++;
                }
            });
            $("#adminMenu_waitingForAssignee").text(
                result["data"].length - inReviewCount
            );
            $("#adminMenu_newPendings").text(result["data"].length);
            $("#adminMenu_newPendingsStats").removeClass("hidden");
            $(".geocacheApproval-actionAccept").click(function(ev) {
                ev.preventDefault();
                var caller = $(this);
                showConfirmDialog(
                    $(this),
                    "geocacheApproval_acceptTemplate",
                    acceptButtonText,
                    "geocacheApproval-confirmDialog-acceptButton",
                    function() {
                        acceptCache(caller);
                    }
                );
            });
            $(".geocacheApproval-actionBlock").click(function(ev) {
                ev.preventDefault();
                var caller = $(this);
                showConfirmDialog(
                    $(this),
                    "geocacheApproval_blockTemplate",
                    blockButtonText,
                    "geocacheApproval-confirmDialog-blockButton",
                    function() {
                        rejectCache(caller);
                    }
                );
            });
            $(".geocacheApproval-actionAssign").click(function(ev) {
                ev.preventDefault();
                assignCurrentUser($(this));
            });
        } else {
            row = $(
                "#geocacheApproval_rowEmptyTemplate .geocacheApproval-row"
            ).clone(
                true, true
            );
            apprTable.append(row);
            $("#adminMenu_waitingForAssignee").text(0);
            $("#adminMenu_newPendings").text(0);
            $("#adminMenu_newPendingsStats").addClass("hidden");
        }
        $("#geocacheApproval_refreshDatetime").text(result["updated"]);
    })
    .fail(showApiError);
}

$(document).ready(function() {
    $("#geocacheApproval_refresh").click(function(ev) {
        // clear error info if was visible previously
        $("#geocacheApproval_info .geocacheApproval-errorInfo").html("");
        refreshWaitingTable();
    });
    $("#geocacheApproval_refresh").trigger("click");
});
