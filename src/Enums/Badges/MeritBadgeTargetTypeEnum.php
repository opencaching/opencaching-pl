<?php

declare(strict_types=1);

namespace src\Enums\Badges;

/**
 * Type of object a merit badge trigger refers to.
 */
enum MeritBadgeTargetTypeEnum: string
{
    case NONE = 'N';
    case CACHE = 'C';
    case GEOPATH = 'G';

    /**
     * Opis typu celu (po polsku).
     */
    public function description(): string
    {
        return match ($this) {
            self::NONE => 'Brak (nic nie jest sprawdzane)',
            self::CACHE => 'Skrzynka',
            self::GEOPATH => 'Ścieżka geocachingowa',
        };
    }
}
