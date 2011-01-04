<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<title>Google Maps</title>


<!--
<link rel="stylesheet" type="text/css" href="/mapStyle.css" />
<style type="text/css">
	v\:* {
	behavior:url(#default#VML);
	}
</style>
-->

<style type="text/css">

a, span, div, body {
	text-decoration: none;
}
ul {
	margin:0px;
}
img {
	border:none;
}
body,html {
	margin:0px;
	width: 100%;
	height: 100%;
	font-family: verdana;
}
input, select {
	border: 1px solid navy;
}

.topRowCell {
	border: 1px solid navy;
}
.topRowDiv {
	background: #dddddd;
	height:100px;
	margin:5px;
	padding: 3px;
	font: normal 12px courier new;
	text-align:left;
	color: navy;
}

#top1 {
	font: bold 14px courier new;
	text-align:center;

}
.Row2Cell {
	padding-left: 5px;
	padding-right: 5px;
}


#outerMapDiv {
	border: 2px solid navy;
	padding:3px;
}
#mapDiv {
/*
	width:100%;
	height:580px;
*/
}
#mapCell {
/*	border: 2px solid navy; */
	padding:5px;

}

.statusDiv, #link, #mtContainer, #scale, #directions_info, #customMaps_info {
	padding: 3px;
	font: normal 12px courier new;
	text-align:left;
	color: navy;
}

.statusBar, #link, #mtContainer, #scale, #directions_info, #customMaps_info, #opacityContainer, #buttonContainer, #directionsFormTable  {
	border: 1px solid navy;
	margin-left:5px;
	margin-right:5px;
	margin-top:5px;
	background: #dddddd;
}

#directions_info, #customMaps_info {
	height: 585px;
	overflow:auto;
}


#link, #scale {
	text-align:center;
}


/* -------------- Tabs --------------------*/
#tabsContainer {
	text-align: center;
	padding: 2px;
	margin-top: 2px;

}
#tabsTable td{
	text-align: center;
}

#customMapsTabContainer, #directionsTabContainer {
	padding-top: 2px;
}
#directionsTabContainer {

}


.functionsTab, .functionsTabSelected {
	font: bold 10px verdana;
	padding: 2px;
	cursor: pointer;
	border: 1px solid gray;
}
.functionsTab:hover {
	background: #EBB94D;
	color: red;
	border: 1px solid red;
}
.functionsTabSelected {
	background: #9743FF;
	color: #ffffff;

}

/* -------------- end Tabs ----------------*/



#directionsFormTable td{
	font: bold 10px verdana;
	padding: 2px;

}

.ddOption, .ddSelectedOption {
	background: #A6A8CC;
	color: navy;
	border: 2px solid navy;
	text-align:center;
	vertical-align:middle;
	font: bold 10px verdana;
	padding: 2px;
	cursor: pointer;
	margin: 3px;
	z-index:100;
}
.ddSelectedOption {
	border: 2px solid #008000;
	background: #80FF80;
}
.ddOption:hover {
	background: #EBB94D;
	border: 2px solid red;
}


#loadingMessage {
    position: absolute;
    width:  200px;
    text-align: center;
    padding: 10px;
    border: 5px solid #290B8B;
    background: #3F06FA;
	color: #eeeeee;
	font: bold 20px verdana;
    z-index: 1;
	left:0px;
	top:0px;
	opacity: 0.7;

}


.countyInfo, .countyInfoSel {
	font: normal 11px verdana;
	cursor:pointer;
	background: #A6A8CC;
	border: 2px solid navy;
	margin-bottom:5px;
	padding:3px;
}

.countyInfoSel {
	background:#F4E48C;
	border:2px solid #EF3E31;
}

#opacityContainer {
}
#opacityLabel {
	font: normal 12px verdana;
	text-align: center;
	margin: 2px;
}

#opacitySlider {
	border: none;
	background: url(sliderBG_800.jpg) repeat-X;
	cursor: pointer;
	height: 20px;
	text-align: left;
}

