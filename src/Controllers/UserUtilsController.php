<?php

namespace src\Controllers;

use PHPQRCode\QRcode;
use src\Utils\FileSystem\FileManager;
use src\Utils\Generators\TextGen;
use src\Utils\Uri\Uri;

class UserUtilsController extends BaseController
{
    public function isCallableFromRouter(string $actionName): bool
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
        global $config; //TODO: remove it from here

        $this->redirectNotLoggedUsers();

        $this->view->setTemplate('qrCodeGen/qrcode');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/views/qrCodeGen/qrcode.css')
        );

        //set it to writable location, a place for temp generated PNG files
        $qrCodesDirName = 'tmp/qrcodes/';

        $qrCodesDir = $this->ocConfig->getDynamicFilesPath() . $qrCodesDirName;

        if (! file_exists($qrCodesDir)) {
            mkdir($qrCodesDir);
        }

        $qrCodeText = null;

        if (isset($_REQUEST['qrCodeText'])) {
            $qrCodeText = trim($_REQUEST['qrCodeText']);
        }

        if (empty($qrCodeText)) { // load default text value if necessary
            $qrCodeText = $config['qrCodeUrl'];
        }

        // remove images older then 1 hour
        FileManager::removeFilesOlderThan($qrCodesDir, '*.png', 60 * 60);

        $labelFileName = TextGen::randomText(12) . '.png';
        $qrCodeFileName = 'qrCode_' . $labelFileName;

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

        $ocBackgroundImg = imagecreatefromjpeg(__DIR__ . '/../../public/images/qrCodeGen/' . $config['qrCodeLogo']);

        // merge both images
        imagecopymerge($ocBackgroundImg, $qrCodeImg, $xd, $yd, 0, 0, $qrCodeWidth, $qrCodeWidth, 100);

        // Output and free from memory
        imagepng($ocBackgroundImg, $labelFile);

        imagedestroy($qrCodeImg);
        imagedestroy($ocBackgroundImg);

        $this->view->setVar('ocLabelImgUrl', '/' . $qrCodesDirName . $labelFileName);
        $this->view->setVar('qrCodeImgUrl', '/' . $qrCodesDirName . $qrCodeFileName);

        $this->view->buildView();
    }
}
