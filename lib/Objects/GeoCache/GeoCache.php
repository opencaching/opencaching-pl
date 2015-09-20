<?php

namespace lib\Objects\GeoCache;

use \lib\Objects\PowerTrail\PowerTrail;
use \lib\Objects\OcConfig\OcConfig;
use \lib\Database\DataBaseSingleton;

/**
 * Description of geoCache
 *
 * @author Łza
 */
class GeoCache
{
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

    private $id;
    private $geocacheWaypointId;
    private $cacheName;
    private $cacheType;
    private $datePlaced;

    private $sizeId;
    private $ratingId;
    private $status;

    private $recommendations;       //number of recom.
    private $founds;
    private $notFounds;
    private $notesCount;
    private $ratingVotes;
    private $willattends;           //for events only


    /* @var $owner \lib\Objects\User\User */
    private $owner;

    /* @var $altitude \lib\Objects\GeoCache\Altitude */
    private $altitude;

    /**
     * geocache coordinates object (instance of \lib\Objects\Coordinates\Coordinates class)
     * @var $coordinates \lib\Objects\Coordinates\Coordinates
     */
    private $coordinates;

    /**
     * Boolean. If set means that this cache belongs to any PT
     * @var $isPowerTrailPart;
     */
    private $isPowerTrailPart;

    /**
     * PT object
     * @var $powerTrail \lib\Objects\PowerTrail\PowerTrail
     */
    private $powerTrail;

    /**
     * Pointing if geocache is set as power trail final cache.
     * @var bool
     */
    private $isPowerTrailFinalGeocache = false;

    /**
     * @param array $params
     *            'cacheId' => (integer) database cache identifier
     *            'wpId' => (string) geoCache wayPoint (ex. OP21F4)
     */
    public function __construct(array $params = array())
    {
        if (isset($params['cacheId'])) { // load from DB if cachId param is set

            $db = DataBaseSingleton::Instance();
            $this->id = (int) $params['cacheId'];

            $queryById = "SELECT size, status, founds, notfounds, topratings, votes, notes,  name, type, date_hidden, longitude, latitude, wp_oc, user_id FROM `caches` WHERE `cache_id`=:1 LIMIT 1";
            $db->multiVariableQuery($queryById, $this->id);

            $cacheDbRow = $db->dbResultFetch();

            if(is_array($cacheDbRow))
                $this->loadFromRow($cacheDbRow);
            else{
                //TODO: cache-not-found handling?
            }
            $this->loadCacheLocation($db);

        } else
            if (isset($params['okapiRow'])) {
                $this->loadFromOkapiRow($params['okapiRow']);
            }
    }

    /**
     * Load cache data based on OKAPI response data
     *
     * @param array $okapiRow
     */
    public function loadFromOkapiRow($okapiRow)
    {
        foreach ($okapiRow as $field => $value) {
            switch ($field) {
                case 'code':
                    $this->geocacheWaypointId = $value;
                    break;
                case 'name':
                    $this->cacheName = $value;
                    break;
                case 'location':
                    $this->coordinates = new \lib\Objects\Coordinates\Coordinates(array(
                        'okapiRow' => $value
                    ));
                    break;
                case 'type':
                    $this->cacheType = self::CacheTypeIdFromOkapi($value);
                    break;
                case 'size2':
                    $this->sizeId = self::CacheSizeIdFromOkapi($value);
                    break;
                case 'rating':
                    $this->ratingId = $value;
                    break;
                case 'recommendations':
                    $this->recommendations = $value;
                    break;
                case 'founds':
                    $this->founds = $value;
                    break;
                case 'notfounds':
                    $this->notFounds = $value;
                    break;
                case 'willattends':
                    $this->willattends = $value;
                    break;
                case 'rating_votes':
                    $this->ratingVotes = $value;
                    break;
                case 'status':
                    $this->status = self::CacheStatusIdFromOkapi($value);
                    break;
                case 'owner':
                    $this->owner = new \lib\Objects\User\User(array(
                        'okapiRow' => $value
                    ));
                    break;
                case 'internal_id':
                    $this->id = $value;
                    break;
                default:
                    error_log("File:" . __METHOD__ . ": Unknown field: $field (value: $value)");
            }
        }
    }

    /**
     * Load object data based on DB data-row
     *
     * @param array $row
     */
    public function loadFromRow(array $cacheDbRow)
    {
        $this->cacheType = $cacheDbRow['type'];
        $this->cacheName = $cacheDbRow['name'];
        $this->geocacheWaypointId = $cacheDbRow['wp_oc'];
        $this->datePlaced = strtotime($cacheDbRow['date_hidden']);
        if(isset($cacheDbRow['cache_id'])){
            $this->id = (int) $cacheDbRow['cache_id'];
        }
        $this->sizeId = (int) $cacheDbRow['size'];
        $this->status = (int) $cacheDbRow['status'];
        $this->founds = (int) $cacheDbRow['founds'];
        $this->notFounds = (int) $cacheDbRow['notfounds'];
        $this->recommendations = (int) $cacheDbRow['topratings'];
        $this->ratingVotes = $cacheDbRow['votes'];
        $this->notesCount = (int) $cacheDbRow['notes'];
        $this->coordinates = new \lib\Objects\Coordinates\Coordinates(array(
            'dbRow' => $cacheDbRow
        ));
        $this->altitude = new \lib\Objects\GeoCache\Altitude($this);
        $this->owner = new \lib\Objects\User\User(array(
            'userId' => $cacheDbRow['user_id']
        ));
        return $this;
    }

