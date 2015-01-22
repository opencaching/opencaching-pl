<?php

namespace lib\Objects\User;

/**
 * Description of user
 *
 * @author Łza
 */
class User
{

    private $userId;
    private $userName;
    private $foundGeocachesCount;
    private $notFoundGeocachesCount;
    private $hiddenGeocachesCount;
    private $email;

    /* @var $homeCoordinates \lib\Objects\Coordinates\Coordinates */
    private $homeCoordinates;
    private $medals;
    private $country;

    /**
     * construct class using $userId (fields will be loaded from db)
     * OR, if you have already user data row fetched from db row ($userDbRow), object is created using this data
     *
     * @param type $userId - user identifier in db
     * @param type $userDbRow - array - user data taken from db, from table user.
     */
    public function __construct($userId, $userDbRow = null)
    {
        if ($userId && $userDbRow === null) {
            $this->userId = (int) $userId;
            $this->loadUserDataFromDb();
        } else {
            $this->userId = (int) $userDbRow['user_id'];
            $this->setUserFieldsByUsedDbRow($userDbRow);
        }
        $this->buildMedals();
    }

    public function getMedals()
    {
        return $this->medals;
    }

    private function loadUserDataFromDb()
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $queryById = "SELECT username, founds_count, notfounds_count, hidden_count, latitude, longitude, country, email FROM `user` WHERE `user_id`=:1 LIMIT 1";
        $db->multiVariableQuery($queryById, $this->userId);
        $userDbRow = $db->dbResultFetch();
        $this->setUserFieldsByUsedDbRow($userDbRow);
    }

    private function setUserFieldsByUsedDbRow($userDbRow)
    {
        $this->userName = $userDbRow['username'];
        $this->foundGeocachesCount = $userDbRow['founds_count'];
        $this->notFoundGeocachesCount = $userDbRow['notfounds_count'];
        $this->hiddenGeocachesCount = $userDbRow['hidden_count'];
        $this->homeCoordinates = new \lib\Objects\Coordinates\Coordinates($userDbRow);
        $this->email = $userDbRow['email'];
    }

    private function buildMedals()
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $query = 'SELECT `medal_type`, `prized_time`, `medal_level` FROM `medals` WHERE `user_id`=:1';
        $db->multiVariableQuery($query, $this->userId);
        $medalsDb = $db->dbResultFetchAll();
        $this->medals = new \ArrayObject;
        foreach ($medalsDb as $medalRow) {
            $this->medals[] = new \lib\Objects\Medals\Medal(array('prizedTime' => $medalRow['prized_time'], 'medalId' => (int) $medalRow['medal_type'], 'level' => $medalRow['medal_level']));
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

}
