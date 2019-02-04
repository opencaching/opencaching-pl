<?php
namespace lib\Objects\User;

use Utils\Debug\Debug;
use Utils\Email\Email;
use Utils\Email\EmailFormatter;
use Utils\Uri\CookieBase;
use lib\Objects\ApplicationContainer;
use lib\Objects\BaseObject;
use Utils\Uri\SimpleRouter;
use Utils\Generators\TextGen;

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
    public static function verify()
    {

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
     * Try to login user based on credentials
     *
     * @param string $username
     * @param string $password
     * @return enum const LOGIN_* (look above)
     */
    public static function checkCredentials($username, $password)
    {

        // check if there is not too manby login tries
        if(self::areTooManyLoginAttempts()){
            return self::LOGIN_TOOMUCHLOGINS;
        }

        // add 'permanent_login' collumn to collumns to read from DB
        $neededUserColumns = User::AUTH_COLUMNS;

        // try to find the user based on given username/email
        if( ($user = User::fromUsernameFactory($username, $neededUserColumns)) ||
            ($user = User::fromEmailFactory($username, $neededUserColumns))){

            if($user->isActive()){

                // user active - check password
                if ( PasswordManager::verifyPassword($user->getUserId(), $password) ) {

                    //login OK, password OK
                    self::initOcSession($user);
                    self::initContextVars($user);

                    User::updateLastLogin($user->getUserId());

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
    public static function logout()
    {

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

        // set obsolete user_is in session
        $_SESSION['user_id'] = $user->getUserId();

        // set obsolete global $usr[] array
        global $usr;
        $usr['username'] = $user->getUserName();
        $usr['userFounds'] = $user->getFoundGeocachesCount();
        $usr['userid'] = $user->getUserId();
        $usr['email'] = $user->getEmail();
        $usr['latitude'] = $user->getHomeCoordinates()->getLatitude();
        $usr['longitude'] = $user->getHomeCoordinates()->getLongitude();
        $usr['admin'] = $user->hasOcTeamRole();

    }

    private static function clearContextVars(){

        // clear AppContainer
        ApplicationContainer::SetAuthorizedUser(null);

        // clear obsolete global $usr[] array
        global $usr;
        $usr = false;

        // set obsolete user_is in session
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

        $result = CookieBase::setCookie(self::getAuthCookieName(), $sessionId, $cookieExpiry, '/',
            false, true, CookieBase::SAME_SITE_RESTRICTION_LAX);

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

        $result = CookieBase::deleteCookie(self::getAuthCookieName());
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
                    WHERE `timestamp` < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

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
            "SELECT user_id, permanent, TIMESTAMPDIFF(SECOND, last_login, NOW()) AS lastTouch
             FROM sys_sessions WHERE uuid = :1 LIMIT 1", $sessionId);

        $row = $db->dbResultFetchOneRowOnly($stmt);
        if($row && is_array($row) && isset($row['user_id'])){

            // check if session is not obsolete
            if(
                ( $row['permanent'] == 1 && $row['lastTouch'] > self::PERMANENT_LOGIN_TIMEOUT ) ||
                ( $row['permanent'] == 0 && $row['lastTouch'] > self::LOGIN_TIMEOUT )
              ){

               // obsolete session found 0 delete this and other obsolete sessions
               self::deleteObsoleteOcSessions();
               return null;
            }

            // touch last_login from time-to-time
            if( $row['lastTouch'] > self::LOGIN_TIMEOUT/10){
                $db->multiVariableQuery(
                    "UPDATE sys_sessions SET last_login=NOW() WHERE uuid = :1", $sessionId);

                User::updateLastLogin($row['user_id']); //also update last_login in user table
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

    private static function deleteObsoleteOcSessions(){

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

    /**
     * Returns array with users with lastlog in last quater.
     *
     * @return array with columns "user_id, usernames"
     */
    public static function getOnlineUsersFromDb()
    {
        $stmt = self::db()->multiVariableQuery(
            "SELECT DISTINCT s.user_id AS user_id, u.username
             FROM sys_sessions AS s
                LEFT JOIN user AS u USING (user_id)
             WHERE s.last_login > DATE_SUB( NOW(), INTERVAL 15 MINUTE) ");

        $result = [];
        while($row = self::db()->dbResultFetch($stmt)){
            $result[ $row['user_id'] ] = $row['username'];
        }

        return $result;
    }


    private static function saveLoginFail(){

        self::db()->multiVariableQuery(
            "INSERT INTO sys_logins (remote_addr, `timestamp`)
             VALUES (:1, NOW())", $_SERVER['REMOTE_ADDR']);
    }

    private static function generateSessionId(){
        return self::db()->simpleQueryValue('SELECT UUID()',null);
    }

    /**
     * Generates new code for password change, stores it in DB
     * and sends e-mail with link to change password to $user
     *
     * @param User $user - recipient of mail
     * @return boolean - true on success
     */
    public static function sendPwCode(User $user)
    {
        // Stage 1 - generate code and store in DB
        $code = TextGen::randomText(36);
        $result = self::db()->multiVariableQuery('
            UPDATE `user`
            SET `new_pw_code` = :1,
                `new_pw_exp` = DATE_ADD( NOW(), INTERVAL 24 HOUR)
            WHERE `user_id` = :2',
            $code, $user->getUserId());
        if (is_null($result)) {
            return false;
        }
        // Stage 2 - send code via e-mail
        $userTxt = urlencode($user->getUserName());
        $userMessage = new EmailFormatter(__DIR__ . '/../../../tpl/stdstyle/email/reminder_password.email.html', true);
        $userMessage->setVariable('newPwUri', SimpleRouter::getAbsLink('UserAuthorization', 'newPasswordInput', [$userTxt, $code]));
        $userMessage->addFooterAndHeader($user->getUserName());
        $email = new Email();
        $email->addToAddr($user->getEmail());
        $email->setReplyToAddr(ApplicationContainer::Instance()->getOcConfig()->getNoreplyEmailAddress());
        $email->setFromAddr(ApplicationContainer::Instance()->getOcConfig()->getNoreplyEmailAddress());
        $email->addSubjectPrefix(ApplicationContainer::Instance()->getOcConfig()->getMailSubjectPrefixForSite());
        $subject = tr('newpw_mail_subject') . ' ' . ApplicationContainer::Instance()->getOcConfig()->getSiteName();
        $email->setSubject($subject);
        $email->setHtmlBody($userMessage->getEmailContent());
        $result = $email->send();
        if (! $result) {
            error_log(__METHOD__ . ': Mail sending failure to: ' . $user->getEmail());
        }
        return $result;
    }

    /**
     * Check if new password $code is valid for $user
     *
     * @param User $user
     * @param string $code
     * @return boolean
     */
    public static function checkPwCode(User $user, $code) {
        return (bool) self::db()->multiVariableQueryValue('
            SELECT COUNT(*)
            FROM `user`
            WHERE `user_id` = :1
                AND `is_active_flag` = TRUE
                AND `new_pw_code` LIKE :2
                AND `new_pw_exp` > NOW()
            LIMIT 1
            ', 0, $user->getUserId(), $code);
    }

    /**
     * Removes new password code from DB for $user
     *
     * @param User $user
     * @return boolean
     */
    public static function removePwCode(User $user)
    {
        $result = self::db()->multiVariableQuery('
            UPDATE `user`
            SET `new_pw_code` = NULL,
                `new_pw_exp` = NULL
            WHERE `user_id` = :1',
            $user->getUserId());
        return (! is_null($result));
    }

    /**
     * Removes all sessions of $user from DB. Used while lock/ban user
     *
     * @param User $user
     * @return boolean
     */
    public static function removeUserSessions(User $user)
    {
        return (null !== self::db()->multiVariableQuery(
            "DELETE FROM sys_sessions WHERE user_id = :1", $user->getUserId()));
    }
}