#sliderHandle {
	border: 2px solid black;
	width: 5px;
	height: 18px;
	position: relative;
}


/* -------------- Simplify ----------------- */
.button, .selectedButton, .buttonB, .button3 {
	background: #A6A8CC;
	color: navy;
	border: 2px solid navy;
	text-align:center;
	vertical-align:middle;
	font: normal 10px verdana;
	padding: 2px;
	cursor: pointer;
	margin: 3px;
}
.button3 {
	margin: 0px;
}

.selectedButton {
	border: 2px solid #008000;
	background: #80FF80;
	color: #008000;
}
.buttonB {
	border: 2px solid #ECB052;
	background: #F6D84C;

}
.button:hover, .buttonB:hover, .button3:hover {
	background: #EBB94D;
	color: red;
	border: 2px solid red;
}

/* ------------------------------------------------- */

/* ------------ Driving directions ----------------- */
.stepRow td {
	border-top: 1px solid #bbbbbb;
	vertical-align: top;
	padding:2px;
	cursor: pointer;
}

.globalSummaryDiv {
	border: 1px solid navy;
	background: #888888;
	color: #ffffff;
}

.routeSummaryDiv {
	border: 1px solid navy;
	cursor: pointer;
	background: #cccccc;
}

#detailmap {
	width: 250px;
	height: 150px;
	border:1px solid gray;
}
.bubble {
	font: normal 12px verdana;
	width: 250px;
	height: 150px;
}








#POI_controls {
	font: normal 12px verdana;
	padding:2px;
	text-align: left;
}

#driveVia {
	border: 1px solid gray;
	font: normal 10px verdana;


}

</style>
<script src="/key.js"></script>
<script>
	var scriptTag = '<' + 'script src="http://maps.google.com/maps?file=api&v=2.s&key=ABQIAAAA4DS0L5IhPNkkzhAejJ1YghQmw8g3SyoYQoey3nQkQjZ-xBIKWxQBStwSQ5otzHFYPFzfrBNiNotrGQ">'+'<'+'/script>';
	document.write(scriptTag);
</script>
<script src="/dragzoom.js" type="text/javascript"></script>

<script type="text/javascript">
//<![CDATA[

var IE = document.all ? true : false;
var map;
var detailmap;
var dirObj;
var container;
var opacity = 1;
var clckTimeOut = null;
var loading = false;
var saveState = false;
var directionsInfoDiv;
var iw;
var polyline;
var pLine;
var startMarker;
var endMarker;
var dragMarker;
var pLinePoints = Array();
var midRouteMarkers = Array();
var markerDragging = false;
var typeChanging = false;
var baseIcon = new GIcon();
baseIcon.iconSize=new GSize(16,16);
baseIcon.iconAnchor=new GPoint(8,8);
baseIcon.infoWindowAnchor=new GPoint(10,0);

var yellowIcon = (new GIcon(baseIcon, "/images/yellowSquare.png", null, ""));
var greenIcon = (new GIcon(baseIcon, "/images/greenCircle.png", null, ""));
var redIcon = (new GIcon(baseIcon, "/images/redCircle.png", null, ""));
var orangeIcon = (new GIcon(baseIcon, "/images/orangeCircle.png", null, ""));
var blueIcon = (new GIcon(baseIcon, "/images/blueCircle.png", null, ""));
var violetIcon = (new GIcon(baseIcon, "/images/violetCircle.png", null, ""));

var baseIcon2 = new GIcon();
baseIcon2.iconSize=new GSize(8,8);
baseIcon2.iconAnchor=new GPoint(4,4);
baseIcon2.infoWindowAnchor=new GPoint(4,0);
var redIcon8 = (new GIcon(baseIcon2, "/images/redSquare_8.png", null, ""));




