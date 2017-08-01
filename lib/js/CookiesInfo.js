/*//////////////////////////////////////////////////////////////
  CookiesInfo - JG (triPPer)

  Info about cookies
/////////////////////////////////////////////////////////////////*/

var CookiesInfo;

function WHCreateCookie(name, value, days) {
    var date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    var expires = "; expires=" + date.toGMTString();
    document.cookie = name+"="+value+expires+"; path=/";
}

function WHReadCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

if (window.addEventListener)
    window.addEventListener('load', WHCheckCookies, false);
else if (window.attachEvent)
    window.attachEvent('onload', WHCheckCookies);

function WHCheckCookies() {
    if(WHReadCookie('cookies_accepted') != 'T') {
        var message_container = document.createElement('div');
        message_container.id = 'cookies-message-container';
        var html_code =
        '<div class="content-container" id="cookies-message" style="bottom: 0px; z-index: 999;">'
        + '<p class="align-center">' + CookiesInfo
        + '<a href="javascript:WHCloseCookiesWindow();" id="accept-cookies-checkbox" name="accept-cookies" class="btn btn-primary" style="margin: 5px;">x</a>'
        + '</p></div>';

        message_container.innerHTML = html_code;
        document.body.appendChild(message_container);
    }
}

function WHCloseCookiesWindow() {
    WHCreateCookie('cookies_accepted', 'T', 365);
    document.getElementById('cookies-message-container').removeChild(document.getElementById('cookies-message'));
}

function WHSetText( text )
{
    CookiesInfo = text;
}
