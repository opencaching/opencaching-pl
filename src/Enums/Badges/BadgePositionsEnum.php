<?php

namespace src\Enums\Badges;

class BadgePositionsEnum
{
    public const LIMIT = 50;

    public const FOUND_ALL = 'all';
    public const FOUND_NOT_FOUND = 'not_found';
    public const FOUND_FOUND = 'found';

    public const FOUND_OPTIONS = [
        self::FOUND_ALL => 'badge_positions_filter_all',
        self::FOUND_NOT_FOUND => 'badge_positions_filter_not_found',
        self::FOUND_FOUND => 'badge_positions_filter_found',
    ];

    public const SORT_ID_DESC = 0;
    public const SORT_ID_ASC = 1;
    public const SORT_NAME_DESC = 2;
    public const SORT_NAME_ASC = 3;
    public const SORT_DATE_PUBLISHED_DESC = 4;
    public const SORT_DATE_PUBLISHED_ASC = 5;

    private const SORT_MAPPER = [
        self::SORT_ID_DESC => '`c`.`cache_id` DESC',
        self::SORT_ID_ASC => '`c`.`cache_id` ASC',
        self::SORT_NAME_DESC => '`c`.`name` DESC',
        self::SORT_NAME_ASC => '`c`.`name` ASC',
        self::SORT_DATE_PUBLISHED_DESC => '`c`.`date_published` DESC',
        self::SORT_DATE_PUBLISHED_ASC => '`c`.`date_published` ASC',
    ];

    public static function sortMapper(int $sort): string
    {
        return self::SORT_MAPPER[$sort] ?? self::SORT_MAPPER[self::SORT_ID_DESC];
    }

    public static function sortOptions(): array
    {
        return [
            self::SORT_ID_DESC => tr('sort_id_desc'),
            self::SORT_ID_ASC => tr('sort_id_asc'),
            self::SORT_NAME_DESC => tr('sort_name_desc'),
            self::SORT_NAME_ASC => tr('sort_name_asc'),
            self::SORT_DATE_PUBLISHED_DESC => tr('sort_date_desc'),
            self::SORT_DATE_PUBLISHED_ASC => tr('sort_date_asc'),
        ];
    }
}