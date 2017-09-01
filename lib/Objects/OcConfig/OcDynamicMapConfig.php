<?php

namespace lib\Objects\OcConfig;

/**
 * This class is place for code used to load map config fomr settings.
 * This is a little bit complicated (complex JS and PHP code) and need further refactoring.
 *
 * Most of the code based on former /lib/CacheMap3Lib.inc.php file.
 */

class OcDynamicMapConfig
{
    public static function getJsAttributionMap()
    {
        $result = '';
        foreach(OcConfig::mapsConfig() as $key => $val){
            if (self::shouldSkip($val)){
                continue;
            }
            if (isset($val['attribution'])){
                $attribution = $val['attribution'];
                $attribution = str_replace('\'', '\\\'', $attribution);
                if ($result !== ''){
                    $result .= ",\n";
                }
                $result .= "\t$key:'$attribution'";
            }
        }
        return "{\n" . $result . "\n}";

    }

    public static function getJsMapItems()
    {
        $result = '';
        foreach(OcConfig::mapsConfig() as $key => $val){
            if (self::shouldSkip($val)){
                continue;
            }

            if ($result !== ''){
                $result .= ",\n";
            }
            $result .= "\t$key:" . self::generateMapItem($key, $val);
        }
        return "{\n" . $result . "\n}";

    }

    /**
     * This function provides JS function WMSImageMapTypeOptions necessary for config
     * (setting.inc.php) parsing.
     *
     *
     * BE SURE THAT you defined the JS getGoogleMapObject()
     * function which returns proper variable
     */
    public static function getWMSImageMapTypeOptions(){
        return <<<'EOF'


function WMSImageMapTypeOptions(
    wmsName, wmsURL, wmsLayers, wmsStyles, wmsFormat, wmsVersion, wmsBgColor)
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
        var proj = getGoogleMapObject().getProjection();
        var zfactor = Math.pow(2, zoom);
        var lULP = new google.maps.Point(
            point.x * 512 / zfactor, (point.y + 1) * 512 / zfactor);
        var lLRP = new google.maps.Point(
            (point.x + 1) * 512 / zfactor, point.y * 512 / zfactor);
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

EOF;

//BEWARE OF BUGS!: EOF must be at the beginig of line!

    }

    /**
     * Check if mapItem is marked to be hidden
     * @param array $mapItem
     */
    private static function shouldSkip(array $mapItem)
    {
        return (isset($mapItem['hidden']) && $mapItem['hidden'] === true);
    }

    private static function generateMapItem($key, array $val)
    {
        if (isset($val['imageMapTypeJS'])){
            return 'function(){return ' . $val['imageMapTypeJS'] . ";\n\t}";
        }

        unset($val['showInPowerTrail']);
        unset($val['attribution']);

        $outMap = array();
        $outMapTypes = array();

        $getTileUrl = '';
        if (isset($val['tileUrlJS'])){
            $getTileUrl = $val['tileUrlJS'];
            unset($val['tileUrlJS']);
            unset($val['tileUrl']);
        } elseif (isset($val['tileUrl'])) {
            $tileUrl = $val['tileUrl'];

            $tileUrl = preg_replace('/{([0-9-]*[*]?[0-9]*)z([+*-][0-9]+)?}/', '" + (${1}z${2}) + "', $tileUrl);
            $tileUrl = preg_replace('/{([0-9-]*[*]?[0-9]*)x([+*-][0-9]+)?}/', '" + (${1}p.x${2}) + "', $tileUrl);
            $tileUrl = preg_replace('/{([0-9-]*[*]?[0-9]*)y([+*-][0-9]+)?}/', '" + (${1}p.y${2}) + "', $tileUrl);

            unset($val['tileUrl']);
            $getTileUrl = 'function(p,z){return "' . $tileUrl . '";}';
        } else {
            $getTileUrl = 'function(p,z){return null}';
        }
        $outMap['getTileUrl'] = $getTileUrl;
        $outMapTypes['getTileUrl'] = 'raw';
        unset($getTileUrl);

        if (isset($val['tileSize'])){
            $tileSize = $val['tileSize'];
            unset($val['tileSize']);

            $sizes = array();
            if (preg_match('/^([0-9]+)x([0-9]+)$/', $tileSize, $sizes)){
                $tileSize = 'new google.maps.Size(' . $sizes[1] . ', ' . $sizes[2] . ')';
            } elseif (self::StartsWith($tileSize, 'raw:')){
                $tileSize = substr($tileSize, 4);
            }

            $outMap['tileSize'] = $tileSize;
            $outMapTypes['tileSize'] = 'raw';
        }

        foreach($val as $k => $v){
            if (is_numeric($v) || $v === 'true' || $v === 'false'){
                $outMap[$k] = $v;
                $outMapTypes[$k] = 'raw';
            } elseif (self::StartsWith($v, 'raw:')) {
                $v = substr($v, 4);
                $outMap[$k] = $v;
                $outMapTypes[$k] = 'raw';
            } else {
                $outMap[$k] = $v;
                $outMapTypes[$k] = 'text';
            }
        }

        $result = "function(){return new google.maps.ImageMapType({\n";
        $any = false;
        foreach($outMap as $k => $v){
            $type = isset($outMapTypes[$k]) ? $outMapTypes[$k] : 'text';
            switch($type){
                case 'raw':
                    break;
                case 'text':
                    $v = '\'' . str_replace('\'', '\\\'', $v) . '\'';
            }
            if ($any){
                $result .= ",\n";
            } else {
                $any = true;
            }
            $result .= "\t\t$k:$v";
        }
        $result .= "\n\t});}";
        return $result;
    }

    private static function StartsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

}


