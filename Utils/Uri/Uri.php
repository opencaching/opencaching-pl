<?php
namespace Utils\Uri;


class Uri {

    /**
     * Returns url with set given param to given value
     * + remove old value of given param if neccessary
     *
     * @param unknown $paramName
     * @param unknown $paramValue
     * @param unknown $uri
     */
    public static function setOrReplaceParamValue(
        $paramName, $paramValue, $uri=null){

        if(is_null($uri)){
            $uri = self::getCurrentUri(true);
        }

        $paramArr = [];
        parse_str( parse_url($uri, PHP_URL_QUERY), $paramArr);
        $paramArr[$paramName] = $paramValue;

        //rebuild the uri
        return strtok($uri, '?').'?'.http_build_query($paramArr);

    }

    public static function addAnchorName($anchorName, $uri=null)
    {
        if(is_null($uri)){
            $uri = $_SERVER['REQUEST_URI'];
        }

        list ($uriWithoutHash) = explode('#', $uri);

        return $uriWithoutHash.'#'.$anchorName;

    }

    /**
     * Remove given param from URL
     *
     * @param unknown $paramName
     * @param unknown $uri
     */
    public static function removeParam($paramName, $uri=null){
        if(is_null($uri)){
            $uri = $_SERVER['REQUEST_URI'];
        }

        $paramArr = [];
        parse_str( parse_url($uri, PHP_URL_QUERY), $paramArr);

        if( isset($paramArr[$paramName]) ){
            unset($paramArr[$paramName]);

            //rebuild the uri
            $uri = strtok($uri, '?').'?'.http_build_query($paramArr);
        }

        return $uri;
    }

    public static function getCurrentUri($savePrecedingSlash = false)
    {
       return ($savePrecedingSlash) ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 1);
    }

    /**
     * This is usefull if to prevent browser to cache for example css/js file
     * Returns link to file with ?<modification-time> which makes browser to download file if contents has changed
     *
     * @param unknown $rootPath - path to the file (from root of thw site)
     * @return string
     */
    public static function getLinkWithModificationTime($rootPath)
    {
        return $rootPath.'?'.filemtime(__dir__.'/../../'.$rootPath);
    }

}
