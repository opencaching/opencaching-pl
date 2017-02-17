<?php

namespace lib\Objects\GeoCache;

use \lib\Objects\PowerTrail\PowerTrail;
use \lib\Objects\OcConfig\OcConfig;
use Utils\Database\XDb;
use Utils\Database\OcDb;
use lib\Objects\User\User;

/**
 * Description of geoCache
 *
 * @author Łza
 */
class GeoCache extends StaticGeoCache
{
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

    private $ownerId;

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
     *
     * @var $cacheLocationObj \lib\Objects\GeoCache\CacheLocation
     */
    private $cacheLocationObj;

    /**
     * @param array $params
     *            'cacheId' => (integer) database cache identifier
     *            'wpId' => (string) geoCache wayPoint (ex. OP21F4)
     */
    public function __construct(array $params = array())
    {
        if (isset($params['cacheId'])) { // load from DB if cachId param is set
            $this->loadByCacheId($params['cacheId']);

        } elseif (isset($params['okapiRow'])) {
            $this->loadFromOkapiRow($params['okapiRow']);

        } elseif (isset($params['cacheWp'])){
            $this->loadByWp($params['cacheWp']);

        } elseif (isset($params['cacheUUID'])){
            $this->loadByUUID($params['cacheUUID']);
        }

        if($this->id != null){
            // load cache location if there is cache_id
            $this->cacheLocationObj = new CacheLocation($this->id);
        }

        $this->dictionary = \cache::instance();
    }

