<?php

use src\Utils\Uri\SimpleRouter;
use src\Utils\View\View;

/** @var View $view */
?>

<script>
  $( function() {
    $.datepicker.setDefaults($.datepicker.regional["<?=$view->getLang()?>"]);
    $.datepicker.setDefaults({ dateFormat: '<?=$view->dateformat_jQuery?>' });
    $( "#date-publication" ).datepicker();
    $( "#date-expiration" ).datepicker({
        onSelect: function() {
            document.getElementById('no-date-expiration').checked = false;
        }
    });
    $( "#date-mainpageexp" ).datepicker({
        onSelect: function() {
            document.getElementById('no-date-mainpageexp').checked = false;
        }
    });
  } );

<?php if (!empty($view->news->getId())) { // this news is already saved in DB ?>

  function imgUploadPicker(tinyMceCallback, value, meta) {

    if (meta.filetype != 'image') {
      console.log ("Only images can be attached here!");
      return;
    }

    /*
    ocUpload takes two params:
        - params json - see UploadModel
        - callback function

       on end of upload callback will be called with JSON param:
         {
           success: true|false,                // true on success | false on error
           message: 'error-description',       // tech. error description in english (usually not for end-user) (only on fail)
           newfiles: ['fileA','fileB','fileC'] // list of urls to new files saved on server (only on success)
         }
     */
     ocUpload(<?=$view->picsUploadModelJson?>, function(uploadResult) {
       if(uploadResult.success){

         // upload successed
         console.log(uploadResult);

         $.each(uploadResult.newFiles, function(i, pic) {
           loadPicturesToTable(pic);
         });

         //call tinyMce callback
         tinyMceCallback(uploadResult.newFiles[0].fullPicUrl, {alt: uploadResult.newFiles[0].title});

       } else {
         // upload failed
         console.error(uploadResult);
         tinyMceCallback('', {alt: ''});
       }
     });
   }

<?php } // if (!empty($view->news->getId()) { // this news is already saved in DB ?>

</script>

<?php
    $uploadPicker = empty($view->news->getId()) ? null : 'imgUploadPicker';
    $view->callChunk('tinyMCE', true, '.tinymce', $uploadPicker);
