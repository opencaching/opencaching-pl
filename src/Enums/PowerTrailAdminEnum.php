<?php

namespace src\Enums;

class PowerTrailAdminEnum
{
    public const LIMIT = 50;

    public const SORT_ID_DESC = 0;
    public const SORT_ID_ASC = 1;
    public const SORT_NAME_DESC = 2;
    public const SORT_NAME_ASC = 3;
    public const SORT_CACHE_COUNT_DESC = 4;
    public const SORT_CACHE_COUNT_ASC = 5;
    public const SORT_DATE_CREATED_DESC = 6;
    public const SORT_DATE_CREATED_ASC = 7;

    public const DISPLAY_NAME_SQL = "COALESCE(NULLIF(TRIM(`name`), ''), CONCAT('GEOPATH NULL NAME (', `id`, ')'))";

    private const SORT_MAPPER = [
        self::SORT_ID_DESC => '`id` DESC',
        self::SORT_ID_ASC => '`id` ASC',
        self::SORT_NAME_DESC => self::DISPLAY_NAME_SQL . ' DESC',
        self::SORT_NAME_ASC => self::DISPLAY_NAME_SQL . ' ASC',
        self::SORT_CACHE_COUNT_DESC => '`cacheCount` DESC',
        self::SORT_CACHE_COUNT_ASC => '`cacheCount` ASC',
        self::SORT_DATE_CREATED_DESC => '`dateCreated` DESC',
        self::SORT_DATE_CREATED_ASC => '`dateCreated` ASC',
    ];

    public static function sortMapper(int $sort): string
    {
        return self::SORT_MAPPER[$sort] ?? self::SORT_MAPPER[self::SORT_ID_DESC];
    }

    public static function sortOptions(): array
    {
        return [
            self::SORT_ID_DESC => tr('powertrail_admin_sort_id_desc'),
            self::SORT_ID_ASC => tr('powertrail_admin_sort_id_asc'),
            self::SORT_NAME_DESC => tr('powertrail_admin_sort_name_desc'),
            self::SORT_NAME_ASC => tr('powertrail_admin_sort_name_asc'),
            self::SORT_CACHE_COUNT_DESC => tr('powertrail_admin_sort_cache_count_desc'),
            self::SORT_CACHE_COUNT_ASC => tr('powertrail_admin_sort_cache_count_asc'),
            self::SORT_DATE_CREATED_DESC => tr('powertrail_admin_sort_date_created_desc'),
            self::SORT_DATE_CREATED_ASC => tr('powertrail_admin_sort_date_created_asc'),
        ];
    }
}
