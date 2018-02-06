<?php
namespace Controllers;

use lib\Objects\User\OAuthSimpleUser\FacebookOAuth;
use lib\Objects\User\OAuthSimpleUser\GoogleOAuth;
use Utils\Text\UserInputFilter;
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

        $fbTestEnabled = FacebookOAuth::isEnabledForTests();
        $gTestEnabled = GoogleOAuth::isEnabledForTests();

        $this->view->setVar('fbTestEn', $fbTestEnabled);
        if($fbTestEnabled){
            $this->view->setVar('fbLink',
                FacebookOAuth::getOAuthStartUrl(
                    Uri::getCurrentUriBase().'/Test/oAuthCallback/Facebook'));
        }


        $this->view->setVar('gTestEn', $gTestEnabled);
        if($gTestEnabled){
            $this->view->setVar('gLink',
                GoogleOAuth::getOAuthStartUrl(
                    Uri::getCurrentUriBase().'/Test/oAuthCallback/Google'));
        }

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
                if(FacebookOAuth::isEnabledForTests()){
                    $oAuth = FacebookOAuth::oAuthCallbackHandler();
                }else{
                    $this->displayCommonErrorPageAndExit('Unknown oAuth service', 404);
                    exit;
                }
                break;
            case 'Google':
                if(GoogleOAuth::isEnabledForTests()){
                    $oAuth = GoogleOAuth::oAuthCallbackHandler();
                }else{
                    $this->displayCommonErrorPageAndExit('Unknown oAuth service', 404);
                    exit;
                }
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


    /**
     * This method allow testing of HTML strings cleaning used by OC code
     */
    public function userInputFilter()
    {
        $this->view->setTemplate('test/userInputFilterTest');

        if(isset($_POST['html'])){
            $html = htmlentities($_POST['html']);
            $context = [];
            $rawCleanedHtml = UserInputFilter::purifyHtmlString($_POST['html'], $context);
            if(isset($context['errors'])){
                $errors = $context['errors'];
                $errorHTML = $errors->getHTMLFormatted(UserInputFilter::getConfig());
            }else{
                $errorHTML = '';
            }
            $cleanedHTML = htmlentities($rawCleanedHtml);

        }else{
            $html='';
            $errorHTML='';
            $rawCleanedHtml = '';
            $cleanedHTML = '';
        }

        $this->view->setVar('html', $html);
        $this->view->setVar('errorHTML', $errorHTML);
        $this->view->setVar('rawCleanedHtml', $rawCleanedHtml);
        $this->view->setVar('cleanedHTML', $cleanedHTML);

        $this->view->buildView();

    }
}

