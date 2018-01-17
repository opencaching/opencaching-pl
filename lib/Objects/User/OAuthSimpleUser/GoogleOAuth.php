<?php
/**
 * This class implements Google oAuth process described here:
 * https://developers.google.com/identity/protocols/OAuth2
 *
 * Google API playground:
 * https://developers.google.com/oauthplayground/
 *
 */
namespace lib\Objects\User\OAuthSimpleUser;

use Utils\Generators\Uuid;
use Utils\Uri\Uri;


/**
 * This class handle oAuth authentication by Google services.
 * Steps in process:
 * - step 1: redirect user to right Google page to login
 * - step 2: after authorization Google redirect user to given callback (OC) page
 * - step 3: data given by Google should be check (inspect the state, take a code...)
 * - step 4: retrive access_token from code given by Google
 * - step 5: retrive user data from Google
 *
 */
class GoogleOAuth extends OAuthSimpleUserBase
{
    private $accessToken=null;   // Google access token value

    /* private */ const SESSION_VAR = 'googleOAuthVar';
    /* private */ const USERINFO_SCOPE = 'https://www.googleapis.com/auth/userinfo.email';

    public static function isEnabledForUsers()
    {
        global $config;
        return $config['oAuth']['google']['prodEnabled'];
    }

    public static function isEnabledForTests()
    {
        global $config;
        return $config['oAuth']['google']['testEnabled'];
    }

    /**
     * This method returns url (step 1) where user should be redirect to start
     * oAuth process
     *
     * @param string $redirectUrl - url to which Google should redirect user after authorization
     * @param boolean $urlForHtml - encode to use in HTML
     * @return string - url
     */
    public static function getOAuthStartUrl($redirectUrl, $urlForHtml=true)
    {
        $stateStr = Uuid::create();
        self::setStateSessionVar($stateStr);

        $redirectUrl = urldecode($redirectUrl);

        $url="https://accounts.google.com/o/oauth2/v2/auth?".
            "scope=" . urlencode(self::USERINFO_SCOPE) .
            "&access_type=offline&include_granted_scopes=true" .
            "&state=$stateStr" .
            "&redirect_uri=$redirectUrl" .
            "&response_type=code" .
            "&client_id=" . self::getClientId();

        if($urlForHtml){
            return htmlspecialchars($url);
        }else{
            return $url;
        }
    }

    /**
     * This method should be call from within callback
     * to which user is redirected from Google services after authorization
     */
    public static function oAuthCallbackHandler()
    {
        $instance = new self();
        // step 3: check if user is authorized by Google
        if( !$instance->isUserAuthorizedByGoogle()){
            return $instance;
        }
        // step 4: retrive access-token from Google
        if( !$instance->isAccessTokenRetrived()){
            return $instance;
        }

        // step 5: retrive user data from Google
        if( !$instance->isUserDataReady() ){
            return $instance;
        }

        return $instance;
    }

    /**
     * step 3: check the state and code returned by Google
     */
    private function isUserAuthorizedByGoogle()
    {
        if( !isset($_GET['code']) ){
            $this->error = self::ERROR_NO_CODE;
            return false;
        }

        if( !isset($_GET['state']) ){
            $this->error = self::ERROR_NO_STATE;
            return false;
        }

        // check state string
        if(!self::checkStateSessionVar($_GET['state'])){
            $this->error = self::ERROR_STATE_INVALID;
            return false;
        }

        // OK, code present, state checked!
        return true;
    }

    /**
     * step 4: replace code on access_token
     */
    private function isAccessTokenRetrived()
    {
        $code = $_GET['code'];

        // Google suggests using POST for token retrive
        $tokenServiceUrl = 'https://www.googleapis.com/oauth2/v4/token';
        $postData = [
            'code' => $code,
            'client_id' => self::getClientId(),
            'client_secret' => self::getClientSecret(),
            'redirect_uri' => Uri::getCurrentRequestUri(),
            'grant_type' => 'authorization_code'
        ];
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded",
                'content' => http_build_query($postData),
                'timeout' => 60
            )
        );

        $context  = stream_context_create($opts);
        $response = file_get_contents($tokenServiceUrl, false, $context);
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
     * step 5: retrive user data from Google
     */
    private function isUserDataReady()
    {

        $url = 'https://www.googleapis.com/userinfo/v2/me?'.
            "access_token={$this->accessToken}";

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
    }


    private static function getClientId()
    {
        global $config;
        return $config['oAuth']['google']['clientId'];
    }

    private static function getClientSecret()
    {
        global $config;
        return $config['oAuth']['google']['clientSecret'];
    }


}

