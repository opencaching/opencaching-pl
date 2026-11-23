<?php

namespace src\Models\Badges;

use src\Models\ChunkModels\UploadModel;

class BadgeImageUploadModel extends UploadModel
{
    protected function __construct()
    {
        parent::__construct();
    }

    public static function forBadge(int $badgeId): self
    {
        $obj = new self();
        $obj->dialog->title = tr('badge_image_upload');
        $obj->allowedTypesRegex = 'image/png|image/webp';
        $obj->maxFileSize = 120 * 1024;
        $obj->setMaxFileNumber(1);
        $obj->submitUrl = '/Badges.Badges/uploadImageAjax/' . $badgeId;
        $obj->setDirs(self::DEFAULT_TMP_DIR);
        return $obj;
    }
}
