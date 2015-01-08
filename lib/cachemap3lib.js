function WMSImageMapTypeOptions(wmsName, wmsURL, wmsLayers, wmsStyles, wmsFormat, wmsVersion, wmsBgColor)
{
    var myBaseURL = wmsURL;
    var myLayers = wmsLayers;
    var myStyles = (wmsStyles ? wmsStyles : "");
    var myFormat = (wmsFormat ? wmsFormat : "image/gif");
    var myVersion = (wmsVersion ? wmsVersion : "1.1.1");
    var myBgColor = (wmsBgColor ? wmsBgColor : "0xFFFFFF");

    this.tileSize = new google.maps.Size(512, 512);
    this.name = wmsName;
    this.maxZoom = 19;

    this.getTileUrl = function(point, zoom) {
        var proj = map.getProjection();
        var zfactor = Math.pow(2, zoom);
        var lULP = new google.maps.Point(point.x * 512 / zfactor, (point.y + 1) * 512 / zfactor);
        var lLRP = new google.maps.Point((point.x + 1) * 512 / zfactor, point.y * 512 / zfactor);
        var lUL = proj.fromPointToLatLng(lULP);
        var lLR = proj.fromPointToLatLng(lLRP);
        var lBbox = lUL.lng() + "," + lUL.lat() + "," + lLR.lng() + "," + lLR.lat();
        var lSRS = "EPSG:4326";
        var lURL = myBaseURL;
        lURL += "?REQUEST=GetMap";
        lURL += "&SERVICE=WMS";
        lURL += "&VERSION=" + myVersion;
        lURL += "&LAYERS=" + myLayers;
        lURL += "&STYLES=" + myStyles;
        lURL += "&FORMAT=" + myFormat;
        lURL += "&BGCOLOR=" + myBgColor;
        lURL += "&SRS=" + lSRS;
        lURL += "&BBOX=" + lBbox;
        lURL += "&WIDTH=768";
        lURL += "&HEIGHT=768";
        return lURL;
    };
}

function createAttributionDiv()
{
	var attributionDiv = document.createElement('div'); 
    attributionDiv.id = "map-copyright";
    attributionDiv.style.fontSize = "10px";
    attributionDiv.style.fontFamily = "Arial, sans-serif";
    attributionDiv.style.padding = "3px 6px";
    attributionDiv.style.whiteSpace = "nowrap";
    attributionDiv.style.opacity = "0.7";
    attributionDiv.style.background = "#fff";
    return attributionDiv;
}