    /**
     * Factory
     * @param unknown $cacheId
     * @return \lib\Objects\GeoCache object or null if no such geocache
     */
    public static function fromCacheIdFactory($cacheId){
        try{
            return new self( array('cacheId' => $cacheId) );
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * Factory - creats Geocache object based on waypoint aka OC2345
     * @param unknown $wp
     * @return \lib\Objects\GeoCache object or null if no such geocache
     */
    public static function fromWayPointFactory($wp){
        try{
            return new self( array('cacheWp' => $wp) );
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * Factory - creats Geocache object based on geocache UUID
     * @param unknown $wp
     * @return \lib\Objects\GeoCache object or null if no such geocache
     */
    public static function fromUUIDFactory($uuid){
        try{
            return new self( array('cacheUUID' => $uuid) );
        }catch (\Exception $e){
            return null;
        }
    }

    private function loadByWp($wp){
        $wpColumn = self::getWpColumnName($wp);
        $db = OcDb::instance();

        $stmt = $db->multiVariableQuery("SELECT * FROM caches WHERE $wpColumn = :1 LIMIT 1", $wp);
        $cacheDbRow = $db->dbResultFetch($stmt);

        if(is_array($cacheDbRow)) {
            $this->loadFromRow($cacheDbRow);
        } else {
            throw new \Exception("Cache not found");
        }

    }

    private static function getWpColumnName($wp){
        switch(mb_strtoupper(mb_substr($wp, 0, 2))){
            case 'GC': return 'wp_gc';
            case 'NC': return 'wp_nc';
            case 'QC': return 'wp_qc';
            default: return 'wp_oc';
        }
    }

    private function loadByCacheId($cacheId){
        $db = OcDb::instance();

        //find cache by Id
        $s = $db->multiVariableQuery("SELECT * FROM caches WHERE cache_id = :1 LIMIT 1", $cacheId);

        $cacheDbRow = $db->dbResultFetch($s);

        if(is_array($cacheDbRow)) {
            $this->loadFromRow($cacheDbRow);
        } else {
            throw new \Exception("Cache not found");
        }
    }

    private function loadByUUID($uuid){
        $db = OcDb::instance();
        $this->id = (int) $params['cacheId'];

        //find cache by Id
        $s = $db->multiVariableQuery("SELECT * FROM caches WHERE uuid = :1 LIMIT 1", $uuid);

        $cacheDbRow = $db->dbResultFetch($s);

        if(is_array($cacheDbRow)) {
            $this->loadFromRow($cacheDbRow);
        } else {
            throw new \Exception("Cache not found");
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
        $this->id = $geocacheDbRow['cache_id'];
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

        $this->ownerId = (int) $geocacheDbRow['user_id'];
        $this->owner = null; //reset owner data

        if($geocacheDbRow['org_user_id'] != ''){
            $this->founder = new \lib\Objects\User\User(array('userId' => $geocacheDbRow['org_user_id']));
        }
        $this->score = $geocacheDbRow['score'];
        $this->setDateActivate($geocacheDbRow['date_activate']);
        return $this;
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
     * perform update on specified elements only.
     * @param array $elementsToUpdate
     */
    public function updateGeocacheLogenteriesStats()
    {
        $sqlQuery = "UPDATE `caches` SET `last_found`=:1, `founds`=:2, `notfounds`= :3, `notes`= :4 WHERE `cache_id`= :5";
        $db = OcDb::instance();
        $db->multiVariableQuery($sqlQuery, $this->lastFound, $this->founds, $this->notFounds, $this->notesCount, $this->id);
    }

    public function getPowerTrail()
    {
        return $this->powerTrail;
    }

    public function getCacheType()
    {
        return $this->cacheType;
    }

    public function getCacheTypeName(){

    }

    public function isEvent(){
        return $this->cacheType == self::TYPE_EVENT;
    }

    public function getCacheLocationObj()
    {
        return $this->cacheLocationObj;
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
    public function getAltitudeObj()
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
     * Returns true if cache is adopted and founder is not a current owner
     * @return boolean
     */
    public function isAdopted(){
        return $this->founder && $this->founder->getUserId() != $this->ownerId;
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
        if( is_null($this->owner) )
            $this->owner = User::fromUserIdFactory( $this->getOwnerId() );

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
            $db = OcDb::instance();
            $rsAreasql = "SELECT `parkipl`.`id` AS `npaId`, `parkipl`.`name` AS `npaname`,`parkipl`.`link` AS `npalink`,`parkipl`.`logo` AS `npalogo` FROM `cache_npa_areas` INNER JOIN `parkipl` ON `cache_npa_areas`.`parki_id`=`parkipl`.`id` WHERE `cache_npa_areas`.`cache_id`=:1 AND `cache_npa_areas`.`parki_id`!='0'";
            $s = $db->multiVariableQuery($rsAreasql, $this->id);
            $this->natureRegions = $db->dbResultFetchAll($s);
        }
        return $this->natureRegions;
    }

    public function getNatura2000Sites()
    {
        if($this->natura2000Sites === false){
            $db = OcDb::instance();
            $sql = "SELECT `npa_areas`.`id` AS `npaId`, `npa_areas`.`linkid` AS `linkid`,`npa_areas`.`sitename` AS `npaSitename`, `npa_areas`.`sitecode` AS `npaSitecode`, `npa_areas`.`sitetype` AS `npaSitetype`
                    FROM `cache_npa_areas` INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id`
                    WHERE `cache_npa_areas`.`cache_id`=:1 AND `cache_npa_areas`.`npa_id`!='0'";
            $s = $db->multiVariableQuery($sql, $this->id);
            $this->natura2000Sites = $db->dbResultFetchAll($s);
        }
        return $this->natura2000Sites;
    }

    public function getUsersRecomeded()
    {
        if($this->usersRecomeded === false) {
            $db  = OcDb::instance();
            $sql = "SELECT user.username username FROM `cache_rating` INNER JOIN user ON (cache_rating.user_id = user.user_id) WHERE cache_id=:1 ORDER BY username";
            $s = $db->multiVariableQuery($sql, $this->id);
            $this->usersRecomeded = $db->dbResultFetchAll($s);
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
            $db  = OcDb::instance();
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
            $db  = OcDb::instance();
            $sql = 'SELECT sum(km) AS dystans FROM cache_moved WHERE cache_id=:1';
            $s = $db->multiVariableQuery($sql, $this->id);
            $dst = $db->dbResultFetchOneRowOnly($s);
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

        $r['desc_languages'] = XDb::xMultiVariableQueryValue(
            "SELECT `desc_languages` FROM `caches`
            WHERE `cache_id`= :1 LIMIT 1", null, $cacheid);

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

    /**
     * Returns the list of cache descriptions and its languages
     *
     * @param unknown $cacheId
     */
    public static function getDescriptions($cacheId){
        $rs = XDb::xSql("SELECT `id` AS desc_id, language FROM cache_desc WHERE cache_id = ?", $cacheId);
        $result = array();
        while($row = XDb::xFetchArray($rs)){
            $result[$row['desc_id']] = $row['language'];
        }
        return $result;
    }


    /**
     * update last_modified=NOW() for every object depending on that cacheid
     *
     */
    public static function touchCache($cacheid){
        XDb::xSql(
            "UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`= ? ", $cacheid);
        XDb::xSql(
            "UPDATE `caches`, `cache_logs` SET `cache_logs`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`cache_logs`.`cache_id`
                AND `caches`.`cache_id`= ? AND `cache_logs`.`deleted`= ? ", $cacheid, 0);

        XDb::xSql(
            "UPDATE `caches`, `cache_desc` SET `cache_desc`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`cache_id`= ?", $cacheid);

        XDb::xSql(
            "UPDATE `caches`, `pictures` SET `pictures`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`pictures`.`object_id` AND `pictures`.`object_type`=2 AND `caches`.`cache_id`= ? ", $cacheid);

        XDb::xSql(
            "UPDATE `caches`, `cache_logs`, `pictures` SET `pictures`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`cache_logs`.`cache_id` AND `cache_logs`.`id`=`pictures`.`object_id`
            AND `pictures`.`object_type`=1 AND `caches`.`cache_id`= ?
            AND `cache_logs`.`deleted`= ? ", $cacheid, 0);

        XDb::xSql(
            "UPDATE `caches`, `mp3` SET `mp3`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`mp3`.`object_id` AND `mp3`.`object_type`=2 AND `caches`.`cache_id`= ? ", $cacheid);

        XDb::xSql(
            "UPDATE `caches`, `cache_logs`, `mp3` SET `mp3`.`last_modified`=NOW()
            WHERE `caches`.`cache_id`=`cache_logs`.`cache_id` AND `cache_logs`.`id`=`mp3`.`object_id`
            AND `mp3`.`object_type`=1 AND `caches`.`cache_id`= ?
            AND `cache_logs`.`deleted`= ? ", $cacheid, 0);
    }

}

