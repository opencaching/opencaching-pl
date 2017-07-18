<?php

use Utils\FileSystem\FileManager;
use Utils\Uri\Uri;

require_once('./lib/common.inc.php');

if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
}

/** @var View */
$view = tpl_getView();
tpl_set_tplname('qrCodeGen/qrcode');
$view->addLocalCss(
    Uri::getLinkWithModificationTime('tpl/stdstyle/qrCodeGen/qrcode.css'));



//set it to writable location, a place for temp generated PNG files
$qrCodesDirName = 'tmp/qrcodes/';
$qrCodesDir = $dynbasepath . $qrCodesDirName;

if (!file_exists($qrCodesDir)){
    mkdir($qrCodesDir);
}

$errorCorrectionLevel = 'L';
/*if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L', 'M', 'Q', 'H'))){
    $errorCorrectionLevel = $_REQUEST['level'];
}*/

$matrixPointSize = 4;
/*if (isset($_REQUEST['size'])){
    $matrixPointSize = min(max((int) $_REQUEST['size'], 1), 10);
}*/

$qrCodeText = null;
if ( isset($_REQUEST['qrCodeText']) ) {
    $qrCodeText = trim($_REQUEST['qrCodeText']);
}

if( empty($qrCodeText)){ // load default text value if neccessary
    $qrCodeText = $config['qrCodeUrl'];
}

// remove images older then 1 hour
FileManager::removeFilesOlderThan($qrCodesDir, "*.png", 60*60);

$labelFileName = md5($qrCodeText).'.png';
$qrCodeFileName = 'qrCode_'.$labelFileName;

$qrCodeFile = $qrCodesDir . $qrCodeFileName;
$labelFile = $qrCodesDir . $labelFileName;


// generate QR-code with $qrCodeText to $filename png file
include_once "./lib/phpqrcode/qrlib.php";
tpl_set_var('qrCodeText', $qrCodeText);
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

tpl_set_var('ocLabelImgUrl', '/'.$qrCodesDirName.$labelFileName);
tpl_set_var('qrCodeImgUrl', '/'.$qrCodesDirName.$qrCodeFileName);


tpl_BuildTemplate();
