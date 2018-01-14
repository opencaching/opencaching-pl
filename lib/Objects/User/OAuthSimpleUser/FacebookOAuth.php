<?php
/**
 * This class implements Facebook oAuth process described here:
 * https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow
 *
 * FB API playground:
 * https://developers.facebook.com/tools/explorer?method=GET&path=me%2Fpermissions&version=v2.11
 *
 * How it works:
 *  - stage I:   redirect user to FB
 *  - stage II:  user log in to FB - FB will sends user back to OC to given redirect
 *  - stage III: callback handler check data returned by FB - there should be an oAuth code
 *  - stage IV:  callback handler change code -> access_token
 *  - stage V:   get user info
 *  - stage VI:  (optional) check permission if email is not provided
 *
 */

namespace lib\Objects\User\OAuthSimpleUser;

use Utils\Generators\Uuid;
use Utils\Uri\Uri;


class FacebookOAuth  extends OAuthSimpleUserBase
{

    private $accessToken;   // FB token value

    /* private */ const SDK_VER = 'v2.11';
    /* private */ const SESSION_VAR = 'fbOAuthVar';

    public static function isEnabledForUsers()
    {
        global $config;
        return $config['oAuth']['facebook']['prodEnabled'];
    }

    public static function isEnabledForTests()
    {
        global $config;
        return $config['oAuth']['facebook']['testEnabled'];
    }

    /**
     * Before STAGE I: prepare URL for FB login
     *
     * @param string $redirectUrl
     * @param boolean $urlForHtml
     * @return string
     */
    public static function getOAuthStartUrl($redirectUrl, $urlForHtml=true)
    {
        // generate and set in session random string used to identify request
        $stateStr = Uuid::create();
        self::setStateSessionVar($stateStr);

        $redirectUrl = urldecode($redirectUrl);

        $url = 'https://www.facebook.com/'.self::SDK_VER.'/dialog/oauth?' .
            "client_id=" . self::getAppId() .
            "&redirect_uri=$redirectUrl" .
            "&scope=email" .
            "&state=$stateStr";

        if($urlForHtml){
            return htmlspecialchars($url);
        }else{
            return $url;
        }
    }

    /**
     * this handle process from STAGE II
     * @return OAuthSimpleUserBase - instance of this object
     */
    public static function oAuthCallbackHandler()
    {
        $instance = new self();

        // STAGE III: check if user is authorized by FB
        if( !$instance->isUserAuthorizedByFb() ){
            return $instance;
        }

        // STAGE IV: retrive access-token from FB
        if( !$instance->isAccessTokenRetrived() ){
            return $instance;
        }
        // STAGE V: retrive user data from FB
        if( !$instance->isUserDataReady() ){
            return $instance;
        }

        return $instance;
    }


    /**
     * STAGE III: check if code is present and state is the same as before
     *
     * @return boolean
     */
    private function isUserAuthorizedByFb()
    {
        if( isset($_GET['error'])){

            $this->error = self::ERROR_EXT_DESC;

            $err = $_GET['error'];
            $errReason = isset($_GET['error_reason'])?isset($_GET['error_reason']):'';
            $errDesc = isset($_GET['error_description'])?isset($_GET['error_description']):'';

            $this->errorDesc = "Facebook says: $err ($errDesc - $errReason)";

            return false;
        }

        if( !isset($_GET['code']) ){
            $this->error = self::ERROR_NO_CODE;
            return false;
        }

        if(!isset($_GET['state']) ){
            $this->error = self::ERROR_NO_STATE;
            return false;
        }

        // check state string
        if(!self::checkStateSessionVar($_GET['state'])){
            $this->error = self::ERROR_STATE_INVALID;
            return false;
        }

        // we are OK: code present, state checked - lets continue
        return true;
    }

    /**
     * STAGE IV: retrive FB access-token based on given code
     */
    private function isAccessTokenRetrived()
    {
        $redirect = urlencode(Uri::getCurrentRequestUri());
        $code = $_GET['code'];

        $url = "https://graph.facebook.com/".self::SDK_VER."/oauth/access_token?".
               "client_id=" . self::getAppId() .
               "&redirect_uri=$redirect" .
               "&client_secret=" . self::getAppSecret() .
               "&code=$code";

        // send query
        $response = file_get_contents($url);
        if($response === false){
            $this->error = self::ERROR_CANT_GET_TOKEN;
            return false;
        }

        $respObj = json_decode($response);
        if(is_null($respObj)){
            $this->error = self::ERROR_INVALID_TOKEN_JSON;
            return false;
        }

        $this->accessToken = $respObj->access_token;

        return true;
    }


    /**
     * STAGE V: retrive user info
     */
    private function isUserDataReady()
    {
        $url = "https://graph.facebook.com/" . self::SDK_VER .
        "/me?fields=id,name,email&access_token={$this->accessToken}";

        $response = file_get_contents($url);
        if($response === false){
            $this->error = self::ERROR_CANT_RETRIVE_USER_DATA;
            return false;
        }

        $respObj = json_decode($response);

        if(is_null($respObj) || !isset($respObj->name) ||
            !isset($respObj->id) || !isset($respObj->email)
        ){
            $this->error = self::ERROR_INVALID_USER_DATA_JSON;
            return false;
        }

        $this->username = $respObj->name;
        $this->externalId = $respObj->id;
        $this->email = $respObj->email;

        $this->isUserDataLoaded = true;

        return false;
    }

    private function getAppId()
    {
        global $config;
        return $config['oAuth']['facebook']['appId'];
    }

    private function getAppSecret()
    {
        global $config;
        return $config['oAuth']['facebook']['appSecret'];
    }

}