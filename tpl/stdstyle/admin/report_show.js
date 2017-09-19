function watchOn(reportId) {
	var img_old = document.getElementById("report-img-off");
	var btn_old = document.getElementById("report-btn-off");
	var img_new = document.getElementById("report-img-on");
	var btn_new = document.getElementById("report-btn-on");
	img_old.src="/tpl/stdstyle/images/loader/spinning-circles.svg";
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=watchOn&ajax=true&id=" + reportId,
		error : function(xhr) {
			img_old.src="/images/redcross.gif";
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
	img_old.src="/tpl/stdstyle/images/loader/spinning-circles.svg";
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=watchOff&ajax=true&id=" + reportId,
		error : function(xhr) {
			img_old.src="/images/redcross.gif";
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
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=changeStatus&ajax=true&id=" + reportId + "&status=" + status,
		error : function(xhr) {
			console.log("changeStatus error: " + xhr.responseText);
		},
		success : function() {
			console.log("changeStatus: OK : " + status);
		}
	});
}

function changeLeader(reportId) {
	var leader = document.getElementById("leaderSelectCtrl").value;
	$.ajax({
	type : "get",
	url : "/admin_reports.php?action=changeLeader&ajax=true&id=" + reportId + "&leader=" + leader,
	error : function(xhr) {
		console.log("changeLeader error: " + xhr.responseText);
	},
	success : function() {
		console.log("changeLeader: OK : " + leader);
	}
});}