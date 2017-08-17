<?php
namespace lib\Objects\GeoCache;

use Utils\DateTime\Year;

/**
 * Common consts etc. for geocaches
 *
 */

class GeoCacheCommons{

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

    public static function CacheTypeTranslationKey($type){

        switch($type){
            case self::TYPE_TRADITIONAL:    return 'cacheType_1';
            case self::TYPE_OTHERTYPE:      return 'cacheType_5';
            case self::TYPE_MULTICACHE:     return 'cacheType_2';
            case self::TYPE_VIRTUAL:        return 'cacheType_8';
            case self::TYPE_WEBCAM:         return 'cacheType_7';
            case self::TYPE_EVENT:          return 'cacheType_6';
            case self::TYPE_QUIZ:           return 'cacheType_3';
            case self::TYPE_MOVING:         return 'cachetype_4';
            case self::TYPE_GEOPATHFINAL:   return 'cacheType_9';
            case self::TYPE_OWNCACHE:       return 'cacheType_10';
        }
    }

    public static function CacheStatusTranslationKey($type){

        switch($type){
            case self::STATUS_READY:            return 'cacheStatus_1';
            case self::STATUS_UNAVAILABLE:      return 'cacheStatus_2';
            case self::STATUS_ARCHIVED:         return 'cacheStatus_3';
            case self::STATUS_WAITAPPROVERS:    return 'cacheStatus_4';
            case self::STATUS_NOTYETAVAILABLE:  return 'cacheStatus_5';
            case self::STATUS_BLOCKED:          return 'cacheStatus_6';

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
            case self::SIZE_OTHER:  return 'cacheSize_other';
            case self::SIZE_NANO:   return 'cacheSize_nano';
            case self::SIZE_MICRO:  return 'cacheSize_micro';
            case self::SIZE_SMALL:  return 'cacheSize_small';
            case self::SIZE_REGULAR:return 'cacheSize_regular';
            case self::SIZE_LARGE:  return 'cacheSize_large';
            case self::SIZE_XLARGE: return 'cacheSize_xLarge';
            case self::SIZE_NONE:   return 'cacheSize_none';
            default:
                error_log(__METHOD__ . ' Unknown cache sizeId: ' . $sizeId);
                return 'size_04';
        }
    }

    public static function CacheSizesArray()
    {
        return array(
            self::SIZE_NONE,
            //self::SIZE_NANO,
            self::SIZE_MICRO,
            self::SIZE_SMALL,
            self::SIZE_REGULAR,
            self::SIZE_LARGE,
            self::SIZE_XLARGE,
            //self::SIZE_OTHER
        );
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
                error_log(__METHOD__ . ' Unknown cache type from OKAPI: ' . $okapiType);
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
                error_log(__METHOD__ . ' Unknown cache size from OKAPI: ' . $okapiSize);
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
                error_log(__METHOD__ . ' Unknown cache status from OKAPI: ' . $okapiStatus);
                return self::STATUS_READY;
        }
    }


    /**
     * Retrurn cache icon based on its type and status
     *
     * @param enum $type
     * @param enum $status
     * @return string - path + filename of the right icon
     */
    public static function CacheIconByType($type, $status, $logStatus = null)
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
        switch($logStatus){
            case GeoCacheLog::LOGTYPE_FOUNDIT:
                $logStatusPart = '-found';
                break;
            case GeoCacheLog::LOGTYPE_DIDNOTFIND:
                $logStatusPart = '-dnf';
                break;
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

        return 'tpl/stdstyle/images/cache/' . $typePart . $statusPart . $logStatusPart . '.png';
    }

    /**
     * Note:
     * - Score is stored in OC db and has value in range <-3;3>
     * - RatingId is counted by OKAPI and has value in range <1;5>
     * Do not confuse them with each other!
     *
     * @param unknown $score
     * @return number
     */
    public static function ScoreAsRatingNum($score)
    {
        // former score2ratingnum

        if ($score >= 2.2) return 5;
        if ($score >= 1.4) return 4;
        if ($score >= 0.1) return 3;
        if ($score >= -1.0) return 2;
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
    public static function ScoreNameTranslation($score){

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
        if (!Year::isPrimaAprilisToday()) {
            switch($ratingId){
                case 1: return 'rating_poor';
                case 2: return 'rating_mediocre';
                case 3: return 'rating_avarage';
                case 4: return 'rating_good';
                case 5: return 'rating_excellent';
            }
        } else {
            switch($ratingId){
                case 1: return 'rating_poor_1A';
                case 2: return 'rating_mediocre_1A';
                case 3: return 'rating_avarage_1A';
                case 4: return 'rating_good_1A';
                case 5: return 'rating_excellent_1A';
            }
        }
    }


    /**
     * This function provides abbreviation for cache type
     * @param unknown $type
     */
    public static function Type2Letter($type){
        $type = (int) $type;
        switch ($type) {
            case self::TYPE_OTHERTYPE:
            default:
                return "u";
            case self::TYPE_TRADITIONAL:
                return "t";
            case self::TYPE_MULTICACHE:
                return "m";
            case self::TYPE_VIRTUAL:
                return "v";
            case self::TYPE_WEBCAM:
                return "w";
            case self::TYPE_EVENT:
                return "e";
            case self::TYPE_QUIZ:
                return "q";
            case self::TYPE_MOVING:
                return "m";
        }
    }

    public static function GetCacheUrlByWp($ocWaypoint){
        return '/viewcache.php?wp=' . $ocWaypoint;
    }
}

