<?php
namespace src\Controllers;

use src\Utils\Text\UserInputFilter;
use src\Models\CacheSet\CacheSet;
use src\Utils\FileSystem\FileUploadMgr;
use src\Utils\Img\OcImage;
use src\Models\ChunkModels\UploadModel;
use src\Models\CacheSet\GeopathLogoUploadModel;
use src\Models\OcConfig\OcConfig;
use src\Utils\Generators\Uuid;

class GeoPathController extends BaseController
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
    {
        $this->searchByName(); // Temporary. To be removed in the future
    }

    /**
     * Search GeoPaths by name. Used by search engine in topline
     */
    public function searchByName()
    {
        if (isset($_REQUEST['name'])) {
            $searchStr = UserInputFilter::purifyHtmlString($_REQUEST['name']);
            $searchStr = strip_tags($searchStr);
        } else {
            $searchStr = null;
        }
        $this->view->setVar('geoPaths', CacheSet::getCacheSetsByName($searchStr));
        $this->view->setVar('searchStr', $searchStr);
        $this->view->setTemplate('geoPath/searchByName');
        $this->view->buildView();
    }

    /**
     * This is fast replacement for ajaxImage.php
     */
    public function upoadLogoAjax($geoPathId)
    {
        if (!$this->loggedUser) {
            $this->ajaxErrorResponse("User not authorized!");
        }

        if (!$geoPath = CacheSet::fromCacheSetIdFactory($geoPathId)){
            $this->ajaxErrorResponse("No such geopath!");
        }

        if (!$geoPath->isOwner($this->loggedUser)) {
            $this->ajaxErrorResponse("Logged user is not an geopath owner!");
        }

        $uploadModel = GeopathLogoUploadModel::forGeopath($geoPathId);

        try {
            $tmpLogoFile = FileUploadMgr::processFileUpload($uploadModel);
        } catch (\RuntimeException $e){
            // some error occured on upload processing
            $this->ajaxErrorResponse($e->getMessage(), 500);
        }

        // FileUploadMgr returns single filename saved in server tmp directory on server

        $newLogoPath = OcConfig::getDynFilesPath(true) . CacheSet::DIR_LOGO_IMG . '/' . Uuid::create();

        // resize the new logo
        $newLogoPath = OcImage::createThumbnail($uploadModel->getDirAtServer().'/'.$tmpLogoFile, $newLogoPath, [250,250]);

        // create URL of the image
        $newLogoFileUrl = CacheSet::DIR_LOGO_IMG .'/'.basename($newLogoPath);

        // new log is ready to use - update DB
        $geoPath->updateLogoImg($newLogoFileUrl);

        $this->ajaxJsonResponse($newLogoFileUrl);
    }
}
