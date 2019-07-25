<?php
namespace src\Controllers;

use src\Models\GeoCache\CacheLocation;

class SysController extends BaseController
{
    public function __construct(){
        parent::__construct();

        // test pages are only for users with AdvancedUsers role
        $this->redirectNotLoggedUsers();
        if(!$this->loggedUser->hasSysAdminRole()){
            $this->displayCommonErrorPageAndExit("Sorry, no such page.");
        }
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return TRUE;
    }

    public function index()
    {
        $this->view->setTemplate('sysAdmin/index');
        $this->view->buildView();
    }

    /**
     * This function is called directly from apc.conf.php and should prepare
     * the environment for APC controll panel
     *
     * APC control panel needs to be called directly
     */
    public function apc($redirectToApcScript=TRUE)
    {
        if($redirectToApcScript){
            $this->view->redirect('/src/Libs/Apc/apc.php');
            exit;
        }

        // Use (internal) authentication - best choice if
        // no other authentication is available
        // If set to 0:
        //  There will be no further authentication. You
        //  will have to handle this by yourself!
        // If set to 1:
        //  You need to change ADMIN_PASSWORD to make
        //  this work!
        define('USE_AUTHENTICATION', 0);

        define('DATE_FORMAT',$GLOBALS['datetimeFormat']);
        define('GRAPH_SIZE',200);                         // Image size

    }

    public function phpinfo(){
        phpinfo();
    }

    /**
     * Review cache location and insert cachelocation when needed
     */
    public function fixCachesLocation()
    {
        CacheLocation::fixCachesWithoutLocation();
        CacheLocation::fixCachesWithNulledLocation();
    }
}
