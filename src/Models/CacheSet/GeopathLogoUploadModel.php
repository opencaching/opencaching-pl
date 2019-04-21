<?php
namespace src\Models\CacheSet;

use src\Models\ChunkModels\UploadModel;

/**
 * This is model of geopath logo upload
 *
 */
class GeopathLogoUploadModel extends UploadModel
{
    protected function __construct()
    {
        parent::__construct();
    }

    public static function forGeopath($id)
    {
        $obj = new self();
        $obj->dialog->title = tr('gp_logoUpdateTitle');
        $obj->dialog->preWarning = tr('gp_logoUpdateInfo');
        $obj->allowedTypesRegex = self::MIME_IMAGE_WITH_GD_SUPPORT;
        $obj->setMaxFileSize(3);
        $obj->setMaxFileNumber(1);

        // script hich handle the logo upload
        $obj->submitUrl = "/geoPath/uploadLogoAjax/$id";

        // logo is always resized to given size - so oryginal img. is not important
        $obj->setDirs(self::DEFAULT_TMP_DIR);
        return $obj;
    }
}
