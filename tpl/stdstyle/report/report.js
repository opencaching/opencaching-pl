function recipientChange()
{
	if (document.getElementById("report-type-owner").checked) {
		document.getElementById("report-warning").style= "display: none";
		document.getElementById("report-mail-pub").style= "";
		document.getElementById("report-submit-btn").style= "";
	} else {
		document.getElementById("report-warning").style= "";
		document.getElementById("report-mail-pub").style= "display: none";
		ocdeclCheck();
	}
}

function ocdeclCheck()
{
	if (document.getElementById("report-ocdecl").checked) {
		document.getElementById("report-submit-btn").style= "";
	} else {
		document.getElementById("report-submit-btn").style= "display: none";
	}
}
