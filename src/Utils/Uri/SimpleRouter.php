<?php
namespace src\Utils\Uri;

use ReflectionException;
use src\Utils\Debug\Debug;

/**
 * Route schema:
 * <site>/[ctrl-dir.]<controller>/<action>/<params>
 *
 * - <controller> is a Controller class name (WITHOUT word "Controller")
 * - <action> - must be the public method name of Controller
 * - <params> - are optional values passed to action-method as params
 *
 * Example:
 * opencaching.pl/Admin.Reports
 */

class SimpleRouter
{
    // action which will be used if no action is indicated in request
    const DEFAULT_ACTION = 'index'; //
    // controller which will be used if no controller is indicated in request
    const DEFAULT_CTRL = 'StartPage'; //

    // ctrl used if request is improper (eg. there is no such ctrl)
    const ERROR_CTRL = 'StartPage';
    // action used if request is improper (eg. there is no such action)
    const ERROR_ACTION = 'displayCommonErrorPageAndExit';

    const CTRL_BASE_CLASS = '\src\Controllers\BaseController';
    const ROOT_DIR = __DIR__.'/../../..';

    // GET (url) var used to transfer route
    const ROUTE_GET_VAR = 'r';

    /**
     * Generate proper link from given params.
     * Link is relative (without protocol and server - this is just path started with '/')
     *
     * @param string $ctrl - controller class name (and path) - use php ControllerName::class
     * @param string $action - method name from given controller
     * @param string|array $params - param as string or array of params
     * @return string - link to use
     */
    public static function getLink($ctrl, $action=null, $params=null)
    {
        $ctrl = self::checkControllerName($ctrl);

        // remove "src." from begining of class (if added)
        $ctrl = preg_replace("/^src./i","",$ctrl);

        $link = "/$ctrl";

        if(!is_null($action)) {
            $link .= "/$action";
        } else {
            if(!is_null($params)) {
                // set default action only if $params are present
                $link .= "/".self::DEFAULT_ACTION;
            }
        }

        /**
         * TODO: There is still a problem of slashes in arg. content
         *  - default apache config prevents %2F in non-query part of URI
         */
        if(!is_null($params)){
            if(is_array($params)){
                array_walk($params, function (&$val, $x){
                    $val = urlencode($val);
                });
                $link .= '/'.implode('/',$params);
            } else {
                $link .= '/'.urlencode($params);
            }
        }

        return $link;
    }

    /**
     * Return link with full domain name
     *
     * @param string $ctrl - controller class name (and path) - use php ControllerName::class
     * @param string $action - method name
     * @param string|array $params - param as string or array of params
     * @return string
     */
    public static function getAbsLink($ctrl, $action=null, $params=null)
    {
        $link = self::getLink($ctrl, $action, $params);

        return Uri::getCurrentUriBase().$link;
    }

    /**
     * This is router entry point.
     * After call router parse url and load requested route.
     */
    public static function run()
    {
        // identify requested (or default) Controller/Action/params
        list($ctrlName, $actionName, $params) = self::parse();

        // first check the class filename
        if(!file_exists(self::getClassFilePath($ctrlName))) {
            self::displayErrorAndExit("No such file: $ctrlName", 403);
        }

        // create class reflection
        try {
            $ctrlReflection = new \ReflectionClass ($ctrlName);
        } catch (ReflectionException $ex) {
            self::displayErrorAndExit('Improper ctrl name', 403);
        }

        // check if the controller is not abstract
        if ($ctrlReflection->isAbstract()) {
            self::displayErrorAndExit('Abstr. ctrl', 403);
        }

        // check if this is the subclass of BaseController
        if(!$ctrlReflection->isSubclassOf(self::CTRL_BASE_CLASS)){
            self::displayErrorAndExit('Not instance of BaseController', 403);
        }

        // check if action can be called
        $ctrl = $ctrlReflection->newInstance($actionName);
        if (!$ctrl->isCallableFromRouter($actionName)) {
            self::displayErrorAndExit('Not callable from router', 403);
        }

        try {
            $actionReflection = $ctrlReflection->getMethod($actionName);
        } catch (ReflectionException $ex) {
            self::displayErrorAndExit('Wrong action', 403);
        }

        if (!$actionReflection->isPublic()){
            self::displayErrorAndExit('Calling non-public method', 403);
        }

        // check if the given params number is enough for this action
        $numOfReqParams = $actionReflection->getNumberOfRequiredParameters();
        if ($numOfReqParams != 0 && (!is_array($params) || $numOfReqParams > count($params)) ) {
            self::displayErrorAndExit('Not enough params', 403);
        }

        // run this requests
        call_user_func_array(array($ctrl, $actionName), $params);
        exit;
    }

