<?php

namespace src\Models\GeoCache;

use src\Models\BaseObject;
use src\Utils\Debug\Debug;

/**
 * Common constants etc. for geocache log
 */
class GeoCacheLogCommons extends BaseObject
{
    public const LOGTYPE_FOUNDIT = 1;

    public const LOGTYPE_DIDNOTFIND = 2;

    public const LOGTYPE_COMMENT = 3;

    public const LOGTYPE_MOVED = 4;

    public const LOGTYPE_NEEDMAINTENANCE = 5;

    public const LOGTYPE_MADEMAINTENANCE = 6;

    public const LOGTYPE_ATTENDED = 7;

    public const LOGTYPE_WILLATTENDED = 8;

    public const LOGTYPE_ARCHIVED = 9;

    public const LOGTYPE_READYTOSEARCH = 10;

    public const LOGTYPE_TEMPORARYUNAVAILABLE = 11;

    public const LOGTYPE_ADMINNOTE = 12;

    private const ICON_PATH = '/images/log/'; //path to the dir with log-type icons

    public static function GetIconForType(int $logType, bool $fileNameOnly = false): string
    {
        switch ($logType) {
            case self::LOGTYPE_FOUNDIT:
                $icon = 'found.svg';
                break;
            case self::LOGTYPE_DIDNOTFIND:
                $icon = 'dnf.svg';
                break;
            case self::LOGTYPE_COMMENT:
                $icon = 'note.svg';
                break;
            case self::LOGTYPE_MOVED:
                $icon = 'moved.svg';
                break;
                case self::LOGTYPE_NEEDMAINTENANCE:
                $icon = 'need-maintenance.svg';
                break;
            case self::LOGTYPE_MADEMAINTENANCE:
                $icon = 'made-maintenance.svg';
                break;
            case self::LOGTYPE_ATTENDED:
                $icon = 'attend.svg';
                break;
            case self::LOGTYPE_WILLATTENDED:
                $icon = 'will_attend.svg';
                break;
            case self::LOGTYPE_ARCHIVED:
                $icon = 'trash.svg';
                break;
            case self::LOGTYPE_READYTOSEARCH:
                $icon = 'published.svg';
                break;
            case self::LOGTYPE_TEMPORARYUNAVAILABLE:
                $icon = 'temporary.svg';
                break;
            case self::LOGTYPE_ADMINNOTE:
                $icon = 'octeam.svg';
                break;
            default:
                Debug::errorLog("Unknown log type: {$logType}");
                $icon = 'found.svg';
                break;
        }

        if (! $fileNameOnly) {
            $icon = self::ICON_PATH . $icon;
        }

        return $icon;
    }

    public static function typeTranslationKey(int $logType): string
    {
        switch ($logType) {
            case self::LOGTYPE_FOUNDIT:
                return 'logType1';
            case self::LOGTYPE_DIDNOTFIND:
                return 'logType2';
            case self::LOGTYPE_COMMENT:
                return 'logType3';
            case self::LOGTYPE_MOVED:
                return 'logType4';
            case self::LOGTYPE_NEEDMAINTENANCE:
                return 'logType5';
            case self::LOGTYPE_MADEMAINTENANCE:
                return 'logType6';
            case self::LOGTYPE_ATTENDED:
                return 'logType7';
            case self::LOGTYPE_WILLATTENDED:
                return 'logType8';
            case self::LOGTYPE_ARCHIVED:
                return 'logType9';
            case self::LOGTYPE_READYTOSEARCH:
                return 'logType10';
            case self::LOGTYPE_TEMPORARYUNAVAILABLE:
                return 'logType11';
            case self::LOGTYPE_ADMINNOTE:
                return 'logType12';
            default:
                Debug::errorLog("Unknown log type: {$logType}");

                return '';
        }
    }

