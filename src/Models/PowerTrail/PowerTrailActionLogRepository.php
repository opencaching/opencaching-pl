<?php

declare(strict_types=1);

namespace src\Models\PowerTrail;

use src\Enums\PowerTrailActionLogEnum;
use src\Utils\Database\OcDb;

class PowerTrailActionLogRepository
{
    private readonly OcDb $db;

    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?? OcDb::instance();
    }

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
