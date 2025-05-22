
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

    leafletMap = L.map(container).setView([map_params.lat, map_params.lon], map_params.zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(leafletMap);

    directionsInfoDiv = document.getElementById("directions_info");

    leafletMap.on('click', mapClick);
    routingControl = null;
}

function mapClick(event) {

    var clickPosition = event.latlng;

    if (!startMarker) {
        var oDriveFrom = document.getElementById('driveFrom');
        startMarker = L.marker(clickPosition, {
            draggable: true,
            icon: L.icon({
                iconUrl: 'https://maps.gstatic.com/mapfiles/markers2/marker_greenA.png',
                iconSize: [20, 32]
            })
        }).addTo(leafletMap);

        startMarker.on('dragend', function(event) {
            var position = event.target.getLatLng();
            oDriveFrom.value = position.lat.toFixed(6) + ',' + position.lng.toFixed(6);
        });

        oDriveFrom.value = clickPosition.lat.toFixed(6) + ',' + clickPosition.lng.toFixed(6);
        return;
    } else if (!endMarker) {
        var oDriveTo = document.getElementById('driveTo');
        endMarker = L.marker(clickPosition, {
            draggable: true,
            icon: L.icon({
                iconUrl: 'https://maps.gstatic.com/mapfiles/markers2/marker_greenB.png',
                iconSize: [20, 32]
            })
        }).addTo(leafletMap);

        endMarker.on('dragend', function(event) {
            var position = event.target.getLatLng();
            oDriveTo.value = position.lat.toFixed(6) + ',' + position.lng.toFixed(6);
        });

        oDriveTo.value = clickPosition.lat.toFixed(6) + ',' + clickPosition.lng.toFixed(6);
        return;
    }
}

function indicateLoading(show) {
    loading = show;
    displayLoadingMsg();
}

function displayLoadingMsg() {
    var oLMsg = document.getElementById('loadingMessage');

    if (loading) {
        oLMsg.style.display = '';
        oLMsg.style.left = container.offsetLeft + (container.clientWidth / 2) - (oLMsg.clientWidth / 2) + 'px';
        oLMsg.style.top = container.offsetTop + (container.clientHeight / 2) - (oLMsg.clientHeight / 2) + 'px';
        oLMsg.style.opacity = '0.7';
    } else {
        oLMsg.style.display = 'none';
    }
}

function cleanManualMarkers() {
    // Clean manually placed markers (if present)
    if (startMarker) {
        leafletMap.removeLayer(startMarker);
        startMarker = null;
    }

    if (endMarker) {
        leafletMap.removeLayer(endMarker);
        endMarker = null;
    }
}

var viaWaypoints = [];

function getCoordinatesFromAddress(address) {
    return fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => data.length > 0 ? L.latLng(parseFloat(data[0].lat), parseFloat(data[0].lon)) : null)
        .catch(() => null);
}

function getDirections() {

    const oDriveFrom = document.getElementById('driveFrom').value;
    const oDriveTo = document.getElementById('driveTo').value;
    const oDriveVia = document.getElementById('driveVia').value;

    directionsInfoDiv.innerHTML = '';

    if (oDriveFrom && oDriveTo) {
        const promises = [];
        const waypointsList = [];

        const addWaypoint = (input, index) => {
            const coords = input.split(',');
            if (coords.length === 2) {
                waypointsList[index] = L.latLng(parseFloat(coords[0]), parseFloat(coords[1]));
            } else {
                promises.push(getCoordinatesFromAddress(input).then(latLng => {
                    if (latLng) {
                        waypointsList[index] = latLng;
                    }
                }));
            }
        };

        let index = 0;
        addWaypoint(oDriveFrom, index++);

        if (oDriveVia) {
            const viaSteps = oDriveVia.split(/[\n\r;,\s]+/).filter(step => step.trim() !== '');
            viaSteps.forEach(step => addWaypoint(step.trim(), index++));
        }

        addWaypoint(oDriveTo, index++);

        Promise.all(promises).then(() => {
            // Sort waypoints by their original index to maintain order
            viaWaypoints = waypointsList.filter(Boolean);

            if (viaWaypoints.length < 2) {
                directionsInfoDiv.innerHTML = 'Insufficient waypoints to calculate a route.';
                return;
            }

            calculateRoute(viaWaypoints);
        }).catch(error => {
            directionsInfoDiv.innerHTML = `Error processing waypoints: ${error.message}`;
        });
    } else {
        directionsInfoDiv.innerHTML = 'Please provide both starting and ending locations.';
    }
}


function calculateRoute() {
    if (routingControl) leafletMap.removeControl(routingControl);

    indicateLoading(true);

    routingControl = L.Routing.control({
        waypoints: viaWaypoints,
        routeWhileDragging: true,
        showAlternatives: false,
        router: L.Routing.osrmv1({
            serviceUrl: 'https://router.project-osrm.org/route/v1',
            language: 'pl'
        }),
        createMarker: (i, waypoint, n) => L.marker(waypoint.latLng, {
            draggable: true,
            icon: L.icon({
                iconUrl: i === 0
                    ? 'https://maps.gstatic.com/mapfiles/markers2/marker_greenA.png'
                    : (i === n - 1
                        ? 'https://maps.gstatic.com/mapfiles/markers2/marker_greenB.png'
                        : 'https://maps.gstatic.com/mapfiles/markers2/boost-marker-mapview.png'),
                iconSize: [20, 32]
            })
        })
    }).addTo(leafletMap);

    routingControl.on('routesfound', function (e) {
        indicateLoading(false);
        const route = e.routes[0];

        const totalTimeInMinutes = route.summary.totalTime / 60;
        const hours = Math.floor(totalTimeInMinutes / 60);
        const minutes = Math.round(totalTimeInMinutes % 60);
        const formattedTime = `${hours > 0 ? hours + ' godz. ' : ''}${minutes} min`;

        setTimeout(function() {
            var instructions = document.querySelector('.leaflet-routing-alt');
            if (instructions) directionsInfoDiv.innerHTML = instructions.innerHTML;
        }, 500);

        saveRouteData(e);
        cleanManualMarkers();
    });

    routingControl.on('routingerror', function () {
        indicateLoading(false);
        directionsInfoDiv.innerHTML = 'Error calculating directions. Please try again.';
    });
}

function saveRouteData(response) {
    var points = [];
    var total_distance = 0;

    var coordinates = response.routes[0].coordinates;

    coordinates.forEach(coord => {
        var lat = coord.lat.toFixed(6);
        var lng = coord.lng.toFixed(6);

        var exists = points.some(function(existingPoint) {
            var [existingLat, existingLng] = existingPoint.split(',');
            return Math.abs(existingLat - lat) < 0.006 && Math.abs(existingLng - lng) < 0.006;
        });

        if (!exists) {
            points.push(lat + ',' + lng);
        }
    });

    document.forms['myroute_form'].route_points.value = points.join(' ');

    total_distance = response.routes[0].summary.totalDistance;
    document.forms['myroute_form'].distance.value = (total_distance / 1000).toFixed(1);
}

function removeResults() {
    if (routingControl) {
        leafletMap.removeControl(routingControl);
        routingControl = null;
    }
    cleanManualMarkers();
    document.forms['myroute_form'].route_points.value = '';
    document.forms['myroute_form'].distance.value = '';
    directionsInfoDiv.innerHTML = '';
}

function resetMap() {
    removeResults();
    document.getElementById('driveFrom').value = '';
    document.getElementById('driveTo').value = '';
    document.getElementById('driveVia').value = '';
    leafletMap.setView([map_params.lat, map_params.lon], map_params.zoom);
}
