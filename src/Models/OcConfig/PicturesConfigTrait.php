<?php
namespace src\Models\OcConfig;

/**
 * This trait group access to email settings stored in /config/email.* conf. files
 * BEWARE OF FUNCTIONS NAME COLLISION BETWEEN CONFIG TRAITS!
 */
trait PicturesConfigTrait {

    protected $picturesConfig = null;

    /**
     *
     * @return array [width, height] of max size of the small thumbnail
     */
    public static function getPicSmallThumbnailSize()
    {
         $conf = self::getPicturesVar('thumbnailSmall');
         if(!is_array($conf)){
            throw new \Exception("thumbnailSmall setting not an array?: see /config/pictures.*");
         }
         return $conf;
    }

    /**
     *
     * @return array [width, height] of max size of the small thumbnail
     */
    public static function getPicMediumThumbnailSize()
    {
        $conf = self::getPicturesVar('thumbnailMedium');
        if(!is_array($conf)){
            throw new \Exception("thumbnailMedium setting no an array?: see /config/pictures.*");
        }
        return $conf;
    }

    /**
     * former: $picdir
     * @return string - path to the folder where uploaded pictures should be stored
     */
    public static function getPicUploadFolder()
    {
        return self::getDynFilesPath(true) . self::getPicturesVar('picturesUploadFolder');
    }

    /**
     * former: $picurl
     * @return string - base of the url under which pics are accessible
     */
    public static function getPicBaseUrl()
    {
        return self::getPicturesVar('picturesBaseUrl');
    }

    /**
     * @return string - path to the folder where thumbnails should be stored
     */
    public static function getPicThumbnailsFolder(){
        return self::getDynFilesPath(true) . self::getPicturesVar('thumbnailFolder');
    }

    /**
     * Read config from files
     * @return array
     */
    private function getPicturesConfig(){
        if ($this->picturesConfig == null) {
            $this->picturesConfig = self::getConfig('pictures', 'pictures');
        }
        return $this->picturesConfig;
    }

    /**
     * Get Var from pictures.* files
     *
     * @param string $varName
     * @throws \Exception
     * @return string
     */
    private static function getPicturesVar($varName)
    {
        $config = self::instance()->getPicturesConfig();
        if (!is_array($config) || !isset($config[$varName]) ) {
            throw new \Exception("Invalid $varName setting: see /config/pictures.*");
        }
        return $config[$varName];
    }
}
