<?php
namespace src\Models\GeoCache;

use src\Models\BaseObject;
use src\Utils\Debug\Debug;

/**
 * Common consts etc. for geocaches
 *
 */

class GeoCacheCommons extends BaseObject {

    const TYPE_OTHERTYPE = 1;
    const TYPE_TRADITIONAL = 2;
    const TYPE_MULTICACHE = 3;
    const TYPE_VIRTUAL = 4;
    const TYPE_WEBCAM = 5;
    const TYPE_EVENT = 6;
    const TYPE_QUIZ = 7;
    const TYPE_MOVING = 8;
    const TYPE_GEOPATHFINAL = 9;    //TODO: old -podcast- type?
    const TYPE_OWNCACHE = 10;

    const STATUS_READY = 1;
    const STATUS_UNAVAILABLE = 2;
    const STATUS_ARCHIVED = 3;
    const STATUS_WAITAPPROVERS = 4;
    const STATUS_NOTYETAVAILABLE = 5;
    const STATUS_BLOCKED = 6;

    const SIZE_NONE = 7;
    const SIZE_NANO = 8;
    const SIZE_MICRO = 2;
    const SIZE_SMALL = 3;
    const SIZE_REGULAR = 4;
    const SIZE_LARGE = 5;
    const SIZE_XLARGE = 6;
    const SIZE_OTHER = 1;

    const RECOMENDATION_RATIO = 10; //percentage of founds which can be recomeded by user

    const MIN_SCORE_OF_RATING_5 = 2.2;
    const MIN_SCORE_OF_RATING_4 = 1.4;
    const MIN_SCORE_OF_RATING_3 = 0.1;
    const MIN_SCORE_OF_RATING_2 = -1.0;

    const ICON_PATH = '/images/cache/'; //path to the dir with cache icons

    const TYPE_TRADITIONAL_TR_KEY    = 'cacheType_1';
    const TYPE_OTHERTYPE_TR_KEY      = 'cacheType_5';
    const TYPE_MULTICACHE_TR_KEY     = 'cacheType_2';
    const TYPE_VIRTUAL_TR_KEY        = 'cacheType_8';
    const TYPE_WEBCAM_TR_KEY         = 'cacheType_7';
    const TYPE_EVENT_TR_KEY          = 'cacheType_6';
    const TYPE_QUIZ_TR_KEY           = 'cacheType_3';
    const TYPE_MOVING_TR_KEY         = 'cacheType_4';
    const TYPE_GEOPATHFINAL_TR_KEY   = 'cacheType_9';
    const TYPE_OWNCACHE_TR_KEY       = 'cacheType_10';

    const STATUS_READY_TR_KEY            = 'cacheStatus_1';
    const STATUS_UNAVAILABLE_TR_KEY      = 'cacheStatus_2';
    const STATUS_ARCHIVED_TR_KEY         = 'cacheStatus_3';
    const STATUS_WAITAPPROVERS_TR_KEY    = 'cacheStatus_4';
    const STATUS_NOTYETAVAILABLE_TR_KEY  = 'cacheStatus_5';
    const STATUS_BLOCKED_TR_KEY          = 'cacheStatus_6';

    const SIZE_OTHER_TR_KEY   = 'cacheSize_other';
    const SIZE_NANO_TR_KEY   = 'cacheSize_nano';
    const SIZE_MICRO_TR_KEY   = 'cacheSize_micro';
    const SIZE_SMALL_TR_KEY   = 'cacheSize_small';
    const SIZE_REGULAR_TR_KEY = 'cacheSize_regular';
    const SIZE_LARGE_TR_KEY   = 'cacheSize_large';
    const SIZE_XLARGE_TR_KEY  = 'cacheSize_xLarge';
    const SIZE_NONE_TR_KEY   = 'cacheSize_none';

    public function __construct()
    {
        parent::__construct();
    }

