function watchOn(reportId) {
	var img_old = document.getElementById("report-img-off");
	var btn_old = document.getElementById("report-btn-off");
	var img_new = document.getElementById("report-img-on");
	var btn_new = document.getElementById("report-btn-on");
	img_old.src = "/tpl/stdstyle/images/loader/spinning-circles.svg";
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=watchOn&ajax=true&id=" + reportId,
		error : function(xhr) {
			img_old.src = "/images/redcross.gif";
			console.log("watchOn error: " + xhr.responseText);
		},
		success : function() {
			btn_old.style = "display: none;";
			img_new.src = "/tpl/stdstyle/images/misc/eye.svg";
			btn_new.style = "";
		}
	});
}

function watchOff(reportId) {
	var img_old = document.getElementById("report-img-on");
	var btn_old = document.getElementById("report-btn-on");
	var img_new = document.getElementById("report-img-off");
	var btn_new = document.getElementById("report-btn-off");
	img_old.src = "/tpl/stdstyle/images/loader/spinning-circles.svg";
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=watchOff&ajax=true&id=" + reportId,
		error : function(xhr) {
			img_old.src = "/images/redcross.gif";
			console.log("watchOff error: " + xhr.responseText);
		},
		success : function() {
			btn_old.style = "display: none;";
			img_new.src = "/tpl/stdstyle/images/misc/eye-off.svg";
			btn_new.style = "";
		}
	});
}

function changeStatus(reportId) {
	var status = document.getElementById("statusSelectCtrl").value;
	var inform = document.getElementById("report-status");
	inform.innerHTML = "<img src=\"/tpl/stdstyle/images/loader/spinning-circles.svg\" class=\"report-watch-img\">";
	$
			.ajax({
				type : "get",
				url : "/admin_reports.php?action=changeStatus&ajax=true&id="
						+ reportId + "&status=" + status,
				error : function(xhr) {
					inform.innerHTML = "<img src=\"/images/redcross.gif\" class=\"report-watch-img\">";
					console.log("changeStatus error: " + xhr.responseText);
				},
				success : function(data) {
					inform.innerHTML = data.message;
				}
			});
}

function changeLeader(reportId) {
	var leader = document.getElementById("leaderSelectCtrl").value;
	var inform = document.getElementById("report-leader");
	inform.innerHTML = "<img src=\"/tpl/stdstyle/images/loader/spinning-circles.svg\" class=\"report-watch-img\">";
	$
			.ajax({
				type : "get",
				url : "/admin_reports.php?action=changeLeader&ajax=true&id="
						+ reportId + "&leader=" + leader,
				error : function(xhr) {
					inform.innerHTML = "<img src=\"/images/redcross.gif\" class=\"report-watch-img\">";
					console.log("changeLeader error: " + xhr.responseText);
				},
				success : function(data) {
					if (data.message == "reqReloadPage") {
						location.reload();
					} else {
						inform.innerHTML = data.message;
					}
				}
			});
}

function enableEmail() {
	document.getElementById("report-email-row").style = "";
	document.getElementById("report-note-row").style = "display: none;";
	document.getElementById("report-poll-row").style = "display: none;";
	document.getElementById("report-log-row").style = "display: none;";
	document.getElementById("reports-btn-note").className = "btn btn-sm btn-default";
	document.getElementById("reports-btn-log").className = "btn btn-sm btn-default";
	document.getElementById("reports-btn-email").className = "btn btn-sm btn-success";
	document.getElementById("reports-btn-poll").className = "btn btn-sm btn-default";
}

function enableNote() {
	document.getElementById("report-note-row").style = "";
	document.getElementById("report-email-row").style = "display: none;";
	document.getElementById("report-poll-row").style = "display: none;";
	document.getElementById("report-log-row").style = "display: none;";
	document.getElementById("reports-btn-note").className = "btn btn-sm btn-success";
	document.getElementById("reports-btn-log").className = "btn btn-sm btn-default";
	document.getElementById("reports-btn-email").className = "btn btn-sm btn-default";
	document.getElementById("reports-btn-poll").className = "btn btn-sm btn-default";
}

function enablePoll() {
	document.getElementById("report-poll-row").style = "";
	document.getElementById("report-note-row").style = "display: none;";
	document.getElementById("report-email-row").style = "display: none;";
	document.getElementById("report-log-row").style = "display: none;";
	document.getElementById("reports-btn-note").className = "btn btn-sm btn-default";
	document.getElementById("reports-btn-log").className = "btn btn-sm btn-default";
	document.getElementById("reports-btn-email").className = "btn btn-sm btn-default";
	document.getElementById("reports-btn-poll").className = "btn btn-sm btn-success";
}

