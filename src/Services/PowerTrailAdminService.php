<?php

namespace src\Services;

use RuntimeException;
use src\Controllers\PowerTrailController;
use src\Enums\PowerTrailStatusesEnum;
use src\Models\ApplicationContainer;
use src\Models\PowerTrail\Log as PowerTrailLog;
use src\Models\PowerTrail\PowerTrail;
use src\Models\User\User;
use src\Utils\Database\OcDb;
use src\Utils\Generators\Uuid;

/**
 * Admin operations for GeoPaths. All public methods require an Octeam user
 * and an HTTP POST request.
 */
class PowerTrailAdminService
{
    /** @var OcDb */
    private $db;

    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?: OcDb::instance();
    }

    /** Return the complete PowerTrail row. */
    public function getGeoPathById(int $powerTrailId): ?array
    {
        $this->requireOcteamPost();

        $statement = $this->db->multiVariableQuery(
            'SELECT * FROM `PowerTrail` WHERE `id` = :1 LIMIT 1',
            $powerTrailId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row : null;
    }

    public function getDescription(int $powerTrailId): ?string
    {
        $this->requireOcteamPost();

        $statement = $this->db->multiVariableQuery(
            'SELECT `description` FROM `PowerTrail` WHERE `id` = :1 LIMIT 1',
            $powerTrailId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row['description'] : null;
    }

    public function updateDescription(int $powerTrailId, string $description): bool
    {
        $this->requireOcteamPost();

        if (! $this->geoPathExists($powerTrailId)) {
            return false;
        }

        $this->db->multiVariableQuery(
            'UPDATE `PowerTrail` SET `description` = :1 WHERE `id` = :2',
            htmlspecialchars($description, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8'),
            $powerTrailId
        );

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

        $statement = $this->db->multiVariableQuery(
            'SELECT `status` FROM `PowerTrail` WHERE `id` = :1 LIMIT 1',
            $powerTrailId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? (int) $row['status'] : null;
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

        $commentTypes = [
            PowerTrailStatusesEnum::STATUS_OPEN => PowerTrailLog::TYPE_OPENING,
            PowerTrailStatusesEnum::STATUS_INSERVICE => PowerTrailLog::TYPE_DISABLING,
            PowerTrailStatusesEnum::STATUS_CLOSED => PowerTrailLog::TYPE_CLOSING,
        ];
        $commentType = $commentTypes[$status] ?? PowerTrailLog::TYPE_COMMENT;

        if (trim($comment) === '') {
            $commentKeys = [
                PowerTrailStatusesEnum::STATUS_OPEN => 'pt215',
                PowerTrailStatusesEnum::STATUS_INSERVICE => 'pt217',
                PowerTrailStatusesEnum::STATUS_CLOSED => 'pt218',
            ];
            $comment = isset($commentKeys[$status])
                ? tr($commentKeys[$status]) . '!'
                : tr('pt056') . '!';
        }

        $this->db->multiVariableQuery(
            'INSERT INTO `PowerTrail_comments`
                (`userId`, `PowerTrailId`, `commentType`, `commentText`,
                 `logDateTime`, `dbInsertDateTime`, `deleted`, `uuid`)
             VALUES (:1, :2, :3, :4, NOW(), NOW(), 0, ' . Uuid::getSqlForUpperCaseUuid() . ')',
            $user->getUserId(),
            $powerTrailId,
            $commentType,
            htmlspecialchars($comment, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8')
        );

        $this->db->multiVariableQuery(
            'INSERT INTO `PowerTrail_actionsLog`
                (`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`)
             VALUES (:1, :2, NOW(), 6, :3, 0)',
            $powerTrailId,
            $user->getUserId(),
            'change PowerTrail status'
        );

        return $result;
    }

    /** WIS is stored in PowerTrail.perccentRequired. */
    public function getWis(int $powerTrailId): ?int
    {
        $this->requireOcteamPost();

        $statement = $this->db->multiVariableQuery(
            'SELECT `perccentRequired` FROM `PowerTrail` WHERE `id` = :1 LIMIT 1',
            $powerTrailId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? (int) $row['perccentRequired'] : null;
    }

    public function updateWis(int $powerTrailId, int $wis): bool
    {
        $this->requireOcteamPost();

        if ($wis < PowerTrailController::MINIMUM_PERCENT_REQUIRED || $wis > 100) {
            return false;
        }

        $statement = $this->db->multiVariableQuery(
            'UPDATE `PowerTrail` SET `perccentRequired` = :1 WHERE `id` = :2',
            $wis,
            $powerTrailId
        );

        return $this->db->rowCount($statement) > 0;
    }

    /** Return all users assigned as GeoPath founders/owners (not the mentor). */
    public function getFounders(int $powerTrailId): array
    {
        $this->requireOcteamPost();

        $statement = $this->db->multiVariableQuery(
            'SELECT u.`user_id`, u.`username`, u.`email`, pto.`privileages`
             FROM `PowerTrail_owners` AS pto
             JOIN `user` AS u ON u.`user_id` = pto.`userId`
             WHERE pto.`PowerTrailId` = :1
             ORDER BY u.`username`, u.`user_id`',
            $powerTrailId
        );

        return $this->db->dbResultFetchAll($statement);
    }

    public function addFounder(int $powerTrailId, int $userId): bool
    {
        $actor = $this->requireOcteamPost();

        if (! $this->geoPathExists($powerTrailId) || ! $this->userExists($userId)) {
            return false;
        }

        $statement = $this->db->multiVariableQuery(
            'INSERT IGNORE INTO `PowerTrail_owners` (`PowerTrailId`, `userId`, `privileages`)
             VALUES (:1, :2, 1)',
            $powerTrailId,
            $userId
        );
        if ($this->db->rowCount($statement) === 0) {
            return false;
        }

        $this->writeOwnerActionLog($powerTrailId, $actor->getUserId(), 4, $userId);

        return true;
    }

    public function removeFounder(int $powerTrailId, int $userId): bool
    {
        $actor = $this->requireOcteamPost();

        $ownerCount = $this->db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1',
            0,
            $powerTrailId
        );
        if ($ownerCount <= 1 || ! $this->geoPathExists($powerTrailId)) {
            return false;
        }

        $statement = $this->db->multiVariableQuery(
            'DELETE FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1 AND `userId` = :2',
            $powerTrailId,
            $userId
        );
        if ($this->db->rowCount($statement) === 0) {
            return false;
        }

        $this->writeOwnerActionLog($powerTrailId, $actor->getUserId(), 5, $userId);

        return true;
    }

    private function userExists(int $userId): bool
    {
        return $this->db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM `user` WHERE `user_id` = :1',
            0,
            $userId
        ) > 0;
    }

    private function geoPathExists(int $powerTrailId): bool
    {
        return $this->db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM `PowerTrail` WHERE `id` = :1',
            0,
            $powerTrailId
        ) > 0;
    }

    private function writeOwnerActionLog(int $powerTrailId, int $actorUserId, int $actionType, int $affectedUserId): void
    {
        $descriptions = [
            4 => 'add another owner to PowerTrail',
            5 => 'remove owner from PowerTrail',
        ];

        $this->db->multiVariableQuery(
            'INSERT INTO `PowerTrail_actionsLog`
                (`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`)
             VALUES (:1, :2, NOW(), :3, :4, :5)',
            $powerTrailId,
            $actorUserId,
            $actionType,
            $descriptions[$actionType] . '; affected user: ' . $affectedUserId,
            $affectedUserId
        );
    }

    private function requireOcteamPost(): User
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            throw new RuntimeException('POST required', 405);
        }

        $user = ApplicationContainer::GetAuthorizedUser();
        if (! $user || ! $user->hasOcTeamRole()) {
            throw new RuntimeException('Octeam access required', 403);
        }

        return $user;
    }
}