    public static function CacheTypeTranslationKey($type)
    {

        switch ($type) {
            case self::TYPE_TRADITIONAL:    return self::TYPE_TRADITIONAL_TR_KEY;
            case self::TYPE_OTHERTYPE:      return self::TYPE_OTHERTYPE_TR_KEY;
            case self::TYPE_MULTICACHE:     return self::TYPE_MULTICACHE_TR_KEY;
            case self::TYPE_VIRTUAL:        return self::TYPE_VIRTUAL_TR_KEY;
            case self::TYPE_WEBCAM:         return self::TYPE_WEBCAM_TR_KEY;
            case self::TYPE_EVENT:          return self::TYPE_EVENT_TR_KEY;
            case self::TYPE_QUIZ:           return self::TYPE_QUIZ_TR_KEY;
            case self::TYPE_MOVING:         return self::TYPE_MOVING_TR_KEY;
            case self::TYPE_GEOPATHFINAL:   return self::TYPE_GEOPATHFINAL_TR_KEY;
            case self::TYPE_OWNCACHE:       return self::TYPE_OWNCACHE_TR_KEY;
        }
    }

    public static function CacheTypesArray()
    {
        return [
            self::TYPE_OTHERTYPE,
            self::TYPE_TRADITIONAL,
            self::TYPE_MULTICACHE,
            self::TYPE_VIRTUAL,
            self::TYPE_WEBCAM,
            self::TYPE_EVENT,
            self::TYPE_QUIZ,
            self::TYPE_MOVING,
            self::TYPE_GEOPATHFINAL,    //TODO: old -podcast- type?
            self::TYPE_OWNCACHE,
        ];
    }

    public static function CacheStatusTranslationKey($type)
    {
        switch ($type) {
            case self::STATUS_READY:            return self::STATUS_READY_TR_KEY;
            case self::STATUS_UNAVAILABLE:      return self::STATUS_UNAVAILABLE_TR_KEY;
            case self::STATUS_ARCHIVED:         return self::STATUS_ARCHIVED_TR_KEY;
            case self::STATUS_WAITAPPROVERS:    return self::STATUS_WAITAPPROVERS_TR_KEY;
            case self::STATUS_NOTYETAVAILABLE:  return self::STATUS_NOTYETAVAILABLE_TR_KEY;
            case self::STATUS_BLOCKED:          return self::STATUS_BLOCKED_TR_KEY;
        }
    }

    /**
     * Returns the cache size key based on size numeric identifier
     *
     * @param int $sizeId
     * @return string - size key for translation
     */
    public static function CacheSizeTranslationKey($sizeId)
    {
        switch ($sizeId) {
            case self::SIZE_OTHER:   return self::SIZE_OTHER_TR_KEY;
            case self::SIZE_NANO:    return self::SIZE_NANO_TR_KEY;
            case self::SIZE_MICRO:   return self::SIZE_MICRO_TR_KEY;
            case self::SIZE_SMALL:   return self::SIZE_SMALL_TR_KEY;
            case self::SIZE_REGULAR: return self::SIZE_REGULAR_TR_KEY;
            case self::SIZE_LARGE:   return self::SIZE_LARGE_TR_KEY;
            case self::SIZE_XLARGE:  return self::SIZE_XLARGE_TR_KEY;
            case self::SIZE_NONE:    return self::SIZE_NONE_TR_KEY;

            default:
                Debug::errorLog('Unknown cache sizeId: ' . $sizeId);
                return 'size_04';
        }
    }

    public static function CacheSizesArray()
    {
        // Sizes will always be displayed in this order.
        // TODO: move "other" before "none" if the meaning is "other"
        //       (see OKAPI issue #519)
        //       (+ adjust order in myroutes_search.tpl.php)

        return array(
            self::SIZE_NANO,
            self::SIZE_MICRO,
            self::SIZE_SMALL,
            self::SIZE_REGULAR,
            self::SIZE_LARGE,
            self::SIZE_XLARGE,
            self::SIZE_NONE,
            self::SIZE_OTHER
        );
    }

    /**
     * Returns array of all cache statuses
     *
     * @return integer[]
     */
    public static function CacheStatusArray()
    {
        return [
            self::STATUS_READY,
            self::STATUS_UNAVAILABLE,
            self::STATUS_ARCHIVED,
            self::STATUS_WAITAPPROVERS,
            self::STATUS_NOTYETAVAILABLE,
            self::STATUS_BLOCKED
        ];
    }

