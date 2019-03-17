<?php
namespace src\Models\Pictures;

use Exception;
use src\Models\BaseObject;
use src\Utils\Debug\Debug;
use src\Models\OcConfig\OcConfig;

/**
 * Generic representation of
 * @author kojoty
 *
 */

class OcPicture extends BaseObject
{
    const DB_COLS = ['uuid', 'local', 'url', 'thumb_last_generated', 'last_modified',
        'uuid', 'thumb_url', 'spoiler',
    ];

    private $uuid;      // UUID of image
    private $url;       // full url to image
    private $isLocal;
    private $isSpoiler; // if this image can be a spoiler for a cache and should be hide by default

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

    public static function getUrl($uuid, $showSpoiler, $size)
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