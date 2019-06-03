<?php
namespace src\Controllers;

use src\Models\CacheSet\CacheSet;
use src\Models\User\OAuthSimpleUser\FacebookOAuth;
use src\Models\User\OAuthSimpleUser\GoogleOAuth;
use src\Utils\Text\UserInputFilter;
use src\Utils\Uri\Uri;
use src\Models\User\UserPreferences\UserPreferences;
use src\Models\User\UserPreferences\TestUserPref;
use src\Models\ChunkModels\UploadModel;
use src\Models\ChunkModels\DynamicMap\CacheMarkerModel;
use src\Models\ChunkModels\DynamicMap\CacheWithLogMarkerModel;
use src\Models\ChunkModels\DynamicMap\DynamicMapModel;
use src\Models\ChunkModels\DynamicMap\CacheSetMarkerModel;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\MultiCacheStats;
use src\Models\GeoCache\MultiLogStats;
use src\Models\User\MultiUserQueries;
use src\Models\GeoCache\GeoCacheLog;
use src\Utils\Text\Formatter;
use src\Utils\Uri\OcCookie;
use src\Utils\FileSystem\FileUploadMgr;
use src\Models\OcConfig\OcConfig;
use src\Utils\Uri\SimpleRouter;
use src\Models\Coordinates\Coordinates;
use src\Models\Coordinates\Altitude;
use src\Controllers\Cron\Jobs\AltitudeUpdateJob;

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
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            switch($method){
                case "__construct":
                case "isCallableFromRouter":
                case "index":
                    // skip methods above
                    break;
                default:
                    $link = SimpleRouter::getLink(self::class, $method);
                    echo "<a href='$link'>$method</a> <br/>";
            }
        }
    }

    public function lorenIpsumContent(){
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
            Uri::getLinkWithModificationTime('/views/test/oAuth.css'));

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
            Uri::getLinkWithModificationTime('/views/test/oAuth.css'));

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
            Uri::getLinkWithModificationTime('/views/test/dynamicMap.css'));

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
        foreach(MultiCacheStats::getGeocachesDataById([1,2,3,4,5]) as $c){
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

        try{
            // save uploaded files
            $newFiles = FileUploadMgr::processFileUpload($uploadModel);

        } catch (\RuntimeException $e){
            // some error occured on upload processing
            $this->ajaxErrorResponse($e->getMessage(), 500);
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
            '/views/test/userRegistration.css'));


        $this->view->buildView();
    }

    private function alreadyRegistered()
    {
        $this->view->setTemplate('test/alreadyRegistered');


        $this->view->buildView();
    }

    public function checkConfig()
    {

        d(OcConfig::getEmailAddrOcTeam());
        d(OcConfig::getOcteamEmailsSignature());
        d(OcConfig::getEmailAddrNoReply());
        d(OcConfig::getEmailAddrTechAdmin());
        d(OcConfig::getEmailSubjectPrefixForOcTeam());
        d(OcConfig::getEmailSubjectPrefix());
    }

    public function routerTester($arg1=null, $arg2=null)
    {
        d($_GET);
        d($arg1);
        d($arg2);

        $link = SimpleRouter::getLink(self::class, 'routerTester',[$arg1, $arg2]);
        echo "<a href='$link'>GO</a>";
    }

    public function altitudeTest($lat=null, $lon=null)
    {
        if(!$lat){
            $lat = 54;
        }
        if(!$lon){
            $lon = 18;
        }

        $coords = Coordinates::FromCoordsFactory($lat, $lon);
        $altitude = Altitude::getAltitude($coords);
        d($coords);
        d($altitude);
    }
}
