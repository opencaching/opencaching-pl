/*//////////////////////////////////////////////////////////////
  CookiesInfo - JG (triPPer)

  Info about cookies
/////////////////////////////////////////////////////////////////*/

var CookiesInfo;

function WHCreateCookie(name, value, days) {
	var date = new Date();
	date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
	var expires = "; expires=" + date.toGMTString();
	document.cookie = name + "=" + value + expires + "; path=/";
}

function WHReadCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ')
			c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0)
			return c.substring(nameEQ.length, c.length);
	}
	return null;
}

if (window.addEventListener)
	window.addEventListener('load', WHCheckCookies, false);
else if (window.attachEvent)
	window.attachEvent('onload', WHCheckCookies);

function WHCheckCookies() {
	if (WHReadCookie('cookies_accepted') != 'T') {
		document.getElementById('cookies-message-div').style.display = 'block';
		document.getElementById('cookies-message-div').hidden = "";
	}
}

function WHCloseCookiesWindow() {
	WHCreateCookie('cookies_accepted', 'T', 365);
	document.getElementById('cookies-message-div').style.display = 'none';
	document.getElementById('cookies-message-div').hidden = "hidden";
}
