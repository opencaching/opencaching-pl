<?php
namespace src\Models\Pictures;

use Exception;
use src\Models\BaseObject;
use src\Utils\Debug\Debug;
use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheLog;
use src\Utils\Generators\Uuid;
use src\Utils\FileSystem\FileManager;

/**
 * Generic representation of picture atahed to log/cache/...
 *
 */

class OcPicture extends BaseObject
{
    public const TYPE_LOG = 1;
    public const TYPE_CACHE = 2;

    private const DB_COLS = ['id', 'uuid', 'local', 'url', 'thumb_last_generated', 'last_modified',
        'thumb_url', 'spoiler', 'object_type', 'object_id', 'title', 'display', 'seq'
    ];

    private $uuid;      // UUID of image
    private $url;       // full url to image
    private $isLocal;
    private $isSpoiler; // if this image can be a spoiler for a cache and should be hide by default
    private $isHidden;  // if this image should be display on the list of cache/log images

    private $parentType;
    private $parentId;
    private $parent = null;

    private $fileUploadDate;
    private $filename = null;

    private $order;
    private $title;

    private function __construct()
    {
        parent::__construct();
        $this->uuid = null;
    }

    /**
     * Create OcPicture object based on given uuid
     *
     * @param string $uuid
     * @throws \Exception
     */
    public static function fromUuidFactory($uuid)
    {
        try {
            $obj = new self();
            $obj->loadByUuid($uuid);
        } catch (Exception $e) {
            return null;
        }
        return $obj;
    }

    public static function getNewPicPlaceholder($parentType, $parentObj)
    {
        $obj = new self();
        $obj->parentType = $parentType;
        $obj->parent = $parentObj;
        $obj->isHidden = FALSE;
        $obj->isSpoiler = FALSE;
        $obj->isLocal = TRUE;

        switch ($parentType) {
            case self::TYPE_CACHE:
                /* @var $parentObj GeoCache */
                $obj->parentId = $parentObj->getCacheId();
                break;
            case self::TYPE_LOG:
                /* @var $parentObj GeoCacheLog */
                $obj->parentId = $parentObj->getId();
                break;
        }

        return $obj;
    }

    public static function getListForParent($parentType, $parentId)
    {
        $db = self::db();
        $cols = implode(',',self::DB_COLS);
        $rs = $db->multiVariableQuery(
            "SELECT $cols FROM pictures
                WHERE object_id = :1 AND object_type = :2
                ORDER BY seq ASC, date_created DESC", $parentId, $parentType);

        return $db->dbFetchAllAsObjects($rs, function ($row) {
            $pic = new self();
            $pic->loadFromRow($row);
            return $pic;
        });
    }

    /**
     * Add this pic to DB (this is for save in DB already uploaded files info)
     */
    public function addToDb()
    {
        $thumbUrl = $this->regenerateThumbnails();

        // this is new picture - add it
        $this->db->multiVariableQuery("INSERT INTO pictures (
            uuid, local, url, thumb_last_generated, last_modified,
            thumb_url, spoiler, object_type, object_id, title,
            date_created, display, seq, node) VALUES (
            :1, :2, :3, NOW(), NOW(),
            :4, :5, :6, :7, :8,
            NOW(), :9, :10, :11)",
            $this->uuid, $this->isLocal, $this->url,
            $thumbUrl, $this->isSpoiler, $this->parentType, $this->parentId, $this->title,
            !$this->isHidden, $this->order, OcConfig::getSiteNodeId());

        // update pic count in parent recod (+last_modified)
        $this->updateParentPicturesCountInDb(1);
    }

    public function updateOrderInDb ()
    {
        $this->db->multiVariableQuery(
            "UPDATE pictures SET seq = :1, last_modified = NOW() WHERE uuid = :2 LIMIT 1",
            $this->order, $this->uuid);

        $this->updateParentLastUpdateInDb();
    }

    public function updateTitleInDb()
    {
        $this->db->multiVariableQuery(
            "UPDATE pictures SET title = :1, last_modified = NOW() WHERE uuid = :2 LIMIT 1",
            $this->title, $this->uuid);

        $this->updateParentLastUpdateInDb();
    }

    public function updateSpoilerAttrInDb()
    {
        $this->db->multiVariableQuery(
            "UPDATE pictures SET spoiler = :1, last_modified = NOW() WHERE uuid = :2 LIMIT 1",
            $this->isSpoiler, $this->uuid);

        $this->regenerateThumbnails();
        $this->updateParentLastUpdateInDb();
    }

