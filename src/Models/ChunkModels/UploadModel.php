<?php

namespace src\Models\ChunkModels;

use src\Models\OcConfig\OcConfig;
use src\Utils\Text\TextConverter;

/**
 * This is model of file upload operation.
 * The main purpose of it is to store in one place parameters of upload used in process of uploads.
 * This params can be used both at browser and server side.
 *
 * See /test/upload for example of useage of it.
 */
class UploadModel {

  const MIME_IMAGE = 'image/*';
  const MIME_AUDIO = 'audio/*';
  const MIME_ANYFILE = 'image/*|audio/*|application/*|video/*|text/*';
  const MIME_TEXT = 'text/*';

  const DEFAULT_TMP_DIR = 'move files to server tmp dir';

  // public data send in JSON to browser

  /** @var DialogContent */
  public $dialog;
  public $maxFilesNumber;

  // MIME: https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types
  // text.*|image.*|application.*
  public $allowedTypesRegex;
  public $maxFileSize; //in MB
  public $submitUrl;
  public $formVarName = 'fileToUpload'; // name of the file name in $_FILES after upload


  // private data used server side only
  protected $dirAtServer = null; // directory to store files (under dynamic-files-dir!)
  protected $urlBase = null;     // rootPath to the uploaded file

  protected function __construct()
  {
    $this->dialog = new DialogContent();
    $this->maxFilesNumber = 1;
    $this->allowedTypesRegex = null;
    $this->setMaxFileSize(3);
    $this->submitUrl = null;
  }

  /**
   * Test upload model - just txt files up to 1MB stored in $dynPath/tpm/test/upload
   * @return self
   */
  public static function TestTxtUploadFactory()
  {
      $obj = new self();
      $obj->dialog->title = "TestUploadHeader";
      $obj->dialog->preWarning = "This is just test of the upload. Only small txt files are allowed";
      $obj->allowedTypesRegex = self::MIME_TEXT;
      $obj->setMaxFileSize(1);
      $obj->setMaxFileNumber(2);
      $obj->submitUrl = '/test/uploadAjax';
      $obj->setDirs('/tmp/test/upload');
      return $obj;
  }

  // add more upload configurations like TestTxtUploadFactory here...

  public function addUrlBaseToNewFilesArray(array &$newFiles){
      array_walk(
          $newFiles,
          function(&$file, $key, $urlBase) { $file = $urlBase.'/'.$file; },
          $this->getBaseUrl());
  }

  public function getJsonParams()
  {
      return json_encode($this, JSON_PRETTY_PRINT);
  }

  public function getDirAtServer()
  {
      return $this->dirAtServer;
  }

  public function getBaseUrl()
  {
      if(!$this->urlBase){
        throw new \Exception("Trying to use unset baseUrl for uploaded file!");
      }
      return $this->urlBase;
  }

  /**
   * Set the dir where uploaded files should be stored
   *
   * self::DEFAULT_TMP_DIR can be used as $dirInDirBasePath to tore files in tmp dir
   *
   * @param string $dirInDirBasePath - directory related to "dynamicBasePath"
   * @param string $urlPath - optional url path under which file can be accessed
   */
  protected function setDirs($dirInDirBasePath, $urlPath=null)
  {
      if($dirInDirBasePath == self::DEFAULT_TMP_DIR) {
          $this->urlBase = null; // files are not accessible in TMP dir - it will be moved in separate code
          $this->dirAtServer = sys_get_temp_dir();
          return;
      }

      if(!$urlPath) {
          $this->urlBase = $dirInDirBasePath;
      } else {
          $this->urlBase = $urlPath;
      }

      $this->dirAtServer = OcConfig::getDynFilesPath(true).$dirInDirBasePath;

      if (!is_dir($this->dirAtServer)) {
          throw(new \Exception("Improper path to save uploaded files! ({$this->dirAtServer})"));
      }
  }

  protected function setMaxFileSize ($maxSizeInMB)
  {
      $this->maxFileSize = $maxSizeInMB * 1024 * 1024;
      $phpMaxFilesize = TextConverter::bytesNumberWithUnitToBytes(ini_get('upload_max_filesize'));

      if($this->maxFileSize > $phpMaxFilesize) {
          throw new \Exception("Uploaded size in model {$this->maxFileSize} > php.ini::upload_max_filesize ($phpMaxFilesize)");
      }
  }

  protected function setMaxFileNumber ($maxNumberOfFiles)
  {
      $this->maxFilesNumber = $maxNumberOfFiles;
  }
}


class DialogContent
{
    public $title = null;
    public $preWarning = null;
    public $preInfo = null;
}
