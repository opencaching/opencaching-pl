<?php
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
 * 
 * Size of editor set via CSS. For example:
 * textarea.desc, textarea.cachelog {
 *     width: 100%;
 *     height: 30em;
 * }
 * 
 */

return function ($media = true, $selector = '.tinymce') {
    //start of chunk
    $mediatxt = ($media == true) ? ' media' : '';
    ?>

<!-- TinyMCE chunk start -->
<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<script type="text/javascript">
  tinymce.init({
    selector: "<?=$selector?>",
    menubar: false,
    toolbar_items_size: "small",
    browser_spellcheck: true,
    relative_urls: false,
    remove_script_host: false,
    entity_encoding: "raw",
    fontsize_formats: "8px 10px 11px 12px 13px 14px 18px 24px 36px",
    content_style: "* {font-size: 12px; font-family: arial, sans serif; margin: 0px 0px 0.5em 0px;} ol {padding: 0px 0px 0px 25px;} body {margin: 5px;}",
    language: "<?=$GLOBALS['lang']?>",
    toolbar1: "newdocument | styleselect formatselect fontselect fontsizeselect",
    toolbar2: "cut copy paste | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image<?=$mediatxt?> code | preview ",
    toolbar3: "bold italic underline strikethrough |  alignleft aligncenter alignright alignjustify | hr | subscript superscript | charmap emoticons | forecolor backcolor | nonbreaking ",
    plugins: [
      "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
      "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
      "table directionality emoticons template textcolor paste textcolor<?=$mediatxt?>"
    ],
  });
</script>
<!-- TinyMCE chunk end -->

<?php
};

// end of chunk - nothing should be added below
