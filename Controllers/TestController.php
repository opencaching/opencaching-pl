<?php
namespace Controllers;

use lib\Objects\User\OAuthSimpleUser\FacebookOAuth;
use lib\Objects\User\OAuthSimpleUser\GoogleOAuth;
use Utils\Uri\Uri;

class TestController extends BaseController
{
    public function __construct(){
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return TRUE;
    }

    public function index()
    {

        $this->view->setTemplate('test/testTemplate');

        $this->view->buildView();
    }

    public function newLayout()
    {
        $this->view->setTemplate('test/testTemplate');

        $this->view->display();
    }

    /**
     * This method allow to init test authorization process based on external service
     */
    public function oAuth()
    {
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/test/oAuth.css'));

        $this->view->setTemplate('test/oAuth');

        $this->view->setVar('fbLink',
            FacebookOAuth::getOAuthStartUrl(
                Uri::getCurrentUriBase().'/Test/oAuthCallback/Facebook'));
        $this->view->setVar('gLink',
            GoogleOAuth::getOAuthStartUrl(
                Uri::getCurrentUriBase().'/Test/oAuthCallback/Google'));

        $this->view->buildView();
    }

    /**
     * This is callback for external services in test authorizatio by exernal services
     * @param string $service - service name
     */
    public function oAuthCallback($service=null)
    {
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/test/oAuth.css'));

        $this->view->setTemplate('test/oAuthCallback');

        switch($service){
            case 'Facebook':
                $oAuth = FacebookOAuth::oAuthCallbackHandler();
                break;
            case 'Google':
                $oAuth = GoogleOAuth::oAuthCallbackHandler();
                break;
            default:
                $this->displayCommonErrorPageAndExit('Unknown oAuth service', 404);
                exit;
        }

        $this->view->setVar('service', $service);

        if(!$oAuth->isUserAuthorized()){
            $this->view->setVar('error', true);
            $this->view->setVar('errorDesc', $oAuth->getErrorDescription());
        }else{
            $this->view->setVar('error', false);
            $this->view->setVar('oAuthObj', $oAuth);
        }

        $this->view->buildView();
    }

}

