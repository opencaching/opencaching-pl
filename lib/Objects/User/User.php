<?php
namespace lib\Objects\User;

use Utils\Database\OcDb;
use lib\Objects\GeoCache\GeoCache;
use lib\Controllers\Php7Handler;
use Utils\Database\XDb;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\BaseObject;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\PowerTrail\PowerTrail;


/**
 * Description of user
 */
class User extends BaseObject
{

    private $userId;
    private $isAdmin = false;
    private $isGuide;
    private $userName;

    private $foundGeocachesCount;
    private $notFoundGeocachesCount;
    private $hiddenGeocachesCount;
    private $logNotesCount;
    private $email;

    /* @var $homeCoordinates Coordinates */
    private $homeCoordinates;

    private $country;

    private $profileUrl = null;

    private $ingnoreGeocacheLimitWhileCreatingNewGeocache = null;

    /* @var $powerTrailCompleted \ArrayObject() */
    private $powerTrailCompleted = null;

    /* @var $powerTrailOwed \ArrayObject() */
    private $powerTrailOwed = null;

    /* @var $geocaches \ArrayObject() */
    private $geocaches = null;

    /* @var $geocachesNotPublished \ArrayObject() */
    private $geocachesNotPublished = null;

    /* @var $geocachesWaitAproove \ArrayObject() */
    private $geocachesWaitAproove = null;

    /* @var $geocachesBlocked \ArrayObject() */
    private $geocachesBlocked = null;

    private $eventsAttendsCount = null;

    private $dateCreated;
    private $description;
    private $lastLogin;
    private $isActive = null;
    private $hideBan = false;

    private $verifyAll = null;

    /* user identifier used to communication with geoKrety Api*/
    private $geokretyApiSecid;

    private $rulesConfirmed = false;

    const REGEX_USERNAME = '^[a-zA-Z0-9ęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚ@-][a-zA-ZęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚ0-9\.\-=_ @ęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚäüöÄÜÖ=)(\/\\\ -=&*+~#]{2,59}$';
    const REGEX_PASSWORD = '^[a-zA-Z0-9\.\-_ @ęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚäüöÄÜÖ=)(\/\\\$&*+~#]{3,60}$';


    const COMMON_COLLUMNS = "user_id, username, founds_count, notfounds_count,
                       hidden_count, latitude, longitude, country,
                       email, admin, guru, verify_all, rules_confirmed";

    /**
     * construct class using $userId (fields will be loaded from db)
     * OR, if you have already user data row fetched from db row ($userDbRow), object is created using this data
     *
     * @param type $userId - user identifier in db
     * @param type $userDbRow - array - user data taken from db, from table user.
     */
    public function __construct(array $params=null)
    {
        parent::__construct();

        if(is_null($params)){
            $params = array();
        }

        if (isset($params['fieldsStr'])) {
            //get selected columns only
            $fields = $params['fieldsStr'];
        } else {
            //default column list loaded from DB
            $fields = self::COMMON_COLLUMNS;
        }

        if (isset($params['userId'])) {
            $this->userId = (int) $params['userId'];
            $this->loadDataFromDb($fields);

        } elseif (isset($params['username'])) {
            $this->loadDataFromDbByUsername($params['username'], $fields);

        } elseif (isset($params['userDbRow'])) {
            $this->setUserFieldsByUsedDbRow($params['userDbRow']);

        } elseif (isset($params['okapiRow'])) {
            $this->loadFromOKAPIRsp($params['okapiRow']);

        }
    }

    /**
     * Factory
     * @param unknown $username
     * @return User object or null on error
     */
    public static function fromUsernameFactory($username){

        $u = new self();
        if($u->loadDataFromDbByUsername($username, self::COMMON_COLLUMNS)){
            return $u;
        }
        return null;
    }

    /**
     * Factory
     * @param $username
     * @param $fields - comma separatd list of columns to get from DB
     * @return User object or null on error
     */
    public static function fromUserIdFactory($userId, $fields = null){
        $u = new self();
        $u->userId = $userId;

        if(is_null($fields)){
            $fields = self::COMMON_COLLUMNS;
        }

        if($u->loadDataFromDb($fields)){
            return $u;
        }
        return null;
    }

    public function loadExtendedSettings()
    {
        $db = OcDb::instance();
        $queryById = "SELECT `newcaches_no_limit` AS ingnoreGeocacheLimitWhileCreatingNewGeocache "
                   . "FROM `user_settings` WHERE `user_id` = :1 LIMIT 1";
        $stmt = $db->multiVariableQuery($queryById, $this->userId);
        $dbRow = $db->dbResultFetchOneRowOnly($stmt);

        if ($dbRow && $dbRow['ingnoreGeocacheLimitWhileCreatingNewGeocache'] == 1) {
            $this->ingnoreGeocacheLimitWhileCreatingNewGeocache = true;
        }else{
            $this->ingnoreGeocacheLimitWhileCreatingNewGeocache = false;
        }
    }

