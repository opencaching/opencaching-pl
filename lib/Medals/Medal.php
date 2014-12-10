<?php

namespace lib\Medals;

/**
 * Description of medal
 *
 * @author Łza
 */
class Medal
{

    private $typeId;
    private $image;
    private $name;
    protected $dateIntroduced;
    protected $conditions;
    protected $prizedTime = false;
    /* must be a instance of \lib\Medals\OcConfig */
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
        $details = medalsController::getMedalTypeDetails($this->typeId);
        $this->conditions = $details['conditions'];
        $this->name = $details['name'];
        $this->dateIntroduced = $details['dateIntroduced'];
        if (isset($params['prizedTime'])) {
            $this->prizedTime = $params['prizedTime'];
        }
        $this->setImage();
        $this->config = \lib\Medals\OcConfig::Instance();
    }

    protected function checkConditionsForUser(\lib\User\User $user)
    {

    }

    /**
     * store in db (or remove) medal awerded for specified user.
     * @param \lib\user $user
     */
    protected function storeMedalStatus(\lib\User\User $user)
    {
        $alreadyPrized = $this->isCurrentMedalAlreadyPrized($user);
        if ($this->prizedTime === false && $alreadyPrized) { /* user has no medal, remove it from db */
            $this->removeMedalFromUsersMedalsDb($user);
        } elseif (!$alreadyPrized && $this->prizedTime) { /* user win medal now, store it in db */
            $this->addMedalToUserMedalsDb($user);
        }
    }

    private function removeMedalFromUsersMedalsDb(\lib\User\User $user)
    {
        $query = 'DELETE FROM `medals` WHERE `user_id` = :1 AND `medal_type` = :2';
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $user->getUserId(), $this->typeId);
    }

    private function addMedalToUserMedalsDb(\lib\User\User $user)
    {
        $query = 'INSERT INTO `medals`(`user_id`, `medal_type`, `prized_time`) VALUES (:1, :2, :3)';
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $user->getUserId(), $this->typeId, $this->prizedTime);
    }

    private function isCurrentMedalAlreadyPrized(\lib\User\User $user)
    {
        $userMedals = $user->getMedals();
//        if(!is_object($userMedals)){
//            return false;
//        }
        $iterator = $userMedals->getIterator();
        /* @var $currentMedal \medals\medal */
        while ($iterator->valid()) {
            $currentMedal = $iterator->current();
            if ($currentMedal->getTypeId() === $this->typeId) { /* current medal */
                return true;
            }
            $iterator->next();
        }
        return false;
    }

    private function setImage()
    {
        $medalsLayout = new \tpl\stdstyle\lib\MedalsLayout();
        $this->image = $medalsLayout->getImage($this->typeId);
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getTypeId()
    {
        return $this->typeId;
    }

}
