<?php

declare(strict_types=1);

namespace src\Services;

use src\Controllers\PowerTrailController;
use src\Enums\PowerTrailStatusesEnum;
use src\Models\PowerTrail\Log as PowerTrailLog;
use src\Models\PowerTrail\PowerTrail;
use src\Models\PowerTrail\PowerTrailAdminRepository;

/**
 * Admin operations for GeoPaths. All public methods require an Octeam user
 * and an HTTP POST request.
 */
class PowerTrailAdminService
{
    use RequiresOcteamPostTrait;

    private const ACTION_TYPE_CHANGE_STATUS = 6;
    private const ACTION_TYPE_ADD_OWNER = 4;
    private const ACTION_TYPE_REMOVE_OWNER = 5;

    private readonly PowerTrailAdminRepository $repository;

    public function __construct(?PowerTrailAdminRepository $repository = null)
    {
        $this->repository = $repository ?? new PowerTrailAdminRepository();
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
     * Update status using the existing GeoPath rules and write the usual logs.
     *
     * @return array Result returned by PowerTrail::setAndStoreStatus().
     */
    public function updateStatus(int $powerTrailId, int $status, string $comment = ''): array
    {
        $user = $this->requireOcteamPost();

        if (! array_key_exists($status, PowerTrailStatusesEnum::statuses())) {
            return ['updateStatusResult' => false, 'message' => 'Invalid GeoPath status'];
        }

        $powerTrail = new PowerTrail(['id' => $powerTrailId]);
        if (! $powerTrail->isDataLoaded()) {
            return ['updateStatusResult' => false, 'message' => 'GeoPath not found'];
        }

        $result = $powerTrail->setAndStoreStatus($status);
        if (! $result['updateStatusResult']) {
            return $result;
        }

        $commentType = match ($status) {
            PowerTrailStatusesEnum::STATUS_OPEN => PowerTrailLog::TYPE_OPENING,
            PowerTrailStatusesEnum::STATUS_INSERVICE => PowerTrailLog::TYPE_DISABLING,
            PowerTrailStatusesEnum::STATUS_CLOSED => PowerTrailLog::TYPE_CLOSING,
            default => PowerTrailLog::TYPE_COMMENT,
        };

        if (trim($comment) === '') {
            $comment = tr(match ($status) {
                PowerTrailStatusesEnum::STATUS_OPEN => 'pt215',
                PowerTrailStatusesEnum::STATUS_INSERVICE => 'pt217',
                PowerTrailStatusesEnum::STATUS_CLOSED => 'pt218',
                default => 'pt056',
            }) . '!';
        }

        $this->repository->insertComment(
            $powerTrailId,
            $user->getUserId(),
            $commentType,
            $this->escape($comment)
        );
        $this->repository->insertActionLog(
            $powerTrailId,
            $user->getUserId(),
            self::ACTION_TYPE_CHANGE_STATUS,
            'change PowerTrail status'
        );

        return $result;
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

        $this->writeOwnerActionLog($powerTrailId, $actor->getUserId(), self::ACTION_TYPE_ADD_OWNER, $userId);

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

        $this->writeOwnerActionLog($powerTrailId, $actor->getUserId(), self::ACTION_TYPE_REMOVE_OWNER, $userId);

        return true;
    }

    private function writeOwnerActionLog(int $powerTrailId, int $actorUserId, int $actionType, int $affectedUserId): void
    {
        $description = match ($actionType) {
            self::ACTION_TYPE_ADD_OWNER => 'add another owner to PowerTrail',
            self::ACTION_TYPE_REMOVE_OWNER => 'remove owner from PowerTrail',
        };

        $this->repository->insertActionLog(
            $powerTrailId,
            $actorUserId,
            $actionType,
            $description . '; affected user: ' . $affectedUserId,
            $affectedUserId
        );
    }

    private function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8');
    }
}
