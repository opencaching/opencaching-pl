<?php

use Utils\Uri\Uri;

return function () {

  $chunkCss = Uri::getLinkWithModificationTime('/tpl/stdstyle/chunks/upload/upload.css');
  $chunkJs  = Uri::getLinkWithModificationTime('/tpl/stdstyle/chunks/upload/upload.js');

?>

<link rel="stylesheet" href="<?=$chunkCss?>">
<script src="<?=$chunkJs?>"></script>

<script id="upload_chunkDialogTpl" type="text/x-handlebars-template">
  <?php
  // template for upload dialog popup
  $load = __DIR__."/uploadDialog.tpl.php";
  require($load);
  ?>
</script>

<script id="upload_filePreviewTpl" type="text/x-handlebars-template">
  <?php
  // template for upload preview entry
  $load = __DIR__."/uploadedFilePreviewTpl.tpl.php";
  require($load);
  ?>
</script>

<?php
};

