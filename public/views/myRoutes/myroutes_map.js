
var TILE_SIZE = 256;

function bound(value, opt_min, opt_max) {
    if (opt_min != null) value = Math.max(value, opt_min);
    if (opt_max != null) value = Math.min(value, opt_max);
    return value;
}

function degreesToRadians(deg) {
    return deg * (Math.PI / 180);
}

function radiansToDegrees(rad) {
    return rad / (Math.PI / 180);
}

function MercatorProjection() {
    this.pixelOrigin_ = { x: TILE_SIZE / 2, y: TILE_SIZE / 2 };
    this.pixelsPerLonDegree_ = TILE_SIZE / 360;
    this.pixelsPerLonRadian_ = TILE_SIZE / (2 * Math.PI);
}

MercatorProjection.prototype.fromLatLngToPoint = function(latLng) {
    var me = this;
    var point = {};
    var origin = me.pixelOrigin_;

    point.x = origin.x + latLng.lng * me.pixelsPerLonDegree_;

    // NOTE(appleton): Truncating to 0.9999 effectively limits latitude to
    // 89.189.  This is about a third of a tile past the edge of the world
    // tile.
    var siny = bound(Math.sin(degreesToRadians(latLng.lat)), -0.9999, 0.9999);
    point.y = origin.y + 0.5 * Math.log((1 + siny) / (1 - siny)) * -me.pixelsPerLonRadian_;
    return point;
};

MercatorProjection.prototype.fromPointToLatLng = function(point) {
    var me = this;
    var origin = me.pixelOrigin_;
    var lng = (point.x - origin.x) / me.pixelsPerLonDegree_;
    var latRadians = (point.y - origin.y) / -me.pixelsPerLonRadian_;
    var lat = radiansToDegrees(2 * Math.atan(Math.exp(latRadians)) - Math.PI / 2);
    return { lat: lat, lng: lng };
};

function decodePath(encoded) {
    var poly = [];
    var index = 0, len = encoded.length;
    var lat = 0, lng = 0;

    while (index < len) {
        var b, shift = 0, result = 0;
        do {
            b = encoded.charCodeAt(index++) - 63;
            result |= (b & 0x1f) << shift;
            shift += 5;
        } while (b >= 0x20);
        var dlat = ((result & 1) ? ~(result >> 1) : (result >> 1));
        lat += dlat;

        shift = 0;
        result = 0;
        do {
            b = encoded.charCodeAt(index++) - 63;
            result |= (b & 0x1f) << shift;
            shift += 5;
        } while (b >= 0x20);
        var dlng = ((result & 1) ? ~(result >> 1) : (result >> 1));
        lng += dlng;

        poly.push({ lat: lat / 1e5, lng: lng / 1e5 });
    }
    return poly;
}

function computeOffset(from, distance, heading) {
    var R = 6371e3; // Earth's radius in meters
    var bearing = degreesToRadians(heading);
    var lat1 = degreesToRadians(from.lat);
    var lon1 = degreesToRadians(from.lng);

    var lat2 = Math.asin(Math.sin(lat1) * Math.cos(distance / R) +
        Math.cos(lat1) * Math.sin(distance / R) * Math.cos(bearing));
    var lon2 = lon1 + Math.atan2(Math.sin(bearing) * Math.sin(distance / R) * Math.cos(lat1),
        Math.cos(distance / R) - Math.sin(lat1) * Math.sin(lat2));

    return { lat: radiansToDegrees(lat2), lng: radiansToDegrees(lon2) };
}

var leafletMap = null;

function load(searchRadius, encodedPath) {
    leafletMap = L.map('map').setView([0, 0], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(leafletMap);

    var decodedPath = decodePath(encodedPath);
    var mercProj = new MercatorProjection();

    // Calculate buffer size in Mercator projection for specified search radius
    var p1 = decodedPath[0];
    var p2 = computeOffset(p1, searchRadius * 1000, 0);

    var bufSize = mercProj.fromLatLngToPoint(p1).y - mercProj.fromLatLngToPoint(p2).y;

    var geometryFactory = new jsts.geom.GeometryFactory();
    var coords = decodedPath.map(point => {
        var wc = mercProj.fromLatLngToPoint(point);
        return new jsts.geom.Coordinate(wc.x, wc.y);
    });
    var lineString = geometryFactory.createLineString(coords);

    // Calculate buffer
    var buffer = lineString.buffer(bufSize);

    // Convert back from Mercator to lat/lon
    var bufferLatLngs = buffer.shell.points.map(point => {
        var latLng = mercProj.fromPointToLatLng({ x: point.x, y: point.y });
        return [latLng.lat, latLng.lng];
    });

    var latLngs = decodedPath.map(point => [point.lat, point.lng]);
    L.polyline(latLngs, { color: 'blue', weight: 5 }).addTo(leafletMap);

    L.polygon(bufferLatLngs, {
        color: '#ccc',
        fillColor: '#ccc',
        fillOpacity: 0.5
    }).addTo(leafletMap);

    var bounds = L.latLngBounds(latLngs);
    leafletMap.fitBounds(bounds);
}