var NormalLayer = G_NORMAL_MAP.getTileLayers()[0]
var TerrainLayer = G_PHYSICAL_MAP.getTileLayers()[0]
var SatelliteLayer = G_SATELLITE_MAP.getTileLayers()[0]
var satProj = G_SATELLITE_MAP.getProjection();	
var normalProj = G_NORMAL_MAP.getProjection();	

	



var cRight = new GCopyrightCollection('OpenCaching');
var copyright = new GCopyright(1, new GLatLngBounds(new GLatLng(-90, -180), new GLatLng(90, 180)), 0, "OpenCaching");
cRight.addCopyright(copyright);


// Defaults --------------------------------------------
var zoom = 5;
var centerPoint = new GLatLng(52,18);
var mType = 0;


var USstates = Array('Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming')
var pattern = '(' + USstates.join('|') + ')';


function load() {
	doLoad();
}


function doLoad() {
	if (GBrowserIsCompatible()) {
		container = document.getElementById("mapDiv");
		resizePage();
		map = new GMap2(container, {draggableCursor:"crosshair"});

		map.addMapType(G_PHYSICAL_MAP)
		TerrainLayer.getOpacity = function () {return opacity;};
		var layers = [NormalLayer,TerrainLayer];
		addCustomMapType("Terrain N",layers,17,0);

		directionsInfoDiv = document.getElementById("directions_info");
		dirObj = new GDirections();

		var mapState = getMapState();
		if (typeof(mapState) != 'undefined') {
			if (mapState['lat'] && mapState['lon']) {
				centerPoint = new GLatLng(parseFloat(mapState['lat']), parseFloat(mapState['lon']));
			}	
			if (mapState['z']) {
				zoom = parseInt(mapState['z']);
			}
			if (mapState['mType']) {
				mType = parseInt(mapState['mType']);

				if (mType == 4) {
					opacity = 0.4;
				}
			}

			
			if (mapState['driveFrom']) {
				var oDriveFrom = document.getElementById('driveFrom').value = mapState['driveFrom'];
			}
			if (mapState['driveTo']) {
				var oDriveFrom = document.getElementById('driveTo').value = mapState['driveTo'];
			}
			if (mapState['driveVia']) {
				var oDriveFrom = document.getElementById('driveVia').value = mapState['driveVia'];
			}
			if (mapState['locale']) {
				var oDriveFrom = document.getElementById('locale').value = mapState['locale'];
			}
		}

		map.setCenter(centerPoint, zoom, map.getMapTypes()[mType]);

		map.addControl(new GScaleControl());
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
//map.addControl(new DragZoomControl(),new GControlPosition(G_ANCHOR_TOP_RIGHT,new GSize(-10,10)));

//		map.enableDoubleClickZoom(); 
		map.enableContinuousZoom();
		map.enableScrollWheelZoom();

		GEvent.addListener(map, 'mousemove', mouseMove);
/*
		GEvent.addListener(map, "moveend", moveEnd);
		GEvent.addListener(map, "zoomend", zoomEnd);
		GEvent.addListener(map, "maptypechanged", mapTypeChenged);
*/
		GEvent.addListener(map, 'click', mapClick);

		GEvent.addListener(dirObj, "load", onDirectionsLoad);
		GEvent.addListener(dirObj, "error", onDirectionsError);

		var ovcontrol = new GOverviewMapControl(new GSize(165,165));
		map.addControl(ovcontrol);
		var ov_map = ovcontrol.getOverviewMap();
		GEvent.addListener(map, 'maptypechanged', function(){
//			ov_map.setMapType(G_NORMAL_MAP);
		}); 


		if (mapState['driveFrom'] && mapState['driveTo']) {
			getDirections();
		}

	}
}




function addCustomMapType(mName,layers,maxRes,minRes) {
	var TerrainN = new GMapType(layers, normalProj, mName, {maxResolution:maxRes, minResolution:minRes, errorMessage:'Boom!'}); 
	map.addMapType(TerrainN);
}



