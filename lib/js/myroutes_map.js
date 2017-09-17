// Simple implementation of spherical Mercator projection based on Google Maps example
// https://google-developers.appspot.com/maps/documentation/javascript/examples/map-coordinates

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
    this.pixelOrigin_ = new google.maps.Point(TILE_SIZE / 2, TILE_SIZE / 2);
    this.pixelsPerLonDegree_ = TILE_SIZE / 360;
    this.pixelsPerLonRadian_ = TILE_SIZE / (2 * Math.PI);
}

MercatorProjection.prototype.fromLatLngToPoint = function(latLng, opt_point) {
    var me = this;
    var point = opt_point || new google.maps.Point(0, 0);
    var origin = me.pixelOrigin_;

    point.x = origin.x + latLng.lng() * me.pixelsPerLonDegree_;

    // NOTE(appleton): Truncating to 0.9999 effectively limits latitude to
    // 89.189.  This is about a third of a tile past the edge of the world
    // tile.
    var siny = bound(Math.sin(degreesToRadians(latLng.lat())), -0.9999, 0.9999);
    point.y = origin.y + 0.5 * Math.log((1 + siny) / (1 - siny)) * -me.pixelsPerLonRadian_;
    return point;
};

MercatorProjection.prototype.fromPointToLatLng = function(point) {
    var me = this;
    var origin = me.pixelOrigin_;
    var lng = (point.x - origin.x) / me.pixelsPerLonDegree_;
    var latRadians = (point.y - origin.y) / -me.pixelsPerLonRadian_;
    var lat = radiansToDegrees(2 * Math.atan(Math.exp(latRadians)) - Math.PI / 2);
    return new google.maps.LatLng(lat, lng);
};


// Douglas-Pecker line simplification algorithm
// Ported from JTS Topology Suite (Java class DouglasPeuckerLineSimplifier)
// http://www.vividsolutions.com/jts/JTSHome.htm
// http://www.vividsolutions.com/jts/javadoc/com/vividsolutions/jts/simplify/DouglasPeuckerLineSimplifier.html

function DouglasPeuckerLineSimplifier (inputLine) {
    this.pts = inputLine;
    this.seg = new jsts.geom.LineSegment();
}

DouglasPeuckerLineSimplifier.simplify = function(inputLine, distanceTol) {
    var simp = new DouglasPeuckerLineSimplifier(inputLine);
    return simp.simplify(distanceTol);
};

DouglasPeuckerLineSimplifier.prototype.simplify = function(distanceTol) {
    var i;

    this.usePt = [];
    for (i = 0; i < this.pts.length; i++) {
        this.usePt.push(true);
    }
    this.distanceTolerance = distanceTol;

    this.simplifySection(0, this.pts.length - 1);

    coordList = [];
    for (i = 0; i < this.pts.length; i++) {
        if (this.usePt[i]) {
            coordList.push(this.pts[i]);
        }
    }

    return coordList;
};

DouglasPeuckerLineSimplifier.prototype.simplifySection = function (i, j) {

    if ((i+1) == j) {
        return;
    }

    this.seg.p0 = this.pts[i];
    this.seg.p1 = this.pts[j];

    var maxDistance = -1.0;
    var maxIndex = i;
    for (var k = i + 1; k < j; k++) {
        var distance = this.seg.distance(this.pts[k]);
        if (distance > maxDistance) {
            maxDistance = distance;
            maxIndex = k;
        }
    }

    if (maxDistance <= this.distanceTolerance) {
        for(k = i + 1; k < j; k++) {
            this.usePt[k] = false;
        }
    }
    else {
        this.simplifySection(i, maxIndex);
        this.simplifySection(maxIndex, j);
    }
};


var map = null;

function load(searchRadius, encodedPath) {

    map = new google.maps.Map(document.getElementById("map"), {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      scaleControl: true,
      gestureHandling: 'greedy', //disable ctrl+ zooming
    });

    var decodedPath = google.maps.geometry.encoding.decodePath(encodedPath);

    var mercProj = new MercatorProjection();

    // Calculate buffer size in Mercator projection for specified search radius
    var p1 = decodedPath[0];
    var p2 = google.maps.geometry.spherical.computeOffset(p1, searchRadius * 1000, 0);
    var bufSize = mercProj.fromLatLngToPoint(p1).y - mercProj.fromLatLngToPoint(p2).y;

    // Convert path coordinates to Mercator
    var coords = [];
    for (var i = 0; i < decodedPath.length; i++) {
        var wc = mercProj.fromLatLngToPoint(decodedPath[i]);
        coords.push(new jsts.geom.Coordinate(wc.x, wc.y));
    }

    // Simplify path before calculating the buffer, keep 5% (1/20) tolerance of the buffer size.
    // This will make buffer calculation much faster.
    var pathSimplified = DouglasPeuckerLineSimplifier.simplify(coords, bufSize / 20.0);

    // Calculate buffer
    var buffer = new jsts.geom.GeometryFactory().createLineString(pathSimplified).buffer(bufSize);

    // Convert back from Mercator to lat/lon
    var bufferLatLng = [];
    var bounds = new google.maps.LatLngBounds();
    var bufpoints = buffer.shell.points;
    for (i = 0; i < bufpoints.length; i++) {
        var bpLatLng = mercProj.fromPointToLatLng(bufpoints[i]);
        bufferLatLng.push(bpLatLng);
        bounds.extend(bpLatLng);
    }

    var route = new google.maps.Polyline({
        path: decodedPath,
        strokeColor: "#00C",
        strokeOpacity: 0.5,
        strokeWeight: 5,
        map: map
    });

    var routeBuffer = new google.maps.Polygon({
        path: bufferLatLng,
        fillColor: "#ccc",
        fillOpacity: 0.5,
        strokeWeight: 0,
        map: map
    });

    map.fitBounds(bounds);

}
