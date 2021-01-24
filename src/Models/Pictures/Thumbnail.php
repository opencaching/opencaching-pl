<?php
namespace src\Models\Pictures;

use src\Models\BaseObject;
use src\Models\OcConfig\OcConfig;
use src\Utils\I18n\I18n;
use src\Utils\Img\OcImage;
use src\Utils\Debug\Debug;


/**
 * This class provide methods to manage the thumbnails.
 *
 *  Main idea:
 *  - there is a folder where uploaded pictures are stored (OcConfig::getPicUploadFolder)
 *  - in this folder there are subfolder for storing thumbnails (::THUMBS_FOLDER)
 *  - thumbnails subfolder has internal structure:
 *      [non_spoilers|spoiers]/<thumb-size>/<uuid[0]>/<uuid[1]>/<uuid[2]>/<uuid>.[jpg|gif|png]
 *
 */
class Thumbnail extends BaseObject
{
    // Error paceholders
    const PHD_ERROR_404     = 'thumb404.gif';     // file not found
    const PHD_ERROR_INTERN  = 'thumbintern.gif';  // internal error
    const PHD_ERROR_FORMAT  = 'thumbunknown.gif'; // unknown file format
    const PHD_EXTERN        = 'thumbextern.gif';  // external image, no thumb available
    const PHD_SPOILER       = 'thumbspoiler.gif'; // spoiler image

    const THUMBS_DIR     = '/thumbnails';
    const SPOILER_DIR       = '/spoilers';
    const NON_SPOILER_DIR   = '/non_spoilers';

    const SIZE_SMALL        = '/sizeSmall';
    const SIZE_MEDIUM       = '/sizeMedium';


    /**
     * Returns the uri to the thumbnail placeholder (like spoiler or error) in the current lang.
     *
     * @param string $placeholder - the placeholder name
     * @return string
     */
    public static function placeholderUri($placeholder)
    {
        $path = '/images/thumb/'.I18n::getCurrentLang().'/'.$placeholder;
        if (!file_exists($path)) {
            $path = '/images/thumb/en/'.$placeholder;
        }
        return $path;
    }

    /**
     * Returns the url to the thumbnail based on uuid, given size and spoiler-status
     *
     * @param unknown $uuid
     * @param unknown $showSpoiler
     * @param unknown $thumbSize
     * @return NULL|string
     */
    public static function getUrl($uuid, $showSpoiler, $thumbSize)
    {

        //$basePath = OcConfig::getPicUploadFolder(true) . self::THUMBS_FOLDER;

        // first try to localize non-spoiler thumbnail
        $nonSpoilerUrl = self::getThumbnailUrl($uuid, self::NON_SPOILER_DIR, $thumbSize);
        if ($nonSpoilerUrl) {
            // non-spoiler is found
            return $nonSpoilerUrl;
        }

        // non-spoiler not found - look for spoiler thumb
        $spoilerUrl = self::getThumbnailUrl($uuid, self::SPOILER_DIR, $thumbSize);
        if ($spoilerUrl) {
            if ($showSpoiler) {
                // spoiler thumb found and should be display
                return $spoilerUrl;
            } else {
                return self::placeholderUri(self::PHD_SPOILER);
            }
        }

        // both spoiler and non-spoiler not found
        return null;
    }

    /**
     * Generate thumbnail
     * @param string $orginalImagePath
     * @param string $uuid
     * @param SIZE_* $thumbSize
     *
     * @return URL to generated thumbnail
     */
    public static function generateThumbnail($orginalImagePath, $uuid, $thumbSize, $isSpoiler)
    {
        switch ($thumbSize) {
            case self::SIZE_SMALL:
                $maxSize = OcConfig::getPicSmallThumbnailSize();
                break;
            case self::SIZE_MEDIUM:
                $maxSize = OcConfig::getPicMediumThumbnailSize();
                break;
            default:
                Debug::errorLog("Unknown thumbnail size: $thumbSize");
                return null;
        }

        if($isSpoiler) {
            $spoiler = self::SPOILER_DIR;
        } else {
            $spoiler = self::NON_SPOILER_DIR;
        }

        $path = self::buildPath($uuid, $spoiler, $thumbSize);

        $outPath = OcConfig::getPicUploadFolder().$path;

        // be sure that $outPath exists
        if(!is_dir($outPath)){
            mkdir($outPath, 0750, true);
        }

        try {
            //ddd($orginalImagePath, $outPath, $maxSize);
            $outPath = OcImage::createThumbnail($orginalImagePath, "$outPath/$uuid", $maxSize);
        } catch ( \Exception $e) {
            Debug::logException($e);
            return null;
        }

        return OcConfig::getPicBaseUrl().$path.'/'.basename($outPath);
    }

    /**
     * Remove all thumbnails under given uuid
     * @param string $uuid
     */
    public static function remove ($uuid)
    {
        $basePath = OcConfig::getPicUploadFolder();

        $allSizes = [self::SIZE_MEDIUM, self::SIZE_SMALL];
        $spoilerDirs = [self::NON_SPOILER_DIR, self::SPOILER_DIR];

        foreach ($allSizes as $size) {
            foreach ($spoilerDirs as $spoiler) {

                $path = self::buildPath($uuid, $spoiler, $size);
                if ($result = glob("$basePath$path/$uuid.*")) {
                    if (!empty($result) || !is_array($result)) {
                        // thumbnail found
                        foreach ($result as $thumb) {
                            unlink ($thumb);
                        }
                    }
                }
            }
        }
    }

    /**
     * Try to locate the file bu UUID on path:
     *  [non_spoilers|spoiers]/<thumb-size>/<uuid[0]>/<uuid[1]>/<uuid[2]>/<uuid>.[jpg|gif|png]
     *
     * @param unknown $uuid -
     * @param unknown $spoiler -
     * @param unknown $size -
     */
    private static function getThumbnailUrl($uuid, $spoiler, $size)
    {
        // construct the path
        $basePath = OcConfig::getPicUploadFolder();
        $baseUrl = OcConfig::getPicBaseUrl();

        $path = self::buildPath($uuid, $spoiler, $size);

        if ($result = glob("$basePath$path/$uuid.*")) {
            if (!empty($result)) {
                // thumbnail found
                return $baseUrl.$path.'/'.basename($result[0]);
            }
        }
        // no such file located in given path
        return null;
    }


    private static function buildPath($uuid, $spoiler, $size )
    {
        $path = self::THUMBS_DIR;
        $path .= $spoiler;
        $path .= $size;
        $path .= "/{$uuid[0]}/{$uuid[1]}/{$uuid[2]}";
        return $path;
    }

}
