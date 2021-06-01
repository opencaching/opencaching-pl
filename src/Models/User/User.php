<?php
namespace src\Models\User;

use ArrayObject;
use DateTime;
use src\Utils\Generators\TextGen;
use src\Utils\Generators\Uuid;
use src\Models\Coordinates\Coordinates;
use src\Models\GeoCache\GeoCache;
use src\Models\OcConfig\OcConfig;
use src\Utils\Debug\Debug;
use src\Utils\Text\Formatter;
use src\Utils\DateTime\OcDateTime;
use src\Models\Admin\AdminNote;

/**
 * Description of user
 */
class User extends UserCommons
{

    private $userId;
    private $userUuid;
    private $isGuide;
    private $userName;
    private $role;

    private $foundGeocachesCount;
    /** @var int */
    private $foundPhysicalGeocachesCount = null;
    private $notFoundGeocachesCount;
    private $hiddenGeocachesCount;
    private $logNotesCount;
    private $email;

    /** @var $homeCoordinates Coordinates */
    private $homeCoordinates;
    private $notifyRadius;

    private $profileUrl = null;

    /** @var boolean */
    private $newCachesNoLimit = null;

    /** @var $geocaches ArrayObject() */
    private $geocaches = null;

    /** @var $geocachesNotPublished ArrayObject() */
    private $geocachesNotPublished = null;

    /** @var $geocachesWaitApprove ArrayObject() */
    private $geocachesWaitApprove = null;

    /** @var $geocachesBlocked ArrayObject() */
    private $geocachesBlocked = null;

    /** @var DateTime */
    private $dateCreated;

    private $description;

    /** @var DateTime */
    private $lastLogin = null;
    private $isActive = null;

    private $verifyAll = null;

    /** @var boolean */
    private $statBan;

    private $permanentLogin = false;


    /* user identifier used to communication with geoKrety Api*/
    private $geokretyApiSecid;

    private $rulesConfirmed = false;
    private $watchmailMode;
    private $watchmailDay;
    private $watchmailHour;
    /** @var bool */
    private $notifyCaches;
    /** @var bool */
    private $notifyLogs;
    private $activationCode;

    const COMMON_COLUMNS = "user_id, username, founds_count, notfounds_count,
                       log_notes_count, hidden_count, latitude, longitude,
                       email, role, guru, verify_all, rules_confirmed,
                       notify_radius, watchmail_mode, watchmail_day,
                       watchmail_hour, notify_caches, notify_logs,
                       is_active_flag, stat_ban, description, activation_code,
                       date_created, last_login, uuid";

    const AUTH_COLUMNS = self::COMMON_COLUMNS . ', permanent_login_flag';