function mouseMove(mousePt) {
	mouseLatLng = mousePt;
	
	var zoom = map.getZoom();
	var mousePx = normalProj.fromLatLngToPixel(mousePt, zoom);
	var oStatusDiv = document.getElementById("mouseTrack")	
	if (oStatusDiv) {
		oStatusDiv.innerHTML = 'Mouse LatLng: ' + mousePt.y.toFixed(6) + ', ' + mousePt.x.toFixed(6) ;
		oStatusDiv.innerHTML += '<br> ';
		oStatusDiv.innerHTML += 'Mouse Px: ' + mousePx.x + ', ' + mousePx.y;
		oStatusDiv.innerHTML += '<br>';
		oStatusDiv.innerHTML += 'Tile: ' + Math.floor(mousePx.x / 256) + ', ' + Math.floor(mousePx.y / 256);
	}

getNearestVertex(mouseLatLng);

}



function mapTypeChenged() {
	if (typeChanging) {
		return;
	}
	typeChanging = true;

	var mtype = map.getCurrentMapType().getName();
	if (mtype == 'Terrain N') {
		opacity = 0.4;
	}
	else {
		opacity = 1;
	}
	var currentType = map.getCurrentMapType();
	map.setMapType(G_NORMAL_MAP);
	map.setMapType(currentType);
	typeChanging = false;

}








///////////////////////////////////////////////////////////////////////////////////////////////////////



function mapClick(marker, point) {
	if (clckTimeOut) {
		window.clearTimeout(clckTimeOut);
		clckTimeOut = null;
		doubleClick(marker, point);
	}
	else {
		clckTimeOut = window.setTimeout(function(){singleClick(marker, point)},500);
	}
}

function doubleClick(marker, point) {

}

function singleClick(marker, point) {
	window.clearTimeout(clckTimeOut);
	clckTimeOut = null;

	if (point) {
		if (!startMarker) {
			var oDriveFrom = document.getElementById('driveFrom');
			startMarker = new GMarker(point,{icon:greenIcon,draggable:true,bouncy:false});
			startMarker.formField = oDriveFrom;
			GEvent.addListener(startMarker,'drag',markerDrag);
			GEvent.addListener(startMarker,'dragend',getDirections);
			map.addOverlay(startMarker);
			oDriveFrom.value = startMarker.getPoint().lat().toFixed(6) + ',' + startMarker.getPoint().lng().toFixed(6);
			return;
		}
		else if (!endMarker) {
			var oDriveTo = document.getElementById('driveTo');
			endMarker = new GMarker(point,{icon:redIcon,draggable:true,bouncy:false});
			endMarker.formField = oDriveTo;
			GEvent.addListener(endMarker,'drag',markerDrag);
			GEvent.addListener(endMarker,'dragend',getDirections);
			map.addOverlay(endMarker);
			oDriveTo.value = endMarker.getPoint().lat().toFixed(6) + ',' + endMarker.getPoint().lng().toFixed(6);
			return;
		}
	}
} 



function markerDrag() {
	this.formField.value = this.getPoint().lat().toFixed(5) + ',' + this.getPoint().lng().toFixed(5);
}


function indicateLoading() {
	loading = true;
	displayLoadingMsg();
}

function displayLoadingMsg() {
	var oLMsg = document.getElementById('loadingMessage');
	oLMsg.style.display = '';
	oLMsg.style.left = container.offsetLeft + (container.clientWidth / 2) - (oLMsg.clientWidth / 2) + 'px';
	oLMsg.style.top = container.offsetTop + (container.clientHeight / 2) - (oLMsg.clientHeight / 2) + 'px';
	oLMsg.style.filter="alpha(opacity=70)";

	if (loading){
		var to = window.setTimeout('displayLoadingMsg()',100);
	}
	else {
		oLMsg.style.display = 'none';
	}
}






