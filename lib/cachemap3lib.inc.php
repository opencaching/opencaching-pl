<?php
/* * *************************************************************************
 *
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
* ************************************************************************** */

class CacheMap3Lib {
    
    public static function generateAttributionMap()
    {
        global $config;
        if (!isset($config['mapsConfig'])){
            return '{}';
        }
        $result = '';
        $mapsConfig = $config['mapsConfig'];
        foreach($mapsConfig as $key => $val){
            if (isset($val['hidden']) && $val['hidden'] === true){
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
    
    public static function generateMapItems()
    {
        global $config;
        if (!isset($config['mapsConfig'])){
            return '{}';
        }
        $result = '';
        $mapsConfig = $config['mapsConfig'];
        foreach($mapsConfig as $key => $val){
            if (isset($val['hidden']) && $val['hidden'] === true){
                continue;
            }
            if ($result !== ''){
                $result .= ",\n";
            }
            $result .= "\t$key:" . self::generateMapItem($key, $val);
        }
        return "{\n" . $result . "\n}";
        
    }
    
    public static function generateShowMapsWhenMore()
    {
        global $config;
        if (!isset($config['mapsConfig'])){
            return '{}';
        }
        $result = '';
        $mapsConfig = $config['mapsConfig'];
        foreach($mapsConfig as $key => $val){
            if (isset($val['hidden']) && $val['hidden'] === true){
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
    
    
    private static function generateMapItem($key, array $val)
    {
        if (isset($val['imageMapTypeJS'])){
            return 'function(){return ' . $val['imageMapTypeJS'] . ";\n\t}";
        }
        
        unset($val['showOnlyIfMore']);
        
        $outMap = array();
        $outMapTypes = array();
        
        $getTileUrl = '';
        if (isset($val['tileUrlJS'])){
            $getTileUrl = $val['tileUrlJS'];
            unset($val['tileUrlJS']);
            unset($val['tileUrl']);
        } elseif (isset($val['tileUrl'])) {
            $tileUrl = $val['tileUrl'];
            
            $tileUrl = preg_replace('/{z([+-][0-9]+)?}/', '" + (z${1}) + "', $tileUrl);
            $tileUrl = preg_replace('/{x([+-][0-9]+)?}/', '" + (p.x${1}) + "', $tileUrl);
            $tileUrl = preg_replace('/{y([+-][0-9]+)?}/', '" + (p.y${1}) + "', $tileUrl);
            
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
            } elseif (self::startsWith($tileSize, 'raw:')){
                $tileSize = substr($tileSize, 4);
            }
            
            $outMap['tileSize'] = $tileSize;
            $outMapTypes['tileSize'] = 'raw';
        }
        
        foreach($val as $k => $v){
            if (is_numeric($v) || $v === 'true' || $v === 'false'){
                $outMap[$k] = $v;
                $outMapTypes[$k] = 'raw';
            } elseif (self::startsWith($v, 'raw:')) {
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
    
    private static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}