    /**
     * construct class using userId (fields will be loaded from db)
     * OR, if you have already user data row fetched from db row ($userDbRow), object is created using this data
     *
     * @param array $params
     */
    public function __construct(array $params = null)
    {
        parent::__construct();

        if (is_null($params)) {
            $params = array();
        }

        if (isset($params['fieldsStr'])) {
            //get selected columns only
            $fields = $params['fieldsStr'];
        } else {
            //default column list loaded from DB
            $fields = self::COMMON_COLUMNS;
        }

        if (isset($params['userId'])) {
            $this->userId = (int) $params['userId'];
            $this->loadDataFromDb($fields);

        } elseif (isset($params['userUuid'])) {
            $this->userUuid = $params['userUuid'];
            $this->loadDataFromDbByUuid($fields);

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
     * @param string $username
     * @param string $fields
     * @return User object or null on error
     */
    public static function fromUsernameFactory($username, $fields = null)
    {

        if (!$fields) {
            $fields = self::COMMON_COLUMNS;
        }

        $u = new self();
        if ($u->loadDataFromDbByUsername($username, $fields)) {
            return $u;
        }
        return null;
    }

    /**
     * Factory
     * @param string $email
     * @param string $fields
     * @return User object or null on error
     */
    public static function fromEmailFactory($email, $fields = null)
    {

        if (!$fields) {
            $fields = self::COMMON_COLUMNS;
        }

        $u = new self();
        if ($u->loadDataFromDbByEmail($email, $fields)) {
            return $u;
        }
        return null;
    }


    /**
     * Factory
     * @param int $userId
     * @param string $fields - comma separated list of columns to get from DB
     * @return User object or null on error
     */
    public static function fromUserIdFactory($userId, $fields = null): ?User
    {
        $u = new self();
        $u->userId = $userId;

        if (is_null($fields)) {
            $fields = self::COMMON_COLUMNS;
        }

        if ($u->loadDataFromDb($fields)) {
            return $u;
        }
        return null;
    }

    /**
     * Load extended settings from user_settings table
     */
    private function loadExtendedSettings()
    {
        $value = $this->db->multiVariableQueryValue("
            SELECT `newcaches_no_limit`
            FROM `user_settings`
            WHERE `user_id` = :1
            LIMIT 1
            ", 0, $this->userId);
        $this->newCachesNoLimit = boolval($value);
    }

    public function loadFromOKAPIRsp($okapiRow)
    {
        // load user data from row returned by OKAPI
        foreach ($okapiRow as $field => $value) {
            switch ($field) {
                case 'internal_id': // geocache owner's user ID,
                    $this->userId = (int) $value;
                    break;
                case 'uuid': // geocache owner's user UUID,
                    $this->userUuid = $value;
                    break;
                case 'username': // name of the user,
                    $this->userName = $value;
                    break;
                case 'profile_url': // URL of the user profile page,
                    $this->profileUrl = $value;
                    break;
                default:
                    Debug::errorLog("Unknown field: $field (value: $value)");
            }
        }

        $this->dataLoaded = true; //mark object as containing data

    }

    private function loadDataFromDb($fields)
    {

        $stmt = $this->db->multiVariableQuery(
            "SELECT $fields FROM `user` WHERE `user_id`=:1 LIMIT 1", $this->userId);

        if ($row = $this->db->dbResultFetchOneRowOnly($stmt)) {
            $this->setUserFieldsByUsedDbRow($row);
            return true;
        }
        return false;
    }

    private function loadDataFromDbByUuid($fields)
    {

        $stmt = $this->db->multiVariableQuery(
            "SELECT $fields FROM `user` WHERE `uuid`=:1 LIMIT 1", $this->userUuid);

        if ($row = $this->db->dbResultFetchOneRowOnly($stmt)) {
            $this->setUserFieldsByUsedDbRow($row);
            return true;
        }
        return false;
    }

    private function loadDataFromDbByUsername($username, $fields)
    {

        $stmt = $this->db->multiVariableQuery(
            "SELECT $fields FROM `user` WHERE `username`=:1 LIMIT 1", $username);

        if ($row = $this->db->dbResultFetchOneRowOnly($stmt)) {
            $this->setUserFieldsByUsedDbRow($row);
            return true;
        }
        return false;
    }

    private function loadDataFromDbByEmail($email, $fields)
    {

        $stmt = $this->db->multiVariableQuery(
            "SELECT $fields FROM `user` WHERE `email`=:1 LIMIT 1", $email);

        if ($row = $this->db->dbResultFetchOneRowOnly($stmt)) {
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
                case 'uuid':
                    $this->userUuid = $value;
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
                case 'latitude':
                case 'longitude':
                    // lat|lon are handling below
                    $cordsPresent = true;
                    break;
                case 'notify_radius':
                    $this->notifyRadius = $value;
                    break;
                case 'role':
                    $this->role = $value;
                    break;
                case 'guru':
                    $this->isGuide = boolval($value);
                    break;
                case 'log_notes_count':
                    $this->logNotesCount = (int) $value;
                    break;
                case 'verify_all':
                    $this->verifyAll = boolval($value);
                    break;
                case 'stat_ban':
                    $this->statBan = boolval($value);
                    break;
                case 'rules_confirmed':
                    $this->rulesConfirmed = boolval($value);
                    break;
                case 'date_created':
                    $this->dateCreated = new DateTime($value);
                    break;
                case 'description':
                    $this->description = $value;
                    break;
                case 'last_login':
                    if (empty($value) || $value == '0000-00-00 00:00:00') {
                        $this->lastLogin = null;
                    } else {
                        $this->lastLogin = new DateTime($value);
                    }
                    break;
                case 'is_active_flag':
                    $this->isActive = $value == User::STATUS_ACTIVE;
                    break;
                case 'watchmail_mode':
                    $this->watchmailMode = (int) $value;
                    break;
                case 'watchmail_day':
                    $this->watchmailDay = (int) $value;
                    break;
                case 'watchmail_hour':
                    $this->watchmailHour = (int) $value;
                    break;
                case 'activation_code':
                    $this->activationCode = $value;
                    break;
                case 'notify_caches':
                    $this->notifyCaches = boolval($value);
                    break;
                case 'notify_logs':
                    $this->notifyLogs = boolval($value);
                    break;
                case 'permanent_login_flag':
                    $this->permanentLogin = boolval($value);
                    break;

                /* db fields not used in this class yet*/
                case 'password':
                    // just skip it...
                    break;

                default:
                    Debug::errorLog("Unknown column: $key");
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

        $this->db->multiVariableQuery($query, $this->userId);

        $stmt = $this->db->multiVariableQuery(
            'SELECT `founds_count`, `notfounds_count`, `log_notes_count` FROM  `user` WHERE `user_id` =:1',
            $this->userId);
        $dbResult = $this->db->dbResultFetchOneRowOnly($stmt);

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
     * Global user identifier. (Used worldwide)
     * @return integer
     */
    public function getUserUuid()
    {
        return $this->userUuid;
    }

    /**
     * User email address
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getProfileUrl()
    {
        if (!$this->profileUrl) {
            $this->profileUrl = self::GetUserProfileUrl($this->getUserId());
        }
        return $this->profileUrl;
    }

    /**
     * Can user create cache without any finds (from user_settings table)
     *
     * @return boolean
     */
    public function getNewCachesNoLimit()
    {
        if (is_null($this->newCachesNoLimit)) {
            $this->loadExtendedSettings();
        }
        return $this->newCachesNoLimit;
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
     *
     * @return integer
     */
    public function getNotifyRadius()
    {
        return $this->notifyRadius;
    }

    /**
     * Returns URL of user's avatar
     * Will be developed in the future
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        return self::getDefaultAvatarUrl();
    }

    public function hasOcTeamRole()
    {
        return self::hasRole(self::ROLE_OC_TEAM);
    }

    public function hasAdvUserRole()
    {
        return self::hasRole(self::ROLE_ADV_USER);
    }

    public function hasNewsPublisherRole()
    {
        return self::hasRole(self::ROLE_NEWS_PUBLISHER) || self::hasRole(self::ROLE_OC_TEAM);
    }

    public function hasSysAdminRole()
    {
        return self::hasRole(self::ROLE_SYS_ADMIN);
    }

    public function hasRole($roleId)
    {
        return in_array(self::getRoleName($roleId), explode(',', $this->role));
    }

    public function isGuide()
    {
        return $this->isGuide;
    }

    public function areRulesConfirmed()
    {
        return $this->rulesConfirmed;
    }

    public function getGeocaches()
    {
        if ($this->geocaches === null) {
            $this->geocaches = new ArrayObject;

            $stmt = $this->db->multiVariableQuery(
                "SELECT * FROM `caches` where `user_id` = :1 ", $this->userId);

            foreach ($this->db->dbResultFetchAll($stmt) as $geocacheRow) {
                $geocache = new GeoCache();
                $geocache->loadFromRow($geocacheRow);
                $this->geocaches->append($geocache);
                if ($geocache->getStatus() === GeoCache::STATUS_NOTYETAVAILABLE) {
                    $this->appendNotPublishedGeocache($geocache);
                }
                if ($geocache->getStatus() === GeoCache::STATUS_WAITAPPROVERS) {
                    $this->appendWaitApproveGeocache($geocache);
                }
                if ($geocache->getStatus() === GeoCache::STATUS_BLOCKED) {
                    $this->appendBlockedGeocache($geocache);
                }
            }
        }
        return $this->geocaches;
    }

    public function appendNotPublishedGeocache(GeoCache $geocache)
    {
        if ($this->geocachesNotPublished === null) {
            $this->geocachesNotPublished = new ArrayObject;
        }
        $this->geocachesNotPublished->append($geocache);
    }

    public function getGeocachesNotPublished()
    {
        if ($this->geocachesNotPublished === null) {
            $this->geocachesNotPublished = new ArrayObject;
            $this->getGeocaches();
        }
        return $this->geocachesNotPublished;
    }

    public function appendWaitApproveGeocache(GeoCache $geocache)
    {
        if ($this->geocachesWaitApprove === null) {
            $this->geocachesWaitApprove = new ArrayObject;
        }
        $this->geocachesWaitApprove->append($geocache);
    }

    public function getGeocachesWaitApprove()
    {
        if ($this->geocachesWaitApprove === null) {
            $this->geocachesWaitApprove = new ArrayObject;
            $this->getGeocaches();
        }
        return $this->geocachesWaitApprove;
    }

    public function appendBlockedGeocache(GeoCache $geocache)
    {
        if ($this->geocachesBlocked === null) {
            $this->geocachesBlocked = new ArrayObject;
        }
        $this->geocachesBlocked->append($geocache);
    }

    public function getGeocachesBlocked()
    {
        if ($this->geocachesBlocked === null) {
            $this->geocachesBlocked = new ArrayObject;
            $this->getGeocaches();
        }
        return $this->geocachesBlocked;
    }

    public function getVerifyAll()
    {
        return $this->verifyAll;
    }

    public function getStatBan()
    {
        return $this->statBan;
    }

    /**
     * This function return true if user:
     * - own less than 3 (APPROVE_LIMIT) active caches
     * - has 'verify-all' flag set by COG/admins
     *
     * @return boolean
     */
    public function isUnderCacheVerification()
    {
        if ( $this->getVerifyAll() == 1 ) {
            return true;
        }

        //get published geocaches count
        $activeCachesNum = $this->db->multiVariableQueryValue(
            "SELECT COUNT(*) FROM `caches`
             WHERE `user_id` = :1 AND status = 1",
             0, $this->getUserId());

        if ($activeCachesNum < OcConfig::getMinCachesToSkipNewCacheVerification()) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if user is able to create new cache
     *
     * @return boolean
     */
    public function canCreateNewCache()
    {
        return ($this->getFoundPhysicalGeocachesCount() >= OcConfig::getMinUserFoundsForNewCache()
            || $this->getNewCachesNoLimit());
    }

    /**
     * Returns number of finds caches with physical container (used by newcache)
     *
     * @return int
     */
    public function getFoundPhysicalGeocachesCount()
    {
        if (is_null($this->foundPhysicalGeocachesCount)) {
            $this->foundPhysicalGeocachesCount = $this->db->multiVariableQueryValue("
                SELECT COUNT(`cache_logs`.`cache_id`)
                FROM `cache_logs`, `caches`
                WHERE `cache_logs`.`cache_id` = `caches`.`cache_id`
                AND `caches`.`type` IN (1, 2, 3, 7, 8)
                AND `cache_logs`.`type` = 1
                AND `cache_logs`.`deleted` = 0
                AND `cache_logs`.`user_id` = :1
                ", 0, $this->getUserId());
        }
        return $this->foundPhysicalGeocachesCount;
    }

    /**
     * This function return true if user is allowed to adopt caches
     * This means when:
     * - is not under verification
     * - has rights to create new cache
     */
    public function isAdoptionApplicable()
    {
        if ( $this->canCreateNewCache() && !$this->isUnderCacheVerification() ) {
            return true;
        }
        return false;
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

    /**
     * @return DateTime
     */
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

    public function getWatchmailMode()
    {
        return $this->watchmailMode;
    }

    public function getWatchmailDay()
    {
        return $this->watchmailDay;
    }

    public function getWatchmailHour()
    {
        return $this->watchmailHour;
    }

    public function getNotifyCaches()
    {
        return $this->notifyCaches;
    }

    public function getNotifyLogs()
    {
        return $this->notifyLogs;
    }

    public function getActivationCode()
    {
        return $this->activationCode;
    }

    public function isActive()
    {
        return $this->isActive && !empty($this->getEmail());
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function usePermanentLogin()
    {
        return $this->permanentLogin;
    }

    public function getGeokretyApiSecid()
    {
        if ($this->geokretyApiSecid === null) {
            $this->geokretyApiSecid =
            $this->db->multiVariableQueryValue(
                    'SELECT `secid` FROM `GeoKretyAPI` WHERE `userID` = :1 LIMIT 1',
                    '', $this->userId);
        }
        return $this->geokretyApiSecid;
    }

    public function getCacheWatchEmailSettings()
    {

        return $this->db->dbResultFetchOneRowOnly(
            $this->db->multiVariableQuery(
                'SELECT watchmail_mode, watchmail_hour, watchmail_day
                FROM user WHERE user_id = :1 LIMIT 1', $this->userId));
    }

    public function updateCacheWatchEmailSettings(
        $watchmail_mode, $watchmail_hour, $watchmail_day) {

        $this->db->multiVariableQuery(
            'UPDATE user SET watchmail_mode=:1, watchmail_hour=:2, watchmail_day=:3
             WHERE user_id=:4 LIMIT 1',
            $watchmail_mode, $watchmail_hour, $watchmail_day, $this->userId);

    }

    /**
     * Updates primary user's MyNeighbourhood coords
     * These coords are also "home coords" for user
     *
     * @param Coordinates $coords
     * @param int $radius
     * @return boolean
     */
    public function updateUserNeighbourhood(Coordinates $coords, $radius)
    {
        $this->homeCoordinates = $coords;
        $this->notifyRadius = $radius;
        return (null !== $this->db->multiVariableQuery('
            UPDATE `user` SET
              `latitude` = :1,
              `longitude` = :2,
              `notify_radius` = :3
            WHERE `user_id` = :4
            LIMIT 1
            ', $coords->getLatitude(), $coords->getLongitude(), (int) $radius, $this->userId));
    }

    public static function updateLastLogin($userId)
    {
        self::db()->multiVariableQuery(
            "UPDATE `user` SET `last_login` = NOW() WHERE `user_id` = :1", $userId);
    }

    /**
     * Adds new user into the DB
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param boolean $rulesConfirmed
     * @return boolean
     */
    public static function addUser($username, $password, $email, $rulesConfirmed = true): bool
    {
        return (null !== self::db()->multiVariableQuery('
            INSERT INTO `user`
                (`username`, `password`, `email`, `role`, `last_modified`,
                `is_active_flag`, `date_created`, `uuid`, `activation_code`, `node`,
                 `rules_confirmed`, `statpic_text`)
            VALUES
                (:1, :2, :3, \'\', NOW(),
                  0 , NOW(), :4, :5, :6,
                 :7, :8)',
            $username, hash('sha512', md5($password)), $email,
            Uuid::create(), TextGen::randomText(13), OcConfig::getSiteNodeId(),
            boolval($rulesConfirmed), OcConfig::getUserDefaultStatPicText()));
    }

    /**
     * Activate user (after registration)
     *
     * @param int $userId
     * @return boolean
     */
    public static function activateUser($userId)
    {
        return (null !== self::db()->multiVariableQuery('
            UPDATE `user`
            SET `is_active_flag` = 1, `activation_code`=\'\', `last_modified` = NOW()
            WHERE `user_id`= :1
            ', $userId));
    }

    /**
     * Check if user is already activated (after registration)
     *
     * @return boolean
     */
    public function isUserActivated()
    {
        return (empty($this->activationCode) || ($this->isActive));
    }

    /**
     * Sets rules_confirmed flag for user and stores it in DB
     *
     * @return boolean
     */
    public function confirmRules()
    {
        $this->rulesConfirmed = true;
        return (null !== $this->db->multiVariableQuery('
            UPDATE `user`
            SET `rules_confirmed` = 1
            WHERE `user_id` = :1
            LIMIT 1
            ', $this->getUserId()));
    }

    /**
     * Returns translations string for user last_login date
     * see 'this_month', 'more_one_month', 'more_six_month', 'more_12_month'
     * in translation files
     *
     * @return string
     */
    public function getLastLoginPeriodString()
    {
        // User has no last login date in DB - return 'unknown'
        if ($this->getLastLoginDate() === null) {
            return 'unknown';
        }

        $dateDiff = $this->getLastLoginDate()->diff(new DateTime());
        $monthsDiff = ($dateDiff->y * 12) + $dateDiff->m;

        if ($monthsDiff > 12) {
            return 'more_12_month';
        } elseif ($monthsDiff > 6) {
            return 'more_six_month';
        } elseif ($monthsDiff > 1) {
            return 'more_one_month';
        } else {
            return 'this_month';
        }
    }

    /**
     * Returns CSS class for user last_login date
     * see also getLastLoginPeriodString()
     *
     * @return string
     */
    public function getLastLoginPeriodClass()
    {
        // User has no last login date in DB
        if ($this->getLastLoginDate() === null) {
            return 'text-color-dark';
        }

        $dateDiff = $this->getLastLoginDate()->diff(new DateTime());
        $monthsDiff = ($dateDiff->y * 12) + $dateDiff->m;

        if ($monthsDiff > 12) {
            return 'text-color-danger';
        } elseif ($monthsDiff > 6) {
            return 'text-color-warning';
        } elseif ($monthsDiff > 1) {
            return 'text-color-secondary';
        } else {
            return 'text-color-success';
        }
    }


    /**
     * Returns array with statPicText and statPicLogo
     *
     * @return array
     */
    public function getStatPicDataArr()
    {
        $row = $this->db->dbResultFetchOneRowOnly(
            $this->db->multiVariableQuery(
                "SELECT statpic_text, statpic_logo FROM user WHERE user_id=:1 LIMIT 1",
                $this->getUserId()));

        return [$row['statpic_text'],$row['statpic_logo']];
    }

    /**
     * Update user statPic
     * (small banner with user statistics)
     *
     * @param int    $statPicLogo
     * @param string $statPicText
     */
    public function changeStatPic($statPicLogo, $statPicText)
    {
        $this->db->multiVariableQuery (
            "UPDATE user SET statpic_text=:1, statpic_logo=:2 WHERE user_id=:3 ",
            $statPicText, $statPicLogo, $this->getUserId());

        // delete previous statPic for the user
        $this->deleteUserStatpic();
    }

    public function deleteUserStatpic()
    {
        self::deleteStatpic($this->getUserId());
    }

    /**
     * Returns TRUE if this account is perm. locked (account is marked as removed)
     *
     * @return bool
     */
    public function isAlreadyRemoved(): bool
    {
        return boolval($this->db->multiVariableQueryValue(
            "SELECT 1 FROM user WHERE user_id = :1 AND is_active_flag = :2 LIMIT 1",
            false, $this->getUserId(), self::STATUS_REMOVED));
    }

    /**
     * Mark this account as removed
     */
    public function removeAccount(User $loggedUser): void
    {
        $dateStr = Formatter::dateTime(OcDateTime::now());
        $removerLog = sprintf("%s:%d", $loggedUser->getUserName(), $loggedUser->getUserId());
        $prefix = OcConfig::getUserRmAccountPrefix();
        $newUsername = sprintf("%s_id:%s", $prefix, $this->getUserId());
        $newEmail = sprintf("%s, %s, %s", $prefix, $removerLog, $dateStr);
        $newDescription = sprintf("%s (%s)", OcConfig::getUserRmAccountDesc(), $dateStr);

        // first remove user account
        $this->db->multiVariableQuery(
            'UPDATE user SET
                is_active_flag = :1, username = :2, email = NULL, password = :3,
                latitude = NULL, longitude = NULL, description = :4, last_modified = NOW()
            WHERE user_id = :5 LIMIT 1',
            self::STATUS_REMOVED, $newUsername, $newEmail, $newDescription, $this->getUserId());

        AdminNote::addAdminNote($loggedUser->getUserId(), $this->userId, FALSE, $newEmail);

        // and remove all specific data associated with this user account
        UserAdmin::removeUserSpecificSettings($this);
    }
}


