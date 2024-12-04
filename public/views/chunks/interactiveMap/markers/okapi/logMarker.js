/**
 * Marker for displaying a cache log
 */
function LogMarker(map, ocData) {
    OkapiBasedMarker.call(this, map, ocData);
    this.iconsDir = '/images/map_markers/okapi/log/';
}

LogMarker.prototype = Object.create(OkapiBasedMarker.prototype);

LogMarker.prototype.constructor = LogMarker;

/**
 * Returns icon image src path part for a cache log type
 */
LogMarker.prototype.getLogTypeSuffix = function(type) {
    var result;
    switch (type.toString()) {
        case logTypeList["LOGTYPE_FOUNDIT"]: result = 'foundit'; break;
        case logTypeList["LOGTYPE_DIDNOTFIND"]: result = 'didnotfind'; break;
        case logTypeList["LOGTYPE_COMMENT"]: result = 'comment'; break;
        case logTypeList["LOGTYPE_MOVED"]: result = 'moved'; break;
        case logTypeList["LOGTYPE_NEEDMAINTENANCE"]:
            result = 'needmaintenance';
            break;
        case logTypeList["LOGTYPE_MADEMAINTENANCE"]:
            result = 'mademaintenance';
            break;
        case logTypeList["LOGTYPE_ATTENDED"]: result = 'attended'; break;
        case logTypeList["LOGTYPE_WILLATTENDED"]: result = 'willattend'; break;
        case logTypeList["LOGTYPE_ARCHIVED"]: result = 'archived'; break;
        case logTypeList["LOGTYPE_READYTOSEARCH"]:
            result = 'readytosearch';
            break;
        case logTypeList["LOGTYPE_TEMPORARYUNAVAILABLE"]:
            result = 'temporaryunavailable';
            break;
        case logTypeList["LOGTYPE_ADMINNOTE"]: result = 'adminnote';
    }
    return result;
}

/**
 * Returns icon image src path part for a cache additional flag (is own, found,
 * new etc)
 */
LogMarker.prototype.getFlagSuffix = function(flag) {
    var result;
    switch (flag) {
        case 1: result = 'own'; break;
        case 2: result = 'found'; break;
        case 3: result = 'new';
    }
    return result;
}

/**
 * Returns icon image src file name basing on given parameters
 */
LogMarker.prototype.getIconFileName = function(sizePrefix, logType, flag) {
    var name = sizePrefix + '_log';
    name += this.getSuffix(logType, "getLogTypeSuffix");
    name += this.getSuffix(flag, "getFlagSuffix");
    name += ".png";
    return name;
}

/**
 * Returns icon image src full path, basing on given size and caption status
 */
LogMarker.prototype.getIconSrc = function(size, showCaption) {
    var result;
    switch(size) {
        case 'tiny':
            result = this.iconsDir
                + this.getIconFileName(
                    'tiny',
                    this.ocData.logType
                );
            break;
        default:
            result = this.iconsDir
                + this.getIconFileName(
                    size,
                    this.ocData.logType,
                    (
                        this.ocData.isOwner
                        ? 1
                        : (
                            this.ocData.logStatus ==
                                logTypeList["LOGTYPE_FOUNDIT"]
                            ? 2
                            : undefined
                        )
                    )
                );
    }
    return result;
}

LogMarker.prototype.getIconStyle =
    OkapiBasedMarker.prototype.getCommonIconStyle;

LogMarker.prototype.getCaptionStyle =
    OkapiBasedMarker.prototype.getCommonCaptionStyle;
