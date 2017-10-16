<?php
namespace Controllers\Cron;

use Controllers\BaseController;
use lib\Objects\User\UserAuthorization;
use Utils\Debug\Debug;

class OnlineUsersController extends BaseController
{

    public function __construct(){
        parent::__construct();
    }

    public function index()
    {}

    private static function getOnlineUsersDumpFile(){
       global $config;
       return $config['path']['dynamicFilesDir'].'onlineUsers.txt';
    }

    public static function dumpOnlineUsers()
    {
        $obj = new \stdClass();
        $obj->onlineUsers = UserAuthorization::getOnlineUsersFromDb();
        $obj->dumpTs = time();

        file_put_contents(self::getOnlineUsersDumpFile(), json_encode($obj));
    }

    /**
     * Returns the array of objects
     * @return NULL|array
     */
    public static function getOnlineUsers()
    {
        $str = file_get_contents ( self::getOnlineUsersDumpFile() );
        if(empty($str)){
            //read error?!
            // Debug::errorLog(__METHOD__.": ERROR: Can't read file with list of online users: ".
            //    self::getOnlineUsersDumpFile());
            return null;
        }

        $obj = json_decode($str);
        if(empty($obj)){
            // Debug::errorLog(__METHOD__.": ERROR: Can't decode list of online users.");
            return null;
        }

        $dumpTs = $obj->dumpTs;
        if(time() > $dumpTs + 60*60){
            // this dump is obsolete (older than 1 hour)
            // Debug::errorLog(__METHOD__.": ERROR: Obsolete list of online users.");
            return null;
        }

        return $obj->onlineUsers;
    }

}

