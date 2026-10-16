<?php

declare(strict_types=1);

namespace src\Services;

use src\Enums\PowerTrailActionLogEnum;
use src\Models\PowerTrail\PowerTrailActionLogRepository;

class PowerTrailActionLogService
{
    private readonly PowerTrailActionLogRepository $repository;

    public function __construct(?PowerTrailActionLogRepository $repository = null)
    {
        $this->repository = $repository ?? new PowerTrailActionLogRepository();
    }

    /**
     * Write an entry to the GeoPath history.
     * Description defaults to the standard text of the action type.
     */
    public function log(
        PowerTrailActionLogEnum $actionType,
        int $powerTrailId,
        int $userId,
        int $cacheId = 0,
        ?string $description = null
    ): void {
        $this->repository->insert(
            $powerTrailId,
            $userId,
            $actionType,
            $description ?? $actionType->description(),
            $cacheId
        );
    }
}
