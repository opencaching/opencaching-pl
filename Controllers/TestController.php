<?php
namespace Controllers;

use lib\Objects\CacheSet\CacheSet;
use lib\Objects\User\OAuthSimpleUser\FacebookOAuth;
use lib\Objects\User\OAuthSimpleUser\GoogleOAuth;
use Utils\Text\UserInputFilter;
use Utils\Uri\Uri;
use lib\Objects\User\UserPreferences\UserPreferences;
use lib\Objects\User\UserPreferences\TestUserPref;
use lib\Objects\ChunkModels\UploadModel;
use lib\Objects\ChunkModels\DynamicMap\CacheMarkerModel;
use lib\Objects\ChunkModels\DynamicMap\CacheWithLogMarkerModel;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;
use lib\Objects\ChunkModels\DynamicMap\CacheSetMarkerModel;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\MultiCacheStats;
use lib\Objects\GeoCache\MultiLogStats;
use lib\Objects\User\User;
use lib\Objects\User\MultiUserQueries;
use lib\Objects\GeoCache\GeoCacheLog;
use Utils\Text\Formatter;
use Utils\Uri\OcCookie;
use lib\Objects\Coordinates\Coordinates;
use Utils\FileSystem\FileUploadMgr;
use lib\Objects\OcConfig\OcConfig;

