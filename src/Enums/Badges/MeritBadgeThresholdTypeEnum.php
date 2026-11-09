<?php

declare(strict_types=1);

namespace src\Enums\Badges;

/**
 * How the levels of a merit badge are presented: by period (e.g. years) or by threshold.
 * Stored in `badges`.`cfg_period_threshold`.
 */
enum MeritBadgeThresholdTypeEnum: string
{
    case PERIOD = 'P';
    case THRESHOLD = 'T';

    /**
     * Opis sposobu prezentacji poziomów (po polsku).
     */
    public function description(): string
    {
        return match ($this) {
            self::PERIOD => 'Okres',
            self::THRESHOLD => 'Próg',
        };
    }
}
