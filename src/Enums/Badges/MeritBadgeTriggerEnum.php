<?php

declare(strict_types=1);

namespace src\Enums\Badges;

/**
 * System triggers of merit badges, stored in `badges`.`trigger_type`.
 *
 * Values match the constants previously defined in the legacy MeritBadgeController.
 * Note: the DB column comment in `badges`.`trigger_type` is outdated.
 */
enum MeritBadgeTriggerEnum: int
{
    case NONE = 0;
    case CRON = 1;
    case LOG_CACHE = 2;
    case RESERVED_3 = 3;
    case TITLED_CACHE = 4;
    case RESERVED_5 = 5;
    case LOG_GEOPATH = 6;
    case LOG_GEOPATH_AUTHOR = 7;
    case RECOMMENDATION = 8;
    case CACHE_AUTHOR = 9;

    /**
     * Opis wyzwalacza (po polsku).
     */
    public function description(): string
    {
        return match ($this) {
            self::NONE => 'Brak wyzwalacza',
            self::CRON => 'Przeliczanie cykliczne (cron)',
            self::LOG_CACHE => 'Dodanie/usunięcie wpisu logu skrzynki',
            self::RESERVED_3 => 'Zarezerwowane (3)',
            self::TITLED_CACHE => 'Skrzynka z tytułem',
            self::RESERVED_5 => 'Zarezerwowane (5)',
            self::LOG_GEOPATH => 'Wpis logu ścieżki geocachingowej',
            self::LOG_GEOPATH_AUTHOR => 'Autor wpisu ścieżki geocachingowej',
            self::RECOMMENDATION => 'Dodanie/usunięcie rekomendacji',
            self::CACHE_AUTHOR => 'Autor skrzynki',
        };
    }

    /**
     * Typ obiektu, którego dotyczy wyzwalacz.
     */
    public function targetType(): MeritBadgeTargetTypeEnum
    {
        return match ($this) {
            self::NONE, self::CRON, self::CACHE_AUTHOR => MeritBadgeTargetTypeEnum::NONE,
            self::LOG_CACHE, self::RESERVED_3, self::TITLED_CACHE,
            self::RESERVED_5, self::RECOMMENDATION => MeritBadgeTargetTypeEnum::CACHE,
            self::LOG_GEOPATH, self::LOG_GEOPATH_AUTHOR => MeritBadgeTargetTypeEnum::GEOPATH,
        };
    }
}