    /**
     * Redirect to new location
     *
     * @param string $uri
     * @param bool $absoluteUri - if set means that uri is absolute (contains protocol and host etc.)
     */
    public static function redirect($uri, $absoluteUri=null)
    {
        if (is_null($absoluteUri)) {
            // if the first char of $uri is not a slash - add slash
            if (substr($uri, 0, 1) !== '/') {
                $uri = '/'.$uri;
            }
            $uri = "//" . $_SERVER['HTTP_HOST'] . $uri;
        }

        header("Location: $uri");
        exit;
    }

    /**
     * Replace ctrl part from route into php-namespace class name
     *
     * @param string $ctrl - route ctrl part
     * @return string
     */
    private static function getControllerWithNamespace($ctrl)
    {
        //be sure the first letter of controller (class) is uppper letter
        $ctrl = ucfirst($ctrl);

        return '\\src\\Controllers\\'.str_replace('.', '\\', $ctrl).'Controller';
    }

    /**
     * Return path to the class filename based on class namespace
     *
     * @param string $classNamespace
     * @return string
     */
    private static function getClassFilePath($classNamespace)
    {
        return self::ROOT_DIR.str_replace('\\', '/', $classNamespace).'.php';
    }

    /**
     * Returns list (controllerName, actionName, params-string)
     *
     * @param string $url
     * @return array|null - parts of the route
     */
    private static function parse()
    {
        if (!isset($_GET[self::ROUTE_GET_VAR])) {
            $routeParts = array();
        } else {
            $routeParts = explode('/', $_GET[self::ROUTE_GET_VAR]);
        }

        if (empty($routeParts)) {
            // this is just emprty route - display DEFAUTS
            $routeParts[0] = self::DEFAULT_CTRL;
        } else {
            // ctrl part is empty - hmm... assume someone add too many slashes
            if (empty($routeParts[0])) {
                array_shift($routeParts);
                if (empty($routeParts) || empty($routeParts[0])) {
                    // stop guess - came back to default
                    $routeParts[0] = self::DEFAULT_CTRL;
                }
            }
        }

        // assume that this is ctrl name - add full namespace path to it
        $ctrl = self::getControllerWithNamespace($routeParts[0]);

        // ctrl found, check the action
        if( !isset($routeParts[1]) || empty($routeParts[1]) ){
            $action = self::DEFAULT_ACTION;
        } else {
            $action = $routeParts[1];
        }

        // and params...
        $params = array_slice($routeParts, 2);

        return array($ctrl, $action, $params);
    }

    private static function checkControllerName($ctrl)
    {
        // remove Controllers/Controller words from the ctrl path
        $ctrl = str_replace(array('Controllers\\','Controller'), '', $ctrl);

        // normalize slashes - replace backslashes
        $ctrl = str_replace('\\', '/', $ctrl);
        $parts = explode('/', $ctrl);
        if(count($parts)==1){
            return $parts[0]; // there is only ctrl name
        }else{
            return implode('.',$parts);
        }
    }

    private static function displayErrorAndExit($message, $httpCode)
    {
        global $debug_page;

        $message = $debug_page ? $message : 'Improper request';

        $ctrlName = self::getControllerWithNamespace(self::ERROR_CTRL);
        call_user_func_array(array(new $ctrlName(self::ERROR_ACTION),  self::ERROR_ACTION), [$message, $httpCode]);
        exit;
    }
}
