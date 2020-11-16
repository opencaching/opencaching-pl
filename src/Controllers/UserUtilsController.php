<?php

namespace src\Controllers;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use src\Utils\FileSystem\FileManager;
use src\Utils\Generators\TextGen;
use src\Utils\Uri\Uri;

class UserUtilsController extends BaseController
{

    private const QR_CODES_DIR_NAME = 'tmp/qrcodes/';

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
        if (!$this->isUserLogged()) {
            $this->redirectToLoginPage();
        }

        $this->view->setTemplate('qrCodeGen/qrcode');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/views/qrCodeGen/qrcode.css'));

        $qrCodesDir = $this->ocConfig->getDynamicFilesPath() . self::QR_CODES_DIR_NAME;

        if (!file_exists($qrCodesDir)) {
            mkdir($qrCodesDir);
        }

        // remove images older then 1 hour
        FileManager::removeFilesOlderThan($qrCodesDir, "*.png", 60 * 60);

        $qrCodeText = trim($_REQUEST['qrCodeText'] ?? $this->ocConfig::getSiteQrCodeText());

        $labelFileName = TextGen::randomText(12) . '.png';
        $qrCodeFileName = 'qrCode_' . $labelFileName;

        $qrCodeFile = $qrCodesDir . $qrCodeFileName;
        $labelFile = $qrCodesDir . $labelFileName;

        // generate QR-code with $qrCodeText to $filename png file
        $this->view->setVar('qrCodeText', $qrCodeText);

        $qrCode = new QrCode($qrCodeText);
        $qrCode->setSize(132);
        $qrCode->setMargin(5);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::MEDIUM());
        $qrCode->writeFile($qrCodeFile);

        // add OC background to result image
        $qrCodeImg = imagecreatefrompng($qrCodeFile);

        // OC background have 171 x 284 - center qrCode
        $qrCodeWidth = imagesx($qrCodeImg);
        $xd = 86 - ($qrCodeWidth / 2);
        $yd = 142 - ($qrCodeWidth / 2);

        $ocBackgroundImg = imagecreatefromjpeg(__DIR__ . '/../../public/images/qrCodeGen/' . $this->ocConfig::getSiteQrCodeImage());

        // merge both images
        imagecopymerge($ocBackgroundImg, $qrCodeImg, $xd, $yd, 0, 0, $qrCodeWidth, $qrCodeWidth, 100);

        // Output and free from memory
        imagepng($ocBackgroundImg, $labelFile);

        imagedestroy($qrCodeImg);
        imagedestroy($ocBackgroundImg);

        $this->view->setVar('ocLabelImgUrl', '/' . self::QR_CODES_DIR_NAME . $labelFileName);
        $this->view->setVar('qrCodeImgUrl', '/' . self::QR_CODES_DIR_NAME . $qrCodeFileName);


        $this->view->buildView();
    }

}