// Map state -------------------------------------------
function getMapState() {
	var qString = window.location.search;
	qString = qString.substring(1, qString.length);

	var cValues = new Array();
	var separator = '&';
//	return cValues;

	if (!qString) {
		qString = getCookie('map2');
		separator = ',';
	}

	if (qString) {
		var nvPairs = qString.split(separator);

		for (var n = 0 ; n < nvPairs.length ; n++ )	{
			var nvPair = nvPairs[n];
			var nv = nvPair.split('=');

			cValues[nv[0]] = unescape(nv[1]);
			
		}
	}
	return cValues;
}


function getCookie(cookieName) {
	if (document.cookie.length > 0) {
		var cIndex = document.cookie.indexOf(cookieName+"=");
		if (cIndex != -1) {
			cIndex += cookieName.length + 1;
			var cLength = document.cookie.indexOf(";", cIndex);
			if (cLength == -1) {
				cLength = document.cookie.length;
			}
			return unescape(document.cookie.substring(cIndex, cLength)); 
		}
	}
	return null;
}



function setCookie(cookieName, value, expiredays) {
	var exp = "";
	if (expiredays) {
		var ExpireDate = new Date ();
		ExpireDate.setTime(ExpireDate.getTime() + (expiredays * 24 * 3600 * 1000));
		exp = ExpireDate.toGMTString();
	}
	document.cookie = cookieName + "=" + escape(value) + "; expires=" + exp + "; path=/";
}



function delCookie (cookieName) {
	if (getCookie(cookieName)) {
		document.cookie = cookieName + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
	}
}

// End Map state -------------------------------------------



function resizePage() {

	var oTable = document.getElementById("outerTable");
	var dDiv = document.getElementById("directions_info");
	
	oTable.style.width = document.body.clientWidth  + 'px';
	var veHeight = 230;
	container.style.height = document.body.clientHeight - veHeight  + 'px';
	dDiv.style.height = document.body.clientHeight - veHeight + 65 + 'px';

	if (map) {
		var center = map.getCenter();
		var zoom = map.getZoom();
		map.checkResize();
		map.setCenter(center,zoom);
	}
}





function unload() {
	doUnload(0);
}

function doUnload(mReset) {
	if (mReset) {
		delCookie('map3');
		saveState = false;
		window.location = window.location.pathname;
	}

	if (saveState) {
		var cookieStr = '';
		var center = map.getCenter();

		cookieStr += 'lat=' + center.lat() + ',';
		cookieStr += 'lon=' + center.lng() + ',';
		cookieStr += 'z=' + map.getZoom() + ',';
		cookieStr += 'mType=' + mType + ',';
		cookieStr += 'mapMode=' + mapMode;

		setCookie('map3', cookieStr, 365);
	}
	GUnload();
}

function rmOverlays() {
	directionsInfoDiv.innerHTML = '';
	map.clearOverlays();
	resizePage();
	startMarker = null;
	endMarker = null;
}



function getDirections() {
	var oDriveFrom = document.getElementById('driveFrom');
	var oDriveTo = document.getElementById('driveTo');
	var oDriveVia = document.getElementById('driveVia');
	var oLocale = document.getElementById('locale');

	directionsInfoDiv.style.display = '';

	var loadStr;

	if (oDriveFrom.value && oDriveTo.value) {
		loadStr = 'from:' + oDriveFrom.value;
		if (oDriveVia.value) {
			var viaValue = oDriveVia.value.replace(/[\n\r]+/,"");
			var viaSteps = viaValue.split(';');
			for (var n = 0 ; n < viaSteps.length ; n++ ) {
				loadStr += ' to: ' + viaSteps[n];
			}
		}
		loadStr += ' to: ' + oDriveTo.value;
	}
	if (loadStr) {
		indicateLoading();
		dirObj.load(loadStr,{locale:oLocale.value,getPolyline:true,getSteps:true});
	}

}




