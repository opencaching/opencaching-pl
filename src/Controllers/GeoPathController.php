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
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\ChunkModels\ListOfCaches\Column_GeoPathIcon;
use src\Models\CacheSet\GeopathCandidate;
use src\Models\ChunkModels\ListOfCaches\Column_CacheName;
use src\Models\ChunkModels\ListOfCaches\Column_SimpleText;
use src\Utils\Text\Formatter;
use src\Models\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use src\Models\ChunkModels\ListOfCaches\Column_OnClickActionIcon;
use src\Models\ChunkModels\ListOfCaches\Column_CacheSetNameAndIcon;
use src\Models\User\MultiUserQueries;
use src\Models\ChunkModels\ListOfCaches\Column_UserName;
use src\Models\ChunkModels\ListOfCaches\Column_ActionButtons;

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
            $tmpLogoFileArr = FileUploadMgr::processFileUpload($uploadModel);
            $tmpLogoFile = array_shift($tmpLogoFileArr);
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
            $this->ajaxErrorResponse("Logged user is not a geopath owner!", self::HTTP_STATUS_FORBIDDEN);
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);
        if(!$cache) {
            $this->ajaxErrorResponse("No such cache!", self::HTTP_STATUS_NOT_FOUND);
        }

        if($cache->getOwnerId() != $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse("Cache not belong to logged user!", self::HTTP_STATUS_CONFLICT);
        }

        if($cache->isPowerTrailPart(TRUE)){
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
                self::HTTP_STATUS_FORBIDDEN);
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
            $this->ajaxErrorResponse("Logged user is not a geopath owner!", self::HTTP_STATUS_FORBIDDEN);
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


    /**
     * Allow geopath owner to update geopath: center point, points, caches count
     *
     * @param integer $geoPathId
     */
    public function refreshCachesNumberAjax ($geoPathId)
    {
        $this->checkUserLoggedAjax();

        $geoPath = CacheSet::fromCacheSetIdFactory($geoPathId);
        if(!$geoPath) {
            $this->ajaxErrorResponse("No such geoPath!", self::HTTP_STATUS_NOT_FOUND);
        }

        if(!$geoPath->isOwner($this->loggedUser)){
            $this->ajaxErrorResponse("Logged user is not a geopath owner!", self::HTTP_STATUS_FORBIDDEN);
        }

        // recalculate Center adn Points
        $geoPath->recalculateCenterPoint();
        $geoPath->updatePoints();
        $geoPath->updateCachesCount();

        $this->ajaxSuccessResponse("Caches number, points and center point updated.",
            ['newCachesCount' => $geoPath->getCacheCount()]);
    }

    /**
     * This method is added temporary to cover old-style links
     * (called only from script confirmCacheCandidate.php
     *
     * @param string $code
     * @param boolean $proposalAccepted
     */
    public function legacyCacheCandidate($code, $proposalAccepted){

        list($geoPathId, $cacheId) = CacheSet::getCandidateDataBasedOnCode($code);

        if(!$geoPathId || !$cacheId){
            $this->displayCommonErrorPageAndExit("No such proposal?!");
        }

        $this->acceptCancelCandidate($geoPathId, $cacheId, $code, $proposalAccepted);
    }

    public function acceptCacheCandidate($geopathId, $cacheId, $code)
    {
        $this->acceptCancelCandidate($geopathId, $cacheId, $code, true);
    }

    public function cancelCacheCandidate($geopathId, $cacheId, $code)
    {
        $this->acceptCancelCandidate($geopathId, $cacheId, $code, false);
    }

    private function acceptCancelCandidate($geopathId, $cacheId, $code, $proposalAccepted)
    {
        $this->redirectNotLoggedUsers();

        $cache = GeoCache::fromCacheIdFactory($cacheId);
        if (!$cache) {
            $this->displayCommonErrorPageAndExit("Unknown cache!");
        }

        $geoPath = CacheSet::fromCacheSetIdFactory($geopathId);
        if (!$geoPath) {
            $this->displayCommonErrorPageAndExit("There is no such geoPath");
        }

        if ($cache->isPowerTrailPart()) {
            $this->displayCommonErrorPageAndExit("This geocache is already part of the geopath!");
        }

        if (!$geoPath->isCandiddateCodeExists($cache, $code)) {
            $this->displayCommonErrorPageAndExit("There is no such proposal!");
        }

        // there was such proposal
        if ($proposalAccepted) {
            try {
                $geoPath->addCache($cache);
            } catch (\RuntimeException $e) {
                $this->displayCommonErrorPageAndExit($e->getMessage());
            }
            // cache added to geopath - cancel all other proposals
            $geoPath->deleteCandidateCode($cache);
        } else {
            // cancel this proposal
            $geoPath->deleteCandidateCode($cache, $code);
        }

        $this->view->redirect($geoPath->getUrl());
    }

    public function cancelCacheCandidateAjax($candidateId)
    {
        $this->checkUserLoggedAjax();

        // find candidate record
        $candidate = GeopathCandidate::fromCandidateIdFactory($candidateId);
        if(!$candidate){
            $this->ajaxErrorResponse('No such candidate!');
        }

        // check if user is allowed to cancel offer (owner of geopath can cancel it)
        $geopath = $candidate->getGeopath();
        if(!$geopath->isOwner($this->loggedUser)) {
            $this->ajaxErrorResponse('User is not owner of this geopath assign to offer!');
        }

        // everything is OK, cancle the candidate
        $candidate->cancelOffer();
        $this->ajaxSuccessResponse("Candidate offer canceled succesful.");
    }

    public function refuseCacheCandidateAjax($candidateId)
    {
        $this->checkUserLoggedAjax();

        // find candidate record
        $candidate = GeopathCandidate::fromCandidateIdFactory($candidateId);
        if (!$candidate) {
            $this->ajaxErrorResponse('No such candidate!');
        }

        // check if user is allowed to cancel offer (owner of geopath can cancel it)
        $cache = $candidate->getGeoCache();
        if ($cache->getOwnerId() != $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse('User is not owner of this geocache assign to offer!');
        }

        $candidate->refuseOffer();
        $this->ajaxSuccessResponse("Candidate offer refused succesful.");
    }

    public function acceptCacheCandidateAjax($candidateId)
    {
        $this->checkUserLoggedAjax();

        // find candidate record
        $candidate = GeopathCandidate::fromCandidateIdFactory($candidateId);
        if (!$candidate) {
            $this->ajaxErrorResponse('No such candidate!');
        }

        // check if user is allowed to cancel offer (owner of geopath can cancel it)
        $cache = $candidate->getGeoCache();
        if ($cache->getOwnerId() != $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse('User is not owner of this geocache assign to offer!');
        }

        try{
            $candidate->acceptOffer();
        } catch (\RuntimeException $e) {
            $this->displayCommonErrorPageAndExit($e->getMessage());
        }

        $this->ajaxSuccessResponse("Candidate offer refused succesful.");
    }

    /**
     * List of caches - candidates to given geopath
     *
     * @param  $geopathId
     */
    public function candidatesList($geopathId)
    {
        $this->redirectNotLoggedUsers();

        if(!$geopath = CacheSet::fromCacheSetIdFactory($geopathId)){
            $this->displayCommonErrorPageAndExit("No such geopath!");
        }

        if(!$geopath->isOwner($this->loggedUser)){
            $this->displayCommonErrorPageAndExit("You are not an owner of this geopath!");
        }

        $this->view->setTemplate('geoPath/gpCandidatesList');
        $this->view->loadJQuery();
        $this->view->setVar('gp', $geopath);

        // init model for list of watched geopaths
        $listModel = new ListOfCachesModel();


        // rows to display
        $candidates = GeopathCandidate::getCacheCandidates($geopath);

        $userDict = [];
        foreach($candidates as $candidate){
            $userDict[$candidate->getGeoCache()->getOwnerId()] = null;
        }

        $userDict = MultiUserQueries::GetUserNamesForListOfIds(array_keys($userDict));

        $listModel->addDataRows(GeopathCandidate::getCacheCandidates($geopath));


        $listModel->addColumn(new Column_SimpleText(tr('gpCandidates_submitedDate'), function(GeopathCandidate $candidate){
            return Formatter::date($candidate->getSubmitedDate());
        }, 'width15'));

        $listModel->addColumn(new Column_CacheTypeIcon('', function(GeopathCandidate $candidate){
            $cache = $candidate->getGeoCache();
            return [
                'type' => $cache->getCacheType(),
                'status' => $cache->getStatus(),
                'user_sts' => null,
            ];
        }, 'width5'));

        $listModel->addColumn(new Column_CacheName(tr('gpCandidates_cacheName'), function(GeopathCandidate $candidate){
            $cache = $candidate->getGeoCache();
            return [
                'cacheWp' => $cache->getWaypointId(),
                'cacheName' => $cache->getCacheName(),
                'isStatusAware' => true,
                'cacheStatus' => $cache->getStatus()
            ];
        }));

        $listModel->addColumn(new Column_UserName(tr('gpCandidates_cacheOwner'), function(GeopathCandidate $candidate) use($userDict){
            $userId = $candidate->getGeoCache()->getOwnerId();
            return [
                'userId' => $userId,
                'userName' => $userDict[$userId],
            ];
        }));

        $listModel->addColumn(new Column_OnClickActionIcon(tr('gpCandidates_action'), function(GeopathCandidate $candidate){
            return [
                'icon' => '/images/log/16x16-trash.png',
                'onClick' => 'cancelCandidateOffer(this, '.$candidate->getId().')',
                'title' => tr('gpCandidates_cancelOffer'),
                ];
        }, 'width10'));



        $this->view->setVar('listModel', $listModel);

        $this->view->buildView();
    }

    /**
     * Display the lists of offers of cache adding to geopath for curent user
     */
    public function myCandidates()
    {
        $this->redirectNotLoggedUsers();

        $this->view->loadJQuery();
        $this->view->setTemplate('geoPath/myCandidatesList');
        $this->view->setVar('user', $this->loggedUser);

        // init model for list of watched geopaths
        $listModel = new ListOfCachesModel();

        $listModel->addColumn(new Column_SimpleText(
            tr('gpMyCandidates_submitedDate'),
            function(GeopathCandidate $candidate){
                return Formatter::date($candidate->getSubmitedDate());
            },
            'width15'
        ));

        $listModel->addColumn(new Column_CacheSetNameAndIcon(
            tr('gpMyCandidates_geopathName'),
            function(GeopathCandidate $candidate){
                $gp = $candidate->getGeopath();
                return [
                    'type' => $gp->getType(),
                    'id'   => $gp->getId(),
                    'name' => $gp->getName(),
                ];
            }
        ));

        $listModel->addColumn(new Column_CacheTypeIcon(
            '',
            function(GeopathCandidate $candidate){
                $cache = $candidate->getGeoCache();
                return [
                    'type' => $cache->getCacheType(),
                    'status' => $cache->getStatus(),
                    'user_sts' => null,
                ];
            },
            'width5'
        ));

        $listModel->addColumn(new Column_CacheName(
            tr('gpMyCandidates_cacheName'),
            function(GeopathCandidate $candidate){
                $cache = $candidate->getGeoCache();
                return [
                    'cacheWp' => $cache->getWaypointId(),
                    'cacheName' => $cache->getCacheName(),
                    'isStatusAware' => true,
                    'cacheStatus' => $cache->getStatus()
                ];
            }
        ));

        $listModel->addColumn(new Column_ActionButtons(
            tr('gpMyCandidates_actions'),
            function(GeopathCandidate $candidate){
                return [
                    [
                        'btnClasses' => 'btn-primary',
                        'btnText' => tr('gpMyCandidates_acceptOffer'),
                        'onClick' => 'acceptOffer(this, '.$candidate->getId().')',
                        'title' => tr('gpMyCandidates_acceptOfferTitle'),
                    ],
                    [
                        'btnClasses' => '',
                        'btnText' => tr('gpMyCandidates_refuseOffer'),
                        'onClick' => 'refuseOffer(this, '.$candidate->getId().')',
                        'title' => tr('gpMyCandidates_refuseOfferTitle'),
                    ],
                ];
            },
            'width25'
        ));

        // load rows to display
        $listModel->addDataRows(GeopathCandidate::getUserGeopathCandidates($this->loggedUser));
        $this->view->setVar('listModel', $listModel);

        $this->view->buildView();
    }
}
