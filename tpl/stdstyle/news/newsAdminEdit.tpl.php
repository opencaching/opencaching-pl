<?php
use Utils\Uri\SimpleRouter;

$view->callChunk('tinyMCE');
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
</script>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="/tpl/stdstyle/images/blue/newspaper.png" class="icon22" alt="Newspaper icon">
    <?php if ($view->news->getId() == 0) { echo tr('news_hdr_add'); } else {
      echo tr('news_hdr_edit');
      if (! empty($view->news->getTitle())) { echo ' "' . $view->news->getTitle() . '"';}}?>
  </div>
  <div class="buffer"></div>
  <form action="<?=SimpleRouter::getLink('News.NewsAdmin', 'saveNews')?>" method="post" class="news-form">
    <table class="table news-table">
      <tbody>
        <tr>
          <td class="news-left-column content-title-noshade"><?=tr('news_lbl_title')?></td>
          <td><input type="text" name="title" maxlength="100" value="<?=$view->news->getTitle()?>" class="form-control input400"></td>
        </tr>
        <tr><td colspan="2" class="buffer"></td></tr>
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