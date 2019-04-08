<?php
namespace src\Controllers;

use src\Models\Pictures\OcPicture;
use src\Models\Pictures\Thumbnail;
use src\Utils\Debug\Debug;
use src\Utils\Generators\Uuid;

class PictureController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->redirectNotLoggedUsers();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {}

    /**
     * This method remove picture by give uuid and redirect to parenObject main page
     * @param string $uuid
     */
    public function remove($uuid)
    {
        // check the UUID param
        if(!Uuid::isValidUuid($uuid)) {
            $this->displayCommonErrorPageAndExit("Improper UUID!");
        }

        $picture = OcPicture::fromUuidFactory($uuid);
        if (!$picture ) {
            $this->displayCommonErrorPageAndExit("No such picture?!");
        }

        if (!$picture->isUserAllowedToRemoveIt($this->loggedUser)) {
            $this->displayCommonErrorPageAndExit("You don't have permissions to remove this picture");
        }

        if (!$picture->remove($this->loggedUser)) {
            $this->displayCommonErrorPageAndExit("Internal error on picture remove!");
        }

        // removed success - redirect to mainpage of the parent object of the picture
        switch ($picture->getParentType()) {
            case OcPicture::TYPE_CACHE:
                $cache = $picture->getParent();
                $this->view->redirectAndExit($cache->getCacheUrl());

            case OcPicture::TYPE_LOG:
                $log = $picture->getParent();
                $this->view->redirectAndExit($log->getLogUrl());

            default:
                Debug::errorLog("Unsupported parent type: {$this->parentType}");
                $this->displayCommonErrorPageAndExit("Unknown picture parent type?!");
        }
    }

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
        if(!Uuid::isValidUuid($uuid)) {
            $this->view->redirectAndExit(Thumbnail::placeholderUri(Thumbnail::PHD_ERROR_404));
        }

        if (!$size) {
            $size = Thumbnail::SIZE_MEDIUM;
        }

        // locate the thumbnail
        if ($thumbUrl = OcPicture::getThumbUrl($uuid, $showSpoiler, $size)) {
            $this->view->redirectAndExit($thumbUrl);
        } else {
            $this->view->redirectAndExit(Thumbnail::placeholderUri(Thumbnail::PHD_ERROR_INTERN));
        }
    }
}
