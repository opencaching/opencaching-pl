<?php
namespace Utils\Uri;

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

    const CTRL_BASE_CLASS = '\Controllers\BaseController';
    const ROOT_DIR = __DIR__.'/../..';

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

        $link = "/$ctrl";

        if(!is_null($action)){
            $link .= "/$action";
        }else{
            if(!is_null($params)){
                // set default action only if $params are present
                $link .= "/".self::DEFAULT_ACTION;
            }
        }

        if(!is_null($params)){
            if(is_array($params)){
                $link .= '/'.implode("/",$params);
            }else{
                $link .= '/'.$params;
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

        // create controller object
        $ctrl = new $ctrlName($actionName);

        // check if controller/action is ready to be call by router
        if( !is_a($ctrl, self::CTRL_BASE_CLASS) ||
            !method_exists ( $ctrl, $actionName ) ||
            !$ctrl->isCallableFromRouter($actionName)){

            // router prevent this call - use defaults instead!
            $ctrlName = self::getControllerWithNamespace(self::ERROR_CTRL);
            $actionName = self::ERROR_ACTION;
            $params = ['Requested action not found',403];

            $ctrl = new $ctrlName($actionName);
        }

        call_user_func_array(array($ctrl, $actionName), $params);

        // this should be a dead code!
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
            // if the first char of $uri is not a slash add slash
            if (substr($uri, 0, 1) !== '/') {
                $uri = '/'.$uri;
            }
            $uri = "//" . $_SERVER['HTTP_HOST'] . $uri;
        }

        header("Location: $uri");
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

        return '\\Controllers\\'.str_replace('.', '\\', $ctrl).'Controller';
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
        if(!isset($_GET[self::ROUTE_GET_VAR])){
            $routeParts = array();
        }else{
            $routeParts = explode('/', $_GET[self::ROUTE_GET_VAR]);
        }

        if(empty($routeParts)){
            // this is just emprty route - display DEFAUTS
            $routeParts[0] = self::DEFAULT_CTRL;
        }else{
            // ctrl part is empty - hmm... assume someone add too many slashes
            if(empty($routeParts[0])){
                array_shift($routeParts);
                if( empty($routeParts) || empty($routeParts[0]) ){
                    // stop guess - came back to default
                    $routeParts[0] = self::DEFAULT_CTRL;
                }
            }
        }

        // assume that this is ctrl name - add full namespace path to it
        $ctrl = self::getControllerWithNamespace($routeParts[0]);

        // check if such file exists
        if(!file_exists(self::getClassFilePath($ctrl))){
            // there is no such file! - display error
            $ctrl = self::getControllerWithNamespace(self::ERROR_CTRL);
            $action = self::ERROR_ACTION;
            $params = ['Requested resource not found!?', 404];

        }else{
            // ctrl found, check the action
            if( !isset($routeParts[1]) || empty($routeParts[1]) ){
                $action = self::DEFAULT_ACTION;
            } else {
                $action = $routeParts[1];
            }
            // and params...
            $params = array_slice($routeParts, 2);
        }

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
}