function enableLog() {
	document.getElementById("report-log-row").style = "";
	document.getElementById("report-poll-row").style = "display: none;";
	document.getElementById("report-note-row").style = "display: none;";
	document.getElementById("report-email-row").style = "display: none;";
	document.getElementById("reports-btn-note").className = "btn btn-sm btn-default";
	document.getElementById("reports-btn-log").className = "btn btn-sm btn-success";
	document.getElementById("reports-btn-email").className = "btn btn-sm btn-default";
	document.getElementById("reports-btn-poll").className = "btn btn-sm btn-default";
	if (document.getElementById("logTemplateSelect") !== null) {
		getLogTemplate();
	}
}

function clearAns3() {
	if (document.getElementById("noans3").checked) {
		document.getElementById("ans3").value = "";
	}
}

function clearNoans3() {
	document.getElementById("noans3").checked = false;
}
function getTemplates(objecttype) {
	document.getElementById("email-spinning-img").style = "";
	var v = document.querySelector('input[name="email-recipient"]:checked').value;
	var reportid = document.getElementById("reportid").value;
	document.getElementById("email-content").style = "";
	$
			.ajax({
				type : "get",
				url : "/admin_reports.php?action=getTemplates&ajax=true&objecttype="
						+ objecttype + "&recipient=" + v,
				error : function(xhr) {
					console.log("watchOn error: " + xhr.responseText);
				},
				success : function(data) {
					var content = "";
					for (var i = 0; i < data.message.length; i++) {
						var element = data.message[i];
						content = content + "<option value=\"" + element.id
								+ "\">" + element.shortdesc + "</option>";
					}
					;
					document.getElementById("templateSelect").innerHTML = content;
					if (data.message.length > 0) {
						document.getElementById("email-template").style = "";
						getTemplate(reportid);
					} else {
						document.getElementById("email-template").style = "display: none;";
						document.getElementById("form-email-textarea").innerHTML = "";
					}
					document.getElementById("email-spinning-img").style = "display: none;";
				}
			});
}

function getTemplate() {
	document.getElementById("email-spinning-img").style = "";
	var templateid = document.getElementById("templateSelect").value;
	var reportid = document.getElementById("reportid").value;
	$
			.ajax({
				type : "get",
				url : "/admin_reports.php?action=getTemplate&ajax=true&id="
						+ reportid + "&templateid=" + templateid,
				error : function(xhr) {
					document.getElementById("email-spinning-img").style = "display: none;";
					console.log("getTemplate error: " + xhr.responseText);
				},
				success : function(data) {
					document.getElementById("form-email-textarea").innerHTML = data.message;
					document.getElementById("email-spinning-img").style = "display: none;";
				}
			});
}

function getLogTemplate() {
	document.getElementById("email-spinning-img").style = "";
	var templateid = document.getElementById("logTemplateSelect").value;
	var reportid = document.getElementById("reportid").value;
	$
			.ajax({
				type : "get",
				url : "/admin_reports.php?action=getTemplate&ajax=true&id="
						+ reportid + "&templateid=" + templateid,
				error : function(xhr) {
					document.getElementById("email-spinning-img").style = "display: none;";
					console.log("getLogTemplate error: " + xhr.responseText);
				},
				success : function(data) {
					document.getElementById("form-log-textarea").innerHTML = data.message;
					document.getElementById("email-spinning-img").style = "display: none;";
				}
			});
}

function showButton(id) {
	document.getElementById("vote-" + id + "-btn").style = "";
}

function confirmPoll() {
	var confirmBox = $("#report-confirm-poll");
	var question = document.getElementById("poll-input-question").value
	var ans1 = document.getElementById("poll-input-ans1").value
	var ans2 = document.getElementById("poll-input-ans2").value
	var ans3 = document.getElementById("ans3").value
	confirmBox.find("#confirm-dlg-question").text(question);
	confirmBox.find("#confirm-dlg-ans1").text(ans1);
	confirmBox.find("#confirm-dlg-ans2").text(ans2);
	confirmBox.find("#confirm-dlg-ans3").text(ans3);
	confirmBox.find("#confirm-dlg-yes,#confirm-dlg-no").unbind().click(function() {
		confirmBox.hide();
	});
	confirmBox.find("#confirm-dlg-yes").click(function() {
		document.getElementById("reports-form-addpoll").submit();
	});
	confirmBox.show();
}
