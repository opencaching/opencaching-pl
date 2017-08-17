<?php

namespace Utils\Uri;

/**
 * Simple url router (dispatcher).
 * It gets value of "route" given by ROUTE_PARAM.
 *
 * The aim of this code is to allow using "nice-urls" like:
 *  "www.opencaching.pl/ViewCache/byWpCode/OP1234"
 * instead of
 *  "www.opencaching.pl/ViewCache.php?WpCode=OP1234" etc...
 *
 * Based on "route" value script allow to load proper controller
 * and call proper method with given params
 *
 */
class OcRouter {

    const ROUTE_PARAM = "route";

    const DEFAULT_FILE = 'StartPage.php';
    const DEFAULT_CONTROLLER = 'StartPage';
    const DEFAUTL_METHOD = 'index';


    /**
     * This method returns parts of the routs:
     *  - script - as path to php file containing requested controller class
     *   (path is relative to /Controllers dir)
     *  - controller - name of the requested class
     *  - method - name of the requested method of controller
     *  - params - array of parameters in route
     *
     * @return array
     */
    public static function getRoutesParts()
    {
        if(!isset($_GET[self::ROUTE_PARAM])){
            $parts = [];
        }else{
            $parts = explode('/', $_GET[self::ROUTE_PARAM]);
        }

        switch( count($parts) ){
            case 0: /* there is no params at all - main page*/
                $file = self::DEFAULT_FILE;
                $controller = self::DEFAULT_CONTROLLER;
                $method = self::DEFAUTL_METHOD;
                $params = [];
                break;

            case 1: /* there is only controller given */
                $file = $parts[0].'php';
                if()


                $controller = $parts[0];
                $method = self::DEFAUTL_METHOD;
                $params = [];
                break;

            case 2: /* controller + method */
                $controller = $parts[0];
                $method = $parts[1];
                $params = [];
                break;

            default: /* controller + method + params */
                $controller = $parts[0];
                $method = $parts[1];
                $params = array_slice($parts, 2);
                break;
        }

        return [
            'controller' => $controller,
            'method' => $method,
            'params' => $params
        ];
    }

    private static function checkControllerFile($file){

    }

}


