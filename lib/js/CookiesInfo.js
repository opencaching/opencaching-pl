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
        '<div class="content2-pagetitle" id="cookies-message" style="padding: 10px 0px; font-size: 14px; line-height: 22px; border-bottom: 1px solid #D3D0D0; text-align: center; position: fixed; bottom: 0px; background-color: #EFEFEF; width: 100%; z-index: 999;">'
        +'<table border="0" cellspacing="2" cellpadding="1" width="97%">'
        +'<tr>'
//        +'<td><img src="./images/oc_logo_winter.png" alt="" style="margin-right:10px;" /></td>'
        +'<td><img src="./images/oc_logo.png" alt="" style="margin-right:10px;" /></td>'
        + ' <td>' + CookiesInfo +  ' </td>'
//        +'<td>Używamy cookies, dzięki którym nasz serwis może działać lepiej. '
//       +'Przeczytaj o prywatności i ochronie danych w naszym <a href ="http://wiki.opencaching.pl/index.php/Regulamin_OC_PL">Regulaminie</a>.'
//        +'<br> Do końca roku 2013 będą zapisywane cookies GA, które pozwolą określić obciążenie naszego serwera </td>'
        +'<td><a href="javascript:WHCloseCookiesWindow();" id="accept-cookies-checkbox" name="accept-cookies" style="background-color: rgb(88,144,168); padding: 1px 10px; color: #FFF; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; display: inline-block; margin-left: 10px; text-decoration: none; cursor: pointer;">x</a></td>'
        +'</tr>'
        +'<table>'
        +'</div>';

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

