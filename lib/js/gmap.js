
var map;
var dirObj;
var dirDisplay;
var container;
var loading = false;
var directionsInfoDiv;
var startMarker;
var endMarker;

/* Requires:
var map_params = {
    lat: latitude,      // from config
    lon: longitude,     // from config
    zoom: zoom_level,   // from config
};
*/

// Defaults --------------------------------------------
//var zoom = 6;  // TODO - country specific config
//var centerPoint = new google.maps.LatLng(52.0,19.0);  // TODO - country specific config

function load() {
    container = document.getElementById("mapDiv");

    var centerPoint = new google.maps.LatLng(map_params.lat,map_params.lon);
    map = new google.maps.Map(container, {
        center: centerPoint, zoom: map_params.zoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scaleControl: true,
        panControl: false,
        draggableCursor: 'crosshair'
    });

    directionsInfoDiv = document.getElementById("directions_info");
    dirObj = new google.maps.DirectionsService();
    dirDisplay = new google.maps.DirectionsRenderer({ draggable: true });

    google.maps.event.addListener(map, 'click', mapClick);
    google.maps.event.addListener(dirDisplay, 'directions_changed', function() {
        // Save new route data after dragging points
        saveRouteData(dirDisplay.directions);
    });
}

function mapClick(event) {

    var clickPosition = event.latLng;

    if (!startMarker) {
        var oDriveFrom = document.getElementById('driveFrom');
        startMarker = new google.maps.Marker({
            position: clickPosition,
            draggable: true,
            icon: 'http://maps.gstatic.com/mapfiles/markers2/marker_greenA.png',
            map: map
        });

        google.maps.event.addListener(startMarker, 'dragend', function(event) {
            oDriveFrom.value = event.latLng.lat().toFixed(6) + ',' + event.latLng.lng().toFixed(6);
        });

        oDriveFrom.value = clickPosition.lat().toFixed(6) + ',' + clickPosition.lng().toFixed(6);
        return;
    }
    else if (!endMarker) {
        var oDriveTo = document.getElementById('driveTo');
        endMarker = new google.maps.Marker({
            position: clickPosition,
            draggable: true,
            icon: 'http://maps.gstatic.com/mapfiles/markers2/marker_greenB.png',
            map: map
        });

        google.maps.event.addListener(endMarker, 'dragend', function(event) {
            oDriveTo.value = event.latLng.lat().toFixed(6) + ',' + event.latLng.lng().toFixed(6);
        });

        oDriveTo.value = clickPosition.lat().toFixed(6) + ',' + clickPosition.lng().toFixed(6);
        return;
    }
}

function indicateLoading(show) {
    loading = show;
    displayLoadingMsg();
}

function displayLoadingMsg() {
    var oLMsg = document.getElementById('loadingMessage');

    if (loading){
        oLMsg.style.display = '';
        oLMsg.style.left = container.offsetLeft + (container.clientWidth / 2) - (oLMsg.clientWidth / 2) + 'px';
        oLMsg.style.top = container.offsetTop + (container.clientHeight / 2) - (oLMsg.clientHeight / 2) + 'px';
        oLMsg.style.filter="alpha(opacity=70)";
    }
    else {
        oLMsg.style.display = 'none';
    }
}

function cleanManualMarkers() {
    // Clean manually placed markers (if present)
    if (startMarker) {
        startMarker.setMap(null);
        startMarker = null;
    }

    if (endMarker) {
        endMarker.setMap(null);
        endMarker = null;
    }
}

function getDirections() {
    var oDriveFrom = document.getElementById('driveFrom');
    var oDriveTo = document.getElementById('driveTo');
    var oDriveVia = document.getElementById('driveVia');
    var viaWaypoints = [];

    directionsInfoDiv.innerHTML = '';

    if (oDriveFrom.value && oDriveTo.value) {
        if (oDriveVia.value) {
            var viaValue = oDriveVia.value.replace(/[\n\r]+/,"");
            var viaSteps = viaValue.split(';');
            for (var n = 0 ; n < viaSteps.length ; n++ ) {
                viaWaypoints.push({ location: viaSteps[n] });
            }
        }

        indicateLoading(true);

        var request = {
            origin: oDriveFrom.value,
            destination: oDriveTo.value,
            waypoints: viaWaypoints,
            travelMode: google.maps.DirectionsTravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.METRIC, // TODO - internationalization
            region: "PL" // TODO - internationalization
        };
        dirObj.route(request, function(response, status) {
            indicateLoading(false);
            if (status == google.maps.DirectionsStatus.OK) {
                onDirectionsLoad(response);
            } else {
                onDirectionsError(status);
            }
        });
    }
}

function saveRouteData(response) {
    var points = [];
    var o_path = response.routes[0].overview_path;
    for (var n = 0; n < o_path.length; n++) {
        points.push(o_path[n].lat().toFixed(6) + ',' + o_path[n].lng().toFixed(6));
    }
    document.forms['myroute_form'].route_points.value = points.join(' ');

    var total_distance = 0;
    for (n = 0; n < response.routes[0].legs.length; n++) {
        total_distance += response.routes[0].legs[n].distance.value;
    }
    // Keep total distance in km (rounding to 0.1)
    document.forms['myroute_form'].distance.value = (total_distance / 1000).toFixed(1);
}

function onDirectionsLoad(response) {
    dirDisplay.setDirections(response); // This will raise 'directions_changed' event on dirDisplay
    dirDisplay.setMap(map);
    dirDisplay.setPanel(directionsInfoDiv);
    cleanManualMarkers();
}

function onDirectionsError(status) {
    directionsInfoDiv.innerHTML = 'Error: ' + status;
}

function removeResults() {
    dirDisplay.setMap(null);
    dirDisplay.setPanel(null);
    cleanManualMarkers();
    document.forms['myroute_form'].route_points.value = '';
    document.forms['myroute_form'].distance.value = '';
}

function resetMap() {
    removeResults();
    document.getElementById('driveFrom').value = '';
    document.getElementById('driveTo').value = '';
    document.getElementById('driveVia').value = '';
    map.setCenter(centerPoint);
    map.setZoom(zoom);
}
