<?php

declare(strict_types=1);

namespace src\Services;

use DateTimeImmutable;
use RuntimeException;
use src\Enums\PowerTrailActionLogEnum;
use src\Enums\PowerTrailPercentRequiredEnum;
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

    /**
     * Update WIS (PowerTrail.perccentRequired) and write the entry to the GeoPath history.
     *
     * @return array{success: bool, message: string, wis: int}
     */
    public function updateWis(int $powerTrailId, int $wis): array
    {
        $user = $this->requireOcteamPost();

        $minWis = PowerTrailPercentRequiredEnum::MINIMUM;
        $maxWis = PowerTrailPercentRequiredEnum::MAXIMUM;

        if ($wis < $minWis || $wis > $maxWis) {
            return [
                'success' => false,
                'message' => sprintf(
                    'WIS must be between %d and %d',
                    $minWis,
                    $maxWis
                ),
                'wis' => $wis,
            ];
        }

        $currentWis = $this->repository->findWis($powerTrailId);

        if ($currentWis === null) {
            return ['success' => false, 'message' => 'GeoPath not found', 'wis' => $wis];
        }

        if ($currentWis === $wis) {
            return ['success' => true, 'message' => 'WIS unchanged', 'wis' => $wis];
        }

        if (! $this->repository->updateWis($powerTrailId, $wis)) {
            return ['success' => false, 'message' => 'Failed to update WIS', 'wis' => $wis];
        }

        $this->actionLogService->log(
            PowerTrailActionLogEnum::CHANGE_PERCENT_REQUIRED,
            $powerTrailId,
            $user->getUserId(),
            0,
            sprintf(
                '%s: %d -> %d',
                PowerTrailActionLogEnum::CHANGE_PERCENT_REQUIRED->description(),
                $currentWis,
                $wis
            )
        );

        return ['success' => true, 'message' => 'WIS updated', 'wis' => $wis];
    }

    /**
     * Update the GeoPath creation date and write the entry to the GeoPath history.
     *
     * The date input contains only the date part; the original time of day is
     * preserved when the date is changed.
     *
     * @return array{success: bool, message: string, dateCreated: string}
     */
    public function updateDateCreated(int $powerTrailId, string $dateCreated): array
    {
        $user = $this->requireOcteamPost();

        $dateCreated = trim($dateCreated);
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateCreated);

        if (
            $powerTrailId <= 0
            || $dateCreated === ''
            || $dateCreated === '0000-00-00'
            || $date === false
            || $date->format('Y-m-d') !== $dateCreated
        ) {
            return [
                'success' => false,
                'message' => tr('date_incorrect'),
                'dateCreated' => $dateCreated,
            ];
        }

        $powerTrail = $this->repository->findById($powerTrailId);
        if ($powerTrail === null) {
            return [
                'success' => false,
                'message' => 'GeoPath not found',
                'dateCreated' => $dateCreated,
            ];
        }

        $currentDateTime = (string) $powerTrail['dateCreated'];
        $currentDate = substr($currentDateTime, 0, 10);

        if ($currentDate === $dateCreated) {
            return [
                'success' => true,
                'message' => tr('powertrail_admin_date_unchanged'),
                'dateCreated' => $dateCreated,
            ];
        }

        $timePart = substr($currentDateTime, 10);
        if (! preg_match('/^\s\d{2}:\d{2}:\d{2}$/', $timePart)) {
            $timePart = ' 00:00:00';
        }

        if (! $this->repository->updateDateCreated($powerTrailId, $dateCreated . $timePart)) {
            return [
                'success' => false,
                'message' => tr('powertrail_admin_change_date_error'),
                'dateCreated' => $dateCreated,
            ];
        }

        $this->actionLogService->log(
            PowerTrailActionLogEnum::CHANGE_DATE_CREATED_BY_OCTEAM,
            $powerTrailId,
            $user->getUserId(),
            0,
            sprintf(
                '%s: %s -> %s',
                PowerTrailActionLogEnum::CHANGE_DATE_CREATED_BY_OCTEAM->description(),
                $currentDate,
                $dateCreated
            )
        );

        return [
            'success' => true,
            'message' => tr('powertrail_admin_date_saved'),
            'dateCreated' => $dateCreated,
        ];
    }

    /**
     * Update the GeoPath description and write the entry to the GeoPath history.
     *
     * @return array{success: bool, message: string}
     */
    public function updateDescription(int $powerTrailId, string $description): array
    {
        $user = $this->requireOcteamPost();

        if ($powerTrailId <= 0) {
            return ['success' => false, 'message' => 'GeoPath not found'];
        }

        $powerTrail = $this->repository->findById($powerTrailId);
        if ($powerTrail === null) {
            return ['success' => false, 'message' => 'GeoPath not found'];
        }

        $description = str_replace(["\r\n", "\r"], "\n", $description);
        $currentDescription = str_replace(
            ["\r\n", "\r"],
            "\n",
            htmlspecialchars_decode((string) $powerTrail['description'], ENT_QUOTES)
        );

        if ($currentDescription === $description) {
            return [
                'success' => true,
                'message' => tr('powertrail_admin_description_unchanged'),
            ];
        }

        $storedDescription = htmlspecialchars(
            $description,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        if (! $this->repository->updateDescription($powerTrailId, $storedDescription)) {
            return [
                'success' => false,
                'message' => tr('powertrail_admin_description_error'),
            ];
        }

        $this->actionLogService->log(
            PowerTrailActionLogEnum::CHANGE_DESCRIPTION_BY_OCTEAM,
            $powerTrailId,
            $user->getUserId(),
            0,
            sprintf(
                '%s: %s (ID: %d)',
                PowerTrailActionLogEnum::CHANGE_DESCRIPTION_BY_OCTEAM->description(),
                $user->getUserName(),
                $user->getUserId()
            )
        );

        return [
            'success' => true,
            'message' => tr('powertrail_admin_description_saved'),
        ];
    }

    /**
     * Add a founder (owner) to the GeoPath and write the entry to the GeoPath history.
     *
     * @return array{user_id: int, username: string}
     * @throws RuntimeException with HTTP-like code (400, 404, 409) on invalid input or state
     */
    public function addFounder(int $powerTrailId, string $username): array
    {
        $user = $this->requireOcteamPost();

        $username = trim($username);
        if ($powerTrailId <= 0 || $username === '') {
            throw new RuntimeException(tr('powertrail_admin_founder_name_required'), 400);
        }

        if (mb_strlen($username) > 60) {
            throw new RuntimeException(tr('powertrail_admin_founder_name_too_long'), 400);
        }

        if (! $this->repository->exists($powerTrailId)) {
            throw new RuntimeException('GeoPath not found', 404);
        }

        $founder = $this->repository->findUserByUsername($username);
        if ($founder === null) {
            throw new RuntimeException(tr('powertrail_admin_founder_not_found'), 404);
        }

        $founderId = (int) $founder['user_id'];
        if ($this->repository->isFounder($powerTrailId, $founderId)) {
            throw new RuntimeException(tr('powertrail_admin_founder_already_exists'), 409);
        }

        if (! $this->repository->addFounder($powerTrailId, $founderId)) {
            throw new RuntimeException(tr('powertrail_admin_founder_already_exists'), 409);
        }

        $this->actionLogService->log(
            PowerTrailActionLogEnum::ADD_FOUNDER_BY_OCTEAM,
            $powerTrailId,
            $user->getUserId(),
            0,
            sprintf(
                '%s: %s (ID: %d)',
                PowerTrailActionLogEnum::ADD_FOUNDER_BY_OCTEAM->description(),
                (string) $founder['username'],
                $founderId
            )
        );

        return [
            'user_id' => $founderId,
            'username' => (string) $founder['username'],
        ];
    }

    /**
     * Remove a founder (owner) from the GeoPath and write the entry to the GeoPath history.
     *
     * @return string Success message.
     * @throws RuntimeException with HTTP-like code (400, 404, 409) on invalid input or state
     */
    public function removeFounder(int $powerTrailId, int $userId): string
    {
        $user = $this->requireOcteamPost();

        if ($powerTrailId <= 0 || $userId <= 0) {
            throw new RuntimeException('Invalid GeoPath or user ID', 400);
        }

        if (! $this->repository->exists($powerTrailId)) {
            throw new RuntimeException('GeoPath not found', 404);
        }

        $founder = $this->repository->findFounder($powerTrailId, $userId);
        if ($founder === null) {
            throw new RuntimeException(tr('powertrail_admin_founder_not_assigned'), 409);
        }

        if (! $this->repository->removeFounder($powerTrailId, $userId)) {
            throw new RuntimeException(tr('powertrail_admin_remove_founder_error'), 409);
        }

        $this->actionLogService->log(
            PowerTrailActionLogEnum::REMOVE_FOUNDER_BY_OCTEAM,
            $powerTrailId,
            $user->getUserId(),
            0,
            sprintf(
                '%s: %s (ID: %d)',
                PowerTrailActionLogEnum::REMOVE_FOUNDER_BY_OCTEAM->description(),
                (string) $founder['username'],
                $userId
            )
        );

        return tr('powertrail_admin_founder_removed');
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
}
