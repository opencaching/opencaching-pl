<?php /** @noinspection PhpUnused */

namespace src\Models\GeoCache;

use DateTime;
use Exception;
use okapi\Facade;
use src\Controllers\MeritBadgeController;
use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Utils\Email\EmailSender;
use src\Utils\Generators\Uuid;
use src\Utils\Text\Formatter;

class GeoCacheLog extends GeoCacheLogCommons
{

    /** @var int */
    private $id;

    /** @var int */
    private $geoCacheId;

    /** @var GeoCache */
    private $geoCache;

    /** @var int */
    private $userId;

    /** @var User */
    private $user;

    /** @var int */
    private $type;

    /** @var DateTime */
    private $date;

    /** @var string */
    private $text;

    /** @var bool */
    private $textHtml;

    /** @var DateTime */
    private $lastModified;

    /** @var DateTime */
    private $okapiSyncbase;

    /** @var string */
    private $uuid;

    /** @var int */
    private $picturesCount;

    /** @var int */
    private $mp3count;

    /** @var DateTime */
    private $dateCreated;

    /** @var bool */
    private $ownerNotified;

    /** @var int */
    private $node;

    /** @var bool */
    private $deleted;

    /** @var int|null */
    private $delByUserId;

    /** @var DateTime|null */
    private $lastDeleted;

    /** @var int|null */
    private $editByUserId;

    /** @var int */
    private $editCount;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getGeoCacheId(): int
    {
        return $this->geoCacheId;
    }