function onDirectionsLoad() {
	var html = '';
	var status = dirObj.getStatus();
	var bounds = dirObj.getBounds();


	map.clearOverlays();

	var copyrightHTML = dirObj.getCopyrightsHtml();
	var summaryHTML = dirObj.getSummaryHtml();
	var distance = dirObj.getDistance();
	var duration = dirObj.getDuration();
	var numRoutes = dirObj.getNumRoutes();
	var oDriveFrom = document.getElementById('driveFrom');
	var oDriveTo = document.getElementById('driveTo');
	var startLatLng = dirObj.getRoute(0).getStep(0).getLatLng();
	var endLatLng = dirObj.getRoute(numRoutes-1).getEndLatLng();

	polyline = dirObj.getPolyline();
	pLine = copyPolyline(polyline);
	map.addOverlay(polyline);

	var numGeoCodes = dirObj.getNumGeocodes();
	var startPoint = dirObj.getGeocode(0);
	var endPoint = dirObj.getGeocode(numGeoCodes-1);
	addDragMarker(startPoint);
	
	if (startMarker) {
		var clickStartPoint = startMarker.getPoint();
		if (clickStartPoint.distanceFrom(startLatLng) > 0) {
			html += 'WARNING: The route starts some distance away from the point clicked.<br>';
		}
	}

	if (endMarker) {
		var clickEndPoint = endMarker.getPoint();
		if (clickEndPoint.distanceFrom(endLatLng) > 0) {
			html += 'WARNING: The route ends some distance away from the point clicked.<br>';
		}
	}

	var realStartMarker = new GMarker(startLatLng,{icon:greenIcon,draggable:true,bouncy:false});
	realStartMarker.formField = oDriveFrom;
	GEvent.addListener(realStartMarker,'drag',markerDrag);
	GEvent.addListener(realStartMarker,'dragend',getDirections);
	map.addOverlay(realStartMarker);

	var realEndMarker = new GMarker(endLatLng,{icon:redIcon,draggable:true,bouncy:false});
	GEvent.addListener(realEndMarker,'drag',markerDrag);
	GEvent.addListener(realEndMarker,'dragend',getDirections);
	realEndMarker.formField = oDriveTo;
	map.addOverlay(realEndMarker);


	html += '<div class="globalSummaryDiv">';
	html += '<table cellspacing="0" cellpadding="2" width="100%">';
	html += '<tr><td valign="top"> <b>' + startPoint.address + '</b> to <b>' + endPoint.address + '</b></td></tr>';
	html += '<tr><td valign="top"> '+summaryHTML+'</td></tr>';
	html += '</table></div>';
	
	
	for (var r = 0 ; r < numRoutes ; r++ ) {
		var route = dirObj.getRoute(r);
		var startGeoCode = dirObj.getGeocode(r);//route.getStartGeocode();
		var endGeoCode = dirObj.getGeocode(r+1);//route.getEndGeocode();
		var endLatLng = route.getEndLatLng();
		var routeSummaryHTML = route.getSummaryHtml();
		var routeDistance = route.getDistance();
		var routeDuration = route.getDuration();
		html += '<div class="routeSummaryDiv" onclick="toggleSteps('+r+')" title="Click to view steps">';
		html += '<table cellspacing="0" cellpadding="2" width="100%">';
		html += '<tr>';
		
		if (numRoutes == 1) {
			html += '<td valign="top"><img src="/images/greenCircle.png"><br><img src="/images/redCircle.png"></td>';
		}
		else {
			if (r == 0) {
				html += '<td valign="top"><img src="/images/greenCircle.png"><br><img src="/images/yellowSquare.png"></td>';
				var midMarker = new GMarker(endLatLng,{icon:yellowIcon});
				map.addOverlay(midMarker);
			}
			else if (r == numRoutes - 1) {
				html += '<td valign="top"><img src="/images/yellowSquare.png"><br><img src="/images/redCircle.png"></td>';
			}
			else {
				html += '<td valign="top"><img src="/images/yellowSquare.png"><br><img src="/images/yellowSquare.png"></td>';
				var midMarker = new GMarker(endLatLng,{icon:yellowIcon});
				map.addOverlay(midMarker);
			}
		}
		
		html += '<td valign="top"><b>' + startGeoCode.address + '</b> to<br> <b>' + endGeoCode.address + '</b><br>'+routeDistance.html+ ' (' + routeDuration.html +  ')</td>';
		html += '</table></div>';

		var numSteps = route.getNumSteps();
		html += '<table cellspacing="0" cellpadding="0" id="routeTable_'+r+'" style="display:none" width="100%">';
		for (var s = 0 ; s < numSteps ; s++ ) {
			var step = route.getStep(s);
			var stepLatLng = step.getLatLng();
			bounds.extend(stepLatLng);

			var stepPolylineIndex = step.getPolylineIndex();
			var stepDescriptionHTML = step.getDescriptionHtml();
			var re = new RegExp(pattern,'g');
			stepDescriptionHTML = stepDescriptionHTML.replace(re,'<b style="color:#CA0039">$1</b>')
			var stepDistance = step.getDistance();
			var stepDuration = step.getDuration();
			html += '<tr class="stepRow" onclick="showStep('+r+','+s+')"><td>&nbsp;&nbsp;' + (s+1) + '.</td><td> ' + stepDescriptionHTML + '</td><td>' + stepDistance.html + '</td></tr>';
		}
		html += '</table>';
	}
	
	directionsInfoDiv.innerHTML = html;
	loading = false;
	polyline = dirObj.getPolyline();

	map.setCenter(bounds.getCenter(map), map.getBoundsZoomLevel(bounds)); 
}