    private function loadCacheLocation()
    {
        $db = DataBaseSingleton::Instance();
        $query = 'SELECT `code1`, `code2`, `code3`, `code4`  FROM `cache_location` WHERE `cache_id` =:1 LIMIT 1';
        $db->multiVariableQuery($query, $this->id);
        $dbResult = $db->dbResultFetch();
        $this->cacheLocation = $dbResult;
    }

    /**
     * Function to check if current cache is part of any PowerTrail.
     * On success PowerTrail object is created.
     *
     * @return boolean true if this cache belongs to any PowerTrail;
     */
    public function isPowerTrailPart()
    {
        if (! OcConfig::Instance()->getPowerTrailModuleSwitchOn()) {
            return false;
        }

        if (is_null($this->isPowerTrailPart)) {
            $ptArr = PowerTrail::CheckForPowerTrailByCache($this->id);
            if (count($ptArr) > 0) {
                // TODO: ASSUMPTION: cache belongs to ONLY one PT
                $this->isPowerTrailPart = true;
                $this->powerTrail = new PowerTrail(array(
                    'dbRow' => $ptArr[0]
                ));
            } else {
                $this->isPowerTrailPart = false;
            }
        }

        return $this->isPowerTrailPart;
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
     * Returns the cache size key based on size numeric identifier
     *
     * @param int $sizeId
     * @return string - size key for translation
     */
    public static function CacheSizeDescBySizeId($sizeId)
    {
        switch ($sizeId) {
            case self::SIZE_OTHER:
                return 'size_00';
            case self::SIZE_NANO:
                return 'size_01';
            case self::SIZE_MICRO:
                return 'size_02';
            case self::SIZE_SMALL:
                return 'size_03';
            case self::SIZE_REGULAR:
                return 'size_04';
            case self::SIZE_LARGE:
                return 'size_05';
            case self::SIZE_XLARGE:
                return 'size_06';
            case self::SIZE_NONE:
                return 'size_07';
            default:
                error_log(__METHOD__ . ' Unknown cache sizeId: ' . $sizeId);
                return 'size_04';
        }
    }

    /**
     * Returns cache reating description based on ratingId
     *
     * @param int $ratingId
     * @return string - rating description key for translation
     */
    public static function CacheRatingDescByRatingId($ratingId)
    {
        switch ($ratingId) {
            case 1:
                return 'rating_poor';
            case 2:
                return 'rating_mediocre';
            case 3:
                return 'rating_avarage';
            case 4:
                return 'rating_good';
            case 5:
                return 'rating_excellent';
        }
    }

    /**
     * Retrurn cache icon based on its type and status
     *
     * @param enum $type
     * @param enum $status
     * @return string - path + filename of the right icon
     */
    public static function CacheIconByType($type, $status)
    {
        $statusPart = "";
        switch ($status) {
            case self::STATUS_UNAVAILABLE:
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

        $typePart = "";
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

        return 'tpl/stdstyle/images/cache/' . $typePart . $statusPart . '.png';
    }

    public function getPowerTrail()
    {
        return $this->powerTrail;
    }

    public function getCacheType()
    {
        return $this->cacheType;
    }

    public function getCacheLocation()
    {
        return $this->cacheLocation;
    }

    public function getCacheName()
    {
        return $this->cacheName;
    }

    public function getDatePlaced()
    {
        return $this->datePlaced;
    }

    /**
     *
     * @return \lib\Objects\Coordinates\Coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     *
     * @return \lib\Objects\GeoCache\Altitude
     */
    public function getAltitude()
    {
        return $this->altitude;
    }

    public function getCacheId()
    {
        return $this->id;
    }

    public function getWaypointId()
    {
        return $this->geocacheWaypointId;
    }


    /**
     *
     * @return \lib\Objects\User\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function getRecommendations()
    {
        return $this->recommendations;
    }

    public function getCacheUrl()
    {
        return '/viewcache.php?wp=' . $this->getWaypointId();
    }

    public function getFounds()
    {
        return $this->founds;
    }

    public function getNotFounds()
    {
        return $this->notFounds;
    }

    public function getRatingVotes()
    {
        return $this->ratingVotes;
    }

    public function getWillattends()
    {
        return $this->willattends;
    }

    public function getRatingId()
    {
        return $this->ratingId;
    }

    public function getRatingDesc()
    {
        return self::CacheRatingDescByRatingId($this->ratingId);
    }

    public function getCacheIcon()
    {
        return self::CacheIconByType($this->cacheType, $this->status);
    }

    public function getSizeId()
    {
        return $this->sizeId;
    }

    public function getSizeDesc()
    {
        return self::CacheSizeDescBySizeId($this->sizeId);
    }

    /**
     * @param mixed $isPowerTrailPart
     * @return GeoCache
     */
    public function setIsPowerTrailPart($isPowerTrailPart)
    {
        $this->isPowerTrailPart = $isPowerTrailPart;
        return $this;
    }

    /**
     * @param PowerTrail $powerTrail
     */
    public function setPowerTrail(\lib\Objects\PowerTrail\PowerTrail $powerTrail)
    {
        $this->powerTrail = $powerTrail;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isIsPowerTrailFinalGeocache()
    {
        return $this->isPowerTrailFinalGeocache;
    }

    /**
     * @param boolean $isPowerTrailFinalGeocache
     * @return GeoCache
     */
    public function setIsPowerTrailFinalGeocache($isPowerTrailFinalGeocache)
    {
        $this->isPowerTrailFinalGeocache = $isPowerTrailFinalGeocache;
        return $this;
    }


}