    /**
     * @return GeoCache
     */
    public function getGeoCache(): GeoCache
    {
        if (empty($this->geoCache)) {
            $this->geoCache = GeoCache::fromCacheIdFactory($this->getGeoCacheId());
        }

        return $this->geoCache;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        if (empty($this->user)) {
            $this->user = User::fromUserIdFactory($this->getUserId());
        }

        return $this->user;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function getTextHtml(): bool
    {
        return $this->textHtml;
    }

    /**
     * @return DateTime
     */
    public function getLastModified(): DateTime
    {
        return $this->lastModified;
    }

    /**
     * @return DateTime
     */
    public function getOkapiSyncbase(): DateTime
    {
        return $this->okapiSyncbase;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return int
     */
    public function getPicturesCount(): int
    {
        return $this->picturesCount;
    }

    /**
     * @return int
     */
    public function getMp3count(): int
    {
        return $this->mp3count;
    }

    /**
     * @return DateTime
     */
    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @return bool
     */
    public function getOwnerNotified(): bool
    {
        return $this->ownerNotified;
    }

    /**
     * @return int
     */
    public function getNode(): int
    {
        return $this->node;
    }

    /**
     * @return bool
     */
    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return int|null
     */
    public function getDelByUserId(): ?int
    {
        return $this->delByUserId;
    }

    /**
     * @return DateTime|null
     */
    public function getLastDeleted(): ?DateTime
    {
        return $this->lastDeleted;
    }

    /**
     * @return int|null
     */
    public function getEditByUserId(): ?int
    {
        return $this->editByUserId;
    }

    /**
     * @return int
     */
    public function getEditCount(): int
    {
        return $this->editCount;
    }

    /**
     * Returns translation key for log type
     *
     * @return string
     */
    public function getTypeTranslationKey(): string
    {
        return self::typeTranslationKey($this->getType());
    }

    /**
     * Return URL of the log object
     *
     * @return string
     */
    public function getLogUrl(): string
    {
        return parent::getLogUrlByLogId($this->getId());
    }

    /**
     * Returns URL of the log icon
     *
     * @return string
     */
    public function getLogIcon(): string
    {
        return parent::GetIconForType($this->getType());
    }

    /**
     * Returns true if $userid recommended cache related with log
     *
     * @param User $user
     * @return boolean
     */
    public function isRecommendedByUser(User $user): bool
    {
        $params = [];
        $params['cacheid']['value'] = $this->getGeoCacheId();
        $params['cacheid']['data_type'] = 'integer';
        $params['userid']['value'] = $user->getUserId();
        $params['userid']['data_type'] = 'integer';
        $query = '
            SELECT COUNT(*)
            FROM `cache_rating`
            WHERE `cache_id` = :cacheid
              AND `user_id` = :userid
        ';
        return (bool)$this->db->paramQueryValue($query, false, $params);
    }

    /**
     * @param $logId
     * @return GeoCacheLog
     */
    public function setId(int $logId): GeoCacheLog
    {
        $this->id = $logId;

        return $this;
    }

    /**
     * @param int $geoCacheId
     * @return GeoCacheLog
     */
    public function setGeoCacheId(int $geoCacheId): GeoCacheLog
    {
        $this->geoCacheId = $geoCacheId;
        $this->geoCache = null;

        return $this;
    }

    /**
     * @param GeoCache $geoCache
     * @return GeoCacheLog
     */
    public function setGeoCache(GeoCache $geoCache): GeoCacheLog
    {
        $this->geoCache = $geoCache;
        $this->geoCacheId = $geoCache->getCacheId();

        return $this;
    }

    /**
     * @param int $userId
     * @return GeoCacheLog
     */
    public function setUserId(int $userId): GeoCacheLog
    {
        $this->userId = $userId;
        $this->user = null;

        return $this;
    }

    /**
     * @param User $user
     * @return GeoCacheLog
     */
    public function setUser(User $user): GeoCacheLog
    {
        $this->user = $user;
        $this->userId = $user->getUserId();
        return $this;
    }

    /**
     * @param int $type
     * @return GeoCacheLog
     */
    public function setType(int $type): GeoCacheLog
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param DateTime $date
     * @return GeoCacheLog
     */
    public function setDate(DateTime $date): GeoCacheLog
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @param string|null $text
     * @return GeoCacheLog
     */
    public function setText(?string $text): GeoCacheLog
    {
        $this->text = ($text != null) ? $text : '';
        return $this;
    }

    /**
     * @param bool $textHtml
     * @return GeoCacheLog
     */
    public function setTextHtml(bool $textHtml): GeoCacheLog
    {
        $this->textHtml = $textHtml;
        return $this;
    }

    /**
     * @param DateTime $lastModified
     * @return GeoCacheLog
     */
    public function setLastModified(DateTime $lastModified): GeoCacheLog
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     * @param DateTime $okapiSyncbase
     * @return GeoCacheLog
     */
    public function setOkapiSyncbase(DateTime $okapiSyncbase): GeoCacheLog
    {
        $this->okapiSyncbase = $okapiSyncbase;
        return $this;
    }

    /**
     * @param string $uuid
     * @return GeoCacheLog
     */
    public function setUuid(string $uuid): GeoCacheLog
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @param int $picturesCount
     * @return GeoCacheLog
     */
    public function setPicturesCount(int $picturesCount): GeoCacheLog
    {
        $this->picturesCount = $picturesCount;
        return $this;
    }

    /**
     * @param int $mp3count
     * @return GeoCacheLog
     */
    public function setMp3count(int $mp3count): GeoCacheLog
    {
        $this->mp3count = $mp3count;
        return $this;
    }

    /**
     * @param DateTime $dateCreated
     * @return GeoCacheLog
     */
    public function setDateCreated(DateTime $dateCreated): GeoCacheLog
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @param bool $ownerNotified
     * @return GeoCacheLog
     */
    public function setOwnerNotified(bool $ownerNotified): GeoCacheLog
    {
        $this->ownerNotified = $ownerNotified;
        return $this;
    }

    /**
     * @param int $node
     * @return GeoCacheLog
     */
    public function setNode(int $node): GeoCacheLog
    {
        $this->node = $node;
        return $this;
    }

    /**
     * @param bool $deleted
     * @return GeoCacheLog
     */
    public function setDeleted(bool $deleted): GeoCacheLog
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @param int|null $delByUserId
     * @return GeoCacheLog
     */
    public function setDelByUserId(?int $delByUserId): GeoCacheLog
    {
        $this->delByUserId = $delByUserId;
        return $this;
    }

    /**
     * @param int|null $editByUserId
     * @return GeoCacheLog
     */
    public function setEditByUserId(?int $editByUserId): GeoCacheLog
    {
        $this->editByUserId = $editByUserId;
        return $this;
    }

    /**
     * @param int $editCount
     * @return GeoCacheLog
     */
    public function setEditCount(int $editCount): GeoCacheLog
    {
        $this->editCount = $editCount;
        return $this;
    }

    /**
     * @param DateTime|null $lastDeleted
     * @return GeoCacheLog
     */
    public function setLastDeleted(?DateTime $lastDeleted): GeoCacheLog
    {
        $this->lastDeleted = $lastDeleted;
        return $this;
    }

    /**
     * @param int $logId
     * @throws Exception
     */
    private function loadByLogId(int $logId)
    {

        //find log by Id
        $s = $this->db->multiVariableQuery(
            "SELECT * FROM cache_logs WHERE id = :1 LIMIT 1", $logId);

        $logDbRow = $this->db->dbResultFetchOneRowOnly($s);

        if (is_array($logDbRow)) {
            $this->loadFromDbRow($logDbRow);
        } else {
            throw new Exception("No such cache_log");
        }
    }

    private function loadFromDbRow($row)
    {
        $this
            ->setId($row['id'])
            ->setGeoCacheId($row['cache_id'])
            ->setUserId($row['user_id'])
            ->setType($row['type'])
            ->setDate(new DateTime($row['date']))
            ->setText($row['text'])
            ->setTextHtml($row['text_html'])
            ->setLastModified(new DateTime($row['last_modified']))
            ->setOkapiSyncbase(new DateTime($row['okapi_syncbase']))
            ->setUuid($row['uuid'])
            ->setPicturesCount($row['picturescount'])
            ->setMp3count($row['mp3count'])
            ->setDateCreated(new DateTime($row['date_created']))
            ->setOwnerNotified($row['owner_notified'])
            ->setNode($row['node'])
            ->setDeleted($row['deleted'])
            ->setDelByUserId($row['del_by_user_id'])
            ->setEditByUserId($row['edit_by_user_id'])
            ->setEditCount($row['edit_count']);
        if (!empty($row['last_deleted'])) {
            $this->setLastDeleted(new DateTime($row['last_deleted']));
        } else {
            $this->setLastDeleted(null);
        }
    }

    /**
     * Create GeoCacheLog object based on logId
     *
     * @param int $logId
     * @return GeoCacheLog|null
     */
    public static function fromLogIdFactory(int $logId): ?GeoCacheLog
    {
        $obj = new self();
        try {
            $obj->loadByLogId($logId);
            return $obj;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param array $row
     * @return GeoCacheLog|null
     */
    public static function fromDbRowFactory(array $row): ?GeoCacheLog
    {
        $obj = new self();
        try {
            $obj->loadFromDbRow($row);
            return $obj;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Check if log can be reverted.
     * Returns true if:
     * - log is deleted
     * - if log type is "found" | "attended" | "will attend" - there is not
     *   an another active log in this type (should be only one)
     *
     * @return bool
     */
    public function canBeReverted(): bool
    {
        if (!$this->getDeleted()) {
            return false; //log is NOT deleted
        }
        if (in_array($this->getType(),
            [GeoCacheLog::LOGTYPE_FOUNDIT,
                GeoCacheLog::LOGTYPE_ATTENDED,
                GeoCacheLog::LOGTYPE_WILLATTENDED])) {
            // There can be only one log "found", "attended", "will attend"
            return (!$this->getGeoCache()->hasUserLogByType($this->getUser(), $this->getType()));
        }
        return true;
    }

    /**
     * Inserts new log into the DB
     * If empty/null $date, current datetime will be used
     *
     * @param int $cacheId
     * @param int $userId
     * @param int $logType
     * @param string $text
     * @param DateTime|null $date
     * @throws Exception
     */
    public static function newLog(int $cacheId, int $userId, int $logType, string $text, DateTime $date = null)
    {
        if (is_null($date)) {
            $date = new DateTime();
        }
        $uuid = Uuid::create();

        self::db()->multiVariableQuery(
            'INSERT INTO `cache_logs`
                (`cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `last_modified`, `uuid`, `date_created`, `node`)
            VALUES (:1 , :2, :3, :4, :5 , 2, NOW(), :6, NOW(), :7)',
            $cacheId, $userId, $logType, Formatter::dateTimeForSql($date), $text, $uuid, OcConfig::getSiteNodeId()
        );
    }

    /**
     * Retrieves undeleted logs for given cache id and user id, optionally
     * entries being given types and results limited to given number of entries.
     *
     * @param int $cacheId The id of a cache to retrieve logs for.
     * @param int $userId The id of a user to retrieve logs for.
     * @param int[]|null $types Log types to narrow down the result to, if null - any
     *     type is considered.
     * @param int|null $limit The number of entries the result will be limited to,
     *     if null there will be no limit.
     *
     * @return array The set of GeoCacheLog objects, sorted by the `date` field.
     */
    public function getCacheLogsForUser(
        int $cacheId, int $userId, ?array $types = null, ?int $limit = null
    ): array
    {
        $params = [$cacheId, $userId];
        if ($types != null) {
            $typesInString = "";
            foreach ($types as $type) {
                if (strlen($typesInString) > 0) {
                    $typesInString .= ",";
                }
                array_push($params, $type);
                $typesInString .= ":" . count($params);
            }
        }
        $stmt = $this->db->multiVariableQuery(
            "SELECT * FROM `cache_logs` WHERE
             `cache_id` = :1 AND `user_id` = :2 AND deleted = 0"
            . (is_array($types) ? " AND `type` IN (" . $typesInString . ")" : "")
            . " ORDER BY `date` DESC"
            . ($limit != null ? " LIMIT " . $this->db()->quoteLimit($limit) : ""),
            $params
        );
        return $this->db->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    /**
     * Remove current log
     * @throws Exception
     */
    public function removeLog()
    {
        // check if current user is allowed to remove the log
        if ($this->getUserId() != $this->getCurrentUser()->getUserId() &&
            $this->getGeoCache()->getOwnerId() != $this->getCurrentUser()->getUserId() &&
            !$this->getCurrentUser()->hasOcTeamRole()) {

            // logged user is not an author of the log && not the owner of cache and not OCTeam
            throw new Exception("User not authorized to remove this log");
        }

        $this->setDeleted(true)
            ->setDelByUserId($this->getCurrentUser()->getUserId())
            ->setLastDeleted(new DateTime())
            ->setLastModified(new DateTime());

        $this->db->multiVariableQuery(
            "UPDATE cache_logs
            SET deleted=1, del_by_user_id=:1, last_modified=:2, last_deleted=:3
            WHERE id=:4 LIMIT 1",
            $this->getDelByUserId(), Formatter::dateTimeForSql($this->getLastModified()),
            Formatter::dateTimeForSql($this->getLastDeleted()), $this->getId());


        if ($this->getType() == self::LOGTYPE_MOVED) {
            MobileCacheMove::updateMovesOnLogRemove($this);
        }

        $this->getUser()->recalculateAndUpdateStats();

        if ($this->getType() == self::LOGTYPE_FOUNDIT ||
            $this->getType() == self::LOGTYPE_ATTENDED) {

            // remove cache from users top caches, because the found log was deleted for some reason
            $this->db->multiVariableQuery(
                "DELETE FROM cache_rating WHERE user_id=:1 AND cache_id=:2",
                $this->getUserId(), $this->getGeoCacheId());

            // Notify OKAPI's replicate module of the change.
            // Details: https://github.com/opencaching/okapi/issues/265
            Facade::schedule_user_entries_check($this->getGeoCacheId(), $this->getUserId());

            GeoCacheScore::updateScoreOnLogRemove($this);

            if (self::OcConfig()->isMeritBadgesEnabled()) {
                $ctrlMeritBadge = new MeritBadgeController;
                $ctrlMeritBadge->updateTriggerLogCache($this->getGeoCacheId(), $this->getUserId());
                $ctrlMeritBadge->updateTriggerTitledCache($this->getGeoCacheId(), $this->getUserId());
                $ctrlMeritBadge->updateTriggerCacheAuthor($this->getGeoCacheId());
            }
        }

        $this->getGeoCache()->recalculateCacheStats();

        // trigger log-author statpic update
        User::deleteStatpic($this->getUserId());

        if ($this->getUserId() != $this->getCurrentUser()->getUserId()) {
            EmailSender::sendRemoveLogNotification($this, $this->getCurrentUser());
        }
    }

    /**
     * Reverts (undeletes) log
     * @throws Exception
     */
    public function revertLog()
    {
        if (!$this->getCurrentUser()->hasOcTeamRole()) {
            throw new Exception('User is not authorized to revert log');
        }

        if (!$this->canBeReverted()) {
            throw new Exception('This log cannot be reverted');
        }

        $this->setDeleted(false)
            ->setLastModified(new DateTime());

        $this->db->multiVariableQuery(
            'UPDATE cache_logs SET deleted=:1, last_modified=:2 WHERE id=:3',
            $this->getDeleted(), Formatter::dateTimeForSql($this->getLastModified()), $this->getId());

        $this->getGeoCache()->recalculateCacheStats();
        $this->getUser()->recalculateAndUpdateStats();

        // trigger log-author statpic update
        $this->getUser()->deleteUserStatpic();

    }

    /**
     * Change the picturescount value by add $value to it
     * @param int $value
     */
    public function addToPicturesCount($value)
    {
        $this->setPicturesCount($this->getPicturesCount() + $value);
        $this->setLastModified(new DateTime());

        $this->db->multiVariableQuery(
            'UPDATE cache_logs SET picturescount=picturescount + :1, last_modified = :2
             WHERE id = :3 LIMIT 1', $value, Formatter::dateTimeForSql($this->getLastModified()), $this->getId());
    }

    public static function updateLastModified ($logId)
    {
        self::db()->multiVariableQuery(
            'UPDATE cache_logs SET last_modified = NOW() WHERE id = :1 LIMIT 1', $logId);
    }
}
