<?php

namespace lib\Objects\ChunkModels;

use lib\Objects\OcConfig\OcConfig;

class UploadModel {

  const MIME_IMAGE = 'image/*';
  const MIME_AUDIO = 'audio/*';
  const MIME_ANYFILE = 'image/*|audio/*|application/*|video/*|text/*';

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
  private $dirAtServer = null; // directory to store files (under dynamic-files-dir!)
  private $urlBase = null;     // rootPath to the uploaded file

  private function __construct()
  {
    $this->dialog = new DialogContent();
    $this->maxFilesNumber = 1;
    $this->allowedTypesRegex = null;
    $this->maxFileSize = 3.5 * 1024 * 1024; //TODO: read from config
    $this->submitUrl = null;
  }

/*
    $this->view->setVar('maxAttachmentSize', $config['limits']['image']['filesize']);
    $this->view->setVar('maxPicResolution', $config['limits']['image']['pixels_text']);
    $this->view->setVar('picAllowedFormats', $config['limits']['image']['extension_text']);
*/

  /**
   * Test upload model - just txt files up to 1MB stored in $dynPath/tpm/test/upload
   * @return self
   */
  public static function TestTxtUploadFactory()
  {
      $obj = new self();
      $obj->dialog->title = "TestUploadHeader";
      $obj->dialog->preWarning = "This is just test of the upload. Only small txt files are allowed";
      $obj->allowedTypesRegex = self::MIME_ANYFILE;
      $obj->maxFileSize = 100 * 1024 * 1024;  //1MB
      $obj->maxFilesNumber = 2;
      $obj->submitUrl = '/test/uploadAjax';
      $obj->setDirAtServer('/tmp/test/upload');
      return $obj;
  }



  public function addUrlBaseToNewFilesArray(array &$newFiles){
      array_walk($newFiles, function(&$file, $key, $urlBase) { $file = $urlBase.'/'.$file; }, $this->getBaseUrl());
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
    return $this->urlBase;
  }

  public function setDirAtServer($dir)
  {
      $this->urlBase = $dir;

      $ocConfig = OcConfig::instance();
      $this->dirAtServer = $ocConfig->getDynamicFilesPath().$dir;
  }
}


class DialogContent
{
    public $title = null;
    public $preWarning = null;
    public $preInfo = null;


}
