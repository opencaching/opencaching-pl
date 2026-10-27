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
    case CHANGE_PERCENT_REQUIRED = 7;
    case CHANGE_PERCENT_REQUIRED_BY_OWNER = 8;
    case CHANGE_PERCENT_REQUIRED_BY_FOUNDER = 9;
    case CHANGE_DESCRIPTION_BY_OCTEAM = 10;
    case CHANGE_DESCRIPTION_BY_OWNER = 11;
    case CHANGE_DESCRIPTION_BY_FOUNDER = 12;
    case REMOVE_FOUNDER_BY_OCTEAM = 13;
    case ADD_FOUNDER_BY_OCTEAM = 14;
    case CHANGE_DATE_CREATED_BY_OCTEAM = 15;

    public function description(): string
    {
        return match ($this) {
            self::CREATE => 'create new Power Trail',
            self::ATTACH_CACHE => 'attach cache to PowerTrail',
            self::REMOVE_CACHE => 'remove cache from PowerTrail',
            self::ADD_OWNER => 'add another owner to PowerTrail',
            self::REMOVE_OWNER => 'remove owner from PowerTrail',
            self::CHANGE_STATUS => 'change PowerTrail status',
            self::CHANGE_PERCENT_REQUIRED => 'Change PerccentRequired by OcTeam',
            self::CHANGE_PERCENT_REQUIRED_BY_OWNER => 'Change PerccentRequired by Owner',
            self::CHANGE_PERCENT_REQUIRED_BY_FOUNDER => 'Change PerccentRequired by Founder',
            self::CHANGE_DESCRIPTION_BY_OCTEAM => 'Change Descruption by OcTeam',
            self::CHANGE_DESCRIPTION_BY_OWNER => 'Change Descruption by Owner',
            self::CHANGE_DESCRIPTION_BY_FOUNDER => 'Change Descruption by Founder',
            self::REMOVE_FOUNDER_BY_OCTEAM => 'Remove Founder by OcTeam',
            self::ADD_FOUNDER_BY_OCTEAM => 'Add Founder by OcTeam',
            self::CHANGE_DATE_CREATED_BY_OCTEAM => 'Change DateCreated by OcTeam',
        };
    }
}
