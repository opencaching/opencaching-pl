<?php

declare(strict_types=1);

namespace src\Services;

use RuntimeException;
use src\Controllers\PowerTrailController;
use src\Enums\PowerTrailActionLogEnum;
use src\Enums\PowerTrailStatusesEnum;
use src\Models\CacheSet\CacheSet;
use src\Models\PowerTrail\PowerTrail;
use src\Models\PowerTrail\PowerTrailAdminRepository;

/**
 * Admin operations for GeoPaths. All public methods require an Octeam user
 * and an HTTP POST request.
 */
class PowerTrailAdminService
{
    use RequiresOcteamPostTrait;

    private const CHANGEABLE_STATUSES = [
        PowerTrailStatusesEnum::STATUS_OPEN,
        PowerTrailStatusesEnum::STATUS_INSERVICE,
        PowerTrailStatusesEnum::STATUS_CLOSED,
    ];

    private readonly PowerTrailAdminRepository $repository;

    private readonly PowerTrailActionLogService $actionLogService;

    public function __construct(
        ?PowerTrailAdminRepository $repository = null,
        ?PowerTrailActionLogService $actionLogService = null
    ) {
        $this->repository = $repository ?? new PowerTrailAdminRepository();
        $this->actionLogService = $actionLogService ?? new PowerTrailActionLogService();
    }

    /** Return the complete PowerTrail row. */
    public function getGeoPathById(int $powerTrailId): ?array
    {
        $this->requireOcteamPost();

        return $this->repository->findRowById($powerTrailId);
    }

    public function getDescription(int $powerTrailId): ?string
    {
        $this->requireOcteamPost();

        return $this->repository->findDescription($powerTrailId);
    }

    public function updateDescription(int $powerTrailId, string $description): bool
    {
        $this->requireOcteamPost();

        if (! $this->repository->exists($powerTrailId)) {
            return false;
        }

        $this->repository->updateDescription($powerTrailId, $this->escape($description));

        return true;
    }

    /** Stats are intentionally deferred for now. */
    public function getStats(int $powerTrailId): void
    {
        $this->requireOcteamPost();
    }

    public function getStatus(int $powerTrailId): ?int
    {
        $this->requireOcteamPost();

        return $this->repository->findStatus($powerTrailId);
    }

    /**
     * Update status using the existing GeoPath rules and write the entry to the GeoPath history.
     *
     * @return array{updateStatusResult: bool, message: string}
     */
    public function updateStatus(int $powerTrailId, int $status): array
    {
        $user = $this->requireOcteamPost();

        if (! in_array($status, self::CHANGEABLE_STATUSES, true)) {
            return ['updateStatusResult' => false, 'message' => 'Invalid GeoPath status'];
        }

        $powerTrail = new PowerTrail(['id' => $powerTrailId]);
        if (! $powerTrail->isDataLoaded()) {
            return ['updateStatusResult' => false, 'message' => 'GeoPath not found'];
        }

        $result = $powerTrail->setAndStoreStatus($status);
        if ($result['updateStatusResult']) {
            $this->actionLogService->log(
                PowerTrailActionLogEnum::CHANGE_STATUS,
                $powerTrailId,
                $user->getUserId()
            );
        }

        return $result;
    }
    /**
     * Remove a cache from the GeoPath, write the history entry and recalculate the GeoPath.
     *
     * @return int New number of caches in the GeoPath.
     * @throws RuntimeException with HTTP-like code (400, 404, 409) on invalid input or state
     */
    public function removeCache(int $powerTrailId, int $cacheId): int
    {
        $user = $this->requireOcteamPost();

        if ($powerTrailId <= 0 || $cacheId <= 0) {
            throw new RuntimeException('Invalid GeoPath or cache ID', 400);
        }

        if (! $this->repository->exists($powerTrailId)) {
            throw new RuntimeException('GeoPath not found', 404);
        }

        if (! $this->repository->removeCacheFromPowerTrail($powerTrailId, $cacheId)) {
            throw new RuntimeException('Cache is not assigned to this GeoPath', 409);
        }

        $this->actionLogService->log(
            PowerTrailActionLogEnum::REMOVE_CACHE,
            $powerTrailId,
            $user->getUserId(),
            $cacheId
        );

        $this->recalculateGeoPath($powerTrailId);

        return (int) ($this->repository->findById($powerTrailId)['cacheCount'] ?? 0);
    }

    /**
     * Recalculate center point, points and caches count of the GeoPath.
     *
     * @return bool false when the GeoPath does not exist
     */
    public function refreshGeoPath(int $powerTrailId): bool
    {
        $this->requireOcteamPost();

        return $this->recalculateGeoPath($powerTrailId);
    }

    private function recalculateGeoPath(int $powerTrailId): bool
    {
        $geoPath = CacheSet::fromCacheSetIdFactory($powerTrailId);
        if (! $geoPath) {
            return false;
        }

        $geoPath->recalculateCenterPoint();
        $geoPath->updatePoints();
        $geoPath->updateCachesCount();

        return true;
    }
    /** WIS is stored in PowerTrail.perccentRequired. */
    public function getWis(int $powerTrailId): ?int
    {
        $this->requireOcteamPost();

        return $this->repository->findWis($powerTrailId);
    }

    public function updateWis(int $powerTrailId, int $wis): bool
    {
        $this->requireOcteamPost();

        if ($wis < PowerTrailController::MINIMUM_PERCENT_REQUIRED || $wis > 100) {
            return false;
        }

        return $this->repository->updateWis($powerTrailId, $wis);
    }

    /** Return all users assigned as GeoPath founders/owners (not the mentor). */
    public function getFounders(int $powerTrailId): array
    {
        $this->requireOcteamPost();

        return $this->repository->findFounders($powerTrailId);
    }

    public function addFounder(int $powerTrailId, int $userId): bool
    {
        $actor = $this->requireOcteamPost();

        if (! $this->repository->exists($powerTrailId) || ! $this->repository->userExists($userId)) {
            return false;
        }

        if (! $this->repository->addFounder($powerTrailId, $userId)) {
            return false;
        }

        // TODO: This method will be extended and reworked in the second iteration.
        // For now it is inactive (not used by any controller yet).
        $this->writeOwnerActionLog($powerTrailId, $actor->getUserId(), PowerTrailActionLogEnum::ADD_OWNER, $userId);

        return true;
    }

    public function removeFounder(int $powerTrailId, int $userId): bool
    {
        $actor = $this->requireOcteamPost();

        if ($this->repository->countFounders($powerTrailId) <= 1 || ! $this->repository->exists($powerTrailId)) {
            return false;
        }

        if (! $this->repository->removeFounder($powerTrailId, $userId)) {
            return false;
        }

        // TODO: This method will be extended and reworked in the second iteration.
        // For now it is inactive (not used by any controller yet).
        $this->writeOwnerActionLog($powerTrailId, $actor->getUserId(), PowerTrailActionLogEnum::REMOVE_OWNER, $userId);

        return true;
    }

    private function writeOwnerActionLog(
        int $powerTrailId,
        int $actorUserId,
        PowerTrailActionLogEnum $actionType,
        int $affectedUserId
    ): void {
        $this->actionLogService->log(
            $actionType,
            $powerTrailId,
            $actorUserId,
            $affectedUserId,
            $actionType->description() . '; affected user: ' . $affectedUserId
        );
    }
    private function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8');
    }
}
