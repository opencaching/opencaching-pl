<?php
namespace Utils\Uri;


class Uri {

    /**
     * Returns url with set given param to given value
     * + remove old value of given param if neccessary
     *
     * @param string $paramName
     * @param string $paramValue
     * @param string $uri
     * @return string
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
     * @param string $paramName
     * @param string $uri
     * @return string
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
            $uri = strtok($uri, '?');
            if (!empty($paramArr)) {
                $uri .= '?'.http_build_query($paramArr);
            }
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
     * @param string $rootPath - path to the file (from root of thw site)
     * @return string
     */
    public static function getLinkWithModificationTime($rootPath)
    {
        // be sure that the path has preceding slash
        $rootPath = self::addPrecedingSlashIfNecessary($rootPath);

        return $rootPath.'?'.filemtime(__dir__.'/../..'.$rootPath);
    }


    /**
     * Return current protocol (http vs https)
     * @return string - https|http
     */
    public static function getCurrentProtocol()
    {
        return $_SERVER['REQUEST_SCHEME'];
    }

    /**
     * Return current domain used by server
     * for example: opencaching.pl
     *
     * @return string
     */
    public static function getCurrentDomain(){
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Returns protocol://domain
     * for example: https://opencaching.pl
     * @return string
     */
    public static function getCurrentUriBase()
    {
        return self::getCurrentProtocol() . '://' . self::getCurrentDomain();
    }

    /**
     * Returns request URI (without params) (works for dynamic routs too).
     *
     * For example for: http://opencaching.pl/StartPage/index?fooParam=1234
     * it returns: http://opencaching.pl/StartPage/index (defult)
     * or: /StartPage/index if $withProtoAndDomain == false
     *
     */
    public static function getCurrentRequestUri($withProtoAndDomain=true)
    {
        $parts = explode('?', $_SERVER['REQUEST_URI'], 2);

        if($withProtoAndDomain){
            return self::getCurrentUriBase() . $parts[0];
        }else{
            return $parts[0];
        }
    }

    private static function addPrecedingSlashIfNecessary($path)
    {
        return '/' . ltrim($path, '/');
    }

}
