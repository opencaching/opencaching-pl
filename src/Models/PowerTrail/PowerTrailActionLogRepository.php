<?php

declare(strict_types=1);

namespace src\Models\PowerTrail;

use src\Enums\PowerTrailActionLogEnum;
use src\Utils\Database\OcDb;

class PowerTrailActionLogRepository
{
    private readonly OcDb $db;

    /**
     * Create the repository with an optional database connection.
     *
     * @param OcDb|null $db Database connection; defaults to the shared application connection.
     */
    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?? OcDb::instance();
    }

    /**
     * Insert a GeoPath history entry.
     *
     * @param int $powerTrailId GeoPath ID.
     * @param int $userId ID of the user performing the action.
     * @param PowerTrailActionLogEnum $actionType Type of the logged action.
     * @param string $description Human-readable history description.
     * @param int $cacheId Related cache ID, or 0 when not applicable.
     * @return void
     */
    public function insert(
        int $powerTrailId,
        int $userId,
        PowerTrailActionLogEnum $actionType,
        string $description,
        int $cacheId = 0
    ): void {
        $this->db->multiVariableQuery(
            'INSERT INTO `PowerTrail_actionsLog`
                (`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`)
             VALUES (:1, :2, NOW(), :3, :4, :5)',
            $powerTrailId,
            $userId,
            $actionType->value,
            $description,
            $cacheId
        );
    }
}