    public function updateHiddenAttrInDb()
    {
        $this->db->multiVariableQuery(
            "UPDATE pictures SET display = :1, last_modified = NOW() WHERE uuid = :2 LIMIT 1",
            !$this->isHidden, $this->uuid);
        $this->updateParentLastUpdateInDb();
    }

    private function updateParentLastUpdateInDb()
    {
        switch ($this->parentType) {
            case self::TYPE_CACHE:
                GeoCache::updateLastModified($this->getParentId());
                break;
            case self::TYPE_LOG:
                GeoCacheLog::updateLastModified($this->getParentId());
                break;
            default:
                Debug::errorLog("Incorrect picture type!");
        }
    }

    private function updateParentPicturesCountInDb($var)
    {
        switch ($this->parentType) {
            case self::TYPE_CACHE:
                $this->getParent()->addToPicturesCount($var);
            case self::TYPE_LOG:
                $this->getParent()->addToPicturesCount($var);
        }
    }

    public function getThumbnail ($size, $showSpoilers)
    {
        return self::getThumbUrl($this->uuid, $showSpoilers, $size);
    }

    public function getFullImgUrl()
    {
        return $this->url;
    }

    public static function getThumbUrl($uuid, $showSpoiler, $size)
    {
        // first just try to locate such thumbnail
        if($thumbUrl = Thumbnail::getUrl($uuid, $showSpoiler, $size)){
            return $thumbUrl;
        }

        // thumbnail not found - try to generate new one
        $instance = self::fromUuidFactory($uuid);
        if(!$instance) {
            // there is no picture with given uuid
            return Thumbnail::PHD_ERROR_404;
        }

        if($thumbUrl = $instance->regenerateThumbnails($size)) {
            return $thumbUrl;
        }

        return null;
    }

    private function loadByUuid($uuid)
    {
        $cols = implode(',', self::DB_COLS);
        $s = $this->db->multiVariableQuery(
            "SELECT $cols FROM pictures WHERE uuid = :1 LIMIT 1", $uuid);

        $row = $this->db->dbResultFetchOneRowOnly($s);
        if (is_array($row)) {
            $this->loadFromRow($row);
        } else {
            throw new \Exception("Picture not found");
        }
    }

    private function loadFromRow(array $row)
    {
        foreach($row as $col=>$val){
            switch($col){
                case 'id':
                    $this->id = $val;
                    break;
                case 'uuid':
                    $this->uuid = $val;
                    break;
                case 'url':
                    $this->url = $val;
                    break;
                case 'local':
                    $this->isLocal = ($val == 1);
                    break;
                case 'spoiler':
                    $this->isSpoiler = ($val == 1);
                    break;
                case 'last_modified':
                    $this->fileUploadDate = new \DateTime($val);
                    break;
                case 'thumb_url':
                    $this->thumbnailUrl = $val;
                    break;
                case 'thumb_last_generated':
                    $this->thumbnailGenDate = new \DateTime($val);
                    break;
                case 'object_id':
                    $this->parentId = $val;
                    break;
                case 'object_type':
                    $this->parentType = $val;
                    break;
                case 'title':
                    $this->title = $val;
                    break;
                case 'display':
                    $this->isHidden = ($val != 1);
                    break;
                case 'seq':
                    $this->order = $val;
                    break;
                default:
                    Debug::errorLog("Column $col not supported ?");
            }
        } // foreach
    }

    /**
     * Returns true if this picture is stored on local server
     * @return boolean
     */
    public function isLocalImg()
    {
        return $this->isLocal;
    }

    /**
     * Returns true if this picture is a spoiler
     * @return boolean
     */
    public function isSpoilerImg()
    {
        return $this->isSpoiler;
    }

    /**
     * Returns TRUE is this picture shouldn't be disaply on list of images (for cache/log)
     * @return boolean
     */
    public function isHidden()
    {
        return $this->isHidden;
    }

    public function getPathToImg()
    {
        $path = OcConfig::getPicUploadFolder();

        if ($result = glob("$path/{$this->uuid}.*")) {
            if (!empty($result)) {
                // thumbnail found
                return $path.'/'.basename($result[0]);
            }
        }
        return null;
    }

    public function isUserAllowedToModifyIt(User $user)
    {
        if ($user->hasOcTeamRole()) {
            return true;
        }

        switch($this->parentType) {
            case self::TYPE_CACHE:
                $cache = $this->getParent();
                return $cache->getOwnerId() == $user->getUserId();

            case self::TYPE_LOG:
                $log = $this->getParent();
                return $log->getUserId() == $user->getUserId();

            default:
                Debug::errorLog("Unsupported parent type: {$this->parentType}");
                return false;
        }
    }

