<?php

namespace lib\Objects\GeoCache;

use lib\Objects\PowerTrail\PowerTrail;
use lib\Objects\OcConfig\OcConfig;
use Utils\Database\XDb;
use Utils\Database\OcDb;
use lib\Objects\User\User;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\GeoCache\Altitude;
use lib\Objects\GeoCache\CacheLocation;

/**
 * Description of geoCache
 *
 * @author Łza
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

    /* @var $datePlaced \DateTime */
    private $datePlaced;

    /* @var $datePlaced \DateTime */
    private $dateCreated;

    /* @var $dateActivate \DateTime */
    private $dateActivate;

    private $sizeId;
    private $ratingId;              //OKAPI rating calculated from score
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

    /* @var $owner User */
    private $founder;

    /* @var $dictionary \cache */
    public $dictionary;

    private $ownerId;

    /* @var $owner User */
    private $owner;

    /* @var $altitude Altitude */
    private $altitude;

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
     * @var arrayObject
     */
    private $waypoints;

    /**
     *
     * @var $cacheLocationObj CacheLocation
     */
    private $cacheLocationObj = null;

    /**
     * List of cache attribites
     */
    private $cacheAttributesList = null;

    /**
     * List of hosted geokrets
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

        $this->dictionary = \cache::instance();
    }

    /**
     * Factory
     * @param unknown $cacheId
     * @return GeoCache object or null if no such geocache
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
     * @return GeoCache object or null if no such geocache
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
     * @return GeoCache object or null if no such geocache
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
                    $this->willattends = $value;
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
        $this->coordinates = new Coordinates(array(
            'dbRow' => $geocacheDbRow
        ));
        $this->altitude = new Altitude($this);

        $this->ownerId = (int) $geocacheDbRow['user_id'];
        $this->owner = null; //reset owner data

        if($geocacheDbRow['org_user_id'] != ''){
            $this->founder = new User(array('userId' => $geocacheDbRow['org_user_id']));
        }
        $this->score = $geocacheDbRow['score'];

        $this->ratingId = self::ScoreAsRatingNum($this->score); //rating is returned by OKAPI only-

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

    /**
     * Returns name of the cache type used in translation files
     *
     */
    public function getCacheTypeTranslationKey(){
        return self::CacheTypeTranslationKey($this->getCacheType());
    }

    public function isEvent(){
        return $this->cacheType == self::TYPE_EVENT;
    }

    public function isMovable(){
        return $this->cacheType == self::TYPE_MOVING || $this->cacheType == self::TYPE_OWNCACHE;
    }

    public function isOpenCheckerApplicable()
    {
        return $this->cacheType == self::TYPE_MOVING ||
            self::TYPE_QUIZ || self::TYPE_OTHERTYPE;
    }

    public function getCacheLocationObj()
    {
        if(!$this->cacheLocationObj){
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
     * @return \DateTime
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
     *
     * @return Altitude
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
     * @return User
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
        return tr(self::CacheRatingTranslationKey($this->ratingId));
    }

    public function getCacheIcon(User $forUser=null)
    {
        $logStatus = null;
        if(!is_null($forUser)){
            $logsCount = $this->getLogsCountByType($forUser, array(GeoCacheLog::LOGTYPE_FOUNDIT, GeoCacheLog::LOGTYPE_DIDNOTFIND));
            if(isset($logsCount[GeoCacheLog::LOGTYPE_FOUNDIT]) && $logsCount[GeoCacheLog::LOGTYPE_FOUNDIT]>0){
                $logStatus = GeoCacheLog::LOGTYPE_FOUNDIT;
            }else if(isset($logsCount[GeoCacheLog::LOGTYPE_DIDNOTFIND]) && $logsCount[GeoCacheLog::LOGTYPE_DIDNOTFIND]>0){
                $logStatus = GeoCacheLog::LOGTYPE_DIDNOTFIND;
            }
        }

        return self::CacheIconByType($this->cacheType, $this->status, $logStatus);
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

    public function isStatusReady(){
        return $this->status == self::STATUS_READY;
    }

    public function getStatusTranslationKey(){
        return self::CacheStatusTranslationKey($this->status);
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
            $s = $db->multiVariableQuery(
                "SELECT `parkipl`.`name` AS `npaname`,`parkipl`.`link` AS `npalink`,`parkipl`.`logo` AS `npalogo`
                 FROM `cache_npa_areas` INNER JOIN `parkipl` ON `cache_npa_areas`.`parki_id`=`parkipl`.`id`
                 WHERE `cache_npa_areas`.`cache_id`=:1 AND `cache_npa_areas`.`parki_id`!='0'", $this->id);

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
            $s = $db->multiVariableQuery(
                "SELECT user.username AS username
                 FROM `cache_rating` INNER JOIN user ON (cache_rating.user_id = user.user_id)
                 WHERE cache_id=:1 ORDER BY username", $this->id);

            $usersArr = [];
            foreach ($db->dbResultFetchAll($s) as $row){
                $usersArr[] = $row['username'];
            }
            $this->usersRecomeded = implode($usersArr, ', ');
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

    public function getWayLenghtFormattedString(){
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

        if ( !empty($this->otherWaypointIds['ge']) && $config['otherSites_gpsgames_org'] == 1 ){
            $otherSite = new \stdClass();
            $otherSite->link = 'http://geocaching.gpsgames.org/cgi-bin/ge.pl?wp='.$this->otherWaypointIds['ge'];
            $otherSite->sitename = 'GPSgames.org';
            $otherSite->wp = $this->otherWaypointIds['ge'];
            $result[] = $otherSite;
        }

        if ( !empty($this->otherWaypointIds['tc']) && $config['otherSites_terracaching_com'] == 1){
            $otherSite = new \stdClass();
            $otherSite->link = 'http://play.terracaching.com/Cache/'.$this->otherWaypointIds['tc'];
            $otherSite->sitename = 'Terracaching.com';
            $otherSite->wp = $this->otherWaypointIds['tc'];
            $result[] = $otherSite;
        }

        if ( !empty($this->otherWaypointIds['nc']) && $config['otherSites_navicache_com'] == 1){
            $otherSite = new \stdClass();
            $wpnc = hexdec(mb_substr($this->otherWaypointIds['nc'], 1));

            $otherSite->link = 'http://www.navicache.com/cgi-bin/db/displaycache2.pl?CacheID='.$wpnc;
            $otherSite->sitename = 'Navicache.com';
            $otherSite->wp = $wpnc;
            $result[] = $otherSite;
        }

        if ( !empty($this->otherWaypointIds['gc']) && $config['otherSites_geocaching_com'] == 1){
            $otherSite = new \stdClass();
            $otherSite->link = 'http://coord.info/'.$this->otherWaypointIds['gc'];
            $otherSite->sitename = 'Geocaching.com';
            $otherSite->wp = $this->otherWaypointIds['gc'];
            $result[] = $otherSite;
        }

        return $result;
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
        if(($this->cacheType === self::TYPE_MOVING || $this->cacheType === self::TYPE_OWNCACHE) && $this->moveCount === -1){
            $this->moveCount = XDb::xMultiVariableQueryValue(
                'SELECT COUNT(*) FROM `cache_logs` WHERE (type=4 OR type=10) AND cache_logs.deleted="0" AND cache_id=:1',
                0, $this->id);
        }
        return $this->moveCount;
    }

    public function getLogsCountByType(User $user, array $typesArray=null, $includeDeleted = false)
    {

        $typesStr = '';
        if(!is_null($typesArray)){
            $typesArray = XDb::xEscape( implode(',', $typesArray) );
        }
        $typeFilterStr = (empty($typesStr))?'':"AND type IN ( $typesStr )";

        $excludeDeletedStr = (!$includeDeleted)?'AND deleted=0':'';

        $s = Xdb::xSql(
            "SELECT count(*) AS count, type FROM `cache_logs` WHERE cache_id = ? AND user_id = ? $typeFilterStr $excludeDeletedStr GROUP BY type",
            $this->id, $user->getUserId());

        $result = array();
        while($row = XDb::xFetchArray($s)){
            $result[$row['type']] = $row['count'];
        }

        return $result;
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
        return Waypoint::GetWaypointsForCacheId($this->id);
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

    /**
     * Returns last modification date
     * @return \DateTime
     */
    public function getLastModificationDate(){

        $lm = XDb::xMultiVariableQueryValue(
            "SELECT MAX(`last_modified`) `last_modified`
             FROM
                 ( SELECT `last_modified` FROM `caches` WHERE `cache_id` = :1
                   UNION
                   SELECT `last_modified` FROM `cache_desc` WHERE `cache_id` = :2 ) `tmp_result`",
            0, $this->id, $this->id );

        return new \DateTime($lm);
    }

    public function getCacheVisits()
    {
        return CacheVisits::GetCacheVisits($this->id);
    }

    public function getPrePublicationVisits()
    {
        $result = User::GetUserNamesForListOfIds(
            CacheVisits::GetPrePublicationVisits($this->id));

        if(empty($result)){
            $result[] = tr('no_visits');
        }
        return $result;
    }

    public function incCacheVisits(User $user=null, $ip)
    {

        global $hide_coords; //hide-coords-for-unauthorized-users

        if(!$user && $hide_coords){
            // don't count visits if coords are hidden
            return;
        }

        if($user && $user->getUserId() == $this->getOwnerId()){
            //skip inc visits for owner
            return;
        }

        $userIdOrIp = ($user) ? $user->getUserId() : $ip;

        if($this->status == self::STATUS_WAITAPPROVERS || $this->status == self::STATUS_NOTYETAVAILABLE){
            CacheVisits::CountCachePrePublicationVisit($userIdOrIp, $this->id);
        }else{
            CacheVisits::CountCacheVisit($userIdOrIp, $this->id);
        }
    }

    public function getCacheAttributesList()
    {
        global $lang, $config;

        if(is_array($this->cacheAttributesList)){
            return $this->cacheAttributesList;
        }

        $s = XDb::xSql(
                "SELECT cache_attrib.text_long AS text, cache_attrib.icon_large AS icon
                FROM cache_attrib, caches_attributes
                WHERE cache_attrib.id=caches_attributes.attrib_id
                    AND cache_attrib.language = ?
                    AND caches_attributes.cache_id = ?
                ORDER BY cache_attrib.category, cache_attrib.id",
            strtoupper($lang), $this->getCacheId());

        if( XDb::xNumRows($s) == 0 ){
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
        while($record = XDb::xFetchArray($s)){

            $attrib = new \stdClass();
            $attrib->iconLarge = $record['icon'];
            $attrib->text = $record['text'];

            $this->cacheAttributesList[] = $attrib;
        }

        // password is a special attribute - not stored in DB... sad...
        if($this->hasPassword()){
            $attrib = new \stdClass();
            $attrib->iconLarge = $config['search-attr-icons']['password'][0];
            $attrib->text = tr('LogPassword');

            $this->cacheAttributesList[] = $attrib;
        }

        return $this->cacheAttributesList;
    }

    /**
     *
     * @param unknown $descLang
     * @return GeoCacheDesc
     */
    public function getCacheDescription($descLang)
    {
         return new GeoCacheDesc($this->id, $descLang);
    }

    /**
     * Returns user modified coordinates or null if there is no such defined
     * @return Coordinates
     */
    public function getUserCoordinates($userId)
    {
        $s = XDb::xSql(
            "SELECT longitude AS lon, latitude AS lat FROM cache_mod_cords
            WHERE cache_id = ? AND user_id = ? LIMIT 1", $this->id, $userId);

        if($row = XDb::xFetchArray($s)){
            return Coordinates::FromCoordsFactory($row['lat'], $row['lon']);
        }else{
            return null;
        }

    }

    public function saveUserCoordinates(Coordinates $coords, $userId)
    {
        //TODO: Table cache_mod_cords should have index on cache_id/user_id instead of autoincrement index!
        //      Then it could be possible to use INSERT ... ON DUPLICATE KEY UPDATE Syntax
        //      DELETE old coords to be sure there is no duplicates...
        $this->deleteUserCoordinates($userId);

        XDb::xSql("INSERT INTO cache_mod_cords
            (cache_id, user_id, longitude, latitude, date)
            VALUES(?, ?, ?, ?, NOW() );",
            $this->id, $userId, $coords->getLongitude(), $coords->getLatitude());
    }

    public function deleteUserCoordinates($userId)
    {
        XDb::xSql("DELETE FROM cache_mod_cords
                   WHERE cache_id = ? AND user_id = ?", $this->id, $userId);
    }

    public function getUserNote($userId)
    {
        return XDb::xMultiVariableQueryValue(
            "SELECT `desc` FROM cache_notes WHERE cache_id = :1 AND user_id = :2 LIMIT 1",
            '', $this->id, $userId);
    }

    public function saveUserNote($userId, $noteContent)
    {
        //TODO: Table cache_notes should have index on cache_id/user_id instead of autoincrement index!
        //      Then it could be possible to use INSERT ... ON DUPLICATE KEY UPDATE Syntax
        //      DELETE old coords to be sure there is no duplicates...
        $this->deleteUserNote($userId);


        $noteContent = htmlspecialchars($noteContent, ENT_COMPAT, 'UTF-8');

        XDb::xSql("INSERT INTO cache_notes
            (cache_id, user_id, `desc`, desc_html, date)
            VALUES(?, ?, ?, ?, NOW() );",
            $this->id, $userId, $noteContent, '0');

    }

    public function deleteUserNote($userId)
    {
        XDb::xSql("DELETE FROM cache_notes
                   WHERE cache_id = ? AND user_id = ?", $this->id, $userId);
    }

    public function getGeokretsHosted()
    {
        if($this->hostedGeokrets===false){
            $s = XDb::xSql("SELECT gk_item.id, name, distancetravelled as distance
                        FROM gk_item INNER JOIN gk_item_waypoint ON (gk_item.id = gk_item_waypoint.id)
                        WHERE gk_item_waypoint.wp = ?
                            AND stateid<>1 AND stateid<>4
                            AND stateid<>5 AND typeid<>2 AND missing=0", $this->geocacheWaypointId);

            $this->hostedGeokrets = array();
            while ($row = Xdb::xFetchArray($s)){
                $this->hostedGeokrets[] = $row;
            }
        }
        return $this->hostedGeokrets;

    }

    public function getMp3List()
    {
        if(is_null($this->mp3List)){

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
     *
     * @param string $changeUrlForSpilers - is used to hide spoilers if neccessary
     */
    public function getPicturesList( $returnSpoilersOnly, $changeUrlForSpoilers = false, $displayThumbsForSpoilers=false )
    {
        if(is_null($this->picturesList)){

            $this->picturesList = array();

            $rs = XDb::xSql(
                "SELECT uuid, title, url, spoiler FROM pictures
                WHERE object_id = ? AND object_type=2 AND display=1
                ORDER BY seq, date_created", $this->id);

            while($row=XDb::xFetchArray($rs)){
                $pic = new \stdClass();             //TODO: it should be refactored to picture-class

                $pic->spoiler = ($row['spoiler'] == '1');
                $pic->title = $row['title'];
                $pic->titleHtml = htmlspecialchars($row['title']);
                $pic->uuid = $row['uuid'];
                $pic->thumbUrl = "thumbs.php?uuid=".$row['uuid'];

                // path to images was changes - why not to fix it in DB?
                $pic->url = str_replace("images/uploads", "upload", $row['url']);


                $this->picturesList[] = $pic;
            }
        }

        if(! $returnSpoilersOnly){
            $result = $this->picturesList;
        }else{
            // filter out non-spoilers
            $result =  array_filter( $this->picturesList, function ($pic){
                return $pic->spoiler;
            });
        }

        if($changeUrlForSpoilers){
            array_walk($result, function($pic){
                if($pic->spoiler){
                    $pic->url = 'tpl\stdstyle\images\thumb\thumbspoiler.gif';
                }
            });
        }

        if($displayThumbsForSpoilers){
            array_walk($result, function($pic){
                if($pic->spoiler){
                    $pic->thumbUrl = $pic->thumbUrl . '&amp;showspoiler=1';
                }
            });
        }

        return $result;
    }

    /**
     * This is used to determine if display link to the gallery of pics from logs...
     * @return unknown|mixed
     */
    public function getPicsInLogsCount()
    {
        if( is_null( $this->picsInLogsCount ) ){

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
     * @param unknown $userId
     * @return boolean
     */
    public function isIgnoredBy($userId)
    {
        return '1' == XDb::xMultiVariableQueryValue(
            "SELECT 1 FROM cache_ignore WHERE cache_id= :1 AND user_id =:2 LIMIT 1",
            0, $this->id, $userId);
    }

    /**
     * returns true if this cache is watched by user identified by given userId
     * @param unknown $userId
     * @return boolean
     */
    public function isWatchedBy($userId)
    {
        return '1' == XDb::xMultiVariableQueryValue(
            "SELECT 1 FROM cache_watches WHERE cache_id=:1 AND user_id=:2 LIMIT 1",
            0, $this->id, $userId);
    }

    /**
     * Returns url of the image which represents dificulty of the tasks
     */
    public function getDifficultyIcon()
    {
        return sprintf("/tpl/stdstyle/images/difficulty/diff-%d.gif", $this->difficulty);
    }

    /**
     * Returns url of the image which represents terrain dificulty
     */
    public function getTerreinIcon()
    {
        return sprintf("/tpl/stdstyle/images/difficulty/terr-%d.gif", $this->terrain);
    }

}

