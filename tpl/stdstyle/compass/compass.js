var map = L.map('mapid').fitWorld();

// Initialise base map layers
var osmStandard = L
		.tileLayer(
				'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
				{
					attribution : '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
					maxZoom : 19
				})

var wikimedia = L
		.tileLayer(
				'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png',
				{
					attribution : '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors. Base map: <a href="https://mediawiki.org/wiki/Maps#Production_maps_cluster">wikimedia maps</a>',
					maxZoom : 18
				})

var toner = L
		.tileLayer(
				'http://{s}.tile.stamen.com/toner/{z}/{x}/{y}.png',
				{
					attribution : '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors. Base map: <a href="http://maps.stamen.com/toner/">toner by stamen</a>',
					maxZoom : 17
				})
var cartodb = L
		.tileLayer(
				'http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png',
				{
					attribution : '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors, Base map: <a href="https://cartodb.com/attributions">CartoDB</a>'
				});

// Initialise overlay layers
var hiking = L.tileLayer('http://tile.lonvia.de/hiking/{z}/{x}/{y}.png', {
	maxZoom : 17
})

var cycling = L.tileLayer(
		'https://tile.waymarkedtrails.org/cycling/{z}/{x}/{y}.png', {
			maxZoom : 17
		})

// Add our preferred default layer choice directly to the map
map.addLayer(osmStandard);

// Name the layers
var baseMaps = {
	'OpenStreetMap' : osmStandard,
	'Wikimedia maps' : wikimedia,
	'Stamen toner' : toner,
	'CartoDB' : cartodb
};

var overlayMaps = {
	'Hiking routes' : hiking,
	'Cycling routes' : cycling
};
var layerControl = L.control.layers(baseMaps, overlayMaps).addTo(map);
var scaleControl = L.control.scale().addTo(map);

L.Control.InfoBox = L.Control
		.extend({
			onAdd : function(map) {
				var div = L.DomUtil.create('div', 'infoBox');
				div.id = 'infoBoxDiv';
				div.innerHTML = '<div id="infoBoxTxt"></div><button id="btnTarget"><img src="/tpl/stdstyle/compass/target.svg" alt="" id="imgTarget"></button><br><img src="/tpl/stdstyle/compass/pointer.svg" alt="" id="imgPointer"><p id="debug"></p>';
				return div;
			},
			onRemove : function(map) {
			}
		});

L.control.infobox = function(opts) {
	return new L.Control.InfoBox(opts);
}

var infoBox = new L.control.infobox({
	position : 'bottomright'
}).addTo(map);

var currPosition, currAccuracy, lineToCache, cacheCoords, translDistance, translBearing;
var updatePosition = true;
var firstTime = true;
var lineToCacheOptions = {
	color : 'blue',
	weight : 2,
	opacity : 0.3
};

map.on('locationfound', onLocationFound);
map.on('locationerror', onLocationError);
map.on('dragstart', disablePositionUpdate);

window.setInterval(locate, 1000);

function disablePositionUpdate(e) {
	updatePosition = false;
	document.getElementById("imgTarget").classList.add("grayed");
}

function onLocationFound(e) {
	if (updatePosition == true) {
		map.panTo(e.latlng, {animate: true});
		if (firstTime == true) {
			map.fitBounds([e.latlng, cacheCoords]);
			map.zoomOut(1);
			firstTime = false;
		}
	}

	if (currPosition) {
		map.removeLayer(currPosition);
		map.removeLayer(currAccuracy);
		map.removeLayer(lineToCache);
	}

	var radius = e.accuracy / 2;
	var distance = Math.round(cacheCoords.distanceTo(e.latlng));
	var direction = Math.round(bearing(e.latlng, cacheCoords));
	currAccuracy = L.circle(e.latlng, {
		radius : radius,
		opacity : 0.2
	}).addTo(map);
	currPosition = L.circleMarker(e.latlng, {
		radius : 3
	}).addTo(map);
	lineToCache = L.polyline([e.latlng, cacheCoords], lineToCacheOptions)
			.addTo(map);
	document.getElementById("infoBoxTxt").innerHTML = translDistance + ": "
			+ distance + "m<br>" + translBearing + ": " + direction + "°";
}

function onLocationError(e) {
	console.log(e.message);
}

document.getElementById("btnTarget").addEventListener("click", function(event) {
	if (updatePosition == true) {
		updatePosition = false;
		document.getElementById("imgTarget").classList.add("grayed");
	} else {
		updatePosition = true;
		document.getElementById("imgTarget").classList.remove("grayed");
		locate();
	}
}, false);

function locate() {
		map.locate({
			setView : false,
			enableHighAccuracy : true
		});
}

function bearing(latlng1, latlng2) {
	var rad = Math.PI / 180, lat1 = latlng1.lat * rad, lat2 = latlng2.lat * rad, lon1 = latlng1.lng
			* rad, lon2 = latlng2.lng * rad, y = Math.sin(lon2 - lon1)
			* Math.cos(lat2), x = Math.cos(lat1) * Math.sin(lat2)
			- Math.sin(lat1) * Math.cos(lat2) * Math.cos(lon2 - lon1);
	var bearing = ((Math.atan2(y, x) * 180 / Math.PI) + 360) % 360;
	return bearing;
}

var compass = document.getElementById('imgPointer');

document.addEventListener("DOMContentLoaded", function(event) {
	if (window.DeviceOrientationEvent) {
		window.addEventListener('deviceorientation', function(eventData) {
			// var dir = eventData.webkitCompassHeading;
			var dir = eventData.alpha;
			deviceOrientationHandler(dir);
		}, false);
	} else {
		console.log("Device does not support orientation :(");
	}

	function deviceOrientationHandler(dir) {
		console.log(Math.ceil(dir));
		document.getElementById("debug").innerHTML = Math.ceil(dir);

		compass.style.Transform = 'rotate(' + dir + 'deg)';
		compass.style.WebkitTransform = 'rotate(' + dir + 'deg)';
		// Rotation is reversed for FF
		compass.style.MozTransform = 'rotate(' + dir + 'deg)';

		// Rotate the disc of the compass.
		// Laat de kompas schijf draaien.
		// var compassDisc = document.getElementById("compassDiscImg");
		// compassDisc.style.webkitTransform = "rotate("+ dir +"deg)";
		// compassDisc.style.MozTransform = "rotate("+ dir +"deg)";
		// compassDisc.style.transform = "rotate("+ dir +"deg)";
	}

});