    public function getLastOrderIndexforParent ()
    {
        $highestOrderIndex = $this->db->multiVariableQueryValue(
            'SELECT MAX(seq) FROM pictures WHERE object_id = :1 AND object_type = :2',
            0, $this->getParentId(), $this->getParentType());

        return $highestOrderIndex+1;
    }

    public function remove(User $user)
    {
        if(!$this->isUserAllowedToModifyIt($user)) {
            return false;
        }

        $this->db->multiVariableQuery('DELETE FROM pictures WHERE uuid=:1 LIMIT 1', $this->uuid);

        $this->db->multiVariableQuery(
            'INSERT INTO removed_objects (localID, uuid, type, removed_date, node)
             VALUES (:1, :2, 6, NOW(), :3)',
            $this->id, $this->uuid, OcConfig::getSiteNodeId());


        // updated picturescount in parent object (+last_modified)
        $this->updateParentPicturesCountInDb(-1);

        // DB is cleared - remove files from disk

        if (!$this->isLocalImg()){
            // external image - there is nothing more to do
            return true;
        }

        $path = $this->getPathToImg();
        if($path) {
            // remove main image
            FileManager::removeFile($path);
        }
        Thumbnail::remove($this->uuid);

        return true;
    }

    public static function getParentObj(int $parentType, int $parentId)
    {
        switch ($parentType) {
            case self::TYPE_CACHE:
                return GeoCache::fromCacheIdFactory($parentId);

            case self::TYPE_LOG:
                return GeoCacheLog::fromLogIdFactory($parentId);

            default:
                Debug::errorLog("Unsupported parent type: {$parentType}");
                return null;
        }
    }

    public function getParent()
    {
        if($this->parent) {
            return $this->parent;
        }

        $this->parent = self::getParentObj($this->parentType, $this->parentId);
        return $this->parent;
    }

    public function getParentType()
    {
        return $this->parentType;
    }

    /**
     * Create the thumbnail for this pic.
     *
     * @param $size
     * @return string   url to given thumbnails size (defaul == medium)
     */
    private function regenerateThumbnails($size=null)
    {
        if(!$this->isLocalImg()) {
            return Thumbnail::placeholderUri(Thumbnail::PHD_EXTERN);
        }

        if(!$this->getPathToImg()) {
            // strange - there is image in DB but no such image on disk
            Debug::errorLog("Can't find image uuid={$this->uuid}");
            return Thumbnail::placeholderUri(Thumbnail::PHD_ERROR_INTERN);
        }

        // remove previous thumbnails
        Thumbnail::remove($this->uuid);

        // genereate the new one thumbnails
        $result = [];
        $result[Thumbnail::SIZE_SMALL] = Thumbnail::generateThumbnail($this->getPathToImg(), $this->uuid, Thumbnail::SIZE_SMALL, $this->isSpoiler);
        $result[Thumbnail::SIZE_MEDIUM] = Thumbnail::generateThumbnail($this->getPathToImg(), $this->uuid, Thumbnail::SIZE_MEDIUM, $this->isSpoiler);

        switch ($size) {
            case Thumbnail::SIZE_SMALL:
                return $result[Thumbnail::SIZE_SMALL];
            case Thumbnail::SIZE_MEDIUM:
            default:
                return $result[Thumbnail::SIZE_MEDIUM];
        }
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function markAsSpoiler($isSpoiler)
    {
        $this->isSpoiler = $isSpoiler;
    }

    public function markAsHidden($isHidden)
    {
        $this->isHidden = $isHidden;
    }

    public function setUuid($uuid)
    {
        if (Uuid::isValidUuid($uuid)) {
            $this->uuid = $uuid;
        }
    }

    public function setFilenameForUrl($filename)
    {
        $this->url = OcConfig::getPicBaseUrl() . '/' . $filename;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setOrderIndex($index)
    {
        $this->order = $index;
    }

    public function getData(): \stdClass
    {
        $obj = new \stdClass();
        $obj->fullPicUrl = $this->getFullImgUrl();
        $obj->thumbUrl = $this->getThumbnail(Thumbnail::SIZE_SMALL, TRUE);
        $obj->uuid = $this->getUuid();
        $obj->title = $this->getTitle();
        $obj->isHidden = $this->isHidden();
        $obj->isSpoiler = $this->isSpoilerImg();
        return $obj;
    }

    public function getDataJson(): string
    {
        return json_encode($this->getData());
    }
}
