/**
 * A super class, gathering common methods used in OKAPI-based style markers
 */
function OkapiBasedMarker(map, ocData) {
    OCZoomCachedMarker.call(this, map, ocData);
    this.zoomRanges = {
        'tiny': [0, 8],
        'medium': [9, 13],
        'large': [14, 1000],
    };
}

OkapiBasedMarker.prototype = Object.create(OCZoomCachedMarker.prototype);

OkapiBasedMarker.prototype.constructor = OkapiBasedMarker;

/**
 * Returns true if caption should be included into computed style, basing on
 * zoom value and surrounding visible features proximity.
 * The result computing is based on okapi source code with some minor
 * improvements.
 */
OkapiBasedMarker.prototype.canShowCaption = function(zoom) {
    var result = (zoom >= 5);
    if (result) {
        var ocLayers = this.map.getLayerGroup().getLayersArray().filter(
            function(layer) {
                return OcLayerServices.isOcInternalCommonLayer(layer);
            }
        );
        if (typeof(ocLayers.length) != "undefined" && ocLayers.length) {
            var coords = this.feature.getGeometry().getCoordinates();
            var featurePx = this.map.getPixelFromCoordinate(coords);
            var closestPx;
            var closestCandidate;
            var minDist;
            var self = this;
            ocLayers.forEach(function(ocLayer) {
                var s = ocLayer.getSource();
                var candidate;
                if (typeof(s["getClosestFeatureToCoordinate"]) === "function") {
                    candidate = s.getClosestFeatureToCoordinate(
                        coords, function(f) {
                            var fc = f.getGeometry().getCoordinates();
                            return (fc[0] != coords[0] || fc[1] != coords[1]);
                        }
                    );
                }
                if (candidate) {
                    var candidatePx = self.map.getPixelFromCoordinate(
                        candidate.getGeometry().getCoordinates()
                    );
                    var candidateDist =
                        Math.sqrt(
                            Math.pow((featurePx[0] - candidatePx[0]), 2)
                            +
                            Math.pow((featurePx[1] - candidatePx[1]), 2)
                        );
                    if (minDist == undefined || candidateDist < minDist) {
                        closestPx = candidatePx;
                        minDist = candidateDist;
                        closestCandidate = candidate;
                    }
                }
            });
            if (closestPx != undefined) {
                /*
                 // does not work as expected, why?
                 result = (
                    Math.abs(
                        ((closestPx[0] + 64) >> 5) - ((featurePx[0] + 64) >> 5)
                    ) > 1
                    ||
                    Math.abs(
                        ((closestPx[1] + 64) >> 5) - ((featurePx[1] + 64) >> 5)
                    ) > 1
                );*/
                result = (
                    Math.abs(closestPx[0] - featurePx[0]) > 64
                    ||
                    Math.abs(closestPx[1] - featurePx[1]) > 64
                );
            }
        }
    }
    return result;
}

/**
 * Returns cached style based on current zoom, the main entry for computing
 * styles for OKAPI-based markers
 */
OkapiBasedMarker.prototype.getFeatureStyle = function(resolution) {
    var zoom = this.map.getView().getZoom();
    return (
        this.getZoomRange(zoom)
        ? this.getCachedZoomStyle(
            zoom, this.computeNewStyle
        )
        : undefined
    );
}

/**
 * Returns a cached icon style, or stores it in the map cache if not found,
 * for given zoom range and caption levels
 */
OkapiBasedMarker.prototype.getCachedIconStyle = function(
    zoomRangeName, captionLevel
) {
    var src;
    var size = captionLevel > 0 ? 'large' : zoomRangeName;
    src = this.getIconSrc(size);
    var cachedStyle;
    var ocIconStyles = this.map.get('_ocIconStyles');
    if (ocIconStyles != undefined) {
        cachedStyle =
            typeof(ocIconStyles[src]) !== "undefined"
            ? ocIconStyles[src]
            : undefined;
    }
    if (!cachedStyle) {
        cachedStyle = this.getIconStyle(size, src);
        if (ocIconStyles == undefined) {
            ocIconStyles = {};
        }
        ocIconStyles[src] = cachedStyle;
        this.map.set('_ocIconStyles', ocIconStyles);
    }
    return cachedStyle;
}

/**
 * Returns a general style, common for each OKAPI-based marker, for given size
 * and image source
 */
