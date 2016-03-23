<?php
/**
 * This class is used to generate maps description based on maps configuration from settings.inc.php
 *
 */
use lib\Objects\OcConfig\OcConfig;

class CacheMap3Lib {

    public static function GenerateAttributionMap()
    {
        $result = '';
        foreach(OcConfig::mapsConfig() as $key => $val){
            if (self::ShouldSkip($val)){
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

    public static function GenerateMapItems()
    {
        $result = '';
        foreach(OcConfig::mapsConfig() as $key => $val){
            if (self::ShouldSkip($val)){
                continue;
            }

            if ($result !== ''){
                $result .= ",\n";
            }
            $result .= "\t$key:" . self::GenerateMapItem($key, $val);
        }
        return "{\n" . $result . "\n}";

    }

    public static function GenerateShowMapsWhenMore()
    {
        $result = '';
        foreach(OcConfig::mapsConfig() as $key => $val){
            if (self::ShouldSkip($val)){
                continue;
            }
            if (!isset($val['showOnlyIfMore'])){
                continue;
            }
            if ($result !== ''){
                $result .= ",\n";
            }
            $result .= "\t$key:" . ($val['showOnlyIfMore'] ? 'true' : 'false');
        }
        return "{\n" . $result . "\n}";

    }

    /**
     * Check if mapItem is marked to be hidden
     * @param array $mapItem
     */
    protected static function ShouldSkip(array $mapItem)
    {
        if (isset($mapItem['hidden']) && $mapItem['hidden'] === true){
            return true;
        }
        return false;
    }

    private static function GenerateMapItem($key, array $val)
    {
        if (isset($val['imageMapTypeJS'])){
            return 'function(){return ' . $val['imageMapTypeJS'] . ";\n\t}";
        }

        unset($val['showOnlyIfMore']);
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
