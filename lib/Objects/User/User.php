<?php
namespace lib\Objects\User;

use \lib\Controllers\MedalsController;
use \lib\Database\DataBaseSingleton;

/**
 * Description of user
 *
 * @author Łza
 */
class User extends \lib\Objects\BaseObject
{

    private $userId;

    private $userName;

    private $foundGeocachesCount;

    private $notFoundGeocachesCount;

    private $hiddenGeocachesCount;

    private $email;

    /* @var $homeCoordinates \lib\Objects\Coordinates\Coordinates */
    private $homeCoordinates;

    private $medals = null;

    private $country;

    private $profileUrl = null;

    private $ingnoreGeocacheLimitWhileCreatingNewGeocache = false;

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
        $db = DataBaseSingleton::Instance();
        $queryById = "SELECT `newcaches_no_limit` AS ingnoreGeocacheLimitWhileCreatingNewGeocache"
                   . "FROM `user_settings` WHERE `user_id` = :1 LIMIT 1";
        $db->multiVariableQuery($queryById, $this->userId);
        $dbRow = $db->dbResultFetch();
        $db->reset();
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
                    $this->userId = $value;
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
        $db = \lib\Database\DataBaseSingleton::Instance();

        if (is_null($fields)) {
            // default user fields
            $fields = "username, founds_count, notfounds_count, hidden_count, latitude, longitude, country, email";
        }

        $queryById = "SELECT $fields FROM `user` WHERE `user_id`=:1 LIMIT 1";

        $db->multiVariableQuery($queryById, $this->userId);

        if ($db->rowCount() != 1) {
            //no such user found in DB?
            $this->dataLoaded = false;  //mark object as NOT containing data
            return;
        }

        $userDbRow = $db->dbResultFetch();
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
                    $this->userId = $value;
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

    public function loadMedalsFromDb()
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $query = 'SELECT `medal_type`, `prized_time`, `medal_level` FROM `medals` WHERE `user_id`=:1';
        $db->multiVariableQuery($query, $this->userId);
        $medalsDb = $db->dbResultFetchAll();
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

    public function getUserId()
    {
        return $this->userId;
    }

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
}
