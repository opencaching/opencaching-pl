<?php
namespace Controllers;

use lib\Objects\CacheSet\CacheSet;
use lib\Objects\User\OAuthSimpleUser\FacebookOAuth;
use lib\Objects\User\OAuthSimpleUser\GoogleOAuth;
use Utils\Text\UserInputFilter;
use Utils\Uri\Uri;
use lib\Objects\User\UserPreferences\UserPreferences;
use lib\Objects\User\UserPreferences\TestUserPref;
use lib\Objects\ChunkModels\DynamicMap\CacheMarkerModel;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;
use lib\Objects\ChunkModels\DynamicMap\CacheSetMarkerModel;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\MultiCacheStats;

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


    public function userPreferences()
    {

        // is key supported (proper config done)
        d(UserPreferences::isKeyAllowed(TestUserPref::KEY));

        // get defaults for key
        d(UserPreferences::getUserPrefsByKey(TestUserPref::KEY));

        // save some value
        d(UserPreferences::savePreferencesJson(TestUserPref::KEY, '{"fooVar":"X"}'));

        // read some value
        d(UserPreferences::getUserPrefsByKey(TestUserPref::KEY));


    }

    /**
     * This method allows test of dynamic map chunk
     */
    public function dynamicMap()
    {
        $this->view->setTemplate('test/dynamicMap');
        $this->view->loadJQuery();
        $this->view->loadGMapApi();

        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/test/dynamicMap.css'));


        $mapModel = new DynamicMapModel();

        // get some geopaths
        $csToArchive = CacheSet::getCacheSetsToArchive();

        $mapModel->addMarkers(
            CacheSetMarkerModel::class, $csToArchive, function($row){

                $markerModel = new CacheSetMarkerModel();

                $markerModel->id = $row['id'];
                $markerModel->type = $row['type'];
                $markerModel->name = $row['name'];

                $markerModel->icon = CacheSet::GetTypeIcon($row['type']);
                $markerModel->lon = $row['centerLongitude'];
                $markerModel->lat = $row['centerLatitude'];

                return $markerModel;
        });

        // get some caches
        $cachesToShow = MultiCacheStats::getLatestCaches(5);
        $mapModel->addMarkers(
            CacheMarkerModel::class, $cachesToShow, function($row){

                $markerModel = new CacheMarkerModel();

                $markerModel->wp_oc = $row['wp_oc'];
                $markerModel->type = $row['type'];
                $markerModel->name = $row['name'];

                $markerModel->icon = GeoCache::CacheIconByType(
                    $row['type'], $row['status']);

                $markerModel->lon = $row['longitude'];
                $markerModel->lat = $row['latitude'];

                return $markerModel;
            });

        $this->view->setVar('mapModel', $mapModel);


        // and one more map... this should stay empty for now
        $emptyMap = new DynamicMapModel();
        $this->view->setVar('emptyMap', $emptyMap);


        $this->view->buildView();
    }
}

