<?php

declare(strict_types=1);

namespace src\Enums;

/**
 * Cache log types stored in `cache_logs`.`type`.
 *
 * The backing values match the legacy GeoCacheLogCommons::LOGTYPE_* constants.
 */
enum LogTypeEnum: int
{
    case FOUND_IT = 1;
    case DID_NOT_FIND = 2;
    case COMMENT = 3;
    case MOVED = 4;
    case NEEDS_MAINTENANCE = 5;
    case MAINTENANCE_PERFORMED = 6;
    case ATTENDED = 7;
    case WILL_ATTEND = 8;
    case ARCHIVED = 9;
    case READY_TO_SEARCH = 10;
    case TEMPORARILY_UNAVAILABLE = 11;
    case OC_TEAM_COMMENT = 12;
}
