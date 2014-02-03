<?php
$rootpath =  __DIR__ . '/../';
require_once __DIR__ . '/../lib/common.inc.php';
db_disconnect();

$destination_path = $picdir.'/';
$powerTrailId = $_REQUEST['powerTrailId'];

    $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg");

    $name = $_FILES['myfile']['name'];
    $size = $_FILES['myfile']['size'];
    if (strlen($name)) {
        list($txt, $ext) = explode(".", $name);
        $ext = strtolower($ext);
        if (in_array($ext, $valid_formats)) {
            if ($size < (1024 * 1024 * 2)) { // Image size max 2 MB
                $actual_image_name = powerTrailBase::powerTrailLogoFileName . $powerTrailId . "." . $ext;

                $result = 0;
                $target_path = $destination_path . $actual_image_name;

                include (__DIR__ . '/SimpleImage.php');
                $image = new SimpleImage();
                $image -> load($_FILES['myfile']['tmp_name']);
                if ($image->getHeight() > $image->getWidth() && $image->getHeight()>250) { //portrait
                    $image->resizeToHeight(250);
                }
                if ($image->getHeight() < $image->getWidth() && $image->getWidth()>250)  {
                    $image -> resizeToWidth(250);
                }
                $image -> save($target_path);

                $query = 'UPDATE `PowerTrail` SET `image`= :1 WHERE `id` = :2';
                $db = new dataBase(false);
                $db->multiVariableQuery($query, $picurl.'/'.$actual_image_name, $powerTrailId);

                $result = '<img src="'.$picurl.'/'.$actual_image_name.'?'.rand(1000, 9999).'" />';
            }
        }
    }
?>

<script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo "'".$result."'"; ?>);</script>
