<?php

namespace lib\Objects\GeoCache;

use lib\Objects\User\User;
use lib\Controllers\Php7Handler;


/**
 *
 */
class GeoCacheLog extends GeoCacheLogCommons
{

    private $id;
    private $geoCache;
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
        if(!($this->geoCache instanceof GeoCache)){
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
        if(!($this->user instanceof User)){
            $this->user = new User(array('userId' => $this->user));
        }
        return $this->user;
    }

    public function getType()
    {
        return $this->type;
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
        $this->user = $userId;
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
        $this->deleted = Php7Handler::Boolval($deleted);
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

    private function loadByLogId($logId){

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
        try{
            $obj->loadByLogId($logId);
            return $obj;
        }catch (\Exception $e){
            return null;
        }
    }
}
