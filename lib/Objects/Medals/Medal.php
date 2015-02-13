<?php

namespace lib\Objects\Medals;

use \lib\Objects\User\User;
use \lib\Database\DataBaseSingleton;
/**
 * Medal Factory - abstract interface
 *
 * @author Andrzej 'Łza' Woźniak
 */
class Medal
{

    private $medalId;
    protected $level;
    private $image;
    private $name;
    protected $dateIntroduced;
    protected $conditions;
    protected $prizedTime = false;
    /* must be a instance of  \lib\Objects\OcConfig\OcConfig */
    protected $config = null;

    /**
     *
     * @param array $params require:
     * 'prizedTime' => (datetime) date time when user were awarded with this medal
     * 'type' => medal type
     */
    public function __construct($params)
    {
        $this->medalId = $params['medalId'];
        $details = \lib\Controllers\MedalsController::getMedalConfigByMedalId($this->medalId);
        $this->conditions = $details['conditions'];
        $this->name = $details['name'];
        $this->dateIntroduced = $details['dateIntroduced'];
        if (isset($params['prizedTime'])) {
            $this->prizedTime = $params['prizedTime'];
            $this->level = (int) $params['level'];
            $this->setImage();
        }
        $this->config = \lib\Objects\OcConfig\OcConfig::Instance();
    }

    protected function checkConditionsForUser(User $user)
    {

    }

    protected function getLevelInfo($level)
    {

    }

    /**
     * store in db (or remove) medal awarded for specified user.
     * @param User $user
     */
    protected function storeMedalStatus(User $user)
    {
        $alreadyPrized = $this->isCurrentMedalAlreadyPrized($user);
        if ($this->prizedTime === false && $alreadyPrized) { /* user has no medal, remove it from db */
            $this->removeMedalFromUsersMedalsDb($user);
        } elseif (!$alreadyPrized && $this->prizedTime) { /* user win medal now, store it in db */
            $this->addMedalToUserMedalsDb($user);
        }
        if ($alreadyPrized && $alreadyPrized->getLevel() !== $this->level) { /* user win medal before, but now acheived other medal level. update it in db */
            $this->updateMedalRowInDb($user);
        }
    }

    /**
     * generate string witch cache types to be used in sql query
     */
    protected function buildCacheTypesSqlString()
    {
        $sqlString = '';
        foreach ($this->conditions['cacheType'] as $cacheType) {
            $sqlString .= $cacheType . ',';
        }
        return rtrim($sqlString, ',');
    }

    /**
     * generate string witch cache types to be used in sql query
     */
    protected function buildCacheStatusSqlString()
    {
        $sqlString = '';
        foreach ($this->conditions['placedCacheStatus'] as $cacheStatus) {
            $sqlString .= $cacheStatus . ',';
        }
        return rtrim($sqlString, ',');
    }

    protected function setMedalPrizedTimeAndAcheivedLevel($level)
    {
        $this->prizedTime = date($this->config->getDbDateTimeFormat());
        $this->level = $level;
    }

    private function updateMedalRowInDb(User $user)
    {
        $query = ' UPDATE `medals` SET `prized_time`= NOW(), `medal_level`=:1 WHERE  `user_id` = :2 AND `medal_type` = :3 ';
        $db = DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $this->level, $user->getUserId(), $this->medalId);
    }

    private function removeMedalFromUsersMedalsDb(User $user)
    {
        $query = 'DELETE FROM `medals` WHERE `user_id` = :1 AND `medal_type` = :2';
        $db = DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $user->getUserId(), $this->medalId);
    }

    private function addMedalToUserMedalsDb(User $user)
    {
        $query = 'INSERT INTO `medals`(`user_id`, `medal_type`, `prized_time`, `medal_level`) VALUES (:1, :2, :3, :4)';
        $db = DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $user->getUserId(), $this->medalId, $this->prizedTime, $this->level);
    }

    private function isCurrentMedalAlreadyPrized(User $user)
    {
        $userMedals = $user->getMedals();
        $iterator = $userMedals->getIterator();
        /* @var $currentMedal \lib\Objects\Medals\Medal */
        while ($iterator->valid()) {
            $currentMedal = $iterator->current();
            if ($currentMedal->getMedalId() === $this->medalId) { /* current medal */
                return $currentMedal;
            }
            $iterator->next();
        }
        return false;
    }

    private function setImage()
    {
        $directory = 'tpl/stdstyle/medals/';
        $path = $directory . $this->medalId.'/'.$this->level.'.png';
        if(file_exists($path)){
            $this->image = $path;
        } else {
           $this->image = $directory . 'medalGeneric.png';
        }
    }

    /**
     * For medals, based on cache placed count and cache found count.
     * Iterate through cacheCountToAward conditions, and set proper medal level
     * when conditions are met.
     * @param integer $foundCount
     * @param integer $placedCount
     */
    protected function findMedalLevelByCacheCount($foundCount, $placedCount)
    {
        $levelSummary = array();
        foreach ($this->conditions['cacheCountToAward'] as $level => $conditions) {
            if ($foundCount >= $conditions['cacheCount']['found']) {
                $foundConditionPassed[$level] = true;
            } else {
                $foundConditionPassed[$level] = false;
            }
            if ($placedCount >= $conditions['cacheCount']['placed']) {
                $placedConditionPassed[$level] = true;
            } else {
                $placedConditionPassed[$level] = false;
            }
            $levelSummary[$level] = ($placedConditionPassed[$level] && $foundConditionPassed[$level]);
            if ($levelSummary[$level] === true) {
                $this->setMedalPrizedTimeAndAcheivedLevel($level);
            }
        }
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getMedalId()
    {
        return $this->medalId;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLevelName($level = null){
        if($level === null){
            $level = $this->level;
        }
        return $this->conditions['cacheCountToAward'][$level]['levelName'];
    }

}
