<?php

namespace src\Models\OcConfig;

use Exception;

/**
 * Loads configuration from pictures.*.php.
 *
 * @mixin OcConfig
 */
trait PicturesConfigTrait
{
    protected $picturesConfig = null;

    /**
     * @return array [width, height] of the maximum size of a small thumbnail.
     */
    public static function getPicSmallThumbnailSize()
    {
        $conf = self::getKeyFromPicturesConfig('thumbnailSmall');

        if (! is_array($conf)) {
            throw new Exception('thumbnailSmall setting not an array?: see /config/pictures.*');
        }

        return $conf;
    }

    /**
     * @return array [width, height] of the maximum size of a medium thumbnail.
     */
    public static function getPicMediumThumbnailSize()
    {
        $conf = self::getKeyFromPicturesConfig('thumbnailMedium');

        if (! is_array($conf)) {
            throw new Exception('thumbnailMedium setting no an array?: see /config/pictures.*');
        }

        return $conf;
    }

    /**
     * Get absolute path of the directory where uploaded pictures should be stored.
     */
    public static function getPicUploadFolder(): string
    {
        return self::getDynFilesPath(true) . self::getPicUploadFolderInDynBaseDir();
    }

    /**
     * Get the path of the directory where uploaded pictures should be stored
     * relative to DynBasePath directory.
     */
    public static function getPicUploadFolderInDynBaseDir(): string
    {
        return self::getKeyFromPicturesConfig('picturesUploadFolder');
    }

    /**
     * Get an absolute url under which pictures are accessible.
     */
    public static function getPicBaseUrl(): string
    {
        return self::getKeyFromPicturesConfig('picturesBaseUrl');
    }

    /**
     * Get the path of the directory where thumbnails should be stored.
     */
    public static function getPicThumbnailsFolder(): string
    {
        return self::getDynFilesPath(true) . self::getKeyFromPicturesConfig('thumbnailFolder');
    }

    /**
     * Note: this size is internal - other limits can be set in http/php server configuration.
     *
     * @return int|float
     */
    public static function getPicMaxSize()
    {
        return self::getKeyFromPicturesConfig('maxFileSize');
    }

    /**
     * Get the maximum size of a picture that is allowed to be stored without
     * resizing (in MB).
     *
     * @return float
     */
    public static function getPicResizeLimit()
    {
        return self::getKeyFromPicturesConfig('resizeLargerThan');
    }

    /**
     * Get the list of allowed picture extensions.
     */
    public static function getPicAllowedExtensions($toDisplay = false): string
    {
        return self::getKeyFromPicturesConfig(
            $toDisplay ? 'allowedExtensionsText' : 'allowedExtensions'
        );
    }

    private function getPicturesConfig(): array
    {
        if (! $this->picturesConfig) {
            $this->picturesConfig = self::getConfig('pictures', 'pictures');
        }

        return $this->picturesConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromPicturesConfig(string $key)
    {
        $picturesConfig = self::instance()->getPicturesConfig();

        return $picturesConfig[$key];
    }
}
