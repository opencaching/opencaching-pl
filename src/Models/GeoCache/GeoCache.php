<?php
namespace src\Models\GeoCache;

use DateTime;
use Exception;
use src\Utils\Database\XDb;
use src\Controllers\PictureController;
use src\Models\Coordinates\Coordinates;
use src\Models\OcConfig\OcConfig;
use src\Models\PowerTrail\PowerTrail;
use src\Models\User\MultiUserQueries;
use src\Models\User\User;
use src\Utils\EventHandler\EventHandler;
use src\Utils\I18n\I18n;
use src\Utils\Uri\SimpleRouter;
use src\Models\User\UserWatchedCache;
use src\Utils\Debug\Debug;
use src\Models\Pictures\Thumbnail;
use stdClass;

/**
 * Description of geoCache
 */
class GeoCache extends GeoCacheCommons
{
    private $id;
    private $geocacheWaypointId;
    private $otherWaypointIds = array(
        'gc' => null,
        'ge' => null,
        'nc' => null,
        'tc' => null
    );
    private $cacheName;
    private $cacheType;

    /** @var $datePlaced DateTime */
    private $datePlaced;

    /** @var $datePlaced DateTime */
    private $dateCreated;

    /** @var $dateActivate DateTime */
    private $dateActivate;

    /** @var DateTime {@see GeoCache::setDatePublished()} */
    private $datePublished;

    private $sizeId;
    private $ratingId;              //OKAPI rating calculated from score
    private $status;
    /** @var string */
    private $country;
    private $searchTime;
    private $recommendations;       //number of recom.
    private $founds;
    private $notFounds;
    private $notesCount;
    private $lastFound;
    private $score;
    private $ratingVotes;
    private $willAttends;           //for events only
    private $natureRegions = false;
    private $natura2000Sites = false;
    private $usersRecommended = false;
    private $wayLength;
    private $difficulty;
    private $terrain;
    private $logPassword = false;
    private $watchingUsersCount;
    private $descLanguagesList;
    private $mp3count;
    private $picturesCount;
    private $uuid;

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

    private $ownerId;

    /** @var $owner User */
    private $owner = null;


    /** @var $founder User */
    private $founder = null;

    private $founderId = null;

    /** @var CacheAdditions */
    private $cacheAddtitions = null;

    /**
     * geocache coordinates object (instance of Coordinates class)
     * @var $coordinates Coordinates
     */
    private $coordinates;

    /**
     * Boolean. If set means that this cache belongs to any PT
     * @var $isPowerTrailPart;
     */
    private $isPowerTrailPart;

    /**
     * PT object
     * @var $powerTrail PowerTrail
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
     * @var $cacheLocationObj CacheLocation
     */
    private $cacheLocationObj = null;

    /**
     * List of cache attributes
     */
    private $cacheAttributesList = null;

    /**
     * List of hosted geoKrets
     * @var array
     */
    private $hostedGeokrets = false;

    /**
     * List of mp3 records assigned to this cache
     * @var array
     */
    private $mp3List = null;

    /**
     * List of pictures assigned to this cache
     */
    private $picturesList = null;

    private $picsInLogsCount = null;

    /**
     * Last active (not deleted) log for cache
     * @var GeoCacheLog|null
     */
    private $lastLog = null;

    /**
     * @param array $params
     *            'cacheId' => (integer) database cache identifier
     *            'wpId' => (string) geoCache wayPoint (ex. OP21F4)
     * @throws Exception
     */
    public function __construct(array $params = array())
    {
        parent::__construct();

        if (isset($params['cacheId'])) { // load from DB if cachId param is set
            $this->loadByCacheId($params['cacheId']);

        } elseif (isset($params['okapiRow'])) {
            $this->loadFromOkapiRow($params['okapiRow']);

        } elseif (isset($params['cacheWp'])) {
            $this->loadByWp($params['cacheWp']);

        } elseif (isset($params['cacheUUID'])) {
            $this->loadByUUID($params['cacheUUID']);
        }
    }

