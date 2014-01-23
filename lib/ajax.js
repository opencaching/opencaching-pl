// AJAX

var AJAX =
{
    Create: function()
    {
        if (typeof XMLHttpRequest != "undefined")
        {
            var xhr = new XMLHttpRequest();

            if (xhr.overrideMimeType)
                xhr.overrideMimeType('text/xml');

            return xhr;
        }

        var xhrVersion = ["MSXML2.XMLHttp.5.0", "MSXML2.XMLHttp.4.0", "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp", "Microsoft.XMLHttp"];

        for (var i = 0; i < xhrVersion.length; i++)
        {
            try
            {
                return new ActiveXObject(xhrVersion[i]);
            } catch (e) { }
        }

        return null
    },

    Request: function(method, url, params, resultFunction, data)
    {
        var xhr = AJAX.Create();

        if (!xhr)
            return null;

        xhr.onreadystatechange = function()
        {
            try
            {
                if (xhr.readyState != 4) return;

                delete xhr['onreadystatechange'];

                if (xhr.status == 200 && resultFunction)
                {
                    if (data)
                        resultFunction(xhr, data);
                    else
                        resultFunction(xhr);
                }

                delete xhr;
            } catch (e) { }
        };

        xhr.open(method, url, true);

        if (method == 'POST' && xhr.setRequestHeader)
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.setRequestHeader('Cache-Control', 'no-cache');

        if (params)
            xhr.setRequestHeader('Content-length', params.length);

        xhr.setRequestHeader('Connection', 'close');
        xhr.send(params);
    }
};

/**
*
*  AJAX IFRAME METHOD (AIM)
*  http://www.webtoolkit.info/
*
**/

AIM = {

    frame : function(c) {

        var n = 'f' + Math.floor(Math.random() * 99999);
        var d = document.createElement('DIV');
        d.innerHTML = '<iframe style="display:none" src="about:blank" id="'+n+'" name="'+n+'" onload="AIM.loaded(\''+n+'\')"></iframe>';
        document.body.appendChild(d);

        var i = document.getElementById(n);
        if (c && typeof(c.onComplete) == 'function') {
            i.onComplete = c.onComplete;
        }

        return n;
    },

    form : function(f, name) {
        f.setAttribute('target', name);
    },

    submit : function(f, c) {
        AIM.form(f, AIM.frame(c));
        if (c && typeof(c.onStart) == 'function') {
            return c.onStart();
        } else {
            return true;
        }
    },

    loaded : function(id) {
        var i = document.getElementById(id);
        if (i.contentDocument) {
            var d = i.contentDocument;
        } else if (i.contentWindow) {
            var d = i.contentWindow.document;
        } else {
            var d = window.frames[id].document;
        }
        if (d.location.href == "about:blank") {
            return;
        }

        if (typeof(i.onComplete) == 'function') {
            i.onComplete(d.body.innerHTML);
        }
    }

}