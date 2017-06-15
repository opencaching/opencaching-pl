<?php

namespace lib\Objects\PowerTrail;

use Utils\Database\OcDb;
use lib\Objects\User\User;
use lib\Objects\PowerTrail\PowerTrail;
use Utils\Generators\Uuid;

class Log
{
    const TYPE_COMMENT = 1;
    const TYPE_CONQUESTED = 2;
    const TYPE_OPENING = 3;
    const TYPE_DISABLING = 4;
    const TYPE_CLOSING = 5;
    const TYPE_ADD_WARNING = 6;

    private $id = false;

    private $type;

    /* @var $powerTrail PowerTrail */
    private $powerTrail;

    /**
     * @var User
     */
    private $user;

    /*@var $dateTime \DateTime */
    private $dateTime;

    private $text;

    private $isDeleted = false;

    public function getPowerTrail()
    {
        return $this->powerTrail;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    public function setPowerTrail(PowerTrail $powerTrail)
    {
        $this->powerTrail = $powerTrail;
        return $this;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    public function setDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function isDeleted()
    {
        return $this->isDeleted;
    }

    function getType()
    {
        return $this->type;
    }

    function setType($type)
    {
        $this->type = $type;
        return $this;
    }


    public function storeInDb()
    {
        $db = OcDb::instance();
        if($_REQUEST['type'] == Log::TYPE_CONQUESTED && $this->powerTrail->isAlreadyConquestedByUser($this->user)){ /* atempt to add second 'conquested' log */
            return false;
        }
        if($this->id){
            ddd('TODO');
        } else {
            if($this->type === self::TYPE_ADD_WARNING && $this->user->getIsAdmin() === false){
                return false; /* regular user is not allowed to add entery of this type */
            }
            $query = 'INSERT INTO `PowerTrail_comments`
                      (`userId`, `PowerTrailId`, `commentType`, `commentText`,
                       `logDateTime`, `dbInsertDateTime`, `deleted`, uuid)
                      VALUES (:1, :2, :3, :4, :5, NOW(),0, '.Uuid::getSqlForUpperCaseUuid().')';
            $db->multiVariableQuery($query, $this->user->getUserId(), $this->powerTrail->getId(), $this->type, $this->text, $this->dateTime->format('Y-m-d H:i:s'));
            if($this->type == self::TYPE_CONQUESTED){
                $this->powerTrail->increaseConquestedCount();
            }
        }
        $this->changePowerTrailStatusAfterLog();
        return true;
    }

    private function changePowerTrailStatusAfterLog()
    {
        $expectedPowerTarilStatus = $this->getPowerTrailStatusByLogType();
        if($expectedPowerTarilStatus && $this->powerTrail->getStatus() != $expectedPowerTarilStatus){ // update powerTrail status
            if($this->type === self::TYPE_OPENING && $this->powerTrail->canBeOpened() === false){ // power Trail do not meet critertia to be opened.
                return;
            }
            $this->powerTrail->setAndStoreStatus($expectedPowerTarilStatus);
        }
    }

    private function getPowerTrailStatusByLogType()
    {
        switch ($this->type) {
            case self::TYPE_CLOSING:
                return PowerTrail::STATUS_CLOSED;
            case self::TYPE_DISABLING:
                return PowerTrail::STATUS_INSERVICE;
            case self::TYPE_OPENING:
                return PowerTrail::STATUS_OPEN;
            case self::TYPE_COMMENT:
            case self::TYPE_CONQUESTED:
            default:
                return false;
        }
    }

}