    /**
     * Factory
     * @param int $cacheId
     * @return GeoCache|null (null if no such geocache)
     */
    public static function fromCacheIdFactory($cacheId)
    {
        try {
            return new self( array('cacheId' => $cacheId) );
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Factory - creates Geocache object based on waypoint aka OC2345
     * @param string $wp
     * @return GeoCache|null object or null if no such geocache
     */
    public static function fromWayPointFactory($wp)
    {
        try {
            return new self( array('cacheWp' => $wp) );
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Factory - creates Geocache object based on geocache UUID
     * @param string $uuid
     * @return GeoCache|null object or null if no such geocache
     */
    public static function fromUUIDFactory($uuid)
    {
        try {
            return new self( array('cacheUUID' => $uuid) );
        } catch (Exception $e) {
            return null;
        }
    }

    private function loadByWp($wp)
    {
        $wpColumn = self::getWpColumnName($wp);

        $stmt = $this->db->multiVariableQuery("SELECT * FROM caches WHERE $wpColumn = :1 LIMIT 1", $wp);
        $cacheDbRow = $this->db->dbResultFetch($stmt);

        if (is_array($cacheDbRow)) {
            $this->loadFromRow($cacheDbRow);
        } else {
            throw new Exception("Cache not found");
        }

    }

    private static function getWpColumnName($wp)
    {
        switch (mb_strtoupper(mb_substr($wp, 0, 2))) {
            case 'GC': return 'wp_gc';
            case 'NC': return 'wp_nc';
            case 'TC': return 'wp_tc';
            case 'GE': return 'wp_ge';
            case 'QC': return 'wp_qc';  // obsolete
            default: return 'wp_oc';
        }
    }

    /**
     * @param int $cacheId
     * @throws Exception
     */
    private function loadByCacheId($cacheId)
    {
        //find cache by Id
        $s = $this->db->multiVariableQuery(
            "SELECT * FROM caches WHERE cache_id = :1 LIMIT 1", $cacheId);

        $cacheDbRow = $this->db->dbResultFetch($s);

        if (is_array($cacheDbRow)) {
            $this->loadFromRow($cacheDbRow);
        } else {
            throw new Exception("Cache not found");
        }
    }

    /**
     * @param string $uuid
     * @throws Exception
     */
    private function loadByUUID($uuid)
    {
        //find cache by UUID
        $s = $this->db->multiVariableQuery(
            "SELECT * FROM caches WHERE uuid = :1 LIMIT 1", $uuid);

        $cacheDbRow = $this->db->dbResultFetch($s);

        if (is_array($cacheDbRow)) {
            $this->loadFromRow($cacheDbRow);
        } else {
            throw new Exception("Cache not found");
        }
    }

    /**
     * Load cache data based on OKAPI response data
     *
     * @param array $okapiRow
     * @throws Exception
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
                    $this->coordinates = new Coordinates(array(
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
                    $this->willAttends = $value;
                    break;
                case 'rating_votes':
                    $this->ratingVotes = $value;
                    break;
                case 'status':
                    $this->status = self::CacheStatusIdFromOkapi($value);
                    break;
                case 'owner':
                    $this->owner = new User(array(
                        'okapiRow' => $value
                    ));
                    break;
                case 'internal_id':
                    $this->id = $value;
                    break;
                case 'date_hidden':
                    $this->datePlaced = new DateTime($value);
                    break;
                default:
                    Debug::errorLog("Unknown field: $field (value: $value)");
            }
        }
    }

    /**
     * Load object data based on DB data-row
     *
     * @param array $geocacheDbRow
     * @return GeoCache
     * @throws Exception
     */
    public function loadFromRow(array $geocacheDbRow)
    {
        $this->id = $geocacheDbRow['cache_id'];
        $this->lastFound = $geocacheDbRow['last_found'];
        $this->cacheType = (int) $geocacheDbRow['type'];
        $this->country = $geocacheDbRow['country'];
        $this->cacheName = $geocacheDbRow['name'];
        $this->geocacheWaypointId = $geocacheDbRow['wp_oc'];
        $this->otherWaypointIds = array(
            'gc' => $geocacheDbRow['wp_gc'],
            'nc' => $geocacheDbRow['wp_nc'],
            'tc' => $geocacheDbRow['wp_tc'],
            'ge' => $geocacheDbRow['wp_ge'],
            'qc' => $geocacheDbRow['wp_qc'],
        );
        $this->datePlaced = new DateTime($geocacheDbRow['date_hidden']);
        $this->dateCreated = new DateTime($geocacheDbRow['date_created']);
        if (isset($geocacheDbRow['cache_id'])) {
            $this->id = (int) $geocacheDbRow['cache_id'];
        }
        $this->sizeId = (int) $geocacheDbRow['size'];
        $this->status = (int) $geocacheDbRow['status'];
        $this->founds = (int) $geocacheDbRow['founds'];
        $this->notFounds = (int) $geocacheDbRow['notfounds'];
        $this->recommendations = (int) $geocacheDbRow['topratings'];
        $this->difficulty = $geocacheDbRow['difficulty'];
        $this->terrain = $geocacheDbRow['terrain'];
        $this->logPassword = !empty($geocacheDbRow['logpw']) ? $geocacheDbRow['logpw'] : false;
        $this->ratingVotes = $geocacheDbRow['votes'];
        $this->notesCount = (int) $geocacheDbRow['notes'];
        $this->wayLength = $geocacheDbRow['way_length'];
        $this->searchTime = $geocacheDbRow['search_time'];
        $this->searchTime = $geocacheDbRow['search_time'];
        $this->watchingUsersCount = (int) $geocacheDbRow['watcher'];
        $this->descLanguagesList = $geocacheDbRow['desc_languages'];
        $this->mp3count = (int) $geocacheDbRow['mp3count'];
        $this->picturesCount = (int) $geocacheDbRow['picturescount'];
        $this->coordinates = new Coordinates(array(
            'dbRow' => $geocacheDbRow
        ));

        $this->ownerId = (int) $geocacheDbRow['user_id'];
        $this->owner = null; //reset owner data

        $this->founder = null;
        if (!empty($geocacheDbRow['org_user_id'])) {
            $this->founderId = $geocacheDbRow['org_user_id'];
        }

        $this->score = $geocacheDbRow['score'];

        $this->ratingId = self::ScoreAsRatingNum($this->score); //rating is returned by OKAPI only-

        $this->setDateActivate($geocacheDbRow['date_activate']);
        $this->setDatePublished($geocacheDbRow['date_published']);
        $this->uuid = $geocacheDbRow['uuid'];
        return $this;
    }

    /**
     * Function to check if current cache is part of any PowerTrail.
     * On success PowerTrail object is created.
     *
     * @param bool $includeHiddenGeoPath
     * @return bool true if this cache belongs to any PowerTrail;
     */
    public function isPowerTrailPart($includeHiddenGeoPath = false)
    {
        if (! OcConfig::instance()->isPowerTrailModuleSwitchOn()) {
            return false;
        }

        if (is_null($this->isPowerTrailPart)) {
            $ptArr = PowerTrail::CheckForPowerTrailByCache($this->id, $includeHiddenGeoPath);
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
     * Load CacheAddition object for this cache
     */
    private function loadCacheAdditions()
    {
        $this->cacheAddtitions = new CacheAdditions($this->getCacheId());
    }

    /**
     * Recalculates last_found, founds, notfounds and notes for cache
     * Saves recalculated values into the DB
     */
    public function recalculateCacheStats()
    {
        if ($this->isEvent()) {
            $founds = $this->db->multiVariableQueryValue(
                'SELECT count(*)
                FROM `cache_logs`
                WHERE `cache_id` = :1 AND TYPE = :2 AND `deleted` = 0',
                0, $this->getCacheId(), GeoCacheLog::LOGTYPE_ATTENDED);
            $notFounds = $this->db->multiVariableQueryValue(
                'SELECT count(*)
                FROM `cache_logs`
                WHERE `cache_id` = :1 AND TYPE = :2 AND `deleted` = 0',
                0, $this->getCacheId(), GeoCacheLog::LOGTYPE_WILLATTENDED);
        } else {
            $founds = $this->db->multiVariableQueryValue(
                'SELECT count(*)
                FROM `cache_logs`
                WHERE `cache_id` = :1 AND TYPE = :2 AND `deleted` = 0',
                0, $this->getCacheId(), GeoCacheLog::LOGTYPE_FOUNDIT);
            $notFounds = $this->db->multiVariableQueryValue(
                'SELECT count(*)
                FROM `cache_logs`
                WHERE `cache_id` = :1 AND TYPE = :2 AND `deleted` = 0',
                0, $this->getCacheId(), GeoCacheLog::LOGTYPE_DIDNOTFIND);
        }
        $notes = $this->db->multiVariableQueryValue(
            'SELECT count(*)
                FROM `cache_logs`
                WHERE `cache_id` = :1 AND TYPE = :2 AND `deleted` = 0',
            0, $this->getCacheId(), GeoCacheLog::LOGTYPE_COMMENT);
        $lastFound = $this->db->multiVariableQueryValue(
            'SELECT MAX(`cache_logs`.`date`)
            FROM `cache_logs`
            WHERE `cache_logs`.`cache_id`= :1
                AND `cache_logs`.`type`IN (:2, :3)
                AND `cache_logs`.`deleted` = 0',
            null, $this->getCacheId(), GeoCacheLog::LOGTYPE_FOUNDIT, GeoCacheLog::LOGTYPE_ATTENDED);

        $this->setFounds($founds)
            ->setNotFounds($notFounds)
            ->setNotesCount($notes)
            ->setLastFound($lastFound);

        $this->db->multiVariableQuery(
            'UPDATE `caches`
            SET `last_found` = :1,
            `founds` = :2,
            `notfounds`= :3,
            `notes`= :4
            WHERE `cache_id`= :5',
            $this->getLastFound(), $this->getFounds(), $this->getNotFounds(),
            $this->getNotesCount(), $this->getCacheId());
    }

    public function getPowerTrail()
    {
        if($this->isPowerTrailPart()){
            return $this->powerTrail;
        } else {
            return null;
        }
    }

    public function getCacheType()
    {
        return $this->cacheType;
    }

    /**
     * Returns name of the cache type used in translation files
     *
     */
    public function getCacheTypeTranslationKey()
    {
        return self::CacheTypeTranslationKey($this->getCacheType());
    }

    public function isEvent()
    {
        return $this->cacheType == self::TYPE_EVENT;
    }

    public function isMovable()
    {
        return $this->cacheType == self::TYPE_MOVING || $this->cacheType == self::TYPE_OWNCACHE;
    }

    public function isOpenCheckerApplicable()
    {
        return $this->cacheType == self::TYPE_MOVING ||
            self::TYPE_QUIZ || self::TYPE_OTHERTYPE || self::TYPE_MULTICACHE;
    }

    public function getCacheLocationObj()
    {
        if (!$this->cacheLocationObj) {
            // load location data
            $this->cacheLocationObj = new CacheLocation($this->id);
        }

        return $this->cacheLocationObj;
    }

    public function getCacheName()
    {
        return $this->cacheName;
    }

    /**
     *
     * @return DateTime
     */
    public function getDatePlaced()
    {
        return $this->datePlaced;
    }

    /**
     * @return Coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @return int altitude of the cache
     */
    public function getAltitude()
    {
        if (!$this->cacheAddtitions) {
            $this->loadCacheAdditions();
        }

        return $this->cacheAddtitions->getAltitude();
    }

    public function getCacheId()
    {
        return $this->id;
    }

    /**
     * Return OC waypoint code like OC1234
     * @return string
     */
    public function getWaypointId()
    {
        return $this->geocacheWaypointId;
    }

    /**
     * Gets userId of the first author of the cache (for caches after adoption)
     * @return integer
     */
    public function getFounderId()
    {
        return $this->founderId;
    }

    /**
     * Gets User obj of the first author of the cache (for caches after adoption)
     * @return User
     */
    public function getFounder()
    {
        if ( !is_null($this->getFounderId()) && is_null($this->founder) ) {
            $this->founder = User::fromUserIdFactory( $this->getFounderId() );
        }

        return $this->founder;
    }

    /**
     * Returns true if cache is adopted and founder is not a current owner
     *
     * @return boolean
     */
    public function isAdopted()
    {
        return $this->founderId && $this->founderId != $this->ownerId;
    }

    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     *
     * @return User
     */
    public function getOwner()
    {
        if ( is_null($this->owner) )
            $this->owner = User::fromUserIdFactory( $this->getOwnerId() );

        return $this->owner;
    }

    public function getRecommendations()
    {
        return $this->recommendations;
    }

    public function getCacheUrl()
    {
        return self::GetCacheUrlByWp($this->getWaypointId());
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

    public function getWillAttends()
    {
        return $this->willAttends;
    }

    public function getRatingId()
    {
        return $this->ratingId;
    }

    public function getRatingDesc()
    {
        return tr(self::CacheRatingTranslationKey($this->ratingId));
    }

    public function getCacheIcon(User $forUser=null)
    {
        $logStatus = null;
        $isOwner = false;
        if (!is_null($forUser)) {
            $logsCount = $this->getLogsCountByType($forUser, array(GeoCacheLog::LOGTYPE_FOUNDIT, GeoCacheLog::LOGTYPE_DIDNOTFIND));
            if (isset($logsCount[GeoCacheLog::LOGTYPE_FOUNDIT]) && $logsCount[GeoCacheLog::LOGTYPE_FOUNDIT]>0) {
                $logStatus = GeoCacheLog::LOGTYPE_FOUNDIT;
            } else if (isset($logsCount[GeoCacheLog::LOGTYPE_DIDNOTFIND]) && $logsCount[GeoCacheLog::LOGTYPE_DIDNOTFIND]>0) {
                $logStatus = GeoCacheLog::LOGTYPE_DIDNOTFIND;
            }
            $isOwner = ($this->getOwnerId() == $forUser->getUserId());
        }

        return self::CacheIconByType(
            $this->cacheType,
            $this->status,
            $logStatus,
            false,
            $isOwner
        );
    }

    public function getSizeId()
    {
        return $this->sizeId;
    }

    public function getSizeTranslationKey()
    {
        return self::CacheSizeTranslationKey($this->sizeId);
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
     * @return GeoCache
     */
    public function setPowerTrail(PowerTrail $powerTrail)
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

    public function isStatusReady()
    {
        return $this->status == self::STATUS_READY;
    }

    public function getStatusTranslationKey()
    {
        return self::CacheStatusTranslationKey($this->status);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    public function getScoreNameTranslation()
    {
        return self::ScoreNameTranslation($this->score);
    }

    public function getScoreAsRatingNum()
    {
        return self::ScoreAsRatingNum($this->score);
    }

    public function isTitled()
    {
        if (is_null($this->isTitled)) {
            $this->isTitled = CacheTitled::isTitled($this->id);
        }
        return $this->isTitled;
    }

    public function getGeocacheWaypointId()
    {
        return $this->geocacheWaypointId;
    }

    // Parki Narodowe, Krajobrazowe
    public function getNatureRegions()
    {
        if ($this->natureRegions === false) {
            $s = $this->db->multiVariableQuery(
                "SELECT `parkipl`.`name` AS `npaname`,`parkipl`.`link` AS `npalink`,`parkipl`.`logo` AS `npalogo`
                 FROM `cache_npa_areas` INNER JOIN `parkipl` ON `cache_npa_areas`.`parki_id`=`parkipl`.`id`
                 WHERE `cache_npa_areas`.`cache_id`=:1 AND `cache_npa_areas`.`parki_id`!='0'", $this->id);

            $this->natureRegions = $this->db->dbResultFetchAll($s);
        }
        return $this->natureRegions;
    }

    public function getNatura2000Sites()
    {
        if ($this->natura2000Sites === false) {
            $sql = "SELECT `npa_areas`.`id` AS `npaId`, `npa_areas`.`linkid` AS `linkid`,`npa_areas`.`sitename` AS `npaSitename`, `npa_areas`.`sitecode` AS `npaSitecode`, `npa_areas`.`sitetype` AS `npaSitetype`
                    FROM `cache_npa_areas` INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id`
                    WHERE `cache_npa_areas`.`cache_id`=:1 AND `cache_npa_areas`.`npa_id`!='0'";
            $s = $this->db->multiVariableQuery($sql, $this->id);
            $this->natura2000Sites = $this->db->dbResultFetchAll($s);
        }
        return $this->natura2000Sites;
    }

    public function getUsersRecomeded()
    {
        if ($this->usersRecommended === false) {
            $s = $this->db->multiVariableQuery(
                "SELECT user.username AS username
                 FROM `cache_rating` INNER JOIN user ON (cache_rating.user_id = user.user_id)
                 WHERE cache_id=:1 ORDER BY username", $this->id);

            $usersArr = [];
            foreach ($this->db->dbResultFetchAll($s) as $row) {
                $usersArr[] = $row['username'];
            }
            $this->usersRecommended = implode($usersArr, ', ');
        }
        return $this->usersRecommended;
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
        return $this->wayLength;
    }

    public function getWayLenghtFormattedString() {
        return sprintf('%01.2f km', $this->getWayLenght());
    }

    public function getSearchTime()
    {
        return $this->searchTime;
    }

    public function getSearchTimeFormattedString()
    {
        $hours = floor($this->getSearchTime());
        $min = ( $this->getSearchTime() - $hours ) * 60;
        $min = sprintf('%02d', round($min, 1));
        return $hours . ':' . $min . ' h';
    }


    public function getOtherWaypointIds()
    {
        return $this->otherWaypointIds;
    }

    public function getFullOtherWaypointsList()
    {
        global $config;

        $result = [];

        if ( !empty($this->otherWaypointIds['ge']) && $config['otherSites_gpsgames_org'] == 1 ) {
            $otherSite = new stdClass();
            $otherSite->link = 'http://geocaching.gpsgames.org/cgi-bin/ge.pl?wp='.$this->otherWaypointIds['ge'];
            $otherSite->sitename = 'GPSgames.org';
            $otherSite->wp = $this->otherWaypointIds['ge'];
            $result[] = $otherSite;
        }

        if ( !empty($this->otherWaypointIds['tc']) && $config['otherSites_terracaching_com'] == 1) {
            $otherSite = new stdClass();
            $otherSite->link = 'http://play.terracaching.com/Cache/'.$this->otherWaypointIds['tc'];
            $otherSite->sitename = 'Terracaching.com';
            $otherSite->wp = $this->otherWaypointIds['tc'];
            $result[] = $otherSite;
        }

        if ( !empty($this->otherWaypointIds['nc']) && $config['otherSites_navicache_com'] == 1) {
            $otherSite = new stdClass();
            $wpnc = hexdec(mb_substr($this->otherWaypointIds['nc'], 1));

            $otherSite->link = 'http://www.navicache.com/cgi-bin/db/displaycache2.pl?CacheID='.$wpnc;
            $otherSite->sitename = 'Navicache.com';
            $otherSite->wp = $wpnc;
            $result[] = $otherSite;
        }

        if ( !empty($this->otherWaypointIds['gc']) && $config['otherSites_geocaching_com'] == 1) {
            $otherSite = new stdClass();
            $otherSite->link = 'http://coord.info/'.$this->otherWaypointIds['gc'];
            $otherSite->sitename = 'Geocaching.com';
            $otherSite->wp = $this->otherWaypointIds['gc'];
            $result[] = $otherSite;
        }

        if ( !empty($this->otherWaypointIds['qc']) && $config['otherSites_qualitycaching_com'] == 1 ) {
            $otherSite = new stdClass();
            $otherSite->link = 'http://www.qualitycaching.com/QCView.aspx?cid='.$this->otherWaypointIds['qc'];
            $otherSite->sitename = 'Qualitycaching.com';
            $otherSite->wp = $this->otherWaypointIds['qc'];
            $result[] = $otherSite;
        }

        return $result;
    }

    /**
     *
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getLogPassword()
    {
        return $this->logPassword;
    }

    public function hasLogPassword()
    {
        return $this->logPassword != false;
    }

    /**
     * if geocache require password for log entry, return true, otherwise false.
     *
     * @return boolean
     */
    public function hasPassword()
    {
        if ($this->logPassword === false) {
            return false;
        } else {
            return true;
        }

    }

    public function getWatchingUsersCount()
    {
        return $this->watchingUsersCount;
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
        if (($this->cacheType === self::TYPE_MOVING || $this->cacheType === self::TYPE_OWNCACHE) && $this->moveCount === -1) {
            $this->moveCount = XDb::xMultiVariableQueryValue(
                'SELECT COUNT(*) FROM `cache_logs` WHERE (type=4 OR type=10) AND cache_logs.deleted="0" AND cache_id=:1',
                0, $this->id);
        }
        return $this->moveCount;
    }

    public function getLogsCountByType(User $user, array $typesArray = null, $includeDeleted = false)
    {

        $typesStr = '';
        if (!is_null($typesArray)) {
            $typesArray = XDb::xEscape( implode(',', $typesArray) );
        }
        $typeFilterStr = (empty($typesStr))?'':"AND type IN ( $typesStr )";

        $excludeDeletedStr = (!$includeDeleted)?'AND deleted=0':'';

        $s = Xdb::xSql(
            "SELECT count(*) AS count, type FROM `cache_logs` WHERE cache_id = ? AND user_id = ? $typeFilterStr $excludeDeletedStr GROUP BY type",
            $this->id, $user->getUserId());

        $result = array();
        while ($row = XDb::xFetchArray($s)) {
            $result[$row['type']] = $row['count'];
        }

        return $result;
    }


    /**
     * get mobile cache distance.
     * (calculate mobile cache distance if were not counted before)
     *
     * @return float
     */
    public function getDistance()
    {
        if ($this->distance === -1) {
            $sql = 'SELECT sum(km) AS dystans FROM cache_moved WHERE cache_id=:1';
            $s = $this->db->multiVariableQuery($sql, $this->id);
            $dst = $this->db->dbResultFetchOneRowOnly($s);
            $this->distance = round($dst['dystans'], 2);
        }
        return $this->distance;
    }

    public function getWaypoints()
    {
        return Waypoint::GetWaypointsForCacheId($this->id);
    }

    /**
     * @return DateTime
     */
    public function getDateActivate()
    {
        return $this->dateActivate;
    }

    private function setDateActivate($dateActivate)
    {
        if ($dateActivate != null) {
            $this->dateActivate = new DateTime($dateActivate);
        }
    }

    /**
     * Gives the cache date of publication
     *
     * @return DateTime the cache date of publication, null if not set
     */
    public function getDatePublished()
    {
        return $this->datePublished;
    }

    /**
     * Sets the date of cache publication
     *
     * @param string the date to set
     * @throws Exception
     */
    private function setDatePublished($datePublished)
    {
        if ($datePublished != null) {
            $this->datePublished = new DateTime($datePublished);
        }
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Sets the language for GPX download of this cache.
     *
     * @param int $cacheid
     */
    public static function setCacheDefaultDescLang($cacheid)
    {
        global $config;

        $descLanguages = XDb::xMultiVariableQueryValue(
            "SELECT `desc_languages` FROM `caches`
            WHERE `cache_id`= :1 LIMIT 1", null, $cacheid
        );
        $desclang = mb_substr($descLanguages, 0, 2);

        foreach ($config['defaultLanguageList'] as $lang) {
            if (mb_strpos($descLanguages, $lang) !== false) {
                $desclang = $lang;
                break;
            }
        }

        XDb::xSql(
            "UPDATE `caches` SET
                `default_desclang`= ?, `last_modified`=NOW()
            WHERE cache_id= ? LIMIT 1",
            $desclang, $cacheid);
    }

    /**
     * Returns the list of cache descriptions and its languages
     *
     * @param int $cacheId
     * @return array
     */
    public static function getDescriptions($cacheId)
    {
        $rs = XDb::xSql("SELECT `id` AS desc_id, language FROM cache_desc WHERE cache_id = ?", $cacheId);
        $result = array();
        while ($row = XDb::xFetchArray($rs)) {
            $result[$row['desc_id']] = $row['language'];
        }
        return $result;
    }


    /**
     * update last_modified=NOW() for every object depending on that cacheid
     *
     */
    public static function touchCache($cacheId)
    {
        self::updateLastModified ($cacheId);

        XDb::xSql(
            "UPDATE `caches`, `cache_logs` SET `cache_logs`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`cache_logs`.`cache_id`
                AND `caches`.`cache_id`= ? AND `cache_logs`.`deleted`= ? ", $cacheId, 0);

        XDb::xSql(
            "UPDATE `caches`, `cache_desc` SET `cache_desc`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`cache_id`= ?", $cacheId);

        XDb::xSql(
            "UPDATE `caches`, `pictures` SET `pictures`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`pictures`.`object_id` AND `pictures`.`object_type`=2 AND `caches`.`cache_id`= ? ", $cacheId);

        XDb::xSql(
            "UPDATE `caches`, `cache_logs`, `pictures` SET `pictures`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`cache_logs`.`cache_id` AND `cache_logs`.`id`=`pictures`.`object_id`
            AND `pictures`.`object_type`=1 AND `caches`.`cache_id`= ?
            AND `cache_logs`.`deleted`= ? ", $cacheId, 0);

        XDb::xSql(
            "UPDATE `caches`, `mp3` SET `mp3`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`mp3`.`object_id` AND `mp3`.`object_type`=2 AND `caches`.`cache_id`= ? ", $cacheId);

        XDb::xSql(
            "UPDATE `caches`, `cache_logs`, `mp3` SET `mp3`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`cache_logs`.`cache_id` AND `cache_logs`.`id`=`mp3`.`object_id`
            AND `mp3`.`object_type`=1 AND `caches`.`cache_id`= ?
            AND `cache_logs`.`deleted`= ? ", $cacheId, 0);
    }


    public static function updateLastModified ($cacheId) {
        self::db()->multiVariableQuery(
            "UPDATE caches SET last_modified=NOW() WHERE cache_id= :1 ", $cacheId);
    }

    public static function getUserActiveCachesCountByType($userId)
    {

        $stmt = XDb::xSql(
            'SELECT type, COUNT(*) as cacheCount
             FROM `caches` WHERE `user_id` = ? AND STATUS != ?
             GROUP by type', $userId, self::STATUS_ARCHIVED);

        $result = [];
        while ($row = Xdb::xFetchArray($stmt)) {
            $result[$row['type']] = $row['cacheCount'];
        }

        return $result;
    }


    /**
     * Returns last modification date
     * @return DateTime
     * @throws Exception
     */
    public function getLastModificationDate()
    {

        $lm = XDb::xMultiVariableQueryValue(
            "SELECT MAX(`last_modified`) `last_modified`
             FROM
                 ( SELECT `last_modified` FROM `caches` WHERE `cache_id` = :1
                   UNION
                   SELECT `last_modified` FROM `cache_desc` WHERE `cache_id` = :2 ) `tmp_result`",
            0, $this->id, $this->id );

        return new DateTime($lm);
    }

    public function getCacheVisits()
    {
        return CacheVisits::GetCacheVisits($this->id);
    }

    public function getPrePublicationVisits()
    {
        $result = MultiUserQueries::GetUserNamesForListOfIds(
            CacheVisits::GetPrePublicationVisits($this->id));

        if (empty($result)) {
            $result[] = tr('no_visits');
        }
        return array_values($result);
    }

    public function incCacheVisits(User $user=null, $ip)
    {

        global $hide_coords; //hide-coords-for-unauthorized-users

        if (!$user && $hide_coords) {
            // don't count visits if coords are hidden
            return;
        }

        if ($user && $user->getUserId() == $this->getOwnerId()) {
            //skip inc visits for owner
            return;
        }

        $userIdOrIp = ($user) ? $user->getUserId() : $ip;

        if ($this->status == self::STATUS_WAITAPPROVERS || $this->status == self::STATUS_NOTYETAVAILABLE) {
            CacheVisits::CountCachePrePublicationVisit($userIdOrIp, $this->id);
        } else {
            CacheVisits::CountCacheVisit($userIdOrIp, $this->id);
        }
    }

    public function getCacheAttributesList()
    {
        global $config;

        if (is_array($this->cacheAttributesList)) {
            return $this->cacheAttributesList;
        }

        $s = XDb::xSql(
                "SELECT cache_attrib.text_long AS text, cache_attrib.icon_large AS icon
                FROM cache_attrib, caches_attributes
                WHERE cache_attrib.id=caches_attributes.attrib_id
                    AND cache_attrib.language = ?
                    AND caches_attributes.cache_id = ?
                ORDER BY cache_attrib.category, cache_attrib.id",
            strtoupper(I18n::getCurrentLang()), $this->getCacheId());

        if (XDb::xNumRows($s) == 0) {
            //TODO: there can be a lack of cache attrib translation in current language - then retrive translation in english
            $s = XDb::xSql(
                "SELECT cache_attrib.text_long AS text, cache_attrib.icon_large AS icon
                FROM cache_attrib, caches_attributes
                WHERE cache_attrib.id=caches_attributes.attrib_id
                    AND cache_attrib.language = 'EN'
                    AND caches_attributes.cache_id = ?
                ORDER BY cache_attrib.category, cache_attrib.id",
                $this->getCacheId());
        }

        $this->cacheAttributesList = [];
        while ($record = XDb::xFetchArray($s)) {

            $attrib = new stdClass();
            $attrib->iconLarge = $record['icon'];
            $attrib->text = $record['text'];

            $this->cacheAttributesList[] = $attrib;
        }

        // password is a special attribute - not stored in DB... sad...
        if ($this->hasPassword()) {
            $attrib = new stdClass();
            $attrib->iconLarge = $config['search-attr-icons']['password'][0];
            $attrib->text = tr('LogPassword');

            $this->cacheAttributesList[] = $attrib;
        }

        return $this->cacheAttributesList;
    }

    /**
     *
     * @param string $descLang
     * @return GeoCacheDesc
     */
    public function getCacheDescription($descLang)
    {
         return GeoCacheDesc::fromCacheIdFactory($this->id, $descLang);
    }

    /**
     * Returns user modified coordinates or null if there is no such defined
     * @param $userId
     * @return Coordinates
     */
    public function getUserCoordinates($userId)
    {
        return UserCacheCoords::getCoords($userId, $this->getCacheId());
    }

    public function saveUserCoordinates(Coordinates $coords, $userId)
    {
        UserCacheCoords::storeCoords($userId, $this->getCacheId(), $coords);
    }

    public function deleteUserCoordinates($userId)
    {
        UserCacheCoords::deleteCoords($this->getCacheId(), $userId);
    }

    public function getUserNote($userId)
    {
        return CacheNote::getNote($userId, $this->getCacheId());
    }

    public function saveUserNote($userId, $noteContent)
    {
        CacheNote::storeNote($userId, $this->getCacheId(), $noteContent);
    }

    public function deleteUserNote($userId)
    {
        CacheNote::deleteNote($userId, $this->getCacheId());
    }

    public function getGeokretsHosted()
    {
        if ($this->hostedGeokrets === false) {
            $s = XDb::xSql("SELECT gk_item.id, name, distancetravelled as distance
                        FROM gk_item INNER JOIN gk_item_waypoint ON (gk_item.id = gk_item_waypoint.id)
                        WHERE gk_item_waypoint.wp = ?
                            AND stateid<>1 AND stateid<>4
                            AND stateid<>5 AND typeid<>2 AND missing=0", $this->geocacheWaypointId);

            $this->hostedGeokrets = array();
            while ($row = Xdb::xFetchArray($s)) {
                $this->hostedGeokrets[] = $row;
            }
        }
        return $this->hostedGeokrets;

    }

    public function getMp3List()
    {
        if (is_null($this->mp3List)) {

        $this->mp3List = array();
        $rs = XDb::xSql(
            'SELECT uuid, title, url FROM mp3
            WHERE object_id = ? AND object_type=2 AND display=1
            ORDER BY seq, date_created', $this->id);

            while ($row = XDb::xFetchArray($rs)) {
                $this->mp3List[] = $row;
            }
        }

        return $this->mp3List;
    }

    /**
     * Update coordinates of the cache
     *
     * @param Coordinates $newCoords
     */
    public function updateCoordinates(Coordinates $newCoords)
    {
        if ($this->coordinates->areSameAs($newCoords)) {
            return;
        }

        $this->db->multiVariableQuery(
            "UPDATE caches SET last_modified=NOW(), latitude=:1, longitude=:2
             WHERE cache_id=:3",
            $newCoords->getLatitude(), $newCoords->getLongitude(), $this->getCacheId());

        $this->coordinates = $newCoords;
        $this->updateCacheLocation();
    }

    /**
     * Update (or set) cache location record based on current coords of the cache
     */
    public function updateCacheLocation()
    {
        $this->cacheLocationObj = CacheLocation::createForCache($this);
        $this->cacheLocationObj->updateInDb();
    }

    /**
     *
     * @param bool $returnSpoilersOnly
     * @param bool $changeUrlForSpoilers
     * @param bool $displayThumbsForSpoilers
     * @return array|null
     */
    public function getPicturesList( $returnSpoilersOnly, $changeUrlForSpoilers = false, $displayThumbsForSpoilers=false )
    {
        if (is_null($this->picturesList)) {

            $this->picturesList = array();

            $rs = XDb::xSql(
                "SELECT uuid, title, url, spoiler FROM pictures
                WHERE object_id = ? AND object_type=2 AND display=1
                ORDER BY seq, date_created", $this->id);

            while ($row=XDb::xFetchArray($rs)) {
                $pic = new stdClass();             //TODO: it should be refactored to picture-class

                $pic->spoiler = ($row['spoiler'] == '1');
                $pic->title = $row['title'];
                $pic->titleHtml = htmlspecialchars($row['title']);
                $pic->uuid = $row['uuid'];
                $pic->thumbUrl = SimpleRouter::getLink(PictureController::class, 'thumbSizeMedium', [$row['uuid']]);

                // path to images was changes - why not to fix it in DB?
                $pic->url = str_replace("images/uploads", "upload", $row['url']);


                $this->picturesList[] = $pic;
            }
        }

        if (! $returnSpoilersOnly) {
            $result = $this->picturesList;
        } else {
            // filter out non-spoilers
            $result =  array_filter( $this->picturesList, function($pic) {
                return $pic->spoiler;
            });
        }

        if ($changeUrlForSpoilers) {
            array_walk($result, function($pic) {
                if ($pic->spoiler) {
                    $pic->url = Thumbnail::placeholderUri(Thumbnail::PHD_SPOILER);
                }
            });
        }

        if ($displayThumbsForSpoilers) {
            array_walk($result, function($pic) {
                if ($pic->spoiler) {
                    $pic->thumbUrl = $pic->thumbUrl . '&amp;showspoiler=1';
                }
            });
        }

        return $result;
    }

    /**
     * This is used to determine if display link to the gallery of pics from logs...
     *
     * @return int
     */
    public function getPicsInLogsCount()
    {
        if ( is_null( $this->picsInLogsCount ) ) {

            $this->picsInLogsCount = Xdb::xMultiVariableQueryValue(
                    "SELECT COUNT(*) FROM pictures, cache_logs
                    WHERE pictures.object_id = cache_logs.id
                        AND pictures.object_type = 1
                        AND cache_logs.cache_id = :1", 0, $this->id);
        }
        return $this->picsInLogsCount;
    }

    public function hasDeletedLog()
    {
        return '1' == Xdb::xMultiVariableQueryValue(
            "SELECT 1 FROM cache_logs
            WHERE deleted = 1 and cache_id= :1 LIMIT 1", 0, $this->id);
    }

    public function getLogEntriesCount($countDeleted = false)
    {

        $excludeDeleted = (!$countDeleted)?"deleted= 0 AND":'';

        return Xdb::xMultiVariableQueryValue(
            "SELECT count(*) FROM cache_logs
            WHERE " . $excludeDeleted . " `cache_id`= :1", 0, $this->id);
    }

    /**
     * returns true if this cache is ignored by user identified by given userId
     *
     * @param int $userId
     * @return boolean
     *
     * @deprecated Use isIgnoredByUser instead
     */
    public function isIgnoredBy($userId)
    {
        return UserIgnoredCache::isCacheIgnoredBy($this->getCacheId(), $userId);
    }

    /**
     * Returns true if this cache is ignored by User
     *
     * @param User $user
     * @return boolean
     */
    public function isIgnoredByUser(User $user)
    {
        return UserIgnoredCache::isCacheIgnoredBy($this->getCacheId(), $user->getUserId());
    }

    /**
     * returns true if this cache is watched by user identified by given userId
     *
     * @param int $userId
     * @return boolean
     */
    public function isWatchedBy($userId)
    {
        return UserWatchedCache::isCacheWatchedByUser($this->getCacheId(), $userId);
    }

    /**
     * Returns url of the image which represents difficulty of tasks
     */
    public function getDifficultyIcon()
    {
        return sprintf("/images/difficulty/diff-%d.gif", $this->difficulty);
    }

    /**
     * Returns url of the image which represents terrain difficulty
     */
    public function getTerrainIcon()
    {
        return sprintf("/images/difficulty/terr-%d.gif", $this->terrain);
    }

    /**
     * Check if $user on this cache already has not deleted $logType log
     * and returns true/false
     *
     * @param User $user
     * @param int $logType
     * @return boolean
     */
    public function hasUserLogByType(User $user, $logType)
    {
        $logType = intval($logType);

        return $this->db->multiVariableQueryValue(
            "SELECT COUNT(*) FROM cache_logs
             WHERE deleted = 0 AND user_id = :1 AND cache_id = :2
                AND type = :3
             LIMIT 1", 0, $user->getUserId(), $this->getCacheId(),
            $logType) > 0;
    }

    /**
     * This method update altitude for this geocache
     */
    public function updateAltitude($newAltitude=null)
    {
        $oldAltitude = $this->getAltitude();

        if ($oldAltitude != $newAltitude) {
            $this->cacheAddtitions->updateAltitude($newAltitude);
        }
    }

    public function updateStatus($newStatus)
    {
        // Status validation
        if (! in_array($newStatus, self::CacheStatusArray())) {
            return false;
        }

        $this->status = $newStatus;

        $this->db->multiVariableQuery(
            'UPDATE `caches`
            SET `status` = :1,
                `last_modified` = NOW()
            WHERE `cache_id` = :2',
            $newStatus, $this->getCacheId()
            );

        return true;
    }

    /**
     * Returns array[username, user_id] of event attenders
     *
     * @return array
     */
    public function getAttenders()
    {
        return EventAttenders::getEventAttenders($this);
    }

    public function recalculateCacheScore()
    {
        list($newVotes, $newScore) = GeoCacheScore::getVotesScoreForCache($this->getCacheId());

        $this->db->multiVariableQuery("UPDATE caches SET votes=:1, score=:2 WHERE cache_id=:3",
            $newVotes, $newScore, $this->getCacheId());
    }

    /**
     * Publishes cache and
     * @return $this
     */
    public function publishCache()
    {
        $this->status = self::STATUS_READY;
        $this->dateActivate = null;

        $this->db->multiVariableQuery(
            "UPDATE `caches`
              SET `status` = :1, `date_activate` = :2, `last_modified` = NOW()
              WHERE `cache_id` = :3",
            $this->status,
            $this->dateActivate,
            $this->id
        );

        self::touchCache($this->id);
        EventHandler::cacheNew($this);

        return $this;
    }

    public static function getSizesInUse()
    {
        static $sizesInUse = null;

        if ($sizesInUse === null) {
            $sizesInUse = self::db()->dbFetchOneColumnArray(
                self::db()->simpleQuery("SELECT DISTINCT size FROM caches"),
                'size'
            );
        }
        return $sizesInUse;
    }

    public static function nanoIsInUse()
    {
        return self::db()->multiVariableQueryValue(
            "SELECT 1 FROM caches WHERE size = :1",
            0,
            self::SIZE_NANO
        ) != 0;
    }

    /**
     * Change the pictures count value by add $value to it
     * @param int $value
     */
    public function addToPicturesCount($value)
    {
        $this->db->multiVariableQuery(
            'UPDATE caches SET picturescount=picturescount + :1, last_modified = NOW()
             WHERE cache_id = :2 LIMIT 1', $value, $this->getCacheId());
    }
    /**
     * Returns last active (not deleted) GeoCacheLog for GeoCache or null if there is no logs for cache
     *
     * @return GeoCacheLog|null
     */
    public function getLastLog()
    {
        if (is_null($this->lastLog)) {
            $stmt = $this->db->multiVariableQuery(
                'SELECT `id`
                            FROM `cache_logs`
                            WHERE `cache_id` = :1
                                AND `deleted` = FALSE
                            ORDER BY `date_created` DESC
                            LIMIT 1',
                $this->getCacheId()
            );
            $logItem = $this->db->dbResultFetch($stmt);
            if (!empty($logItem)) {
                $this->lastLog = GeoCacheLog::fromLogIdFactory($logItem['id']);
            }
        }

        return $this->lastLog;
    }


}
