<?php
namespace lib\Objects\User;


use lib\Objects\ApplicationContainer;
use lib\Objects\BaseObject;
use lib\Objects\User\User;
use Utils\Debug\Debug;
use Utils\Uri\Cookie;
use Utils\Uri\Uri;
use lib\Objects\User\PasswordManager;


class UserAuthorization extends BaseObject
{

    // login statuses
    const LOGIN_OK = 0;            // login succeeded
    const LOGIN_BADUSERPW = 1;     // bad username or password
    const LOGIN_TOOMUCHLOGINS = 2; // too many logins in short time
    const LOGIN_USERNOTACTIVE = 3; // the useraccount locked

    // login timouts in seconds
    const LOGIN_TIMEOUT = 60 * 60; //1-hour
    const PERMANENT_LOGIN_TIMEOUT = 90 * 24 * 60 * 60; //90-days

    const SESSION_ID_KEY = 'loggedUserOcSessionId';

    // limit of login fails per hour
    const MAX_LOGIN_TRIES_PER_HOUR = 12;

    /**
     * This method check if current session is authorized
     * based on sessionId stored in AUTH-cookie
     *
     * @return User object or null if ther is no authorized user
     */
    public static function verify(){

        // try to read sessionId stored in AUTH-cookie
        if(! $cookieSession = self::getSessionFromAuthCookie()){
            // there is no such sessionId
            self::clearContextVars();
            return null;
        }

        // if $cookieSession is the same as sessionId stored in PHP session
        // and ApplicationContainer contains logged user:
        // user has been already verified
        if($cookieSession === self::getLoggedUserSessionId() &&
            $user = ApplicationContainer::GetAuthorizedUser() ){
            // generally it shouldn't happen unless this method is calls again
            // in the same request
            return $user;
        }

        //find this session in DB
        if(! $userId = self::getUserIdFromOcSession($cookieSession)){

            // there is no such session
            self::clearContextVars();
            self::destroyAuthCookie();
            return null;
        }

        // there is proper session - find the user
        if(!$user = User::fromUserIdFactory($userId)){
            //strange: session presents but there is no such user?!
            self::clearContextVars();
            self::destroyAuthCookie();
            Debug::errorLog(__METHOD__."Session present in cookie and DB ".
                "but there is nosuch user!");

            return null;
        }

        // authorized user found
        self::setLoggedUserSessionId($cookieSession);
        self::initContextVars($user);

        return $user;
    }

    /**
     * Try to login user based on credensials
     *
     * @param string $username
     * @param string $password
     * @return enum const LOGIN_* (look above)
     */
    public static function checkCredensials($username, $password){

        // check if there is not too manby login tries
        if(self::areTooManyLoginAttempts()){
            return self::LOGIN_TOOMUCHLOGINS;
        }

        // add 'permanent_login' collumn to collumns to read from DB
        $neededUserColumns = User::AUTH_COLLUMS;

        // try to find the user based on given username/email
        if( ($user = User::fromUsernameFactory($username, $neededUserColumns)) ||
            ($user = User::fromEmailFactory($username, $neededUserColumns))){

            if($user->isActive()){

                // user active - check password
                if ( PasswordManager::verifyPassword($user->getUserId(), $password) ) {

                    //login OK, password OK
                    self::initOcSession($user);
                    self::initContextVars($user);

                    return self::LOGIN_OK;
                }
            }else{
                // skip saving this login try - this is just inactive user
                self::clearOcSession();
                self::clearContextVars();
                return self::LOGIN_USERNOTACTIVE;
            }
        }

        // BAD username or password given
        self::saveLoginFail();
        self::clearOcSession();
        self::clearContextVars();
        return self::LOGIN_BADUSERPW;

    }

    /**
     * Logout current user
     */
    public static function logout(){

        self::clearOcSession();
        self::clearContextVars();
    }


    private static function initOcSession(User $user){

        // generate uniq, random sessionId
        $sessionId = self::generateSessionId();

        // save sessionId at serverSide (in PHP session)
        self::setLoggedUserSessionId($sessionId);

        // save sessionId in AUTH_COOKIE
        self::initAuthCookie($sessionId);

        // save session in sys_sessions DB
        self::insertOcSessionToDb(
            $sessionId, $user->getUserId(), $user->usePermanentLogin());
    }

    private static function clearOcSession(){

        // delete session from DB
        if( $sessionId = self::getLoggedUserSessionId() ){
            self::deleteOcSessionFromDb( $sessionId );
        }

        // clear sessionId at serverSide (in PHP session)
        self::setLoggedUserSessionId(null);

        // clear sessionId in AUTH_COOKIE
        self::destroyAuthCookie();

    }


    private static function initContextVars(User $user){

        //init $user in ApplicationContainer
        ApplicationContainer::SetAuthorizedUser($user);

        // set obsolate user_is in session
        $_SESSION['user_id'] = $user->getUserId();

        // set obsolate global $usr[] array
        global $usr;
        $usr['username'] = $user->getUserName();
        $usr['hiddenCacheCount'] = $user->getHiddenGeocachesCount();
        $usr['logNotesCount'] = $user->getLogNotesCount();
        $usr['userFounds'] = $user->getFoundGeocachesCount();
        $usr['notFoundsCount'] = $user->getNotFoundGeocachesCount();
        $usr['userid'] = $user->getUserId();
        $usr['email'] = $user->getEmail();
        $usr['country'] = $user->getCountry();
        $usr['latitude'] = $user->getHomeCoordinates()->getLatitude();
        $usr['longitude'] = $user->getHomeCoordinates()->getLongitude();
        $usr['admin'] = $user->isAdmin();

    }

