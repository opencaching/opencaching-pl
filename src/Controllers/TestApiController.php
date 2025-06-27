<?php

namespace src\Controllers;

use RuntimeException;
use src\Controllers\Core\ApiBaseController;
use src\Models\ChunkModels\UploadModel;
use src\Utils\FileSystem\FileUploadMgr;
use src\Utils\Uri\HttpCode;

class TestApiController extends ApiBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->checkUserLoggedAjax();

        // test pages are only for users with AdvancedUsers role
        if (! $this->loggedUser->hasAdvUserRole()) {
            $this->ajaxErrorResponse(
                'Sorry, no such page.',
                HttpCode::STATUS_NOT_FOUND
            );
        }
    }

    /**
     * This is test of server-side actions for file upload with UploadChunk
     */
    public function uploadAjax()
    {
        $uploadModel = UploadModel::TestTxtUploadFactory();

        try {
            // save uploaded files
            $newFiles = FileUploadMgr::processFileUpload($uploadModel);
        } catch (RuntimeException $e) {
            // some error occured on upload processing
            $this->ajaxErrorResponse($e->getMessage(), 500);
        }

        // FileUploadMgr returns array of new files saved in given directory on server
        // any specific actions can be done in this moment - for example DB update

        // add correct url to uploaded files before return to browser
        $uploadModel->addUrlBaseToNewFilesArray($newFiles);

        // return to browser the list of files saved on server
        $this->ajaxJsonResponse($newFiles);
    }
}