function copyPolyline(p) {
var str = '';
	pLinePoints = Array();
	for (var n = 0 ; n < p.getVertexCount() ; n++ ) {
		pLinePoints.push(p.getVertex(n));
	}
	var pLine = new GPolyline(pLinePoints,'#F7098A');
	return pLine;
}


function addDragMarker(placemark) {
	markerDragging = false;

	var point = new GLatLng(placemark.Point.coordinates[1],placemark.Point.coordinates[0])

	dragMarker = new GMarker(point, {icon:redIcon8,draggable:true,bouncy:false});
	GEvent.addListener(dragMarker, 'dragend', function(){
		var oDriveVia = document.getElementById('driveVia');
		if (oDriveVia.value) {
			oDriveVia.value += ';\n';
		}
		oDriveVia.value += dragMarker.getPoint().lat().toFixed(5) + ',' + dragMarker.getPoint().lng().toFixed(5);
		if (dragMarker) {
			map.removeOverlay(dragMarker);
		}
		getDirections();

	});

	GEvent.addListener(dragMarker, 'dragstart', function(){
		markerDragging = true;
	});

	map.addOverlay(dragMarker);
	dragMarker.hide();
}





function getNearestVertex(mouseLatLng) {
	if (markerDragging) {
		return;
	}
	if (!dragMarker) {
		return;
	}

	if (pLinePoints.length > 1){
		var bounds = map.getBounds();
		var SW = bounds.getSouthWest();
		var NE = bounds.getNorthEast();
		var diag = SW.distanceFrom(NE);
		threshold = diag / 100;
		var minDist = 9999999999;
		var intermediateIndex = Math.round(pLinePoints.length / 100);

		for (var n = 0 ; n < pLinePoints.length-intermediateIndex ; n+= intermediateIndex ) {
			if (mouseLatLng.distanceFrom(pLinePoints[n]) < minDist) {
				minDist = mouseLatLng.distanceFrom(pLinePoints[n]);
				if (minDist < threshold) {
					dragMarker.show();
					dragMarker.setLatLng(pLinePoints[n]);
				}
				else {
					dragMarker.hide();
				}
			}
		}
	}
}




function onDirectionsError() {
	loading = false;
	directionsInfoDiv.innerHTML = 'Error: ' + dirObj.getStatus().code;
}

function toggleSteps(routeNo) {
	oRouteTable = document.getElementById('routeTable_' + routeNo);
	oRouteTable.style.display = oRouteTable.style.display == 'none' ? '' : 'none';
}

