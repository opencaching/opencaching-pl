<?php
namespace lib\Objects\User;

use \lib\Controllers\MedalsController;
use Utils\Database\OcDb;
use lib\Objects\GeoCache\GeoCache;
use lib\Controllers\Php7Handler;

/**
 * Description of user
 *
 * @author Łza
 */
class User extends \lib\Objects\BaseObject
{

    private $userId;
    private $isAdmin;
    private $isGuide;
    private $userName;

    private $foundGeocachesCount;
    private $notFoundGeocachesCount;
    private $hiddenGeocachesCount;
    private $logNotesCount;
    private $email;

    /* @var $homeCoordinates \lib\Objects\Coordinates\Coordinates */
    private $homeCoordinates;

    private $medals = null;

    private $country;

    private $profileUrl = null;

    private $ingnoreGeocacheLimitWhileCreatingNewGeocache = false;

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

    const REGEX_USERNAME = '^[a-zA-Z0-9ęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚ@-][a-zA-ZęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚ0-9\.\-=_ @ęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚäüöÄÜÖ=)(\/\\\ -=&*+~#]{2,59}$';
    const REGEX_PASSWORD = '^[a-zA-Z0-9\.\-_ @ęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚäüöÄÜÖ=)(\/\\\$&*+~#]{3,60}$';



    /**
     * construct class using $userId (fields will be loaded from db)
     * OR, if you have already user data row fetched from db row ($userDbRow), object is created using this data
     *
     * @param type $userId - user identifier in db
     * @param type $userDbRow - array - user data taken from db, from table user.
     */
    public function __construct(array $params)
    {
        if (isset($params['userId'])) {
            $this->userId = (int) $params['userId'];

            if (isset($params['fieldsStr'])) {
                $this->loadDataFromDb($params['fieldsStr']);
            } else {
                $this->loadDataFromDb();
            }
        } elseif (isset($params['userDbRow'])) {
                $this->setUserFieldsByUsedDbRow($params['userDbRow']);
        } elseif (isset($params['okapiRow'])) {
                    $this->loadFromOKAPIRsp($params['okapiRow']);
        }
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

    public function getMedals()
    {
        // medals are not loaded in constructor - check if it is ready
        if (is_null($this->medals)) {
            // medals not loaded before - load from DB
            $this->loadMedalsFromDb();
        }
        return $this->medals;
    }

    private function loadDataFromDb($fields = null)
    {
        $db = OcDb::instance();

        if (is_null($fields)) {
            // default user fields
            $fields = "username, founds_count, notfounds_count, hidden_count, latitude, longitude, country, email, admin, guru";
        }

        $queryById = "SELECT $fields FROM `user` WHERE `user_id`=:1 LIMIT 1";

        $stmt = $db->multiVariableQuery($queryById, $this->userId);

        if ($db->rowCount($stmt) != 1) {
            //no such user found in DB?
            $this->dataLoaded = false;  //mark object as NOT containing data
            return;
        }

        $userDbRow = $db->dbResultFetchOneRowOnly($stmt);
        if ($userDbRow) {
            $this->setUserFieldsByUsedDbRow($userDbRow);
        }
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

                /* db fields not used in this class yet*/
                case 'password':
                case 'last_login':
                case 'is_active_flag':
                case 'hide_flag':
                case 'date_created':
                case 'description':
                    // just skip it...
                    break;

                default:
                    error_log(__METHOD__ . ": Unknown column: $key");
            }
        }

        // if coordinates are present set the homeCords.
        if ($cordsPresent) {
            $this->homeCoordinates = new \lib\Objects\Coordinates\Coordinates(array(
                'dbRow' => $dbRow
            ));
        }

        $this->dataLoaded = true; //mark object as containing data

    }

    public function loadMedalsFromDb(){

        $db = OcDb::instance();

        $query = 'SELECT `medal_type`, `prized_time`, `medal_level` FROM `medals` WHERE `user_id`=:1';
        $s = $db->multiVariableQuery($query, $this->userId);
        $medalsDb = $db->dbResultFetchAll($s);

        $this->medals = new \ArrayObject();
        $medalController = new MedalsController();
        foreach ($medalsDb as $medalRow) {
            $this->medals[] = $medalController->getMedal(array(
                'prizedTime' => $medalRow['prized_time'],
                'medalId' => (int) $medalRow['medal_type'],
                'level' => $medalRow['medal_level']
            ));
            // $this->medals[] = new \lib\Objects\Medals\Medal(array('prizedTime' => $medalRow['prized_time'], 'medalId' => (int) $medalRow['medal_type'], 'level' => $medalRow['medal_level']));
        }
    }

    /**
     * after delete a log it is a good idea to full recalculate stats of user, that can avoid
     * possible errors which used to appear when was calculated old method.
     *
     * by Andrzej Łza Woźniak, 10-2013
     *
     */
    public function recalculateAndUpdateStats()
    {
        $query = "
            UPDATE `user`
            SET `founds_count`   = (SELECT count(*) FROM `cache_logs` WHERE `user_id` =:1 AND `type` IN (1,7) AND `deleted` =0 ),
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
     * @return \lib\Objects\Coordinates\Coordinates object
     */
    public function getHomeCoordinates()
    {
        return $this->homeCoordinates;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    function getIsGuide()
    {
        return $this->isGuide;
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
                $this->powerTrailCompleted->append(new \lib\Objects\PowerTrail\PowerTrail(array('dbRow' => $ptRow)));
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
                $this->powerTrailOwed->append(new \lib\Objects\PowerTrail\PowerTrail(array('dbRow' => $ptRow)));
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

}