    public static function logTypesArray(): array
    {
        return [
            self::LOGTYPE_FOUNDIT,
            self::LOGTYPE_DIDNOTFIND,
            self::LOGTYPE_COMMENT,
            self::LOGTYPE_MOVED,
            self::LOGTYPE_NEEDMAINTENANCE,
            self::LOGTYPE_MADEMAINTENANCE,
            self::LOGTYPE_ATTENDED,
            self::LOGTYPE_WILLATTENDED,
            self::LOGTYPE_ARCHIVED,
            self::LOGTYPE_READYTOSEARCH,
            self::LOGTYPE_TEMPORARYUNAVAILABLE,
            self::LOGTYPE_ADMINNOTE,
        ];
    }

    /**
     * Returns translation key for cache log if cache changed status
     */
    public static function translationKey4CacheStatus(int $status): string
    {
        switch ($status) {
            case GeoCache::STATUS_READY:
                return 'ready_to_search';
            case GeoCache::STATUS_UNAVAILABLE:
                return 'temporarily_unavailable';
            case GeoCache::STATUS_ARCHIVED:
                return 'archived_cache';
            case GeoCache::STATUS_BLOCKED:
                return 'blocked_by_octeam';
            default:
                Debug::errorLog("Unknown cache status: {$status}");

                return '';
        }
    }

    /**
     * There are many places where log text is displayed as a tooltip
     * It is needed to remove many chars which can break the tooltip display operation
     *
     * @param string $text - original log text
     * @return string - clean log text
     */
    public static function cleanLogTextForToolTip(string $text): string
    {
        //strip all tags but not <li>
        $text = strip_tags($text, '<li>');

        $replace = [
            //'<p>&nbsp;</p>' => '', //duplicated ? by strip_tags above
            '&nbsp;' => ' ',
            //'<p>' => '', //duplicated ? by strip_tags above
            "\n" => ' ',
            "\r" => '',
            //'</p>' => "", //duplicated ? by strip_tags above
            //'<br>' => "", //duplicated ? by strip_tags above
            //'<br />' => "", //duplicated ? by strip_tags above
            //'<br/>' => "", //duplicated ? by strip_tags above
            '<li>' => ' - ',
            '</li>' => '',
            '&oacute;' => 'o',
            '&quot;' => '-',
            //'&[^;]*;' => '', ???
            '&' => '',
            "'" => '',
            '"' => '',
            '<' => '',
            '>' => '',
            '(' => ' -',
            ')' => '- ',
            ']]>' => ']] >',
            '' => '',
        ];

        $text = str_ireplace(array_keys($replace), array_values($replace), $text);

        return preg_replace('/[\x00-\x08\x0E-\x1F\x7F\x0A\x0C]+/', '', $text);
    }

    public static function getLogUrlByLogId($logId): string
    {
        return "/viewlogs.php?logid={$logId}";
    }

    /**
     * Creates an array of log type translation keys available for the cache
     * of given type, for views and templates usability
     *
     * @param int $cacheType the type of cache to create the list for
     *
     * @return array log type translation keys available for given cache,
     *               ordered by log type value
     */
    public static function getLogTypeTplKeys(
        int $cacheType = GeoCacheCommons::TYPE_TRADITIONAL
    ): array {
        $result = [];
        $logTypes = [
            self::LOGTYPE_COMMENT, self::LOGTYPE_NEEDMAINTENANCE,
            self::LOGTYPE_MADEMAINTENANCE, self::LOGTYPE_ARCHIVED,
            self::LOGTYPE_READYTOSEARCH, self::LOGTYPE_TEMPORARYUNAVAILABLE,
            self::LOGTYPE_ADMINNOTE,
        ];

        if ($cacheType == GeoCacheCommons::TYPE_EVENT) {
            array_push(
                $logTypes,
                self::LOGTYPE_ATTENDED,
                self::LOGTYPE_WILLATTENDED
            );
        } else {
            array_push(
                $logTypes,
                self::LOGTYPE_FOUNDIT,
                self::LOGTYPE_DIDNOTFIND
            );

            if ($cacheType == GeoCacheCommons::TYPE_MOVING) {
                $logTypes[] = self::LOGTYPE_MOVED;
            }
        }
        sort($logTypes);

        foreach ($logTypes as $logType) {
            $result[$logType] = self::typeTranslationKey($logType);
        }

        return $result;
    }
}