    private static function clearContextVars(){

        // clear AppContainer
        ApplicationContainer::SetAuthorizedUser(null);

        // clear obsolate global $usr[] array
        global $usr;
        $usr = false;

        // set obsolate user_is in session
        unset($_SESSION['user_id']);

        if(!session_id()){
            // there is initialized session - destroy it!
            session_destroy();
        }
    }


    private static function getAuthCookieName(){
        global $config;
        return $config['cookie']['name'].'_auth';
    }

    private static function initAuthCookie($sessionId){

        $cookieExpiry = time() + self::PERMANENT_LOGIN_TIMEOUT;

        $result = Cookie::setCookie(self::getAuthCookieName(), $sessionId, $cookieExpiry, '/',
            false, true, Cookie::SAME_SITE_RESTRICTION_STRICT);

        if(!$result){
            Debug::errorLog(__METHOD__.": Can't set AUTH cookie");
        }
    }

    private static function getSessionFromAuthCookie(){
        if(isset($_COOKIE[self::getAuthCookieName()])){
            return $_COOKIE[self::getAuthCookieName()];
        }

        return null;
    }

    private static function destroyAuthCookie(){

        unset($_COOKIE[self::getAuthCookieName()]);

        $result = Cookie::deleteCookie(self::getAuthCookieName());
        if(!$result){
            Debug::errorLog(__METHOD__.": Can't delete AUTH cookie");
        }
    }

    public static function isAuthCookiePresent(){
        return isset($_COOKIE[self::getAuthCookieName()]);
    }


    private static function getLoggedUserSessionId(){
        if(isset($_SESSION[self::SESSION_ID_KEY])){
            return $_SESSION[self::SESSION_ID_KEY];
        }else{
            return null;
        }
    }

    private static function setLoggedUserSessionId($sessionId){
        if(!is_null($sessionId)){
            $_SESSION[self::SESSION_ID_KEY] = $sessionId;
        }else{
            unset($_SESSION[self::SESSION_ID_KEY]);
        }
    }


    private static function areTooManyLoginAttempts(){
        $db = self::db();

        // remove login tries older that 1 HOUR
        $db->query("DELETE FROM sys_logins
                    WHERE timestamp < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

        // find number of latest login fails
        $lastHourLoginFails = $db->multiVariableQueryValue(
            "SELECT COUNT(*) FROM sys_logins
             WHERE remote_addr=:1", 0, $_SERVER['REMOTE_ADDR']);

        return $lastHourLoginFails > self::MAX_LOGIN_TRIES_PER_HOUR;
    }


    private static function insertOcSessionToDb($sessionId, $userId, $permanentSession){
        self::db()->multiVariableQuery(
            "INSERT INTO sys_sessions (uuid, user_id, permanent, last_login)
             VALUES (:1, :2, :3, NOW())", $sessionId, $userId, ($permanentSession ? 1 : 0));
    }

    private static function getUserIdFromOcSession($sessionId){
        $db = self::db();
        $stmt = $db->multiVariableQuery(
            "SELECT *, TIMESTAMPDIFF(SECOND, last_login, NOW()) AS lastTouch
             FROM sys_sessions WHERE uuid = :1 LIMIT 1", $sessionId);

        $row = $db->dbResultFetchOneRowOnly($stmt);
        if($row && is_array($row) && isset($row['user_id'])){

            // check if session is not obsolate
            if(
                ( $row['permanent'] == 1 && $row['lastTouch'] > self::PERMANENT_LOGIN_TIMEOUT ) ||
                ( $row['permanent'] == 0 && $row['lastTouch'] > self::LOGIN_TIMEOUT )
              ){

               // obsolate session found 0 delete this and other obsolate sessions
               self::deleteObsolateOcSessions();
               return null;
            }

            // touch last_login from time-to-time
            if( $row['lastTouch'] > self::LOGIN_TIMEOUT/10){
                $db->multiVariableQuery(
                    "UPDATE sys_sessions SET last_login=NOW() WHERE uuid = :1", $sessionId);
            }
            return $row['user_id'];
        }

        // there is no session or user-id
        return null;

    }

    private static function deleteOcSessionFromDb($sessionId){
        // delete oc-session from DB
        self::db()->multiVariableQuery(
            "DELETE FROM sys_sessions WHERE uuid = :1 LIMIT 1", $sessionId);
    }

    private static function deleteObsolateOcSessions(){

        $permLoginTimeout = self::PERMANENT_LOGIN_TIMEOUT;
        $loginTimeout = self::LOGIN_TIMEOUT;

        self::db()->query(
            "DELETE FROM sys_sessions
             WHERE (
                permanent = 0
                AND last_login < DATE_SUB( NOW(), INTERVAL $loginTimeout SECOND)
                ) OR (
                permanent = 1
                AND last_login < DATE_SUB( NOW(), INTERVAL $permLoginTimeout SECOND)
                )");

    }

    private static function saveLoginFail(){

        self::db()->multiVariableQuery(
            "INSERT INTO sys_logins (remote_addr, timestamp)
             VALUES (:1, NOW())", $_SERVER['REMOTE_ADDR']);
    }

    private static function generateSessionId(){
        return self::db()->simpleQueryValue('SELECT UUID()',null);
    }

}
