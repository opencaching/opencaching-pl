<?php
use Utils\Uri\Uri;

/**
 * UploadChunk is a simple browser-side plugin to handle file upload dialog
 * with drag&drop support, progress bar etc.
 *
 * See /test/upload for example of usage.
 *
 */
return function () {

  $chunkCss = Uri::getLinkWithModificationTime('/tpl/stdstyle/chunks/upload/upload.css');
  $chunkJs  = Uri::getLinkWithModificationTime('/tpl/stdstyle/chunks/upload/upload.js');

?>

<link rel="stylesheet" href="<?=$chunkCss?>">
<script src="<?=$chunkJs?>"></script>

<script id="upload_chunkDialogTpl" type="text/x-handlebars-template">
  <?php
  // template for upload dialog popup
  require(__DIR__.'/uploadDialog.tpl.php');
  ?>
</script>

<script id="upload_filePreviewTpl" type="text/x-handlebars-template">
  <?php
  // template for upload preview entry
  require(__DIR__.'/uploadedFilePreviewTpl.tpl.php');
  ?>
</script>

<?php
};

