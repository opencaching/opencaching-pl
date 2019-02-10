function watchOn(reportId) {
	var img_old = document.getElementById("img-off-" + reportId);
	var img_new = document.getElementById("img-on-" + reportId);
	img_old.src="/tpl/stdstyle/images/loader/spinning-circles.svg";
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=watchOn&ajax=true&id=" + reportId,
		error : function(xhr) {
			img_old.src="/images/redcross.gif";
			console.log("watchOn error: " + xhr.responseText);
		},
		success : function() {
			img_old.style = "display: none;";
			img_new.src = "/tpl/stdstyle/images/misc/eye.svg";
			img_new.style = "";
		}
	});
}

function watchOff(reportId) {
	var img_old = document.getElementById("img-on-" + reportId);
	var img_new = document.getElementById("img-off-" + reportId);
	img_old.src="/tpl/stdstyle/images/loader/spinning-circles.svg";
	$.ajax({
		type : "get",
		url : "/admin_reports.php?action=watchOff&ajax=true&id=" + reportId,
		error : function(xhr) {
			img_old.src="/images/redcross.gif";
			console.log("watchOff error: " + xhr.responseText);
		},
		success : function() {
			img_old.style = "display: none;";
			img_new.src = "/tpl/stdstyle/images/misc/eye-off.svg";
			img_new.style = "";
		}
	});
}
