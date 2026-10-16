<?php

declare(strict_types=1);

namespace src\Enums;

enum PowerTrailActionLogEnum: int
{
    case CREATE = 1;
    case ATTACH_CACHE = 2;
    case REMOVE_CACHE = 3;
    case ADD_OWNER = 4;
    case REMOVE_OWNER = 5;
    case CHANGE_STATUS = 6;

    public function description(): string
    {
        return match ($this) {
            self::CREATE => 'create new Power Trail',
            self::ATTACH_CACHE => 'attach cache to PowerTrail',
            self::REMOVE_CACHE => 'remove cache from PowerTrail',
            self::ADD_OWNER => 'add another owner to PowerTrail',
            self::REMOVE_OWNER => 'remove owner from PowerTrail',
            self::CHANGE_STATUS => 'change PowerTrail status',
        };
    }
}
