<?php
use src\Utils\Uri\Uri;
use src\Utils\I18n\I18n;
use src\Models\News\News;

/**
 * This chunk is used to load TinyMCE
 *
 * To use simply add in the template file:
 * $view->callChunk('tinyMCE');
 * and later use:
 * <textarea class="tinymce"></textarea>
 *
 * Parameters:
 * $media - support for insert/edit media like YouTube videos. Default is true. Set it to false to disable media feature
 * $selector - selector to use in <textarea>. Can be ".class" or "#id" or even "viewcache.editor". Default selector is class "tinymce".
 * $imgUploader - url to call with file upload - if not set img upload is disabled
 *
 * Size of editor set via CSS. For example:
 * textarea.desc, textarea.cachelog {
 *     width: 100%;
 *     height: 30em;
 * }
 *
 */

return function ($media = true, $selector = '.tinymce', $filePickerCallback=null) {
    //start of chunk
    $mediatxt = ($media == true) ? ' media' : '';
    ?>

<!-- TinyMCE chunk start -->
<script src="<?=Uri::getLinkWithModificationTime('/js/libs/tinyMCE/5.7.1/tinymce.min.js')?>"></script>
<script>
  tinymce.init({
    selector: "<?=$selector?>",
    <?php if($filePickerCallback) { ?>
      file_picker_callback: <?=$filePickerCallback?>,
    <?php } //if($filePickerCallback) ?>
    image_advtab: true,
    contextmenu: false, /* disable contextmenu (right-click) - native browser context menu will be displaied instead */
    image_title: true,
    menubar: false,
    toolbar_items_size: "small",
    browser_spellcheck: true,
    relative_urls: false,
    remove_script_host: false,
    entity_encoding: "raw",
    fontsize_formats: "8px 10px 11px 12px 13px 14px 18px 24px 36px",
    content_style: "* { margin: 0px 0px 0.5em 0px;} p, ul {font-size: 12px; font-family: arial, sans serif;} ol {padding: 0px 0px 0px 25px; font-family: arial, sans serif;} sub {font-size: 0.7em;} sup {font-size: 0.7em;} br { margin: 0;} body {margin: 3px;} .mce-content-body img.mce-pagebreak{background:transparent url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAB2wAAAAUCAMAAAB28j3QAAAAPFBMVEVMaXG7yd/fybu7wdL/6s67u7vSwbv/3sjI3v/O6v+7u8nB0u7Bu7vJu7u7u8Hu0sHBycm7ydLJybvJu8Gz2eUyAAAACnRSTlMA////P///s7M/0Um8/wAAAQdJREFUeNrt28tuwjAUBNCJzU1CEqCP///XLqhUlS5pKx7nbDxbW4pGtuMEAAAAAAAAAAAA4L+NtSbJqXqSqapqTjJW1XF/Hmu2SgBwVdn2JDlUT041J1OtyTgkbTmPAMB1Zfuyzcm0vPXkMCRfJXs67pUtAPxC2Q5tSNrQenbbmiTTZ8mO3c4W7klVVdXT5CebrnzH+Vy205K871vPbpuTZKo5Y1UNOd/ZLs/6KcsPkZ9ptspWlm+4bHOYp54fO9u25HJna91kZXsPXzVwg8fIaa/jmnZ5Z7vbVsfIAPA7ZTvVcZ/mb2QA+LOyzaEn7fs72yHZbYN3tgAAAAAAAAAAAAAA8OA+APMLKOOI5E5PAAAAAElFTkSuQmCC) repeat-y scroll center center;padding:10px}",
    language: "<?=I18n::getCurrentLang()?>",
    toolbar1: "newdocument | styleselect formatselect fontselect fontsizeselect",
    toolbar2: "cut copy paste searchreplace | bullist numlist | outdent indent | undo redo | nonbreaking link unlink pagebreak image<?=$mediatxt?> | code_editor fullscreen",
    toolbar3: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | hr | subscript superscript | charmap | forecolor backcolor",
    external_plugins: {
      'code_editor' : '/js/tinyMCE_external_plugins/code_editor/plugin.js'
    },
    plugins: [
      "advlist autolink autosave link image lists charmap hr anchor spellchecker searchreplace wordcount code_editor fullscreen nonbreaking",
      "paste pagebreak <?=$mediatxt?>"
    ],
    pagebreak_separator : "<?=News::READ_MORE_TAG?>",
  });
</script>
<!-- TinyMCE chunk end -->

<?php
};

// end of chunk - nothing should be added below
