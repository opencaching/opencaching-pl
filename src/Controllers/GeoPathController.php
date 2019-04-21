<?php
namespace src\Controllers;

use src\Utils\Text\UserInputFilter;
use src\Models\CacheSet\CacheSet;
use src\Utils\FileSystem\FileUploadMgr;
use src\Utils\Img\OcImage;
use src\Models\CacheSet\GeopathLogoUploadModel;
use src\Models\OcConfig\OcConfig;
use src\Utils\Generators\Uuid;
use src\Models\GeoCache\GeoCache;
use src\Utils\I18n\I18n;

class GeoPathController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {
        $this->searchByName(); // Temporary. To be removed in the future
    }

    /**
     * Search GeoPaths by name. Used by search engine in topline
     */
    public function searchByName()
    {
        if (isset($_REQUEST['name'])) {
            $searchStr = UserInputFilter::purifyHtmlString($_REQUEST['name']);
            $searchStr = strip_tags($searchStr);
        } else {
            $searchStr = null;
        }
        $this->view->setVar('geoPaths', CacheSet::getCacheSetsByName($searchStr));
        $this->view->setVar('searchStr', $searchStr);
        $this->view->setTemplate('geoPath/searchByName');
        $this->view->buildView();
    }

    /**
     * Uploading of the new geoPath logo
     * This is fast replacement for ajaxImage.php
     */
    public function uploadLogoAjax($geoPathId)
    {
        if (!$this->loggedUser) {
            $this->ajaxErrorResponse("User not authorized!");
        }

        if (!$geoPath = CacheSet::fromCacheSetIdFactory($geoPathId)){
            $this->ajaxErrorResponse("No such geopath!");
        }

        if (!$geoPath->isOwner($this->loggedUser)) {
            $this->ajaxErrorResponse("Logged user is not an geopath owner!");
        }

        $uploadModel = GeopathLogoUploadModel::forGeopath($geoPathId);

        try {
            $tmpLogoFile = FileUploadMgr::processFileUpload($uploadModel);
        } catch (\RuntimeException $e){
            // some error occured on upload processing
            $this->ajaxErrorResponse($e->getMessage(), 500);
        }

        // FileUploadMgr returns single filename saved in server tmp directory on server

        $newLogoPath = OcConfig::getDynFilesPath(true) . CacheSet::DIR_LOGO_IMG . '/' . Uuid::create();

        // resize the new logo
        $newLogoPath = OcImage::createThumbnail($uploadModel->getDirAtServer().'/'.$tmpLogoFile, $newLogoPath, [250,250]);

        // create URL of the image
        $newLogoFileUrl = CacheSet::DIR_LOGO_IMG .'/'.basename($newLogoPath);

        // new log is ready to use - update DB
        $geoPath->updateLogoImg($newLogoFileUrl);

        $this->ajaxJsonResponse($newLogoFileUrl);
    }

    /**
     * Add cache to the geoPath by cache owner and geopath
     */
    public function addOwnCacheToGeopathAjax($geoPathId, $cacheId)
    {
        $this->checkUserLoggedAjax();

        $geoPath = CacheSet::fromCacheSetIdFactory($geoPathId);
        if(!$geoPath) {
            $this->ajaxErrorResponse("No such geoPath!", self::HTTP_STATUS_NOT_FOUND);
        }

        if(!$geoPath->isOwner($this->loggedUser)){
            $this->ajaxErrorResponse("Logged user is not a geopath owner!", self::HTTP_STATUS_FORBIDEN);
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);
        if(!$cache) {
            $this->ajaxErrorResponse("No such cache!", self::HTTP_STATUS_NOT_FOUND);
        }

        if($cache->getOwnerId() != $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse("Cache not belong to logged user!", self::HTTP_STATUS_CONFLICT);
        }

        if($cache->isPowerTrailPart()){
            $this->ajaxErrorResponse("Already part of GeoPath", self::HTTP_STATUS_CONFLICT);
        }

        if(!CacheSet::isCacheStatusAllowedForGeoPathAdd($cache) ||
           !CacheSet::isCacheTypeAllowedForGeoPath($cache)) {
            $this->ajaxErrorResponse("Cache of improper type/state!", self::HTTP_STATUS_CONFLICT);
        }

        try{
            $geoPath->addCache($cache);
        }catch(\RuntimeException $e){
            $this->ajaxErrorResponse($e->getMessage());
        }

        $this->ajaxSuccessResponse("Cache added.", ['localizedMessage' => tr('gp_ownCacheAddedToGeopath')]);
    }

    /**
     * Remove cache from geopath
     */
    public function rmCacheFromGeopathAjax($cacheId)
    {
        $this->checkUserLoggedAjax();

        if(!$this->loggedUser->hasOcTeamRole()){
            $this->ajaxErrorResponse(
                "Logged user is not allowed to removed caches from geoPath!",
                self::HTTP_STATUS_FORBIDEN);
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);
        if(!$cache) {
            $this->ajaxErrorResponse("No such cache!", self::HTTP_STATUS_NOT_FOUND);
        }

        if(!$cache->isPowerTrailPart(true)){
            $this->ajaxErrorResponse("This cache not belongs to geoPath!", self::HTTP_STATUS_CONFLICT);
        }

        $geoPath = CacheSet::fromCacheSetIdFactory($cache->getPowerTrail()->getId());
        if(!$geoPath){
            $this->ajaxErrorResponse("Cache belongs to unknown geoPath!", self::HTTP_STATUS_NOT_FOUND);
        }

        $geoPath->removeCache($cache);

        $this->ajaxSuccessResponse("Cache removed.", ['localizedMessage' => tr('gp_cacheRemovedFromGeopath')]);
    }

    /**
     * Add cache candidate to geoPath by geoPath owner
     * Cache is not owned by logged user.
     *
     * @param integer $geoPathId
     * @param integer $cacheId
     */
    public function addCacheCandidateAjax($geoPathId, $cacheId, $resendEmail=null)
    {
        $this->checkUserLoggedAjax();

        $geoPath = CacheSet::fromCacheSetIdFactory($geoPathId);
        if(!$geoPath) {
            $this->ajaxErrorResponse("No such geoPath!", self::HTTP_STATUS_NOT_FOUND);
        }

        if(!$geoPath->isOwner($this->loggedUser)){
            $this->ajaxErrorResponse("Logged user is not a geopath owner!", self::HTTP_STATUS_FORBIDEN);
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);
        if(!$cache) {
            $this->ajaxErrorResponse("No such cache!", self::HTTP_STATUS_NOT_FOUND);
        }

        if($cache->getOwnerId() == $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse(
                "Cache belongs to logged user! No need to create candidate.",
                self::HTTP_STATUS_CONFLICT,
                ['localizedMessage' => tr('gp_ownedCacheAddDirect')]);
        }

        // check if this cache is not a part of geocache already
        if($cache->isPowerTrailPart(true)) {
            // this cache is already a part of some geoPath
            $this->ajaxErrorResponse(
                "Already part of GeoPath",
                self::HTTP_STATUS_CONFLICT,
                ['localizedMessage' => tr('gp_candidateAlreadyInGeocache')]);
        }

        // check if geocache is alredy a candidate
        if(!$resendEmail && $geoPath->isCandidateExists($cache)){
            if($geoPath->isCandidateExists($cache, true)){
                // this cache is already a candidate to THIS geopath
                $this->ajaxErrorResponse(
                    "This cache is already a candidate to this geopath!",
                    self::HTTP_STATUS_CONFLICT,
                    ['localizedMessage' => tr('gp_alreadyCandidateToThisGeopath')]);
            } else {
                // this cache is already a candidate but to OTHER geopath
                $this->ajaxErrorResponse(
                    "This cache is already a candidate to other geopath!",
                    self::HTTP_STATUS_CONFLICT,
                    ['localizedMessage' => tr('gp_alreadyCandidateToOtherGeopath')]);
            }
        }

        // this cache is not a candidate to any geopath yet
        $geoPath->addCacheCandidate($cache);

        $this->ajaxSuccessResponse(
            "Candidate saved. Email to cache owner is sent.",
            ['localizedMessage' => tr('gp_candidateProposalSaved')]);
    }

    public function acceptCacheCandidate($geopathId, $cacheId, $code)
    {
        $this->acceptCancelCandidate($geopathId, $cacheId, $code, true);
        echo "ACCEPT OK!";
    }

    public function cancelCacheCandidate($geopathId, $cacheId, $code)
    {
        $this->acceptCancelCandidate($geopathId, $cacheId, $code, false);
        echo "CANCEL OK!";
    }

    private function acceptCancelCandidate($geopathId, $cacheId, $code, $proposalAccepted)
    {
        $cache = GeoCache::fromCacheIdFactory($cacheId);
        if(!$cache){
            $this->displayCommonErrorPageAndExit("Unknown cache!");
        }

        $geoPath = CacheSet::fromCacheSetIdFactory($geopathId);
        if(!$geoPath){
            $this->displayCommonErrorPageAndExit("There is no such geoPath");
        }

        if($cache->isPowerTrailPart()){
            $this->displayCommonErrorPageAndExit("This geocache is already part of the geopath!");
        }


        if(!$geoPath->isCandiddateCodeExists($cache, $code)){
            $this->displayCommonErrorPageAndExit("There is no such proposal!");
        }

        // there was such proposal
        if($proposalAccepted) {
            try{
                $geoPath->addCache($cache);
            } catch (\RuntimeException $e){
                $this->displayCommonErrorPageAndExit($e->getMessage());
            }
            // cache added to geopath - cancel all other proposals
            $geoPath->deleteCandidateCode($cache);
        } else {
            // cancel this proposal
            $geoPath->deleteCandidateCode($cache, $code);
        }
    }
}
