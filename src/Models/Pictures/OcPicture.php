<?php
namespace src\Models\Pictures;

use Exception;
use src\Models\BaseObject;
use src\Utils\Debug\Debug;
use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheLog;

/**
 * Generic representation of picture atahed to log/cache/...
 *
 */

class OcPicture extends BaseObject
{
    const TYPE_LOG = 1;
    const TYPE_CACHE = 2;

    const DB_COLS = ['id', 'uuid', 'local', 'url', 'thumb_last_generated', 'last_modified',
        'uuid', 'thumb_url', 'spoiler', 'object_type', 'object_id'
    ];

    private $uuid;      // UUID of image
    private $url;       // full url to image
    private $isLocal;
    private $isSpoiler; // if this image can be a spoiler for a cache and should be hide by default

    private $parentType;
    private $parentId;
    private $parent = null;

    private $fileUploadDate;
    private $filename = null;

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

        if($thumbUrl = $instance->regenerateThumbnail($size)) {
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

    public function isUserAllowedToRemoveIt(User $user)
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

    public function remove(User $user)
    {
        if(!$this->isUserAllowedToRemoveIt($user)) {
            return false;
        }

        $this->db->multiVariableQuery('DELETE FROM pictures WHERE uuid=:1 LIMIT 1', $this->uuid);

        $this->db->multiVariableQuery(
            'INSERT INTO removed_objects (localID, uuid, type, removed_date, node)
             VALUES (:1, :2, 6, NOW(), :3)',
            $this->id, $this->uuid, OcConfig::getSiteNodeId());


        // updated picturescount in parent object
        switch($this->parentType) {
            case self::TYPE_CACHE:
                $cache = $this->getParent();
                $cache->addToPicturesCount(-1);
                break;

            case self::TYPE_LOG:
                $log = $this->getParent();
                $log->addToPicturesCount(-1);
                break;

            default:
                Debug::errorLog("Unsupported parent type: {$this->parentType}");
                return false;
        }

        // DB is cleared - remove files from disk

        if (!$this->isLocalImg()){
            // extenrla imgage - there is no more to do
            return true;
        }

        $path = $this->getPathToImg();
        if($path) {
            // remove main image
            unlink($path);
        }

        Thumbnail::remove($this->uuid);
        return true;
    }

    public function getParent()
    {
        if($this->parent) {
            return $this->parent;
        }

        switch($this->parentType) {
            case self::TYPE_CACHE:
                return $this->parent = GeoCache::fromCacheIdFactory($this->parentId);

            case self::TYPE_LOG:
                return $log = GeoCacheLog::fromLogIdFactory($this->parentId);

            default:
                Debug::errorLog("Unsupported parent type: {$this->parentType}");
                return null;
        }
    }

    public function getParentType()
    {
        return $this->parentType;
    }


    private function regenerateThumbnail($size)
    {
        if(!$this->isLocalImg()) {
            return Thumbnail::placeholderUri(Thumbnail::PHD_EXTERN);
        }

        if(!$this->getPathToImg()) {
            // starnge - there is image in DB but no such image on disk
            Debug::errorLog("Can't find image uuid={$this->uuid}");
            return Thumbnail::placeholderUri(Thumbnail::PHD_ERROR_INTERN);
        }

        return Thumbnail::generateThumbnail($this->getPathToImg(), $this->uuid, $size, $this->isSpoiler);
    }
}
