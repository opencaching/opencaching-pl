<?php
namespace Utils\Uri;

/**
 *
 * Route schema:
 * <site>/index.php?r=/[ctrl-dir.]<controller>/<action>/<params>
 *
 * - <controller> should be WITHOUT word "Controller"
 * - <action> - must be the public method name of controller
 * - <params> - are optional
 *
 * Example:
 * opencaching.pl/index.php?r=/Admin.Reports
 */

class SimpleRouter
{
    const ROUTE_GET_VAR = 'r';

    const DEFAULT_ACTION = 'index';
    const DEFAULT_CTRL = 'StartPage';

    const ROOT_DIR = __DIR__.'/../..';


    /**
     * Generate proper link from given params
     *
     * @param string $ctrl - controller class name (and path) - use php ControllerName::class
     * @param string $action - method name from given controller
     * @param string|array $params - params -
     * @return string - link to use
     */
    public static function getLink($ctrl, $action=null, $params=null)
    {
        $ctrl = self::checkControllerName($ctrl);

        if(!$action){ //action not set - user default
            $action = 'index';
        }

        $link = '/index.php?'.self::ROUTE_GET_VAR."=/$ctrl/$action";
        if($params){
            if(is_array($params)){
                $link .= '/'.implode(",",$params);
            }else{
                $link .= '/'.$params;
            }
        }
        return $link;
    }

    /**
     * This is router entry point.
     * After call router parse url and load requeset route.
     */
    public static function run()
    {
        list($ctrlName, $actionName, $params) = self::parse();

        $ctrl = new $ctrlName;
        call_user_func_array(array($ctrl, $actionName), $params);

    }

    /**
     * Replace ctrl part from route into php-namespace class name
     *
     * @param string $ctrl - route ctrl part
     * @return string
     */
    private static function getControllerWithNamespace($ctrl)
    {
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
        return self::ROOT_DIR.str_replace('/', '\\', $classNamespace).'.php';
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

        if(empty($parts)){
            $routeParts[0] = self::DEFAULT_CTRL;
        }else{
            if(empty($parts[0])){
                array_shift($routeParts);
            }
        }

        $ctrl = self::getControllerWithNamespace($routeParts[0]);

        if(!file_exists(self::getClassFilePath($ctrl))){
            // there is no such file!
            $ctrlPath = self::getControllerWithNamespace(self::DEFAULT_CTRL);
        }

        $action = ( isset($parts[1]) ? $routeParts[1] : self::DEFAULT_ACTION );
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
}
