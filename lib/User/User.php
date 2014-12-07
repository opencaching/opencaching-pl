<?php

namespace lib\User;

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
    private $medals;

    /**
     * construct class using $userId (fields will be loaded from db)
     * OR, if you have already user data row fetched from db row ($userDbRow), object is created using this data
     *
     * @param type $userId - user identifier in db
     * @param type $userDbRow - array - user data taken from db, from table user.
     */
    public function __construct($userId, $userDbRow = null) {
        if($userId && $userDbRow === null) {
            $this->userId = (int) $userId;
            $this->loadUserDataFromDb();
        } else {
            $this->userId = (int) $userDbRow['user_id'];
            $this->setUserFieldsByUsedDbRow($userDbRow);
        }
        $this->buildMedals();
    }

    public function getMedals() {
        return $this->medals;
    }

    private function loadUserDataFromDb() {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $queryById = "SELECT username, founds_count, notfounds_count, hidden_count FROM `user` WHERE `user_id`=:1 LIMIT 1";
        $db->multiVariableQuery($queryById, $this->userId);
        $userDbRow = $db->dbResultFetch();
        $this->setUserFieldsByUsedDbRow($userDbRow);
    }

    private function setUserFieldsByUsedDbRow($userDbRow){
        $this->userName = $userDbRow['username'];
        $this->foundGeocachesCount = $userDbRow['founds_count'];
        $this->notFoundGeocachesCount = $userDbRow['notfounds_count'];
        $this->hiddenGeocachesCount = $userDbRow['hidden_count'];
    }

    private function buildMedals(){
        $db = \lib\Database\DataBaseSingleton::Instance();
        $query = 'SELECT `medal_type`, `prized_time` FROM `medals` WHERE `user_id`=:1';
        $db->multiVariableQuery($query, $this->userId);
        $medalsDb = $db->dbResultFetchAll();
        $this->medals = new \ArrayObject;
        foreach ($medalsDb as $medalRow) {
            $this->medals[] = new \lib\Medals\Medal(array('prizedTime' => $medalRow['prized_time'], 'type' => (int) $medalRow['medal_type']));
        }
    }

    public function getUserId() {
        return $this->userId;
    }

}
