<?php

namespace lib\Objects\GeoCache;

use \lib\Objects\PowerTrail\PowerTrail;
use \lib\Objects\OcConfig\OcConfig;
use \lib\Database\DataBaseSingleton;
use Utils\Database\XDb;
//use \lib\Objects\GeoCache\CacheTitled;
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
    private $otherWaypointIds = array(
        'gc' => null,
        'ge' => null,
        'nc' => null,
        'tc' => null,
        'tc' => null,
    );
    private $cacheName;
    private $cacheType;

    /* @var $datePlaced \DateTime */
    private $datePlaced;

    /* @var $datePlaced \DateTime */
    private $dateCreated;

    /* @var $dateActivate \DateTime */
    private $dateActivate;

    private $sizeId;
    private $ratingId;
    private $status;
    private $searchTime;
    private $recommendations;       //number of recom.
    private $founds;
    private $notFounds;
    private $notesCount;
    private $lastFound;
    private $score;
    private $ratingVotes;
    private $willattends;           //for events only
    private $natureRegions = false;
    private $natura2000Sites = false;
    private $usersRecomeded = false;
    private $wayLenght;
    private $difficulty;
    private $terrain;
    private $logPassword = false;
    private $watchingUsersCount;
    private $ignoringUsersCount;
    private $descLanguagesList;
    private $mp3count;
    private $picturesCount;

    /**
     * count of moves for mobile geocaches
     * @var integer
     */
    private $moveCount = -1;

    /**
     * mobile cache distance
     * @var integer
     */
    private $distance = -1;

    /* @var $owner \lib\Objects\User\User */
    private $founder;

    /* @var $dictionary \cache */
    public $dictionary;

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

    private $isTitled;

    /**
     * Pointing if geocache is set as power trail final cache.
     * @var bool
     */
    private $isPowerTrailFinalGeocache = false;

    /**
     *
     * @var arrayObject
     */
    private $waypoints;

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

            $queryById = "SELECT size, status, founds, notfounds, topratings, votes, notes, score,  name, type, date_hidden, longitude, latitude, wp_oc, wp_gc, wp_nc, wp_tc, wp_ge, user_id, last_found, difficulty, terrain, way_length, logpw, search_time, date_created, watcher, ignorer_count, org_user_id, desc_languages, mp3count, picturescount, date_activate FROM `caches` WHERE `cache_id`=:1 LIMIT 1";
            $db->multiVariableQuery($queryById, $this->id);

            $cacheDbRow = $db->dbResultFetch();
            if(is_array($cacheDbRow)) {
                $this->loadFromRow($cacheDbRow);
            } else {
                ddd('geocache not found in db? TODO: cache-not-found handling');
                //TODO: cache-not-found handling?
            }
            $this->loadCacheLocation($db);

        } else {
            if (isset($params['okapiRow'])) {
                $this->loadFromOkapiRow($params['okapiRow']);
            }
        }
        $this->dictionary = \cache::instance();
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
                case 'date_hidden':
                    $this->datePlaced = new \DateTime($value);
                    break;
                default:
                    error_log("File:" . __METHOD__ . ": Unknown field: $field (value: $value)");
            }
        }
    }

    /**
     * Load object data based on DB data-row
     *
     * @param array $geocacheDbRow
     */
    public function loadFromRow(array $geocacheDbRow)
    {
        $this->lastFound = $geocacheDbRow['last_found'];
        $this->cacheType = (int) $geocacheDbRow['type'];
        $this->cacheName = $geocacheDbRow['name'];
        $this->geocacheWaypointId = $geocacheDbRow['wp_oc'];
        $this->otherWaypointIds = array(
            'gc' => $geocacheDbRow['wp_gc'],
            'nc' => $geocacheDbRow['wp_nc'],
            'tc' => $geocacheDbRow['wp_tc'],
            'ge' => $geocacheDbRow['wp_ge'],
        );
        $this->datePlaced = new \DateTime($geocacheDbRow['date_hidden']);
        $this->dateCreated = new \DateTime($geocacheDbRow['date_created']);
        if(isset($geocacheDbRow['cache_id'])){
            $this->id = (int) $geocacheDbRow['cache_id'];
        }
        $this->sizeId = (int) $geocacheDbRow['size'];
        $this->status = (int) $geocacheDbRow['status'];
        $this->founds = (int) $geocacheDbRow['founds'];
        $this->notFounds = (int) $geocacheDbRow['notfounds'];
        $this->recommendations = (int) $geocacheDbRow['topratings'];
        $this->difficulty = $geocacheDbRow['difficulty'];
        $this->terrain = $geocacheDbRow['terrain'];
        $this->logPassword = $geocacheDbRow['logpw'] != '' ? $geocacheDbRow['logpw'] : false;
        $this->ratingVotes = $geocacheDbRow['votes'];
        $this->notesCount = (int) $geocacheDbRow['notes'];
        $this->wayLenght = $geocacheDbRow['way_length'];
        $this->searchTime = $geocacheDbRow['search_time'];
        $this->searchTime = $geocacheDbRow['search_time'];
        $this->watchingUsersCount = (int) $geocacheDbRow['watcher'];
        $this->ignoringUsersCount = (int) $geocacheDbRow['ignorer_count'];
        $this->descLanguagesList = $geocacheDbRow['desc_languages'];
        $this->mp3count = (int) $geocacheDbRow['mp3count'];
        $this->picturesCount = (int) $geocacheDbRow['picturescount'];
        $this->coordinates = new \lib\Objects\Coordinates\Coordinates(array(
            'dbRow' => $geocacheDbRow
        ));
        $this->altitude = new \lib\Objects\GeoCache\Altitude($this);
        $this->owner = new \lib\Objects\User\User(array('userId' => $geocacheDbRow['user_id']));
        if($geocacheDbRow['org_user_id'] != ''){
            $this->founder = new \lib\Objects\User\User(array('userId' => $geocacheDbRow['org_user_id']));
        }
        $this->score = $geocacheDbRow['score'];
        $this->setDateActivate($geocacheDbRow['date_activate']);
        return $this;
    }

    private function loadCacheLocation()
    {
        $db = DataBaseSingleton::Instance();
        $query = 'SELECT `code1`, `code2`, `code3`, `code4`, `adm1`, `adm2`, `adm3`, `adm4`  FROM `cache_location` WHERE `cache_id` =:1 LIMIT 1';
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
        if (! OcConfig::instance()->getPowerTrailModuleSwitchOn()) {
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

    /**
     * perform update on specified elements only.
     * @param array $elementsToUpdate
     */
    public function updateGeocacheLogenteriesStats()
    {
        $sqlQuery = "UPDATE `caches` SET `last_found`=:1, `founds`=:2, `notfounds`= :3, `notes`= :4 WHERE `cache_id`= :5";
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery($sqlQuery, $this->lastFound, $this->founds, $this->notFounds, $this->notesCount, $this->id);
        $db->reset();
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

    /**
     *
     * @return \DateTime
     */
    public function getDatePlaced()
    {
        return $this->datePlaced;
    }

    /**
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
    public function getFounder()
    {
        return $this->founder;
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

    public function setFounds($founds)
    {
        $this->founds = $founds;
        return $this;
    }

    public function setNotFounds($notFounds)
    {
        $this->notFounds = $notFounds;
        return $this;
    }

    public function setNotesCount($notesCount)
    {
        $this->notesCount = $notesCount;
        return $this;
    }

    public function getNotesCount()
    {
        return $this->notesCount;
    }

    public function getLastFound()
    {
        return $this->lastFound;
    }

    public function setLastFound($lastFound)
    {
        $this->lastFound = $lastFound;
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

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    public function isTitled()
    {
        if (is_null($this->isTitled)) {
            $this->isTitled = CacheTitled::isTitled($this->id) > 0 ? true : false;
        }
        return $this->isTitled;
    }

    public function getGeocacheWaypointId()
    {
        return $this->geocacheWaypointId;
    }

    // Parki Narodowe , Krajobrazowe
    public function getNatureRegions()
    {
        if($this->natureRegions === false){
            $db = DataBaseSingleton::Instance();
            $rsAreasql = "SELECT `parkipl`.`id` AS `npaId`, `parkipl`.`name` AS `npaname`,`parkipl`.`link` AS `npalink`,`parkipl`.`logo` AS `npalogo` FROM `cache_npa_areas` INNER JOIN `parkipl` ON `cache_npa_areas`.`parki_id`=`parkipl`.`id` WHERE `cache_npa_areas`.`cache_id`=:1 AND `cache_npa_areas`.`parki_id`!='0'";
            $db->multiVariableQuery($rsAreasql, $this->id);
            $this->natureRegions = $db->dbResultFetchAll();
        }
        return $this->natureRegions;
    }

    public function getNatura2000Sites()
    {
        if($this->natura2000Sites === false){
            $db = DataBaseSingleton::Instance();
            $sql = "SELECT `npa_areas`.`id` AS `npaId`, `npa_areas`.`linkid` AS `linkid`,`npa_areas`.`sitename` AS `npaSitename`, `npa_areas`.`sitecode` AS `npaSitecode`, `npa_areas`.`sitetype` AS `npaSitetype`
                    FROM `cache_npa_areas` INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id`
                    WHERE `cache_npa_areas`.`cache_id`=:1 AND `cache_npa_areas`.`npa_id`!='0'";
            $db->multiVariableQuery($sql, $this->id);
            $this->natura2000Sites = $db->dbResultFetchAll();
        }
        return $this->natura2000Sites;
    }

    public function getUsersRecomeded()
    {
        if($this->usersRecomeded === false) {
            $db  = DataBaseSingleton::Instance();
            $sql = "SELECT user.username username FROM `cache_rating` INNER JOIN user ON (cache_rating.user_id = user.user_id) WHERE cache_id=:1 ORDER BY username";
            $db->multiVariableQuery($sql, $this->id);
            $this->usersRecomeded = $db->dbResultFetchAll();
        }
        return $this->usersRecomeded;
    }

    public function getDifficulty()
    {
        return $this->difficulty;
    }

    public function getTerrain()
    {
        return $this->terrain;
    }

    public function getWayLenght()
    {
        return $this->wayLenght;
    }

    public function getSearchTime()
    {
        return $this->searchTime;
    }

    public function getOtherWaypointIds()
    {
        return $this->otherWaypointIds;
    }

    /**
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getLogPassword()
    {
        return $this->logPassword;
    }

    /**
     * if geocache require password for log entery, return true. otherwse false.
     * @return boolean
     */
    public function hasPassword()
    {
        if($this->logPassword === false){
            return false;
        } else {
            return true;
        }

    }

    public function getWatchingUsersCount()
    {
        return $this->watchingUsersCount;
    }

    public function getIgnoringUsersCount()
    {
        return $this->ignoringUsersCount;
    }

    public function getDescLanguagesList()
    {
        return $this->descLanguagesList;
    }
    public function getMp3count()
    {
        return $this->mp3count;
    }

    public function getPicturesCount()
    {
        return $this->picturesCount;
    }

    public function getMoveCount()
    {
        if($this->cacheType === self::TYPE_MOVING && $this->moveCount === -1){
            $db  = DataBaseSingleton::Instance();
            $sql = 'SELECT COUNT(*) FROM `cache_logs` WHERE type=4 AND cache_logs.deleted="0" AND cache_id=:1';
            $this->moveCount = $db->multiVariableQueryValue($sql, 0, $this->id);
        }
        return $this->moveCount;
    }

    /**
     * get mobile cache distnace.
     * (calculate mobile cache distance if were not counted before)
     * @return float
     */
    public function getDistance()
    {
        if($this->distance === -1){
            $db  = DataBaseSingleton::Instance();
            $sql = 'SELECT sum(km) AS dystans FROM cache_moved WHERE cache_id=:1';
            $db->multiVariableQuery($sql, $this->id);
            $dst = $db->dbResultFetchOneRowOnly();
            $this->distance = round($dst['dystans'], 2);
        }
        return $this->distance;
    }

    public function getWaypoints()
    {
        if($this->waypoints === null){
            $this->waypoints = new \ArrayObject();
            \lib\Controllers\GeocacheController::buildWaypointsForGeocache($this);
        }
        return $this->waypoints;
    }

    /**
     * @return \DateTime
     */
    public function getDateActivate()
    {
        return $this->dateActivate;
    }

    private function setDateActivate($dateActivate)
    {
        if($dateActivate != null){
            $this->dateActivate = new \DateTime($dateActivate);
        }
    }

    public function getStatusTranslationIdentifier()
    {
        $statuses = $this->dictionary->getCacheStatuses();
        return $statuses[$this->status]['translation'];
    }

    /**
     * This function is moved from clicompatbase
     * @param unknown $cacheid
     */
    public static function setCacheDefaultDescLang($cacheid){

        $r['desc_languages'] = XDb::xSimpleQueryValue(
            "SELECT `desc_languages` FROM `caches`
            WHERE `cache_id`= ? LIMIT 1", null, $cacheid);

        if (mb_strpos($r['desc_languages'], 'PL') !== false)
            $desclang = 'PL';
        else if (mb_strpos($r['desc_languages'], 'EN') !== false)
            $desclang = 'EN';
        else if ($r['desc_languages'] == '')
            $desclang = '';
        else
            $desclang = mb_substr($r['desc_languages'], 0, 2);

        XDb::xSql(
            "UPDATE `caches` SET
                `default_desclang`= ?, `last_modified`=NOW()
            WHERE cache_id= ? LIMIT 1",
            $desclang, $cacheid);
    }

}

