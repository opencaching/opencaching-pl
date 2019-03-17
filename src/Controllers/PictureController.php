<?php
namespace src\Controllers;

use src\Models\Pictures\OcPicture;
use src\Models\Pictures\Thumbnail;
use src\Utils\Generators\Uuid;

class PictureController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {}

    /**
     * This function redirects browser to the thumbnail of the picture with given uuid
     * If showSpoiler is true thumbanil will be display even if the picture is marked as spoiler
     *
     * @param string $uuid
     * @param boolean $showSpoiler
     */
    public function thumbSizeSmall($uuid, $showSpoiler = false) {
        return $this->thumb($uuid, $showSpoiler, Thumbnail::SIZE_SMALL);
    }

    public function thumbSizeMedium($uuid, $showSpoiler = false) {
        return $this->thumb($uuid, $showSpoiler, Thumbnail::SIZE_MEDIUM);
    }

    private function thumb($uuid, $showSpoiler=false, $size = null)
    {

        // check the UUID param
        if(!Uuid::isValidUpperCaseUuid($uuid)) {
            $this->view->redirectAndExit(Thumbnail::placeholderUri(Thumbnail::ERROR_404));
        }

        if (!$size) {
            $size = Thumbnail::SIZE_MEDIUM;
        }

        // locate the thumbnail
        if ($thumbUrl = OcPicture::getUrl($uuid, $showSpoiler, $size)) {
            $this->view->redirectAndExit($thumbUrl);
        } else {
            $this->view->redirectAndExit(Thumbnail::placeholderUri(Thumbnail::ERROR_INTERN));
        }
    }
}