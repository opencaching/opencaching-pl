<?php

namespace lib\Objects\Medals;

/**
 * Description of medal
 *
 * @author Łza
 */
class Medal
{

    private $typeId;
    protected $level;
    private $image;
    private $name;
    protected $dateIntroduced;
    protected $conditions;
    protected $prizedTime = false;
    /* must be a instance of \lib\Objects\Medals\OcConfig */
    protected $config = null;

    /**
     *
     * @param array $params require:
     * 'prizedTime' => (datetime) date time when user were awarded with this medal
     * 'type' => medal type
     */
    public function __construct($params)
    {
        $this->typeId = $params['type'];
        $details = \lib\Controllers\MedalsController::getMedalTypeDetails($this->typeId);
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

    protected function checkConditionsForUser(\lib\Objects\User\User $user)
    {

    }

    /**
     * store in db (or remove) medal awerded for specified user.
     * @param \lib\user $user
     */
    protected function storeMedalStatus(\lib\Objects\User\User $user)
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
     * @param type $param
     */
    protected function buildCacheTypesSqlString()
    {
        $sqlString = '';
        foreach ($this->conditions['cacheType'] as $cacheType) {
            $sqlString .= $cacheType . ',';
        }
        return rtrim($sqlString, ',');
    }

    protected function setMedalPrizedTimeAndAcheivedLevel($level)
    {
        $this->prizedTime = date($this->config->getDbDateTimeFormat());
        $this->level = $level;
    }

    private function updateMedalRowInDb(\lib\Objects\User\User $user)
    {
        $query = ' UPDATE `medals` SET `prized_time`= NOW(), `medal_level`=:1 WHERE  `user_id` = :2 AND `medal_type` = :3 ';
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $this->level, $user->getUserId(), $this->typeId);
    }

    private function removeMedalFromUsersMedalsDb(\lib\Objects\User\User $user)
    {
        $query = 'DELETE FROM `medals` WHERE `user_id` = :1 AND `medal_type` = :2';
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $user->getUserId(), $this->typeId);
    }

    private function addMedalToUserMedalsDb(\lib\Objects\User\User $user)
    {
        $query = 'INSERT INTO `medals`(`user_id`, `medal_type`, `prized_time`, `medal_level`) VALUES (:1, :2, :3, :4)';
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $user->getUserId(), $this->typeId, $this->prizedTime, $this->level);
    }

    private function isCurrentMedalAlreadyPrized(\lib\Objects\User\User $user)
    {
        $userMedals = $user->getMedals();
        $iterator = $userMedals->getIterator();
        /* @var $currentMedal \medals\medal */
        while ($iterator->valid()) {
            $currentMedal = $iterator->current();
            if ($currentMedal->getTypeId() === $this->typeId) { /* current medal */
                return $currentMedal;
            }
            $iterator->next();
        }
        return false;
    }

    private function setImage()
    {
        $medalsLayout = new \tpl\stdstyle\lib\MedalsLayout();
        $this->image = $medalsLayout->getImage($this->typeId, $this->level);
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getTypeId()
    {
        return $this->typeId;
    }

    public function getLevel()
    {
        return $this->level;
    }

}
