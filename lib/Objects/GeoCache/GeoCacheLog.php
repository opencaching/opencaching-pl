<?php
namespace lib\Objects\GeoCache;

use lib\Objects\User\User;
use Utils\Email\EmailSender;
use Utils\Generators\Uuid;
use Exception;
use lib\Controllers\MeritBadgeController;
use okapi\Facade;
use lib\Objects\OcConfig\OcConfig;

class GeoCacheLog extends GeoCacheLogCommons
{

    private $id;
    private $geoCache;
    private $userId;
    private $user;
    private $type;
    private $date;
    private $text;
    private $textHtml;
    private $textHtmlEdit;
    private $lastModified;
    private $okapiSyncbase;
    private $uuid;
    private $picturesCount;
    private $mp3count;
    private $dateCreated;
    private $ownerNotified;
    private $node;
    private $deleted;
    private $delByUserId;
    private $editByUserId;
    private $editCount;
    private $lastDeleted;

    public function __construct()
    {
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return GeoCache
     */
    public function getGeoCache()
    {
        if (!($this->geoCache instanceof GeoCache)) {
            $this->geoCache = new GeoCache(array('cacheId' => $this->geoCache));
        }

        return $this->geoCache;
    }

    /**
     *
     * @return User
     */
    public function getUser()
    {
        if (!$this->user) {
            $this->user = new User(array('userId' => $this->userId));
        }
        return $this->user;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns translation key for log type
     *
     * @return string
     */
    public function getTypeTranslationKey()
    {
        return self::typeTranslationKey($this->getType());
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getTextHtml()
    {
        return $this->textHtml;
    }

    public function getTextHtmlEdit()
    {
        return $this->textHtmlEdit;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }

    public function getOkapiSyncbase()
    {
        return $this->okapiSyncbase;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getPicturesCount()
    {
        return $this->picturesCount;
    }

    public function getMp3count()
    {
        return $this->mp3count;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getOwnerNotified()
    {
        return $this->ownerNotified;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function getDelByUserId()
    {
        return $this->delByUserId;
    }

    public function getEditByUserId()
    {
        return $this->editByUserId;
    }

    public function getEditCount()
    {
        return $this->editCount;
    }

    public function getLastDeleted()
    {
        return $this->lastDeleted;
    }

    /**
     * Return URL of the log object
     * @return string
     */
    public function getLogUrl()
    {
        return parent::getLogUrlByLogId($this->id);
    }

    /**
     * Returns URL of the log icon
     * @return string
     */
    public function getLogIcon()
    {
        return parent::GetIconForType($this->getType());
    }

    /**
     * Returns true if $userid recommended cache related with log
     * @param integer $userid
     * @return boolean
     */
    public function isRecommendedByUser($userid)
    {
        $params = [];
        $params['cacheid']['value'] = $this->geoCache->getCacheId();
        $params['cacheid']['data_type'] = 'integer';
        $params['userid']['value'] = $userid;
        $params['userid']['data_type'] = 'integer';
        $query = '
            SELECT COUNT(*)
            FROM `cache_rating`
            WHERE `cache_id` = :cacheid
              AND `user_id` = :userid
        ';
        return (bool) $this->db->paramQueryValue($query, 0, $params);
    }

    public function setId($logId)
    {
        $this->id = $logId;
        return $this;
    }

    public function setGeoCache($geoCache)
    {
        $this->geoCache = $geoCache;
        return $this;
    }

    public function setUser($userId)
    {
        $this->userId = $userId;
        $this->user = null;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function setTextHtml($textHtml)
    {
        $this->textHtml = $textHtml;
        return $this;
    }

    public function setTextHtmlEdit($textHtmlEdit)
    {
        $this->textHtmlEdit = $textHtmlEdit;
        return $this;
    }

    public function setLastModified(\DateTime $lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    public function setOkapiSyncbase(\DateTime $okapiSyncbase)
    {
        $this->okapiSyncbase = $okapiSyncbase;
        return $this;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setPicturesCount($picturesCount)
    {
        $this->picturesCount = $picturesCount;
        return $this;
    }

    public function setMp3count($mp3count)
    {
        $this->mp3count = $mp3count;
        return $this;
    }

    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function setOwnerNotified($ownerNotified)
    {
        $this->ownerNotified = $ownerNotified;
        return $this;
    }

    public function setNode($node)
    {
        $this->node = $node;
        return $this;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = boolval($deleted);
        return $this;
    }

    public function setDelByUserId($delByUserId)
    {
        $this->delByUserId = $delByUserId;
        return $this;
    }

    public function setEditByUserId($editByUserId)
    {
        $this->editByUserId = $editByUserId;
        return $this;
    }

    public function setEditCount($editCount)
    {
        $this->editCount = (int) $editCount;
        return $this;
    }

    public function setLastDeleted($lastDeleted)
    {
        $this->lastDeleted = $lastDeleted;
        return $this;
    }

    private function loadByLogId($logId)
    {

        //find log by Id
        $s = $this->db->multiVariableQuery(
            "SELECT * FROM cache_logs WHERE id = :1 LIMIT 1", $logId);

        $logDbRow = $this->db->dbResultFetchOneRowOnly($s);

        if(is_array($logDbRow)) {
            $this->loadFromDbRow($logDbRow);
        } else {
            throw new \Exception("No such cache_log");
        }
    }

    private function loadFromDbRow($row)
    {
        $this
        ->setGeoCache($row['cache_id'])
        ->setDate(new \DateTime($row['date']))
        ->setDateCreated(new \DateTime($row['date_created']))
        ->setDelByUserId($row['del_by_user_id'])
        ->setDeleted($row['deleted'])
        ->setEditByUserId($row['edit_by_user_id'])
        ->setEditCount($row['edit_count'])
        ->setLastDeleted($row['last_deleted'])
        ->setLastModified(new \DateTime($row['last_modified']))
        ->setId($row['id'])
        ->setMp3count($row['mp3count'])
        ->setNode($row['node'])
        ->setOkapiSyncbase(new \DateTime($row['okapi_syncbase']))
        ->setOwnerNotified($row['owner_notified'])
        ->setPicturesCount($row['picturescount'])
        ->setText($row['text'])
        ->setTextHtml($row['text_html'])
        ->setTextHtmlEdit($row['text_htmledit'])
        ->setType($row['type'])
        ->setUser($row['user_id'])
        ->setUuid($row['uuid']);
    }

    /**
     * Create GeoCacheLog object based on logId
     *
     * @param integer $logId
     * @return GeoCacheLog|NULL
     */
    public static function fromLogIdFactory($logId)
    {
        $obj = new self();
        try {
            $obj->loadByLogId($logId);
            return $obj;
        } catch (\Exception $e) {
            return null;
        }
    }

    private static function fromDbRowFactory($row) {
        $obj = new self();
        try {
            $obj->loadFromDbRow($row);
            return $obj;
        } catch (\Exception $e) {
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
     * @return boolean
     */
    public function canBeReverted()
    {
        if (! $this->getDeleted())
        {
            return false; //log is NOT deleted
        }
        if (in_array($this->getType(),
            [GeoCacheLog::LOGTYPE_FOUNDIT,
            GeoCacheLog::LOGTYPE_ATTENDED,
            GeoCacheLog::LOGTYPE_WILLATTENDED])) {
                // There can be only one log "found", "attended", "will attend"
                return (! $this->getGeoCache()->hasUserLogByType($this->getUser(), $this->getType()));
            }
        return true;
    }



    /**
     * Inserts new log into the DB
     * If empty/null $date, current datetime will be used
     *
     * @param integer $cacheId
     * @param integer $userId
     * @param integer $logType
     * @param string $text
     * @param \DateTime $date
     */
    public static function newLog($cacheId, $userId, $logType, $text, \DateTime $date = null)
    {
        if (is_null($date)) {
            $date = new \DateTime();
        }
        $uuid = Uuid::create();

        self::db()->multiVariableQuery(
            'INSERT INTO `cache_logs`
                (`cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `last_modified`, `uuid`, `date_created`, `node`)
            VALUES (:1 , :2, :3, :4, :5 , 2, 1, NOW(), :6, NOW(), :7)',
            $cacheId, $userId, $logType, $date->format(self::OcConfig()->getDbDateTimeFormat()), $text, $uuid, OcConfig::getSiteNodeId()
            );
    }

    /**
     * Retrieves undeleted logs for given cache id and user id, optionally
     * entries being given types and results limited to given number of entries.
     *
     * @param integer $cacheId The id of a cache to retrieve logs for.
     * @param integer $userId The id of a user to retrieve logs for.
     * @param array $types Log types to narrow down the result to, if null - any
     *     type is considered.
     * @param integer $limit The number of entries the result will be limited to,
     *     if null there will be no limit.
     *
     * @return array The set of GeoCacheLog objects, sorted by the `date` field.
     */
    public function getCacheLogsForUser(
        $cacheId, $userId, $types = null, $limit = null
    ) {
        $params = [$cacheId, $userId];
        if ($types != null) {
            $typesInString = "";
            foreach($types as $type) {
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
             .(is_array($types) ? " AND `type` IN (" . $typesInString . ")" : "")
             ." ORDER BY `date` DESC"
             .($limit != null ? " LIMIT " . $this->db()->quoteLimit($limit) : ""),
             $params
        );
        return $this->db->dbFetchAllAsObjects($stmt, function($row) {
            return self::fromDbRowFactory($row);
        });
    }



    /**
     * Remove current log
     */
    public function removeLog()
    {
        // check if current user is allowed to remove the log
        if($this->getUserId() != $this->getCurrentUser()->getUserId() &&
           $this->getGeoCache()->getOwnerId() != $this->getCurrentUser()->getUserId() &&
           !$this->getCurrentUser()->hasOcTeamRole()) {

            // logged user is not an author of the log && not the owner of cache and not OCTeam
            throw new Exception("User not authorize to remove this log");
        }

        $this->db->multiVariableQuery(
            "UPDATE cache_logs
            SET deleted=1, del_by_user_id=:1 , last_modified=NOW(), last_deleted=NOW()
            WHERE id=:2 LIMIT 1", $this->getCurrentUser()->getUserId(), $this->getId());


        if ($this->getType() == self::LOGTYPE_MOVED) {
            MobileCacheMove::updateMovesOnLogRemove($this);
        }

        $this->getCurrentUser()->recalculateAndUpdateStats();


        if ($this->getType() == self::LOGTYPE_FOUNDIT ||
            $this->getType() == self::LOGTYPE_ATTENDED) {

            // remove cache from users top caches, because the found log was deleted for some reason
            $this->db->multiVariableQuery(
                "DELETE FROM cache_rating WHERE user_id=:1 AND cache_id=:2",
                $this->getUserId(), $this->getGeoCache()->getCacheId());

            // Notify OKAPI's replicate module of the change.
            // Details: https://github.com/opencaching/okapi/issues/265
            Facade::schedule_user_entries_check($this->getGeoCache()->getCacheId(), $this->getUserId());

            GeoCacheScore::updateScoreOnLogRemove($this);

            if ( self::OcConfig()->isMeritBadgesEnabled() ){
                $ctrlMeritBadge = new MeritBadgeController;
                $ctrlMeritBadge->updateTriggerLogCache($this->getGeoCache()->getCacheId(), $this->getCurrentUser()->getUserId() );
                $ctrlMeritBadge->updateTriggerTitledCache($this->getGeoCache()->getCacheId(), $this->getCurrentUser()->getUserId());
                $ctrlMeritBadge->updateTriggerCacheAuthor($this->getGeoCache()->getCacheId());
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
     */
    public function revertLog()
    {
        if (!$this->getCurrentUser()->hasOcTeamRole()) {
            throw new Exception('User is not authorized to revert log');
        }

        if (!$this->canBeReverted()) {
            throw new Exception('This log cannot be reverted');
        }

        $this->setDeleted(false);
        $this->db->multiVariableQuery(
            'UPDATE cache_logs SET deleted=:1 WHERE id=:2',
            $this->getDeleted(), $this->getId());

        $this->getGeoCache()->recalculateCacheStats();

        // trigger log-author statpic update
        User::deleteStatpic($this->getUserId());

        $this->getCurrentUser()->recalculateAndUpdateStats();
    }

}
