<?php
namespace src\Controllers;

use src\Models\Pictures\OcPicture;
use src\Models\Pictures\Thumbnail;
use src\Utils\Debug\Debug;
use src\Utils\Generators\Uuid;
use src\Models\ChunkModels\UploadModel;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheLog;
use src\Utils\FileSystem\FileUploadMgr;
use src\Utils\FileSystem\FileManager;

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
     * This function handles file upload by AJAX.
     * Please note this file is stored in tmp folder and notthing is save in DB here
     * (this is only a part of picture editing process)
     */
    public function uploadPicsAjax ($parentType, $parentId)
    {
        // only logged users can test
        $this->checkUserLoggedAjax();

        // check parent object and access rights
        if (!is_numeric($parentId) || !is_numeric($parentType)) {
            $this->ajaxErrorResponse ("Icorrect parentId");
        }

        // create parent object
        $parentObj = OcPicture::getParentObj($parentType, $parentId);
        if (!$parentObj) {
            $this->ajaxErrorResponse("Improper parent type/id");
        }

        // use the same upload model and store the files in the right place on server
        $uploadModel = UploadModel::PicUploadFactory($parentType, $parentId);
        try{
            // save uploaded files
            $newFiles = FileUploadMgr::processFileUpload($uploadModel);
        } catch (\RuntimeException $e){
            // some error occured on upload processing
            $this->ajaxErrorResponse($e->getMessage(), 500);
        }

        // FileUploadMgr returns array of new files saved in given directory on server
        // any specific actions can be done in this moment - for example DB update

        // add correct url to uploaded files before return to browser
        $uploadModel->addUrlBaseToNewFilesArray($newFiles);

        $pics = [];
        foreach ($newFiles as $orgFilename => $path) {

            // create OcPicture object for each new pic and save the records in DB
            $pic = OcPicture::getNewPicPlaceholder($parentType, $parentObj);

            if (!$pic->isUserAllowedToModifyIt($this->loggedUser)) {
                // ups.. someone try to hack something here
                // remove the file
                FileManager::removeFile($path);
                $this->ajaxErrorResponse("User is no allowed to add pic to this object");
            }

            $pic->markAsHidden(FALSE);  // added pics are not hidden
            $pic->markAsSpoiler(FALSE); // added pics are not spoilers (assumption)
            $pic->setUuid(FileManager::getFileNameWithoutExtension($path));
            $pic->setFilenameForUrl(FileManager::getFileNameWithExtension($path));

            $pic->setTitle(FileManager::getFileNameWithoutExtension($orgFilename));
            $pic->setOrderIndex($pic->getLastOrderIndexforParent());

            $pic->addToDb();
            $pics[] = $pic->getData();
        }

        // return to browser the list of files saved on server
        $this->ajaxJsonResponse($pics);
    }

    /**
     * This function handle reorder of the pics.
     * As an argument it takes parent-data + ist of uuids (in POST)
     * UUIDs are ordered in given order.
     */
    public function updatePicsOrderAjax($parentType, $parentId)
    {
        // only logged users can test
        $this->checkUserLoggedAjax();

        $uuids = $_POST['uuidsOrder'] ?? null;
        if (is_null($uuids) || !is_array($uuids) || empty($uuids)) {
            $this->ajaxErrorResponse("Wrong uuids array");
        }

        $pics = [];
        $orderIdx = 0;
        foreach($uuids as $uuid) {
            if (!Uuid::isValidUuid($uuid)) {
                $this->ajaxErrorResponse("Invalid UUID");
            }
            $pic = OcPicture::fromUuidFactory($uuid);
            if (!$pic) {
                $this->ajaxErrorResponse("Unknown UUID");
            }
            // check if this pic is assigned to the same parent
            if ($pic->getParentId() != $parentId || $pic->getParentType() != $parentType ) {
                $this->ajaxErrorResponse("Uuid from another parent");
            }

            // check user rights
            if (!$pic->isUserAllowedToModifyIt($this->loggedUser)) {
                $this->ajaxErrorResponse("User is not allowed to modify pic");
            }

            $pic->setOrderIndex($orderIdx++); // set new order index
            $pics[] = $pic;
        }

        foreach ($pics as $pic) {
            $pic->updateOrderInDb();
        }

        $this->ajaxSuccessResponse("Pics order updated");
    }

    /**
     * Update picture title
     *
     * @param string $uuid  UUID of the picture
     * @param string POST['title']  new value of title
     */
    public function updateTitleAjax($uuid)
    {
        // only logged users can test
        $this->checkUserLoggedAjax();

        $title = $_POST['title'] ?? null;
        if (is_null($title)) {
            $this->ajaxErrorResponse("Empty title!");
        }

        $title = strip_tags ($title);
        if($title == '') {
            $title = "-";
        }

        if (!Uuid::isValidUuid($uuid)) {
            $this->ajaxErrorResponse("Invalid UUID");
        }

        $pic = OcPicture::fromUuidFactory($uuid);
        if (!$pic) {
            $this->ajaxErrorResponse("Unknown UUID");
        }

        // check user rights
        if (!$pic->isUserAllowedToModifyIt($this->loggedUser)) {
            $this->ajaxErrorResponse("User is not allowed to modify pic");
        }

        $pic->setTitle($title);
        $pic->updateTitleInDb();
        $this->ajaxSuccessResponse("Title updated");
    }

    /**
     * Remove given picture
     * @param string $uuid  UUID of the pic to remove
     */
    public function removePicAjax($uuid)
    {
        $pic = $this->commonAttrChangeAjax($uuid);
        $pic->remove($this->loggedUser);
        $this->ajaxSuccessResponse("Picture removed");
    }

    /**
     * Add spoiler attribute to given pic
     * @param string $uuid
     */
    public function addSpoilerAttrAjax($uuid)
    {
        $this->changeSpoilerAttrAjax($uuid, TRUE);
    }

    /**
     * Remove spoiler from given pic
     * @param string $uuid
     */
    public function rmSpoilerAttrAjax($uuid)
    {
        $this->changeSpoilerAttrAjax($uuid, FALSE);
    }

    /**
     * Add hidden attribute to given pic
     * @param string $uuid
     */
    public function addHiddenAttrAjax($uuid)
    {
        $this->changeHiddenAttrAjax($uuid, TRUE);
    }

    /**
     * Remove hidden attribute from given pic
     * @param string $uuid
     */
    public function rmHiddenAttrAjax($uuid)
    {
        $this->changeHiddenAttrAjax($uuid, FALSE);
    }

    private function commonAttrChangeAjax($uuid)
    {
        // only logged users can test
        $this->checkUserLoggedAjax();

        if (!Uuid::isValidUuid($uuid)) {
            $this->ajaxErrorResponse("Invalid UUID");
        }
        $pic = OcPicture::fromUuidFactory($uuid);
        if (!$pic) {
            $this->ajaxErrorResponse("Unknown UUID");
        }

        // check user rights
        if (!$pic->isUserAllowedToModifyIt($this->loggedUser)) {
            $this->ajaxErrorResponse("User is not allowed to modify pic");
        }

        return $pic;
    }

    private function changeSpoilerAttrAjax($uuid, $newVal)
    {
        $pic = $this->commonAttrChangeAjax($uuid);
        $pic->markAsSpoiler($newVal);
        $pic->updateSpoilerAttrInDb();
        $this->ajaxSuccessResponse("Spoiler attr. updated");
    }

    private function changeHiddenAttrAjax($uuid, $newVal)
    {
        $pic = $this->commonAttrChangeAjax($uuid);
        $pic->markAsHidden($newVal);
        $pic->updateHiddenAttrInDb();
        $this->ajaxSuccessResponse("Hidden attr. updated");
    }

    /**
     * This method remove picture by give uuid and redirect to parenObject main page
     * @param string $uuid
     *
     * @deprecated - this function will be soon removed (after refactoring pics for logs)
     */
    public function remove($uuid)
    {
        $this->redirectNotLoggedUsers();

        // check the UUID param
        if(!Uuid::isValidUuid($uuid)) {
            $this->displayCommonErrorPageAndExit("Improper UUID!");
        }

        $picture = OcPicture::fromUuidFactory($uuid);
        if (!$picture ) {
            $this->displayCommonErrorPageAndExit("No such picture?!");
        }

        if (!$picture->isUserAllowedToModifyIt($this->loggedUser)) {
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
