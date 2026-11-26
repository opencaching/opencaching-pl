<?php

namespace src\Models\Badges;

use src\Models\ChunkModels\UploadModel;

class BadgeLevelImageUploadModel extends UploadModel
{
    protected function __construct()
    {
        parent::__construct();
    }

    public static function forLevel(int $badgeId, int $level): self
    {
        $obj = new self();
        $obj->dialog->title = tr('badge_level_image_upload');
        $obj->allowedTypesRegex = 'image/png|image/webp';
        $obj->maxFileSize = 120 * 1024;
        $obj->setMaxFileNumber(1);
        $obj->submitUrl = '/Admin.BadgesAdmin/uploadLevelImageAjax/' . $badgeId . '/' . $level;
        $obj->setDirs(self::DEFAULT_TMP_DIR);
        return $obj;
    }
}