    public function loadFromOKAPIRsp($okapiRow)
    {
        // load user data from row returned by OKAPI
        foreach ($okapiRow as $field => $value) {
            switch ($field) {
                case 'uuid': // geocache owner's user ID,
                    $this->userId = (int) $value;
                    break;
                case 'username': // name of the user,
                    $this->userName = $value;
                    break;
                case 'profile_url': // URL of the user profile page,
                    $this->profileUrl = $value;
                    break;
                default:
                    error_log(__METHOD__ . ": Unknown field: $field (value: $value)");
            }
        }

        $this->dataLoaded = true; //mark object as containing data

    }

    private function loadDataFromDb($fields){

        $stmt = $this->db->multiVariableQuery(
            "SELECT $fields FROM `user` WHERE `user_id`=:1 LIMIT 1", $this->userId);

        if($row = $this->db->dbResultFetchOneRowOnly($stmt)){
            $this->setUserFieldsByUsedDbRow($row);
            return true;
        }
        return false;
    }

    private function loadDataFromDbByUsername($username, $fields){

        $stmt = $this->db->multiVariableQuery(
            "SELECT $fields FROM `user` WHERE `username`=:1 LIMIT 1", $username);

        if($row = $this->db->dbResultFetchOneRowOnly($stmt)){
            $this->setUserFieldsByUsedDbRow($row);
            return true;
        }
        return false;
    }

    private function setUserFieldsByUsedDbRow(array $dbRow)
    {
        $cordsPresent = false;

        foreach ($dbRow as $key => $value) {
            switch ($key) {
                case 'user_id':
                    $this->userId = (int) $value;
                    break;
                case 'username':
                    $this->userName = $value;
                    break;
                case 'founds_count':
                    $this->foundGeocachesCount = (int) $value;
                    break;
                case 'notfounds_count':
                    $this->notFoundGeocachesCount = (int) $value;
                    break;
                case 'hidden_count':
                    $this->hiddenGeocachesCount = (int) $value;
                    break;
                case 'email':
                    $this->email = $value;
                    break;
                case 'country':
                    $this->country = $value;
                    break;
                case 'latitude':
                case 'longitude':
                    // lat|lon are handling below
                    $cordsPresent = true;
                    break;
                case 'admin':
                    $this->isAdmin = Php7Handler::Boolval($value);
                    break;
                case 'guru':
                    $this->isGuide = Php7Handler::Boolval($value);
                    break;
                case 'log_notes_count':
                    $this->logNotesCount = $value;
                    break;
                case 'verify_all':
                    $this->verifyAll = $value;
                    break;
                case 'rules_confirmed':
                    $this->rulesConfirmed = Php7Handler::Boolval($value);
                    break;
                case 'date_created':
                    $this->dateCreated = $value;
                    break;
                case 'description':
                    $this->description = $value;
                    break;
                case 'last_login':
                    $this->lastLogin = $value;
                    break;
                case 'is_active_flag':
                    $this->isActive = Php7Handler::Boolval($value);
                    break;
                case 'hide_flag':
                    $this->hideBan = (int) $value;
                    break;
                /* db fields not used in this class yet*/
                case 'password':
                    // just skip it...
                    break;

                default:
                    error_log(__METHOD__ . ": Unknown column: $key");
            }
        }

        // if coordinates are present set the homeCords.
        if ($cordsPresent) {
            $this->homeCoordinates =
                new Coordinates(array('dbRow' => $dbRow));
        }
        $this->dataLoaded = true; // mark object as containing data
    }

    /**
     * after delete a log it is a good idea to full recalculate stats of user, that can avoid
     * possible errors which used to appear when was calculated old method.
     */
    public function recalculateAndUpdateStats()
    {
        $query = "
            UPDATE `user`
            SET `founds_count`   = (SELECT count(*) FROM `cache_logs` WHERE `user_id` =:1 AND `type` =1 AND `deleted` =0 ),
                `notfounds_count`= (SELECT count(*) FROM `cache_logs` WHERE `user_id` =:1 AND `type` =2 AND `deleted` =0 ),
                `log_notes_count`= (SELECT count(*) FROM `cache_logs` WHERE `user_id` =:1 AND `type` =3 AND `deleted` =0 )
            WHERE `user_id` =:1
        ";
        $db = OcDb::instance();
        $db->multiVariableQuery($query, $this->userId);

        $stmt = $db->multiVariableQuery(
            'SELECT `founds_count`, `notfounds_count`, `log_notes_count` FROM  `user` WHERE `user_id` =:1',
            $this->userId);
        $dbResult = $db->dbResultFetchOneRowOnly($stmt);

        $this->setUserFieldsByUsedDbRow($dbResult);
    }

