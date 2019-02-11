$("#newpw-showpass-switch").click(function() {
	var inputType = $("#newpw-password").prop("type");
	if (inputType == "text") {
		$("#newpw-password").prop("type", "password");
	} else {
		$("#newpw-password").prop("type", "text");
	}
	$("#newpw-showpass-switch").toggleClass("newpw-eyeopen newpw-eyeclosed");
});

$("#newpw-password").keyup(function() {
	var newpw = $("#newpw-password").val();
	var strength = checkStrength(newpw);
	$("#newpw-meter").val(strength);
	if (strength > 1) {
		$("#newpw-submit-btn").removeClass("newpw-nodisplay");
		$("#newpw-submit-btn").attr("disabled", false);
	} else {
		$("#newpw-submit-btn").addClass("newpw-nodisplay");
		$("#newpw-submit-btn").attr("disabled", true);
	}
})

function checkStrength(password) {
	var strengthLen = 0;
	var strenghtDiff = 0;
	if (password.length == 0)
		return 0;
	if (password.length > 5)
		strengthLen += 1;
	if (password.length > 6)
		strengthLen += 1;
	if (password.length > 7)
		strengthLen += 1;
	if (password.length > 9)
		strengthLen += 1;
	if (password.length > 14)
		strengthLen += 1;
	if (password.match(/([A-Z])/))
		strenghtDiff += 1;
	if (password.match(/([a-z])/))
		strenghtDiff += 1;
	if (password.match(/([0-9])/))
		strenghtDiff += 1;
	if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))
		strenghtDiff += 1;
	if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/))
		strenghtDiff += 1;
	if (strenghtDiff < 3)
		strenghtDiff -= 1;
	if ((strenghtDiff < 1) || (strengthLen < 1))
		return 1;
	return strenghtDiff + strengthLen;
}