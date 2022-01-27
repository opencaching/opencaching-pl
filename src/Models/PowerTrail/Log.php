<?php

namespace src\Models\PowerTrail;

use DateTime;
use src\Models\User\User;
use src\Utils\Database\OcDb;
use src\Utils\Generators\Uuid;

class Log
{
    public const TYPE_COMMENT = 1;

    public const TYPE_CONQUESTED = 2;

    public const TYPE_OPENING = 3;

    public const TYPE_DISABLING = 4;

    public const TYPE_CLOSING = 5;

    public const TYPE_ADD_WARNING = 6;

    private int $type;

    private PowerTrail $powerTrail;

    private User $user;

    private DateTime $dateTime;

    private string $text;

    private bool $isDeleted = false;

    public function getPowerTrail(): PowerTrail
    {
        return $this->powerTrail;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setPowerTrail(PowerTrail $powerTrail): Log
    {
        $this->powerTrail = $powerTrail;

        return $this;
    }

    public function setUser(User $user): Log
    {
        $this->user = $user;

        return $this;
    }

    public function setDateTime(DateTime $dateTime): Log
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function setDeleted($isDeleted): Log
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function setText($text): Log
    {
        $this->text = $text;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): Log
    {
        $this->type = $type;

        return $this;
    }

    public function storeInDb(): bool
    {
        $db = OcDb::instance();

        if ($_REQUEST['type'] == Log::TYPE_CONQUESTED && $this->powerTrail->isAlreadyConquestedByUser($this->user)) { // attempt to add second 'conquested' log
            return false;
        }

        if ($this->type === self::TYPE_ADD_WARNING && $this->user->hasOcTeamRole() === false) {
            return false; // regular user is not allowed to add entry of this type
        }
        $query = 'INSERT INTO `PowerTrail_comments`
                      (`userId`, `PowerTrailId`, `commentType`, `commentText`,
                       `logDateTime`, `dbInsertDateTime`, `deleted`, uuid)
                      VALUES (:1, :2, :3, :4, :5, NOW(),0, ' . Uuid::getSqlForUpperCaseUuid() . ')';
        $db->multiVariableQuery($query, $this->user->getUserId(), $this->powerTrail->getId(), $this->type, $this->text, $this->dateTime->format('Y-m-d H:i:s'));

        if ($this->type == self::TYPE_CONQUESTED) {
            $this->powerTrail->increaseConquestedCount();
        }

        $this->changePowerTrailStatusAfterLog();

        return true;
    }

    private function changePowerTrailStatusAfterLog()
    {
        $expectedPowerTrailStatus = $this->getPowerTrailStatusByLogType();

        if ($expectedPowerTrailStatus && $this->powerTrail->getStatus() != $expectedPowerTrailStatus) { // update powerTrail status
            if ($this->type === self::TYPE_OPENING && $this->powerTrail->canBeOpened() === false) { // power Trail do not meet criteria to be opened.
                return;
            }
            $this->powerTrail->setAndStoreStatus($expectedPowerTrailStatus);
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
