<?php
/**
 * This is definition of user object
 * created in process of authorization by external services
 */

namespace lib\Objects\User\OAuthSimpleUser;


abstract class OAuthSimpleUserBase
{

    protected $isUserDataLoaded = false;  // is data is loaded from ext. service

    protected $error = null;  // error state
    protected $errorDesc;     // additional error desc.

    protected $username;      // username from ext. service
    protected $email;         // email from ext. service
    protected $externalId;    // external unique id

    const SESSION_VAR = '__OAuthSimpleUserBase';

    const ERROR_EXT_DESC = 0;
    const ERROR_NO_CODE = 1;
    const ERROR_NO_STATE = 2;
    const ERROR_STATE_INVALID = 3;
    const ERROR_CANT_GET_TOKEN = 4;
    const ERROR_INVALID_TOKEN_JSON = 5;
    const ERROR_CANT_RETRIVE_USER_DATA = 6;
    const ERROR_INVALID_USER_DATA_JSON = 7;



    /**
     * Method returns url at which user start authorization process by external service
     *
     * @param string $redirect - url to which external oAuth service should redirect user
     *                           after authorization
     *
     * @param boolean $urlForHtml - url is ready to put into HTML (ampersands (&) encoded etc)
     *
     * @return string - url at which user shoud start auth process
     */
    public static abstract function getOAuthStartUrl($redirect, $urlForHtml=true);

    /**
     * This method should be called from oAuth callback called by external service
     * after user authorization.
     *
     *
     * @param string $redirect -
     * @return OAuthSimpleUserBase - instance
     */
    public abstract static function oAuthCallbackHandler();

    /**
     * @return true if this OAuth service is enabled in config
     *              for user use
     */
    public abstract static function isEnabledForUsers();

    /**
     * @return true if this OAuth service is enabled in config
     *              for tests only
     */
    public abstract static function isEnabledForTests();

    /**
     * @return true if user is authorized by external service
     */
    public function isUserAuthorized()
    {
        return $this->isUserDataLoaded;
    }

    /**
     * Return information about the error
     * @return string
     */
    public function getErrorDescription()
    {
        switch($this->error){
            case self::ERROR_EXT_DESC:
                return $this->errorDesc;
            case self::ERROR_NO_CODE:
                return "No code returned";
            case self::ERROR_NO_STATE:
                return "No state returned";
            case self::ERROR_STATE_INVALID:
                return "Invalid state";
            case self::ERROR_CANT_GET_TOKEN:
                return "Can't retrive access token";
            case self::ERROR_INVALID_TOKEN_JSON:
                return "Can't decode JSON with token";
            default:
                return "Unknown error!";
        }
    }

    /**
     * @return string - username based on external service
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * @return string|null - user email based on external service
     *                      or null if email is not provided
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string - external user identifier (unique in external service)
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    protected static function setStateSessionVar($var)
    {
        self::checkSession();

        if( !isset($_SESSION[static::SESSION_VAR]) ||
            !is_array($_SESSION[static::SESSION_VAR])){

                $_SESSION[static::SESSION_VAR] = [];
        }
        $_SESSION[static::SESSION_VAR][$var] = true;
    }

    protected static function checkStateSessionVar($var)
    {
        self::checkSession();

        if( isset($_SESSION[static::SESSION_VAR]) &&
            is_array($_SESSION[static::SESSION_VAR]) &&
            isset($_SESSION[static::SESSION_VAR][$var]) ){

                // state var is single-use only
                unset($_SESSION[static::SESSION_VAR][$var]);
                return true;
        }

        return false;
    }

    protected static function checkSession()
    {
        // be sure session is started
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }

}