    /**
     * Database user identifier. (Used system-wide)
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * user email address
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function getUserInformation()
    {
        return array(
            'userName' => $this->userName
        );
    }

    /**
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    public function getProfileUrl()
    {
        return $this->profileUrl;
    }

    /**
     *
     * @return boolean
     */
    public function isIngnoreGeocacheLimitWhileCreatingNewGeocache()
    {

        if( is_null( $this->ingnoreGeocacheLimitWhileCreatingNewGeocache )){
            $this->loadExtendedSettings();
        }

        return $this->ingnoreGeocacheLimitWhileCreatingNewGeocache;
    }

    /**
     *
     * @return integer
     */
    public function getFoundGeocachesCount()
    {
        return $this->foundGeocachesCount;
    }

    /**
     *
     * @return Coordinates object
     */
    public function getHomeCoordinates()
    {
        return $this->homeCoordinates;
    }

    /**
     * @return boolean
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function isAdmin(){
        return $this->isAdmin;
    }

    public function getIsGuide()
    {
        return $this->isGuide;
    }

    public function isGuide()
    {
        return $this->isGuide;
    }

    public function getCountry(){
        return $this->country;
    }

    public function areRulesConfirmed(){
        return $this->rulesConfirmed;
    }

    /**
     * get all PowerTrails user completed
     * @return \ArrayObject
     */
    public function getPowerTrailCompleted()
    {
        if($this->powerTrailCompleted === null) {
            $this->powerTrailCompleted = new \ArrayObject();
            $queryPtList = 'SELECT * FROM `PowerTrail` WHERE `id` IN (SELECT `PowerTrailId` FROM `PowerTrail_comments` WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1 ORDER BY `logDateTime` DESC)';
            $db = OcDb::instance();
            $stmt = $db->multiVariableQuery($queryPtList, $this->userId);
            $ptList = $db->dbResultFetchAll($stmt);

            foreach ($ptList as $ptRow) {
                $this->powerTrailCompleted->append(new PowerTrail(array('dbRow' => $ptRow)));
            }
        }
        return $this->powerTrailCompleted;
    }

    /**
     * get all PowerTrails user own
     * @return \ArrayObject
     */
    public function getPowerTrailOwed()
    {
        if($this->powerTrailOwed === null) {
            $this->powerTrailOwed = new \ArrayObject();

            $db = OcDb::instance();
            $stmt = $db->multiVariableQuery(
                "SELECT `PowerTrail`.* FROM `PowerTrail`, PowerTrail_owners WHERE  PowerTrail_owners.userId = :1 AND PowerTrail_owners.PowerTrailId = PowerTrail.id",
                $this->userId);

            $ptList = $db->dbResultFetchAll( $stmt );
            foreach ($ptList as $ptRow) {
                $this->powerTrailOwed->append(new PowerTrail(array('dbRow' => $ptRow)));
            }
        }
        return $this->powerTrailOwed;
    }

    public function getGeocaches()
    {
        if($this->geocaches === null){
            $this->geocaches = new \ArrayObject;
            $db = OcDb::instance();

            $stmt = $db->multiVariableQuery(
                "SELECT * FROM `caches` where `user_id` = :1 ", $this->userId);

            foreach ($db->dbResultFetchAll($stmt) as $geocacheRow) {
                $geocache = new GeoCache();
                $geocache->loadFromRow($geocacheRow);
                $this->geocaches->append($geocache);
                if($geocache->getStatus() === GeoCache::STATUS_NOTYETAVAILABLE){
                    $this->appendNotPublishedGeocache($geocache);
                }
                if($geocache->getStatus() === GeoCache::STATUS_WAITAPPROVERS){
                    $this->appendWaitAprooveGeocache($geocache);
                }
                if($geocache->getStatus() === GeoCache::STATUS_BLOCKED){
                    $this->appendBlockedGeocache($geocache);
                }
            }
        }
        return $this->geocaches;
    }



    public function appendNotPublishedGeocache(GeoCache $geocache)
    {
        if($this->geocachesNotPublished === null){
            $this->geocachesNotPublished = new \ArrayObject;
        }
        $this->geocachesNotPublished->append($geocache);
    }

    public function getGeocachesNotPublished()
    {
        if($this->geocachesNotPublished === null){
            $this->geocachesNotPublished = new \ArrayObject;
            $this->getGeocaches();
        }
        return $this->geocachesNotPublished;
    }