class TestController extends BaseController
{
    public function __construct(){
        parent::__construct();

        // test pages are only for users with AdvancedUsers role
        $this->redirectNotLoggedUsers();
        if(!$this->loggedUser->hasAdvUserRole()){
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
     * This method allows test dynamic OpenLayers map chunk
     * Could be used as example of dynamic-map-chunk usage
     */
    public function dynamicMap()
    {
        $this->view->setTemplate('test/dynamicMap');
        $this->view->loadJQuery();

        $this->view->addHeaderChunk('openLayers5');

        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/test/dynamicMap.css'));

        $mapModel = new DynamicMapModel();

        // get some geopaths...
        $csToArchive = CacheSet::getCacheSetsToArchive();

        //...and add to map
        $mapModel->addMarkersWithExtractor(
            CacheSetMarkerModel::class, $csToArchive, function($row){

                $markerModel = new CacheSetMarkerModel();

                $markerModel->lat = $row['centerLatitude'];
                $markerModel->lon = $row['centerLongitude'];
                $markerModel->icon = CacheSet::GetTypeIcon($row['type']);

                $markerModel->name = $row['name'];
                $markerModel->link = CacheSet::getCacheSetUrlById($row['id']);

                return $markerModel;
            });

        // get some caches...
        $cachesToShow = MultiCacheStats::getLatestCaches(5);
        // ..and add to map
        $mapModel->addMarkersWithExtractor(
            CacheMarkerModel::class, $cachesToShow, function($row){

                $markerModel = new CacheMarkerModel();

                $markerModel->lat = $row['latitude'];
                $markerModel->lon = $row['longitude'];
                $markerModel->icon = GeoCache::CacheIconByType($row['type'], $row['status']);

                $markerModel->wp = $row['wp_oc'];
                $markerModel->name = $row['name'];
                $markerModel->username = $row['username'];
                $markerModel->link = GeoCache::GetCacheUrlByWp($row['wp_oc']);

                return $markerModel;
            });


        // get some caches with logs...
        $caches = [];
        $userIds = [];
        foreach(MultiCacheStats::getGeocachesById([1,2,3,4,5]) as $c){
            $caches[$c['cache_id']] = $c;
            $userIds[$c['user_id']] = null;
        }

        foreach(MultiLogStats::getLastLogForEachCache(array_keys($caches),
            ['id as log_id','cache_id','text','date','type as log_type','user_id as log_user_id']) as $log){
                $caches[$log['cache_id']] = array_merge($log, $caches[$log['cache_id']]);
                $userIds[$log['log_user_id']] = null;
        }

        $users = MultiUserQueries::GetUserNamesForListOfIds(array_keys($userIds));

        foreach ($caches as &$c){
            $c['owner'] = $users[$c['user_id']];
            $c['log_username'] = $users[$c['log_user_id']];
        }

        // ...and add to map
        $mapModel->addMarkersWithExtractor(
            CacheWithLogMarkerModel::class, $caches, function($row){

                $markerModel = new CacheWithLogMarkerModel();

                $markerModel->lon = $row['longitude'];
                $markerModel->lat = $row['latitude'];
                $markerModel->icon = GeoCache::CacheIconByType($row['type'], $row['status']);

                $markerModel->wp = $row['wp_oc'];
                $markerModel->name = $row['name'];
                $markerModel->link = GeoCache::GetCacheUrlByWp($row['wp_oc']);
                $markerModel->username = $row['owner'];


                $markerModel->log_icon = GeoCacheLog::GetIconForType($row['log_type']);
                $markerModel->log_text = strip_tags($row['text'], '<br><p>');
                $markerModel->log_username = $row['log_username'];
                $markerModel->log_typeName = tr(GeoCacheLog::typeTranslationKey($row['type']));
                $markerModel->log_link = GeoCacheLog::getLogUrlByLogId($row['log_id']);
                $markerModel->log_date = Formatter::date($row['date']);

                return $markerModel;
            });

        $this->view->setVar('mapModel', $mapModel);


        // and one more map...
        $emptyMap = new DynamicMapModel();
        $emptyMap->setInfoMessage('Just empty map...');
        $this->view->setVar('emptyMap', $emptyMap);

        // and model for drawing
        $drawMap = new DynamicMapModel();
        $this->view->setVar('drawMap', $drawMap);

        $this->view->buildView();
    }


    /**
     * This is test of file upload with UploadChunk
     */
    public function upload()
    {
        $this->redirectNotLoggedUsers();

        $this->view->setTemplate('test/upload');
        $this->view->loadJQuery();


        $this->view->addHeaderChunk('upload/upload');
        $this->view->addHeaderChunk('handlebarsJs');


        // prepare Upload Model
        /** @var UploadModel */
        $uploadModel = UploadModel::TestTxtUploadFactory();
        $this->view->setVar('uploadModelJson', $uploadModel->getJsonParams());

        $this->view->buildView();
    }

    /**
     * This is test of server-side actions for file upload with UploadChunk
     */
    public function uploadAjax()
    {
        // only logged users can test
        $this->checkUserLoggedAjax();

        // use the same upload model
        $uploadModel = UploadModel::TestTxtUploadFactory();

        // save uploaded files
        $newFiles = FileUploadMgr::processFileUpload($uploadModel);

        // check the upload status
        if(!is_array($newFiles)){
          // something goes wrong - error returned!
          $this->ajaxErrorResponse($newFiles, 500);
          exit;
        }

        // FileUploadMgr returns array of new files saved in given directory on server
        // any specific actions can be done in this moment - for example DB update

        // add correct url to uploaded files before return to browser
        $uploadModel->addUrlBaseToNewFilesArray($newFiles);

        // return to browser the list of files saved on server
        $this->ajaxJsonResponse($newFiles);
    }

    /**
     * This method test the cookie work
     */
    public function cookieTest()
    {
        echo "Cookie test";

        d($_COOKIE);

        OcCookie::debug();

        OcCookie::set('hello', 'buu');

        OcCookie::debug();

        OcCookie::saveInHeader();

        OcCookie::set('bay!', 'foo');

        OcCookie::saveInHeader();

        OcCookie::debug();

        d(headers_list());

    }

    public function registration()
    {
        if($this->isUserLogged()){
            return $this->alreadyRegistered();
        }

        $this->view->loadJQuery();
        $this->view->setTemplate('test/userRegistration');
        // local css
        $this->view->addLocalCss( Uri::getLinkWithModificationTime(
            '/tpl/stdstyle/test/userRegistration.css'));


        $this->view->buildView();
    }

    private function alreadyRegistered()
    {
        $this->view->setTemplate('test/alreadyRegistered');


        $this->view->buildView();
    }

    public function checkConfig()
    {
        d($this->ocConfig->getEmailConfig());

        d(OcConfig::getEmailAddrOcTeam());
        d(OcConfig::getOcteamEmailsSignature());
        d(OcConfig::getEmailAddrNoReply());
        d(OcConfig::getEmailAddrTechAdmin());
        d(OcConfig::getEmailSubjectPrefixForOcTeam());
        d(OcConfig::getEmailSubjectPrefix());
    }

}
