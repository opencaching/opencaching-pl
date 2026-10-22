<?php

namespace src\Enums;

final class PowerTrailStatusesEnum
{
    public const STATUS_OPEN = 1;
    public const STATUS_UNAVAILABLE = 2;
    public const STATUS_CLOSED = 3;
    public const STATUS_INSERVICE = 4;

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN => 'cs_statusPublic',
            self::STATUS_UNAVAILABLE => 'cs_statusNotYetAvailable',
            self::STATUS_CLOSED => 'cs_statusClosed',
            self::STATUS_INSERVICE => 'cs_statusInService',
        ];
    }
}
