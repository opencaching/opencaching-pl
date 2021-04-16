<?php
namespace src\Utils\Uri;

use Exception;
use src\Models\OcConfig\OcConfig;

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
        $paramName, $paramValue, $uri = null)
    {

        if (is_null($uri)) {
            $uri = self::getCurrentUri(true);
        }

        $paramArr = [];
        parse_str( parse_url($uri, PHP_URL_QUERY), $paramArr);
        $paramArr[$paramName] = $paramValue;

        //rebuild the uri
        return strtok($uri, '?') . '?'.http_build_query($paramArr);

    }

    public static function addAnchorName($anchorName, $uri = null)
    {
        if (is_null($uri)) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        list ($uriWithoutHash) = explode('#', $uri);

        return $uriWithoutHash.'#' . $anchorName;

    }

    /**
     * Remove given param from URL
     *
     * @param string $paramName
     * @param string $uri
     * @return string
     */
    public static function removeParam($paramName, $uri = null)
    {
        if (is_null($uri)) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        $paramArr = [];
        parse_str( parse_url($uri, PHP_URL_QUERY), $paramArr);

        if ( isset($paramArr[$paramName]) ) {
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
     * @throws Exception
     */
    public static function getLinkWithModificationTime($rootPath)
    {
        $ROOT = __DIR__.'/../../..';

        // be sure that the path has preceding slash
        $rootPath = self::addPrecedingSlashIfNecessary($rootPath);

        $realPath = $ROOT.$rootPath;
        if (!file_exists($realPath)) {
            // there is no such file!
            if (file_exists($ROOT.'/public'.$rootPath)) {
                // there is such file in '/public'
                $realPath = $ROOT.'/public'.$rootPath;
            } else {
                throw new Exception("No such file: $rootPath");
            }
        }

        return $rootPath.'?' . filemtime($realPath);
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
     * IMPORTANT: Returned value can't be trust - it can be spoofed by malicious or broken request
     *
     * @param $verifyDomain - allow to skip domain verification if this is not a necessary for usage case
     *
     * @return string|null
     */
    public static function getCurrentDomain($verifyDomain = true)
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            // HOST not set in request - just ignore the domain
            return null;
        }

        $domain = $_SERVER['HTTP_HOST'];

        if (!$verifyDomain) {
            return $domain;
        }

        // main domain or any its subdomain
        $domainRegex = "/([a-z0-9|-]+\.)*".OcConfig::getSiteMainDomain()."$/";
        if(preg_match($domainRegex, $domain)){
            return $domain;
        } else {
            return null;
        }
    }

    /**
     * Returns path with filename from uri
     *
     * @param uri $uri
     * @return string
     */
    public static function getPathfromUrl($uri){
        return parse_url($uri, PHP_URL_PATH);
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
     * @param bool $withProtoAndDomain
     * @return string
     */
    public static function getCurrentRequestUri($withProtoAndDomain = true)
    {
        $parts = explode('?', $_SERVER['REQUEST_URI'], 2);

        if ($withProtoAndDomain) {
            return self::getCurrentUriBase() . $parts[0];
        } else {
            return $parts[0];
        }
    }

    /**
     * Returns full, absolute URI for (relative) $path
     * $paht MAY have preceding slash
     *
     * @param string $path
     * @return string
     */
    public static function getAbsUri($path = null)
    {
        return self::getCurrentUriBase() . self::addPrecedingSlashIfNecessary($path);
    }

    private static function addPrecedingSlashIfNecessary($path)
    {
        return '/' . ltrim($path, '/');
    }

    /**
     * Returns a given URI with parameters added.
     *
     * @param string $uri
     * @param array dictionary of params
     * @return string
     */
    public static function addParamsToUri($uri, array $params = [])
    {
        $delimiter = strpos($uri, '?') ? '&' : '?';

        foreach ($params as $key => $value) {
            if ($value) {
                $uri .= $delimiter . $key . '=' . urlencode($value);
            } else {
                $uri .= $delimiter . $key;
            }
            $delimiter = '&';
        }
        return $uri;
    }

    /**
     * From page-relative path returns the path as local server path (in server filesystem)
     *
     * @param string $pageRootPath
     * @return string
     */
    public static function getAbsServerPath($pageRootPath)
    {
        $rootPath = self::getPageRootPathOnServer();
        return $rootPath.$pageRootPath;
    }

    /**
     * Returns the page root-path on server
     * @return string
     */
    private static function getPageRootPathOnServer()
    {
        return preg_replace('/(\/[^\/]*){3}$/', '', __DIR__);
    }
}
