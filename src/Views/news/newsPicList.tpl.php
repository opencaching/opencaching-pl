
<table id="picTable" class="table">
  <thead>
  <tr>
    <th title="<?=tr('news_picsTableImgThTitle')?>"><?=tr('news_picsTableImgTh')?></th>
    <th title="<?=tr('news_picsTableUrlThTitle')?>"><?=tr('news_picsTableUrlTh')?></th>
    <th title="<?=tr('news_picsTableRemoveThTitle')?>"><?=tr('news_picsTableRemoveTh')?></th>
  </tr>
  </thead>

  <tbody id="picsTbody"><!-- data is loaded dynamically here --></tbody>
</table>

<script id="pictureRowTpl" type="text/x-handlebars-template">
  <?php
  // template for each row of pictures table
  require(__DIR__.'/newsPicRow.tpl.php');
  ?>
</script>

<script type="text/javascript">
$( function() {
  // load pictures attached to this geocache
  <?php foreach ($view->picList as $pic) { ?>
    console.log(<?=$pic->getDataJson()?>)
    loadPicturesToTable(<?=$pic->getDataJson()?>);
  <?php } //foreach-pic ?>
});

// Template global var
var picRowContentTpl = null;

// Loader of each picture table row
function loadPicturesToTable(picRowDataParams) {

  var picTableBody = $('#picsTbody');

  // compile handlebars template if needed
  if (!picRowContentTpl) {
    var picRowContentHtml = $("#pictureRowTpl").html();
    picRowContentTpl = Handlebars.compile(picRowContentHtml);
  }

  // be sure pictable is visible
  $('#picTable').removeClass('picTableEmpty');

  // load row at the end of table
  picTableBody.append(picRowContentTpl(picRowDataParams));
}


// removing picture from the cache
function removePicAction(icon, uuid){

  var jQueryIcon = $(icon);

  jQueryIcon.attr("src", "/images/loader/spinning-circles.svg");
  jQueryIcon.attr("title", "<?=tr('news_actionInProgress')?>");

  $.ajax({
    type:  "get",
    cache: false,
    url:   "/picture/removePicAjax/"+uuid,
    error: function (xhr) {

        console.debug("removePicAction: " + xhr.responseText);

        jQueryIcon.attr("src", "/images/redcross.gif");
        jQueryIcon.attr("title", "<?=tr('news_removePicError')?>");
    },
    success: function (data, status) {

      jQueryIcon.attr("src", "/images/ok.gif");
      jQueryIcon.attr("title", "<?=tr('news_removePicSuccess')?>");
    }
  });
}

</script>
