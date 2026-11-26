<?php

declare(strict_types=1);

namespace src\Enums;

/**
 * Cache statuses stored in `caches`.`status`.
 *
 * The backing values match the `cache_status`.`id` column.
 */
enum CacheStatusEnum: int
{
    case READY = 1;
    case TEMPORARILY_UNAVAILABLE = 2;
    case ARCHIVED = 3;
    case AWAITING_APPROVAL = 4;
    case NOT_YET_AVAILABLE = 5;
    case BLOCKED_BY_OC_TEAM = 6;
}