OkapiBasedMarker.prototype.getCommonIconStyle =  function(size, src) {
    var result;
    if (size == 'large') {
        result = new ol.style.Style({
            image: new ol.style.Icon({
                anchorOrigin: 'bottom-left',
                anchorXUnits: 'pixel',
                anchorYUnits: 'pixel',
                anchor: [ 13,  6 ],
                src: src,
            }),
        });
    } else if (size == 'medium') {
        result = new ol.style.Style({
            image: new ol.style.Icon({
                anchorOrigin: 'bottom-left',
                anchorXUnits: 'pixel',
                anchorYUnits: 'pixel',
                anchor: [ 7,  7 ],
                src: src,
            }),
        });
    } else {
        result = new ol.style.Style({
            image: new ol.style.Icon({
                src: src,
            }),
        });
    }
    return result;
}

/**
 * Returns a general caption style, common for each OKAPI-based marker
 */
OkapiBasedMarker.prototype.getCommonCaptionStyle = function(showCaption) {
    var result;
    if (showCaption) {
        result = new ol.style.Style({
            text: this.generateCaptionStyle(this.ocData.name),
        });
    }
    return result;
}
/**
 * Returns a freshly computed style for given zoom and caption level.
 * This function is passed to OCZoomCachedMarker.getCachedZoomStyle as an
 * argument
 */
OkapiBasedMarker.prototype.computeNewStyle = function(zoom, captionLevel) {
    var result = {
        style: undefined,
        popupOffsetY: undefined
    }
    var zoomRange = this.getZoomRange(zoom);
    result.style = [];
    result.style.push(this.getCachedIconStyle(zoomRange.name, captionLevel));
    if (captionLevel > 0) {
        var captionStyle = this.getCaptionStyle(captionLevel > 1);
        if (captionStyle) {
            result.style.push(captionStyle);
        }
    }
    return result;
}

/**
 * Returns true if current cache is a recommended one
 */
OkapiBasedMarker.prototype.isRecommended = function() {
    return (
        this.ocData.ratingId > 4
        && this.ocData.founds > 6
        && (this.ocData.recommendations / this.ocData.founds) > 0.3
    );
}

/**
 * A common function returning suffix being a part of icon image src path by
 * calling a given suffixFunction
 */
OkapiBasedMarker.prototype.getSuffix = function(value, suffixFunction) {
    var result = '';
    if (value != undefined) {
        var suffix = this[suffixFunction](value);
        if (suffix != undefined) {
            result = "_" + suffix;
        }
    }
    return result;
}

/**
 * Returns a new text style used in caption display
 */
OkapiBasedMarker.prototype.generateCaptionStyle = function(caption) {
    var font = "26pt Tahoma,Geneva,sans-serif";
    return new ol.style.Text({
        font: font,
        stroke: new ol.style.Stroke({
            color: [ 255, 255, 255, 1 - 20/127],
            width: 12,
        }),
        fill: new ol.style.Fill({
            color: [ 150, 0, 0, 1 - 40/127],
        }),
        textBaseline: "top",
        scale: 0.25,
        offsetY: 15,
        text: this.wordwrap(font, 64*4, 26*4, 34, caption),
    });
}

/**
 * Returns given text word-wrapped by given parameters and font. Used in
 * creating a marker caption.
 */
OkapiBasedMarker.prototype.wordwrap = function(
    font, maxWidth, maxHeight, lineHeight, text
) {
    var result = '';
    var ctx = this.map.get('wordWrapCtx');
    if (!ctx) {
        var canvas = document.createElement("canvas");
        if (canvas) {
            canvas.width = maxWidth;
            canvas.height = maxHeight;
            ctx = canvas.getContext('2d');
            ctx.font = font;
            ctx.fillStyle = '#960000';
            this.map.set('wordWrapCtx', ctx);
        }
    }
    if (ctx && text) {
        var words = text.split(" ");
        var lines = [];
        var line = '';
        var reminder = '';
        for (var i = 0; (i < words.length || reminder.length > 0); i++) {
            var word = (typeof(words[i]) !== "undefined" ? words[i] : "");
            if (reminder.length > 0) {
                word = reminder + " " + word;
            }
            reminder = "";
            var mStatus = false;
            while (!mStatus) {
                var metrics = ctx.measureText(line + word);
                if (metrics.width <= maxWidth) {
                    line += word + " ";
                    mStatus = true;
                } else if (line.length > 0) {
                    lines.push(line.trim());
                    line = "";
                } else {
                    reminder = word.substr(word.length - 1) + reminder;
                    word = word.substr(0, word.length - 1);
                }
            }
        }
        if (line.length > 0) {
            lines.push(line.trim());
        }
        while ((lines.length * lineHeight) > maxHeight && lines.length > 0) {
            lines.pop();
        }
        result = lines.join("\n");
    }
    return result;
}