    public function appendWaitAprooveGeocache(GeoCache $geocache)
    {
        if($this->geocachesWaitAproove === null){
            $this->geocachesWaitAproove = new \ArrayObject;
        }
        $this->geocachesWaitAproove->append($geocache);
    }

    public function getGeocachesWaitAproove()
    {
        if($this->geocachesWaitAproove === null){
            $this->geocachesWaitAproove = new \ArrayObject;
            $this->getGeocaches();
        }
        return $this->geocachesWaitAproove;
    }

    public function appendBlockedGeocache(GeoCache $geocache)
    {
        if($this->geocachesBlocked === null){
            $this->geocachesBlocked = new \ArrayObject;
        }
        $this->geocachesBlocked->append($geocache);
    }

    public function getGeocachesBlocked()
    {
        if($this->geocachesBlocked === null){
            $this->geocachesBlocked = new \ArrayObject;
            $this->getGeocaches();
        }
        return $this->geocachesBlocked;
    }

    public function getVerifyAll(){
        return $this->verifyAll;
    }

    /**
     * This function return true if user:
     * - own less than 3 (APPROVE_LIMIT) active caches
     * - has 'verify-all' flag set by COG/admins
     * -
     */
    public function isUnderCacheVerification(){

        if( $this->getVerifyAll() == 1 ){
            return true;
        }

        //get published geocaches count
        $activeCachesNum = XDb::xMultiVariableQueryValue(
            "SELECT COUNT(*) FROM `caches`
             WHERE `user_id` = :1 AND status = 1",
             0, $this->getUserId());

        if($activeCachesNum < OcConfig::getNeedAproveLimit()){

            return true;
        }


        return false;

    }

    /**
     * Returns true if user is able to create new cache:
     * - when found more than X caches
     * - is
     */
    public function canCreateNewCache(){

        // calculate found caches number
        $num_find_caches = XDb::xMultiVariableQueryValue(
            "SELECT COUNT(`cache_logs`.`cache_id`) as num_fcaches
             FROM cache_logs, caches
             WHERE cache_logs.cache_id=caches.cache_id
                AND caches.type IN (1,2,3,7,8)
                AND cache_logs.type=1
                AND cache_logs.deleted=0
                AND `cache_logs`.`user_id` = :1 ",
                0, $this->getUserId() );


        if ($num_find_caches < OcConfig::getNeedFindLimit() &&
            !$this->isIngnoreGeocacheLimitWhileCreatingNewGeocache()){


            return false;
        }

        return true;

    }

    /*
     * This function return true if user is allowed to adopt caches
     * This means when:
     * - is not under verification
     * - has rights to create new cache
     */
    public function isAdoptionApplicable(){

        if( $this->canCreateNewCache() && !$this->isUnderCacheVerification() ){

            return true;
        }
        return false;
    }

    /**
     * @return integer
     */
    public function getEventsAttendsCount()
    {
        if($this->eventsAttendsCount == null) {
            $this->eventsAttendsCount = XDb::xSimpleQueryValue("SELECT COUNT(*) events_count
                            FROM cache_logs
                            WHERE user_id=".$this->userId." AND type=7 AND deleted=0", 0);
        }
        return $this->eventsAttendsCount;
    }

    /**
     * @return integer
     */
    public function getHiddenGeocachesCount()
    {
        return $this->hiddenGeocachesCount;
    }

    /**
     * @return integer
     */
    public function getNotFoundGeocachesCount()
    {
        return $this->notFoundGeocachesCount;
    }

    /**
     * @return integer
     */
    public function getLogNotesCount()
    {
        return $this->logNotesCount;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getLastLoginDate()
    {
        return $this->lastLogin;
    }

    public function isActive()
    {
        return $this->isActive && !empty($this->getEmail());
    }

    public function haveHideBan(){
        return $this->hideBan == 10;
    }

    public function getGeokretyApiSecid()
    {
        if($this->geokretyApiSecid === null){
            $query = 'SELECT `secid` FROM `GeoKretyAPI` WHERE `userID` = :1 LIMIT 1';
            $db = OcDb::instance();
            $result = $db->dbResultFetchOneRowOnly($db->multiVariableQuery($query, $this->userId));
            $this->geokretyApiSecid = $result['secid'];
        }
        return $this->geokretyApiSecid;
    }

    public static function GetUserNamesForListOfIds(array $userIds)
    {

        if(empty($userIds)){
            return array();
        }

        $userIdsStr = XDb::xEscape(implode($userIds, ','));

        $s = XDb::xSql(
            "SELECT username FROM `user`
            WHERE `user_id` IN ( $userIdsStr )");

        $result = array();
        while($row = XDb::xFetchArray($s)){
            $result[] = $row['username'];
        }

        return $result;
    }
}
