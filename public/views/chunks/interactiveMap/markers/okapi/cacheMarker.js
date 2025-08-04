/**
 * Marker for displaying a cache
 */
function CacheMarker(map, ocData) {
    OkapiBasedMarker.call(this, map, ocData);
    this.iconsDir = '/images/map_markers/okapi/cache/';
}

CacheMarker.prototype = Object.create(OkapiBasedMarker.prototype);

CacheMarker.prototype.constructor = CacheMarker;

/**
 * Returns icon image src path part for a cache type
 */
CacheMarker.prototype.getTypeSuffix = function(type) {
    var result;
    switch (type) {
        case 1: result = 'unknown'; break;
        case 2: result = 'traditional'; break;
        case 3: result = 'multi'; break;
        case 4: result = 'virtual'; break;
        case 5: result = 'webcam'; break;
        case 6: result = 'event'; break;
        case 7: result = 'quiz'; break;
        case 8: result = 'moving'; break;
        case 10: result = 'own'; break;
        default: result = 'other';
    }
    return result;
}

/**
 * Returns icon image src path part for a cache status
 */
CacheMarker.prototype.getStatusSuffix = function(status) {
    var result;
    switch (status) {
        case cacheStatusList["STATUS_UNAVAILABLE"]:
            result = 'unavailable';
            break;
        case cacheStatusList["STATUS_ARCHIVED"]:
            result = 'archived';
    }
    return result;
}

/**
 * Returns icon image src path part for a cache rating
 */
CacheMarker.prototype.getRatingSuffix = function(rating) {
    var result;
    if (rating > 4) {
        result = 'excellent';
    }
    return result;
}

/**
 * Returns icon image src path part for a cache recommendation status
 */
CacheMarker.prototype.getRecommendedSuffix = function(recommended) {
    var result;
    if (recommended) {
        result = 'recommended';
    }
    return result;
}

/**
 * Returns icon image src path part for a cache additional flag (is own, found,
 * new etc)
 */
CacheMarker.prototype.getFlagSuffix = function(flag) {
    var result;
    switch (flag) {
        case 1: result = 'own'; break;
        case 2: result = 'found'; break;
        case 3: result = 'new';
    }
    return result;
}

/**
 * Returns icon image src path part for a cache caption status
 */
CacheMarker.prototype.getCaptionSuffix = function(caption) {
    var result;
    if (caption) {
        result = 'caption';
    }
    return result;
}

/**
 * Returns icon image src file name basing on given parameters
 */
CacheMarker.prototype.getIconFileName = function(
    sizePrefix, statOrType, type, status, rating, recommended, flag, caption
) {
    var name = sizePrefix;
    if (statOrType) {
        part = this.getSuffix(status, "getStatusSuffix");
        if (part) {
            name += part;
        } else {
            name += this.getSuffix(type, "getTypeSuffix");
        }
    } else {
        name += this.getSuffix(type, "getTypeSuffix");
        name += this.getSuffix(status, "getStatusSuffix");
    }
    name += this.getSuffix(rating, "getRatingSuffix");
    name += this.getSuffix(recommended, "getRecommendedSuffix");
    name += this.getSuffix(flag, "getFlagSuffix");
    name += this.getSuffix(caption, "getCaptionSuffix");
    name += ".png";
    return name;
}

/**
 * Returns icon image src full path, basing on given size and caption status
 */
CacheMarker.prototype.getIconSrc = function(size, showCaption) {
    var result;
    switch(size) {
        case 'tiny':
            result = this.iconsDir
                + this.getIconFileName(
                    'tiny',
                    true,
                    this.ocData.cacheType,
                    this.ocData.cacheStatus
                );
            break;
        case 'medium':
            result = this.iconsDir
                + this.getIconFileName(
                    'medium',
                    true,
                    this.ocData.cacheType,
                    this.ocData.cacheStatus,
                    (
                        this.ocData.cacheStatus == 1
                        ? this.ocData.ratingId
                        : undefined
                    ),
                    (
                        this.ocData.cacheStatus == 1
                        ? this.isRecommended()
                        : undefined
                    ),
                    (
                        this.ocData.isOwner
                        ? 1
                        : (
                            this.ocData.logStatus ==
                                logTypeList["LOGTYPE_FOUNDIT"]
                            ? 2
                            : undefined
                        )
                    ),
                    false
                );
            break;
        default:
            result = this.iconsDir
                + this.getIconFileName(
                    'large',
                    false,
                    this.ocData.cacheType,
                    this.ocData.cacheStatus,
                    (
                        this.ocData.cacheStatus == 1
                        ? this.ocData.ratingId
                        : undefined
                    ),
                    (
                        this.ocData.cacheStatus == 1
                        ? this.isRecommended()
                        : undefined
                    ),
                    (
                        this.ocData.isOwner
                        ? 1
                        : (
                            this.ocData.logStatus ==
                                logTypeList["LOGTYPE_FOUNDIT"]
                            ? 2
                            : undefined
                        )
                    ),
                    (
                        this.ocData.logStatus == logTypeList["LOGTYPE_FOUNDIT"]
                        ? showCaption
                        : undefined
                    )
                );
    }
    return result;
}

CacheMarker.prototype.getIconStyle =
    OkapiBasedMarker.prototype.getCommonIconStyle;

CacheMarker.prototype.getCaptionStyle =
    OkapiBasedMarker.prototype.getCommonCaptionStyle;