    /**
     * Returns TypeId of the cache based on OKAPI description
     *
     * @param String $okapiType
     * @return int TypeId
     */
    public static function CacheTypeIdFromOkapi($okapiType)
    {
        switch ($okapiType) {
            case 'Traditional':
                return self::TYPE_TRADITIONAL;
            case 'Multi':
                return self::TYPE_MULTICACHE;
            case 'Virtual':
                return self::TYPE_VIRTUAL;
            case 'Webcam':
                return self::TYPE_WEBCAM;
            case 'Event':
                return self::TYPE_EVENT;
            case 'Quiz':
                return self::TYPE_QUIZ;
            case 'Moving':
                return self::TYPE_MOVING;
            case 'Own':
                return self::TYPE_OWNCACHE;
            case 'Other':
                return self::TYPE_OTHERTYPE;
            default:
                Debug::errorLog('Unknown cache type from OKAPI: ' . $okapiType);
                return self::TYPE_TRADITIONAL;
        }
    }

    /**
     * Returns SizeId of the cache based on OKAPI description
     *
     * @param String $okapiType
     * @return int TypeId
     */
    public static function CacheSizeIdFromOkapi($okapiSize)
    {
        switch ($okapiSize) {

            case 'none':
                return self::SIZE_NONE;
            case 'nano':
                return self::SIZE_NANO;
            case 'micro':
                return self::SIZE_MICRO;
            case 'small':
                return self::SIZE_SMALL;
            case 'regular':
                return self::SIZE_REGULAR;
            case 'large':
                return self::SIZE_LARGE;
            case 'xlarge':
                return self::SIZE_XLARGE;
            case 'other':
                return self::SIZE_OTHER;
            default:
                Debug::errorLog('Unknown cache size from OKAPI: ' . $okapiSize);
                return self::SIZE_OTHER;
        }
    }

    /**
     * Returns the cache status based on the okapi response desc.
     *
     * @param string $okapiStatus
     * @return string - internal enum
     */
    public static function CacheStatusIdFromOkapi($okapiStatus)
    {
        switch ($okapiStatus) {
            case 'Available':
                return self::STATUS_READY;
            case 'Temporarily unavailable':
                return self::STATUS_UNAVAILABLE;
            case 'Archived':
                return self::STATUS_ARCHIVED;
            default:
                Debug::errorLog('Unknown cache status from OKAPI: ' . $okapiStatus);
                return self::STATUS_READY;
        }
    }

    /**
     * Retrurn cache icon based on its type and status
     *
     * @param enum $type the cache type
     * @param enum $status the cache status
     * @param enum $logStatus (optional) log status information to include in icon
     * @param bool $fileNameOnly (optional) true if the result should be a filename,
     *     false (default) if it should be prefixed by full path
     * @param bool $isOwner (optional) true if the icon should be for the cache owner,
     *     false (default) otherwise
     * @return string - path + filename of the right icon
     */
    public static function CacheIconByType(
        $type, $status, $logStatus = null, $fileNameOnly = false, $isOwner = false)
    {

        $statusPart = ""; //part of icon name represents cache status
        switch ($status) {
            case self::STATUS_UNAVAILABLE:
            case self::STATUS_NOTYETAVAILABLE:
            case self::STATUS_WAITAPPROVERS:
                $statusPart = "-n";
                break;
            case self::STATUS_ARCHIVED:
                $statusPart = "-a";
                break;
            case self::STATUS_BLOCKED:
                $statusPart = "-d";
                break;
            default:
                $statusPart = "-s";
                break;
        }

        $logStatusPart = ''; //part of icon name represents status for user based on logs
        switch ($logStatus) {
            case GeoCacheLog::LOGTYPE_FOUNDIT:
                $logStatusPart = '-found';
                break;
            case GeoCacheLog::LOGTYPE_DIDNOTFIND:
                $logStatusPart = '-dnf';
                break;
            default:
                if ($isOwner) {
                    $logStatusPart = '-owner';
                }
        }

        $typePart = ""; //part of icon name represents cache type
        switch ($type) {
            case self::TYPE_OTHERTYPE:
                $typePart = 'unknown';
                break;

            case self::TYPE_TRADITIONAL:
            default:
                $typePart = 'traditional';
                break;

            case self::TYPE_MULTICACHE:
                $typePart = 'multi';
                break;

            case self::TYPE_VIRTUAL:
                $typePart = 'virtual';
                break;

            case self::TYPE_WEBCAM:
                $typePart = 'webcam';
                break;

            case self::TYPE_EVENT:
                $typePart = 'event';
                break;

            case self::TYPE_QUIZ:
                $typePart = 'quiz';
                break;

            case self::TYPE_MOVING:
                $typePart = 'moving';
                break;

            case self::TYPE_OWNCACHE:
                $typePart = 'owncache';
                break;
        }

        if ($fileNameOnly) {
            return $typePart . $statusPart . $logStatusPart . '.png';
        } else {
            return self::ICON_PATH . $typePart . $statusPart . $logStatusPart . '.png';
        }
    }

