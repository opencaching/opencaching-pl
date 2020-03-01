<?php
namespace src\Models\GeoCache;

use src\Models\BaseObject;
use src\Utils\Debug\Debug;
use src\Models\User\User;

/**
 * Generic representation of picture atahed to log/cache/...
 *
 */

class Mp3Attachment extends BaseObject
{
    const TYPE_LOG = 1;
    const TYPE_CACHE = 2;

    const DB_COLS = ['id', 'uuid', 'url', 'last_modified', 'title', 'date_created', 'last_url_check',
        'object_id', 'object_type', 'user_id', 'local', 'unknown_format', 'display', 'node', 'seq'
    ];

    private $uuid;      // UUID of image
    private $url;       // full url to image
    private $title;
    private $isLocal;

    private $parentType;
    private $parentId;
    private $parent = null;



    /**
     * Return list of pictures for given geocache
     *
     * @param GeoCache $cache
     */
    public static function getAllForGeocache(GeoCache $cache, $includeHidden = false)
    {
        if(!$includeHidden) {
            $skipHidden = ' AND display = 1';
        } else {
            $skipHidden = '';
        }

        $db = self::db();
        $rs = $db->multiVariableQuery(
            "SELECT * FROM mp3
             WHERE object_id = :1 AND object_type = 2 $skipHidden
             ORDER BY seq, date_created", $cache->getCacheId());

        return $db->dbFetchAllAsObjects($rs,
            function ($row) {
                $obj = new self();
                $obj->loadFromDbRow($row);
                return $obj;
            });
    }


    private function loadFromDbRow(array $row)
    {
        foreach($row as $col=>$val){
            switch($col){
                case 'id':
                    $this->id = $val;
                case 'uuid':
                    $this->uuid = $val;
                    break;
                case 'url':
                    $this->url = $val;
                    break;
                case 'title':
                    $this->title = $val;
                    break;
                case 'local':
                    $this->isLocal = ($val == 1);
                    break;
                case 'last_modified':
                    $this->fileUploadDate = new \DateTime($val);
                    break;
                case 'object_id':
                    $this->parentId = $val;
                    break;
                case 'object_type':
                    $this->parentType = $val;
                    break;
                default:
                    // do nothing for now...
                    // Debug::errorLog("Column $col not supported ?");
            }
        } // foreach
    }

    /**
     * Returns true if this mp3 is stored on local server
     * @return boolean
     */
    public function isLocal()
    {
        return $this->isLocal;
    }

    public function getPathToMp3()
    {
        return ''; //TODO!!!

        /*
        $path = OcConfig::getPicUploadFolder();

        if ($result = glob("$path/{$this->uuid}.*")) {
            if (!empty($result)) {
                // thumbnail found
                return $path.'/'.basename($result[0]);
            }
        }
        return null;*/
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
        /* // TODO:
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
        */
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
            default:
                Debug::errorLog("Unsupported parent type: {$this->parentType}");
                return null;
        }
    }

    public function getParentType()
    {
        return $this->parentType;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}


