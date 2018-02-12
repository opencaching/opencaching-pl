<?php

namespace Controllers;

use PHPQRCode\QRcode;
use Utils\FileSystem\FileManager;
use Utils\Uri\Uri;
use Utils\Generators\TextGen;

class UserUtilsController extends BaseController
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
        // there is nothing to show here :)
    }

    /**
     * This method display QR-code generator
     */
    public function qrCodeGen()
    {
        global $dynbasepath, $config; //TODO: remove it from here


        if(!$this->isUserLogged()){
            $this->redirectToLoginPage();
            exit;
        }


        $this->view->setTemplate('qrCodeGen/qrcode');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('tpl/stdstyle/qrCodeGen/qrcode.css'));


        //set it to writable location, a place for temp generated PNG files
        $qrCodesDirName = 'tmp/qrcodes/';
        $qrCodesDir = $dynbasepath . $qrCodesDirName;

        if (!file_exists($qrCodesDir)){
            mkdir($qrCodesDir);
        }

        $qrCodeText = null;
        if ( isset($_REQUEST['qrCodeText']) ) {
            $qrCodeText = trim($_REQUEST['qrCodeText']);
        }

        if( empty($qrCodeText)){ // load default text value if neccessary
            $qrCodeText = $config['qrCodeUrl'];
        }

        // remove images older then 1 hour
        FileManager::removeFilesOlderThan($qrCodesDir, "*.png", 60*60);

        $labelFileName = TextGen::randomText(12).'.png';
        $qrCodeFileName = 'qrCode_'.$labelFileName;

        $qrCodeFile = $qrCodesDir . $qrCodeFileName;
        $labelFile = $qrCodesDir . $labelFileName;


        // generate QR-code with $qrCodeText to $filename png file
        $this->view->setVar('qrCodeText', $qrCodeText);

        // const values best fit to our usage
        $errorCorrectionLevel = 'L';
        $matrixPointSize = 4;
        QRcode::png($qrCodeText, $qrCodeFile, $errorCorrectionLevel, $matrixPointSize, 2);

        // add OC background to result image
        $qrCodeImg = imagecreatefrompng($qrCodeFile);

        // OC background have 171 x 284 - center qrCode
        $qrCodeWidth = imagesx($qrCodeImg);
        $xd = 86 - ($qrCodeWidth / 2);
        $yd = 142 - ($qrCodeWidth / 2);

        $ocBackgroundImg = imagecreatefromjpeg($GLOBALS['rootpath'] . '/images/' . $config['qrCodeLogo']);

        // merge both images
        imagecopymerge($ocBackgroundImg, $qrCodeImg, $xd, $yd, 0, 0, $qrCodeWidth, $qrCodeWidth, 100);

        // Output and free from memory
        imagepng($ocBackgroundImg, $labelFile);

        imagedestroy($qrCodeImg);
        imagedestroy($ocBackgroundImg);

        $this->view->setVar('ocLabelImgUrl', '/'.$qrCodesDirName.$labelFileName);
        $this->view->setVar('qrCodeImgUrl', '/'.$qrCodesDirName.$qrCodeFileName);


        $this->view->buildView();
    }

}