    /**
     * Note:
     * - Score is stored in OC db and has value in range <-3;3>
     * - RatingId is counted by OKAPI and has value in range <1;5>
     * Do not confuse them with each other!
     *
     * @param float $score
     * @return number
     */
    public static function ScoreAsRatingNum($score)
    {
        // former score2ratingnum

        if ($score >= self::MIN_SCORE_OF_RATING_5) return 5;
        if ($score >= self::MIN_SCORE_OF_RATING_4) return 4;
        if ($score >= self::MIN_SCORE_OF_RATING_3) return 3;
        if ($score >= self::MIN_SCORE_OF_RATING_2) return 2;
        return 1;
    }

    public static function ScoreFromRatingNum($ratingId)
    {
        //former new2oldscore($score)

        if ($ratingId == 5) return 3.0;
        if ($ratingId == 4) return 1.7;
        if ($ratingId == 3) return 0.7;
        if ($ratingId == 2) return -0.5;
        return -2.0;
    }

    /**
     *  Note:
     * - Score is stored in OC db and has value in range <-3;3>
     * - RatingId is counted by OKAPI and has value in range <1;5>
     * Do not confuse them with each other!
     *
     * @param unknown $score
     * @return string|mixed
     */
    public static function ScoreNameTranslation($score)
    {

        $ratingNum = self::ScoreAsRatingNum($score);
        return tr(self::CacheRatingTranslationKey($ratingNum));

    }

    /**
     * Returns cache reating description based on ratingId
     *
     * Note:
     * - Score is stored in OC db and has value in range <-3;3>
     * - RatingId is counted by OKAPI and has value in range <1;5>
     * Do not confuse them with each other!
     *
     * @param int $ratingId
     * @return string - rating description key for translation
     */
    public static function CacheRatingTranslationKey($ratingId)
    {
        switch($ratingId) {
            case 1: return 'rating_poor';
            case 2: return 'rating_mediocre';
            case 3: return 'rating_avarage';
            case 4: return 'rating_good';
            case 5: return 'rating_excellent';
        }
    }

    /**
     * Returns comma separated list of cache status being visible for common
     * users
     *
     * @return string cache status list, ready for use in SQL f.ex.
     */
    public static function CacheVisibleStatusList()
    {
        return implode(', ', [
            self::STATUS_READY,
            self::STATUS_UNAVAILABLE,
            self::STATUS_ARCHIVED
        ]);
    }

    /**
     * Returns comma separated list of cache status being active (not archived)
     *
     * @return string cache status list, ready for use in SQL f.ex.
     */
    public static function CacheActiveStatusList()
    {
        return implode(', ', [
            self::STATUS_READY,
            self::STATUS_UNAVAILABLE
        ]);
    }

    public static function GetCacheUrlByWp($ocWaypoint)
    {
        return '/viewcache.php?wp=' . $ocWaypoint;
    }
}