function showStep(r,s) {
	map.closeInfoWindow();
	var step = dirObj.getRoute(r).getStep(s);
	var stepLatLng = step.getLatLng();
	var stepDescriptionHTML = step.getDescriptionHtml();
	var re = new RegExp(pattern,'g');
	stepDescriptionHTML = stepDescriptionHTML.replace(re,'<b style="color:#CA0039">$1</b>')
	var stepDistance = step.getDistance();
	var stepDuration = step.getDuration();

	var infoHTML = '<div id="tab1" class="bubble">';
	infoHTML += '<table>';
	infoHTML += '<tr class="stepRow"><td>&nbsp;&nbsp;' + (s+1) + '.</td><td> ' + stepDescriptionHTML + '</td><td>' + stepDistance.html + '</td></tr>';
	infoHTML += '<tr class="stepRow"><td>&nbsp;&nbsp;</td colspan="2"><td> ' + stepLatLng + '</td></tr>';
	infoHTML += '</table>';
	infoHTML += '</div>';



	var tab1 = new GInfoWindowTab("Location", '<div id="detailmap"></div>');
	var tab2 = new GInfoWindowTab("Info", infoHTML);
	var infoTabs = [tab1,tab2];

	map.openInfoWindowTabsHtml(stepLatLng,infoTabs);

//	detailmap = null;
// Minimap for driving directions
	var dMapDiv = document.getElementById("detailmap");
	detailmap = new GMap2(dMapDiv);
	detailmap.setCenter(stepLatLng,15);

	detailmap.addOverlay(pLine);

	var CopyrightDiv = dMapDiv.firstChild.nextSibling;
	var CopyrightImg = dMapDiv.firstChild.nextSibling.nextSibling;
	CopyrightDiv.style.display = "none"; 
	CopyrightImg.style.display = "none"; 

	detailmap.addControl(new GSmallMapControl());


}




//]]>
</script>
</head>





<body>


<table cellspacing="0" cellpadding="0" id="outerTable">
<colgroup>
	<col width="200">
	<col id="mapCol">
	<col width="260">
</colgroup>



	<tr>
	
	
			<td valign="top" rowspan="2">
<!-- Begin Info container -->
			<div id="infoContainer">
				<div id="directions_info"></div>
			</div>
<!-- End Info container -->
		</td>

		<td valign="top" id="mapCell">
			<div id="outerMapDiv">
				<div id="mapDiv"></div>
			</div>
		</td>
		<td valign="top" rowspan="2" align="center">
			<div id="buttonContainer">
				<div class="buttonB" onclick="rmOverlays()">Clear Overlays</div>
				<div class="buttonB" onclick="doUnload(1)">Map reset</div>
			</div>
<!-- Begin controls container -->
			<div  id="controlsContainer">
				<div id="directions_controls">
					<table cellspacing="0" cellpadding="0" id="directionsFormTable">
						<tr>
							<td>
								From:
							</td>
							<td>
								<input id="driveFrom" value="Toruñ"><br>
							</td>
						</tr>
						<tr>
							<td>
								To: 
							</td>
							<td>
								<input id="driveTo" value="Bydgoszcz"><br>
							</td>
						</tr>
						<tr>
							<td>
								Via: 
							</td>
							<td>
								<textarea id="driveVia" rows="2" cols="22"></textarea><br>
							</td>
						</tr>
						<tr>
							<td>
								Lang: 
							</td>
							<td>
								<select id="locale" name="locale">
									<option value="pl" selected>Polski</option>
									<option value="en">English</option>

								</select><br>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="button" value="GO" onclick="getDirections()">
							</td>
						</tr>
					</table>
				</div>
			</div>
<!-- End controls container -->
		</td>
		
	</tr>
</table>

<div id="loadingMessage" style="display:none;">Loading ...</div>





<script>
	window.onload = load;
	window.onunload = unload;
	window.onresize = resizePage;

</script>


</body>
</html>