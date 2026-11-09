<?php

declare(strict_types=1);

namespace src\Enums\Badges;

/**
 * How the positions (caches) of a merit badge are shown to the user.
 * Stored in `badges`.`cfg_show_positions`.
 */
enum MeritBadgePositionsDisplayEnum: string
{
    case NONE = '';
    case LIST = 'L';
    case MAP = 'M';

    /**
     * Opis sposobu wyświetlania pozycji (po polsku).
     */
    public function description(): string
    {
        return match ($this) {
            self::NONE => 'Nie pokazuj',
            self::LIST => 'Lista',
            self::MAP => 'Mapa',
        };
    }
}
