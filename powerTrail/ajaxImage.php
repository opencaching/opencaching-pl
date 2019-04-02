<?php
use src\Utils\Database\OcDb;
use src\Models\OcConfig\OcConfig;
use src\Utils\Img\OcImage;

require_once __DIR__ . '/../lib/common.inc.php';

$destination_path = OcConfig::getPicUploadFolder(true).'/';

if (isset($_REQUEST['powerTrailId'])){
    $powerTrailId = $_REQUEST['powerTrailId'];
} else {
    $powerTrailId = null;
}

if(isset($_FILES['myfile'])){
    $name = $_FILES['myfile']['name'];
    $size = $_FILES['myfile']['size'];
} else {
    $name = null;
    $size = null;
}

$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg");
$result = "-error-";

if (!is_null($powerTrailId) && !empty($name) && !empty($_FILES['myfile']['tmp_name'])) {

    $fileInfo = pathinfo($name);
    $txt = $fileInfo['filename'];
    $ext = strtolower($fileInfo['extension']);

    if (in_array($ext, $valid_formats)) {
        if ($size < (1024 * 1024 * 2)) { // Image size max 2 MB
            $actual_image_name = powerTrailBase::powerTrailLogoFileName . $powerTrailId . "." . $ext;

            $target_path = $destination_path . $actual_image_name;

            OcImage::createThumbnail($_FILES['myfile']['tmp_name'], $target_path, [250,250]);

            $query = 'UPDATE `PowerTrail` SET `image`= :1 WHERE `id` = :2';
            $db = OcDb::instance();
            $db->multiVariableQuery($query, OcConfig::getPicBaseUrl().'/'.$actual_image_name, $powerTrailId);

            $result = '<img src="'.OcConfig::getPicBaseUrl().'/'.$actual_image_name.'?'.rand(1000, 9999).'" />';
        }
    }
}
?>

<script>window.top.window.stopUpload(<?php echo "'".$result."'"; ?>);</script>
