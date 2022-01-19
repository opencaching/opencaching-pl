<?php

namespace src\Controllers;

use RuntimeException;
use src\Controllers\Core\ApiBaseController;
use src\Models\CacheSet\CacheSet;
use src\Models\CacheSet\GeopathCandidate;
use src\Models\CacheSet\GeopathLogoUploadModel;
use src\Models\GeoCache\GeoCache;
use src\Models\OcConfig\OcConfig;
use src\Utils\FileSystem\FileUploadMgr;
use src\Utils\Generators\Uuid;
use src\Utils\Img\OcImage;
use src\Utils\Uri\HttpCode;

class GeoPathApiController extends ApiBaseController
{
    /**
     * Add cache to the geoPath by cache owner and geopath
     */
    public function addOwnCacheToGeopathAjax($geoPathId, $cacheId)
    {
        $this->checkUserLoggedAjax();

        $geoPath = CacheSet::fromCacheSetIdFactory($geoPathId);

        if (! $geoPath) {
            $this->ajaxErrorResponse('No such geoPath!', HttpCode::STATUS_NOT_FOUND);
        }

        if (! $geoPath->isOwner($this->loggedUser)) {
            $this->ajaxErrorResponse('Logged user is not a geopath owner!', HttpCode::STATUS_FORBIDDEN);
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);

        if (! $cache) {
            $this->ajaxErrorResponse('No such cache!', HttpCode::STATUS_NOT_FOUND);
        }

        if ($cache->getOwnerId() != $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse('Cache not belong to logged user!', HttpCode::STATUS_CONFLICT);
        }

        if ($cache->isPowerTrailPart(true)) {
            $this->ajaxErrorResponse('Already part of GeoPath', HttpCode::STATUS_CONFLICT);
        }

        if (! CacheSet::isCacheStatusAllowedForGeoPathAdd($cache)
            || ! CacheSet::isCacheTypeAllowedForGeoPath($cache)) {
            $this->ajaxErrorResponse('Cache of improper type/state!', HttpCode::STATUS_CONFLICT);
        }

        try {
            $geoPath->addCache($cache);
        } catch (RuntimeException $e) {
            $this->ajaxErrorResponse($e->getMessage());
        }

        $this->ajaxSuccessResponse('Cache added.', ['localizedMessage' => tr('gp_ownCacheAddedToGeopath')]);
    }

    /**
     * Remove cache from geopath
     */
    public function rmCacheFromGeopathAjax($cacheId)
    {
        $this->checkUserLoggedAjax();

        if (! $this->loggedUser->hasOcTeamRole()) {
            $this->ajaxErrorResponse(
                'Logged user is not allowed to removed caches from geoPath!',
                HttpCode::STATUS_FORBIDDEN
            );
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);

        if (! $cache) {
            $this->ajaxErrorResponse('No such cache!', HttpCode::STATUS_NOT_FOUND);
        }

        if (! $cache->isPowerTrailPart(true)) {
            $this->ajaxErrorResponse('This cache not belongs to geoPath!', HttpCode::STATUS_CONFLICT);
        }

        $geoPath = CacheSet::fromCacheSetIdFactory($cache->getPowerTrail()->getId());

        if (! $geoPath) {
            $this->ajaxErrorResponse('Cache belongs to unknown geoPath!', HttpCode::STATUS_NOT_FOUND);
        }

        $geoPath->removeCache($cache);

        $this->ajaxSuccessResponse('Cache removed.', ['localizedMessage' => tr('gp_cacheRemovedFromGeopath')]);
    }

    /**
     * Uploading of the new geoPath logo
     * This is fast replacement for ajaxImage.php
     */
    public function uploadLogoAjax($geoPathId)
    {
        $this->checkUserLoggedAjax();

        if (! $geoPath = CacheSet::fromCacheSetIdFactory($geoPathId)) {
            $this->ajaxErrorResponse('No such geopath!');
        }

        if (! $geoPath->isOwner($this->loggedUser)) {
            $this->ajaxErrorResponse('Logged user is not an geopath owner!');
        }

        $uploadModel = GeopathLogoUploadModel::forGeopath($geoPathId);

        try {
            $tmpLogoFileArr = FileUploadMgr::processFileUpload($uploadModel);
            $tmpLogoFile = array_shift($tmpLogoFileArr);
        } catch (RuntimeException $e) {
            // some error occurred on upload processing
            $this->ajaxErrorResponse($e->getMessage(), HttpCode::STATUS_INTERNAL_ERROR);
        }

        // FileUploadMgr returns single filename saved in server tmp directory on server

        $newLogoPath = OcConfig::getDynFilesPath(true) . CacheSet::DIR_LOGO_IMG . '/' . Uuid::create();

        // resize the new logo
        $newLogoPath = OcImage::createThumbnail($uploadModel->getDirAtServer() . '/' . $tmpLogoFile, $newLogoPath, [250, 250]);

        // create URL of the image
        $newLogoFileUrl = CacheSet::DIR_LOGO_IMG . '/' . basename($newLogoPath);

        // new log is ready to use - update DB
        $geoPath->updateLogoImg($newLogoFileUrl);

        $this->ajaxJsonResponse($newLogoFileUrl);
    }