?>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="/images/blue/newspaper.png" class="icon22" alt="Newspaper icon">
    <?php if (empty($view->news->getId())) { echo tr('news_hdr_add'); } else {
      echo tr('news_hdr_edit');
      if (! empty($view->news->getTitle())) { echo ' "' . $view->news->getTitle() . '"';}}?>
  </div>
  <div class="buffer"></div>
  <form action="<?=SimpleRouter::getLink('News.NewsAdmin', 'saveNews')?>" method="post" class="news-form">
    <table class="table news-table">
      <tbody>
        <tr>
            <td colspan="2">
              <div class="align-right">
                <button type="submit" name="submit" class="btn btn-primary"><?=tr('save')?></button>
              </div>
            </td>
        </tr>
        <tr>
          <td class="news-left-column content-title-noshade"><?=tr('news_lbl_title')?></td>
          <td><input type="text" name="title" maxlength="100" value="<?=$view->news->getTitle()?>" class="form-control input400"></td>
        </tr>
        <tr><td colspan="2" class="buffer"></td></tr>

        <tr>
          <td class="news-left-column content-title-noshade"><?=tr('news_lbl_category')?></td>
          <td>
          <select name="category" class="form-control input200">
            <?php foreach($view->allCategories as $cat) { ?>
            <option value="<?=$cat?>" <?=($view->news->getCategory()==$cat)?'selected':''?>><?=ltrim($cat, '_') ?></option>
            <?php } //foreach ?>
          </select>
          </td>
        </tr>
        <tr>
          <td class="news-left-column content-title-noshade"><?=tr('news_lbl_publish_from')?></td>
          <td>
            <input type="text" id="date-publication" name="date-publication" class="form-control input100" value="<?=$view->news->getDatePublication(true)?>">
          </td>
        </tr>
        <tr>
          <td class="news-left-column content-title-noshade"><?=tr('news_lbl_publish_to')?></td>
          <td>
            <input type="text" id="date-expiration" name="date-expiration" class="form-control input100" value="<?=$view->news->getDateExpiration(true)?>">
            <input type="checkbox" name="no-date-expiration" id="no-date-expiration" <?php if (is_null($view->news->getDateExpiration())) { ?>checked="checked"<?php } ?>> <label class="content-title-noshade" for="no-date-expiration"><?=tr('news_no_limit')?></label>
          </td>
        </tr>
        <tr>
          <td class="news-left-column content-title-noshade"><?=tr('news_lbl_mainp_to')?></td>
          <td>
            <input type="text" id="date-mainpageexp" name="date-mainpageexp" class="form-control input100" value="<?=$view->news->getDateMainPageExpiration(true)?>">
            <input type="checkbox" name="no-date-mainpageexp" id="no-date-mainpageexp" <?php if (is_null($view->news->getDateMainPageExpiration())) { ?>checked="checked"<?php } ?>> <label class="content-title-noshade" for="no-date-mainpageexp"><?=tr('news_no_limit')?></label>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="notice"><?=tr('news_hlp_mainpage')?>: "<?=tr('news_lbl_show_on_mainp')?>"</div>
    <textarea name="content" class="tinymce desc"><?=$view->news->getContent()?></textarea>
    <div class="buffer"></div>
    <fieldset class="news-fieldset"><legend> <?=tr('news_lbl_options')?> </legend>
      <input type="checkbox" name="hide-author" id="hide-author" <?php if ($view->news->getHideAuthor()) {?>checked="checked"<?php }?>> <label class="content-title-noshade" for="hide-author"><?=tr('news_lbl_hide_author')?> "<?=tr('news_OCTeam')?>"</label><br>
      <input type="checkbox" name="show-onmainpage" id="show-onmainpage" <?php if ($view->news->getShowOnMainpage()) {?>checked="checked"<?php }?>> <label class="content-title-noshade" for="show-onmainpage"><?=tr('news_lbl_show_on_mainp')?></label><br>
      <input type="checkbox" name="show-notlogged" id="show-notlogged" <?php if ($view->news->getShowNotLogged()) {?>checked="checked"<?php }?>> <label class="content-title-noshade" for="show-notlogged"><?=tr('news_lbl_show_notlogged')?></label><br>
      <span class="notice">"<?=tr('news_lbl_hide_author')?> <?=tr('news_OCTeam')?>" - <?=tr('news_hlp_hide_author')?></span><br>
      <span class="notice">"<?=tr('news_lbl_show_on_mainp')?>" - <?=tr('news_hlp_show_on_mainp')?></span><br>
      <span class="notice">"<?=tr('news_lbl_show_notlogged')?>" - <?=tr('news_hlp_show_notlogged')?></span><br>
    </fieldset>
    <div class="buffer"></div>
    <div class="align-center"><button type="submit" name="submit" class="btn btn-primary"><?=tr('save')?></button></div>
    <input type="hidden" name="id" value="<?=$view->news->getId()?>">
    <input type="hidden" name="action" value="save">
  </form>
</div>

<div class="content2-container bg-blue02">
  <p class="content-title-noshade-size1">
    <?=tr('news_listOfImages')?>
  </p>
</div>
<div class="content2-container">
  <?=$v->callSubTpl('/news/newsPicList')?>
</div>

<script>
    document.getElementById('no-date-expiration').onchange = function() {
        if (this.checked) {
            document.getElementById('date-expiration').value = "";
        }
    };
    document.getElementById('no-date-mainpageexp').onchange = function() {
        if (this.checked) {
            document.getElementById('date-mainpageexp').value = "";
        }
    };
</script>
