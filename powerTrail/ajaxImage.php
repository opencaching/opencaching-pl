<?php
use src\Utils\Database\OcDb;
use lib\SimpleImage;

require_once __DIR__ . '/../lib/common.inc.php';

global $picurl, $picdir;

$destination_path = $picdir.'/';

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

            $image = new SimpleImage();
            $image->load($_FILES['myfile']['tmp_name']);

            if ($image->getHeight() > $image->getWidth() && $image->getHeight()>250) { //portrait
                $image->resizeToHeight(250);
            }
            if ($image->getHeight() < $image->getWidth() && $image->getWidth()>250)  {
                $image -> resizeToWidth(250);
            }
            $image -> save($target_path);

            $query = 'UPDATE `PowerTrail` SET `image`= :1 WHERE `id` = :2';
            $db = OcDb::instance();
            $db->multiVariableQuery($query, $picurl.'/'.$actual_image_name, $powerTrailId);

            $result = '<img src="'.$picurl.'/'.$actual_image_name.'?'.rand(1000, 9999).'" />';
        }
    }
}
?>

<script>window.top.window.stopUpload(<?php echo "'".$result."'"; ?>);</script>
