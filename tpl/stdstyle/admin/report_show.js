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
			console.log("watchOn: OK!");
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
			console.log("watchOff: OK!");
		}
	});
}

function changeStatus(reportId) {
	var status = document.getElementById("statusSelectCtrl").value;
	var inform = document.getElementById("report-status");
	inform.innerHTML = "<img src=\"/tpl/stdstyle/images/loader/spinning-circles.svg\" class=\"report-watch-img\">";
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=changeStatus&ajax=true&id=" + reportId
				+ "&status=" + status,
		error : function(xhr) {
			inform.innerHTML = "<img src=\"/images/redcross.gif\" class=\"report-watch-img\">";
			console.log("changeStatus error: " + xhr.responseText);
		},
		success : function(data) {
			console.log("changeStatus: OK");
			inform.innerHTML = data.message;
		}
	});
}

function changeLeader(reportId) {
	var leader = document.getElementById("leaderSelectCtrl").value;
	var inform = document.getElementById("report-leader");
	inform.innerHTML = "<img src=\"/tpl/stdstyle/images/loader/spinning-circles.svg\" class=\"report-watch-img\">";
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=changeLeader&ajax=true&id=" + reportId
				+ "&leader=" + leader,
		error : function(xhr) {
			inform.innerHTML = "<img src=\"/images/redcross.gif\" class=\"report-watch-img\">";
			console.log("changeLeader error: " + xhr.responseText);
		},
		success : function(data) {
			console.log("changeLeader: OK : ");
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
	document.getElementById("report-email-row2").style = "";
	document.getElementById("report-note-row").style = "display: none;";
	document.getElementById("report-note-row2").style = "display: none;";
}

function enableNote() {
	document.getElementById("report-note-row").style = "";
	document.getElementById("report-note-row2").style = "";
	document.getElementById("report-email-row").style = "display: none;";
	document.getElementById("report-email-row2").style = "display: none;";
}

function getTemplates(objecttype) {
	document.getElementById("email-spinning-img").style = "";
	var v = document.querySelector('input[name="email-recipient"]:checked').value;
	document.getElementById("email-content").style = "";
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=getTemplates&ajax=true&objecttype=" + objecttype + "&recipient=" + v,
		error : function(xhr) {
			console.log("watchOn error: " + xhr.responseText);
		},
		success : function(data) {
			var content = "";
			for(var i = 0; i < data.message.length; i++) {
				var element = data.message[i];
				content = content + "<option value=\"" + element.id + "\">" + element.shortdesc + "</option>";
			};
			document.getElementById("templateSelect").innerHTML = content;
			if (data.message.length > 0) {
				document.getElementById("email-template").style = "";
				getTemplate();
			} else {
				document.getElementById("email-template").style = "display: none;";
				document.getElementById("form-email-textarea").innerHTML = "";
			}
			document.getElementById("email-spinning-img").style = "display: none;";
			console.log("my object: %o", data)
			console.log("watchOn: OK!");
		}
	});
}

function getTemplate() {
	document.getElementById("email-spinning-img").style = "";
	var templateid = document.getElementById("templateSelect").value;
	console.log("SELECT: " + templateid);
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=getTemplate&ajax=true&id=" + templateid,
		error : function(xhr) {
			document.getElementById("email-spinning-img").style = "display: none;";
			console.log("getTemplate error: " + xhr.responseText);
		},
		success : function(data) {
			document.getElementById("form-email-textarea").innerHTML = data.message;
			document.getElementById("email-spinning-img").style = "display: none;";
			console.log("getTemplate: OK!");
		}
	});
}