    /**
     * Add cache candidate to geoPath by geoPath owner
     * Cache is not owned by logged user.
     *
     * @param int $geoPathId
     * @param int $cacheId
     */
    public function addCacheCandidateAjax($geoPathId, $cacheId, $resendEmail = null)
    {
        $this->checkUserLoggedAjax();

        $geoPath = CacheSet::fromCacheSetIdFactory($geoPathId);

        if (! $geoPath) {
            $this->ajaxErrorResponse('No such geoPath!', HttpCode::STATUS_NOT_FOUND);
        }

        if (! $geoPath->isOwner($this->loggedUser)) {
            $this->ajaxErrorResponse('Logged user is not a geopath owner!', HttpCode::STATUS_FORBIDDEN);
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);

        if (! $cache) {
            $this->ajaxErrorResponse('No such cache!', HttpCode::STATUS_NOT_FOUND);
        }

        if ($cache->getOwnerId() == $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse(
                'Cache belongs to logged user! No need to create candidate.',
                HttpCode::STATUS_CONFLICT,
                ['localizedMessage' => tr('gp_ownedCacheAddDirect')]
            );
        }

        // check if this cache is not a part of geocache already
        if ($cache->isPowerTrailPart(true)) {
            // this cache is already a part of some geoPath
            $this->ajaxErrorResponse(
                'Already part of GeoPath',
                HttpCode::STATUS_CONFLICT,
                ['localizedMessage' => tr('gp_candidateAlreadyInGeocache')]
            );
        }

        // check if geocache is already a candidate
        if (! $resendEmail && $geoPath->isCandidateExists($cache)) {
            if ($geoPath->isCandidateExists($cache, true)) {
                // this cache is already a candidate to THIS geopath
                $this->ajaxErrorResponse(
                    'This cache is already a candidate to this geopath!',
                    HttpCode::STATUS_CONFLICT,
                    ['localizedMessage' => tr('gp_alreadyCandidateToThisGeopath')]
                );
            } else {
                // this cache is already a candidate but to OTHER geopath
                $this->ajaxErrorResponse(
                    'This cache is already a candidate to other geopath!',
                    HttpCode::STATUS_CONFLICT,
                    ['localizedMessage' => tr('gp_alreadyCandidateToOtherGeopath')]
                );
            }
        }

        // this cache is not a candidate to any geopath yet
        $geoPath->addCacheCandidate($cache);

        $this->ajaxSuccessResponse(
            'Candidate saved. Email to cache owner is sent.',
            ['localizedMessage' => tr('gp_candidateProposalSaved')]
        );
    }

    /**
     * Allow geopath owner to update geopath: center point, points, caches count
     *
     * @param int $geoPathId
     */
    public function refreshCachesNumberAjax($geoPathId)
    {
        $this->checkUserLoggedAjax();

        $geoPath = CacheSet::fromCacheSetIdFactory($geoPathId);

        if (! $geoPath) {
            $this->ajaxErrorResponse('No such geoPath!', HttpCode::STATUS_NOT_FOUND);
        }

        if (! $geoPath->isOwner($this->loggedUser)) {
            $this->ajaxErrorResponse('Logged user is not a geopath owner!', HttpCode::STATUS_FORBIDDEN);
        }

        // recalculate Center adn Points
        $geoPath->recalculateCenterPoint();
        $geoPath->updatePoints();
        $geoPath->updateCachesCount();

        $this->ajaxSuccessResponse(
            'Caches number, points and center point updated.',
            ['newCachesCount' => $geoPath->getCacheCount()]
        );
    }

    public function cancelCacheCandidateAjax($candidateId)
    {
        $this->checkUserLoggedAjax();

        // find candidate record
        $candidate = GeopathCandidate::fromCandidateIdFactory($candidateId);

        if (! $candidate) {
            $this->ajaxErrorResponse('No such candidate!');
        }

        // check if user is allowed to cancel offer (owner of geopath can cancel it)
        $geopath = $candidate->getGeopath();

        if (! $geopath->isOwner($this->loggedUser)) {
            $this->ajaxErrorResponse('User is not owner of this geopath assign to offer!');
        }

        // everything is OK, cancel the candidate
        $candidate->cancelOffer();
        $this->ajaxSuccessResponse('Candidate offer canceled successful.');
    }

    public function refuseCacheCandidateAjax($candidateId)
    {
        $this->checkUserLoggedAjax();

        // find candidate record
        $candidate = GeopathCandidate::fromCandidateIdFactory($candidateId);

        if (! $candidate) {
            $this->ajaxErrorResponse('No such candidate!');
        }

        // check if user is allowed to cancel offer (owner of geopath can cancel it)
        $cache = $candidate->getGeoCache();

        if ($cache->getOwnerId() != $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse('User is not owner of this geocache assign to offer!');
        }

        $candidate->refuseOffer();
        $this->ajaxSuccessResponse('Candidate offer refused successful.');
    }

    public function acceptCacheCandidateAjax($candidateId)
    {
        $this->checkUserLoggedAjax();

        // find candidate record
        $candidate = GeopathCandidate::fromCandidateIdFactory($candidateId);

        if (! $candidate) {
            $this->ajaxErrorResponse('No such candidate!');
        }

        // check if user is allowed to cancel offer (owner of geopath can cancel it)
        $cache = $candidate->getGeoCache();

        if ($cache->getOwnerId() != $this->loggedUser->getUserId()) {
            $this->ajaxErrorResponse('User is not owner of this geocache assign to offer!');
        }

        try {
            $candidate->acceptOffer();
        } catch (RuntimeException $e) {
            $this->ajaxErrorResponse($e->getMessage());
        }

        $this->ajaxSuccessResponse('Candidate offer accepted successful.');
    }
}
