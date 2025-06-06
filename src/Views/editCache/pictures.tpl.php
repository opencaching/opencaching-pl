
<button type="button" class="btn btn-primary btn-sm" id="addNewPic"><?= tr('add_new_pict'); ?></button>

<table id="picTable" class="picTable picTableEmpty">
  <thead>
  <tr>
    <th title="<?= tr('editCache_picsTableOrderThTitle'); ?>"><?= tr('editCache_picsTableOrderTh'); ?></th>
    <th title="<?= tr('editCache_picsTableImgThTitle'); ?>"><?= tr('editCache_picsTableImgTh'); ?></th>
    <th style="width: 50%" title="<?= tr('editCache_picsTableTitleThTitle'); ?>"><?= tr('editCache_picsTableTitleTh'); ?></th>
    <th title="<?= tr('editCache_picsTableSpoilerThTitle'); ?>"><?= tr('editCache_picsTableSpoilerTh'); ?></th>
    <th title="<?= tr('editCache_picsTableHiddenThTitle'); ?>"><?= tr('editCache_picsTableHiddenTh'); ?></th>
    <th title="<?= tr('editCache_picsTableRemoveThTitle'); ?>"><?= tr('editCache_picsTableRemoveTh'); ?></th>
  </tr>
  </thead>
  <tbody id="picsTbody"><!-- data is loaded dynamically here --></tbody>
</table>

<script id="pictureRowTpl" type="text/x-handlebars-template">
<?php
  // template for each row of pictures table
  require __DIR__ . '/pictureRow.tpl.php';
?>
</script>

<script type="text/javascript">
// init sortable list of pictures
$( function() {

  // load pictures attached to this geocache
  <?php foreach ($view->picList as $pic) { ?>
    console.log(<?= $pic->getDataJson(); ?>)
    loadPicturesToTable(<?= $pic->getDataJson(); ?>);
  <?php } //foreach-pic?>

  // init sortable list of pictures
  $("#picsTbody").sortable({
    handle: ".picSortHdlr",
    stop: function( event, ui ) {
      updateOrderOfPics (ui.item);
    }
  });
  $("#picsTbody").disableSelection();

  // init upload
  $('#addNewPic').click(function(e){
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
      ocUpload(<?= $view->picsUploadModelJson; ?>, function(uploadResult) {
        if(uploadResult.success){
          // upload successed
          console.log(uploadResult);

          $.each(uploadResult.newFiles, function(i, pic) {
            loadPicturesToTable(pic);
          });
        } else {
          // upload failed
          console.error(uploadResult);
        }
      });
    });
});

function fadeOutAction(element, action) {
    if (
        element != null
        && element.length > 0
        && typeof action === 'function'
    ) {
        element.on("animationend", action)
        element.attr("onclick", "");
        element.off("click");
        element.removeClass("pointer");
        element.addClass('elementFadeOut');
    }
}

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

// save new order of pictures to DB
function updateOrderOfPics (item){

  // prepare array with current order of pics UUIDs
  var uuidsOrder = [];
  $("input[name=cachePicUuid]").each(function(){
    uuidsOrder.push($(this).val());
  });
  console.debug(uuidsOrder);

  var iconContainer = item.find("td span").first();
  iconContainer.empty(); // remove all current elements

  var jQueryIcon = $('<img src="/images/loader/spinning-circles.svg" />');
  jQueryIcon.attr("title", "<?= tr('editCache_actionInProgress'); ?>");
  jQueryIcon.addClass("icon16");
  iconContainer.prepend(jQueryIcon);

  $.ajax({
    type:  "POST",
    cache: false,
    url: "/picture/updatePicsOrderAjax/<?= $view->picParentType; ?>/<?= $view->picParentId; ?>",
    data: { uuidsOrder: uuidsOrder },
    error: function (xhr) {

        console.debug("savePicTitleAction: " + xhr.responseText);

        jQueryIcon.attr("src", "/images/redcross.gif");
        jQueryIcon.attr("title", "<?= tr('editCache_orderPicsSaveErr'); ?>");
    },
    success: function (data, status) {
      jQueryIcon.attr("src", "/images/ok.gif");
      jQueryIcon.attr("title", "<?= tr('editCache_orderPicsSaveSuccess'); ?>");
    }
  });
}

// enable edit of the picture title
function editPicTitleAction(icon, uuid){

    // update icon
    var jQueryIcon = $(icon);
    jQueryIcon.attr("src", "/images/action/16x16-save.png");
    jQueryIcon.attr("title", "<?= tr('editCache_editPicTitleSave'); ?>");
    jQueryIcon.attr("onclick","savePicTitleAction(this, '"+uuid+"')");
    jQueryIcon.attr("uuid", uuid);

    // enable text input
    var textInput = jQueryIcon.prev();
    textInput.prop("disabled", false);
}

function savePicTitleAction (icon, uuid) {
  var jQueryIcon = $(icon);

  jQueryIcon.attr("src", "/images/loader/spinning-circles.svg");
  jQueryIcon.attr("title", "<?= tr('editCache_actionInProgress'); ?>");

  var titleVal = jQueryIcon.prev().val();

  $.ajax({
    type:  "POST",
    cache: false,
    url:   "/picture/updateTitleAjax/"+uuid,
    data: { title: titleVal },
    error: function (xhr) {

        console.debug("savePicTitleAction: " + xhr.responseText);

        jQueryIcon.attr("src", "/images/redcross.gif");
        jQueryIcon.attr("title", "<?= tr('editCache_editPicTitleErr'); ?>");
        jQueryIcon.attr("onclick", "editPicTitleAction(this, '" + jQueryIcon.attr("uuid") + "')");
    },
    success: function (data, status) {
        jQueryIcon.attr("src", "/images/ok.gif");
        jQueryIcon.attr("title", "<?= tr('editCache_editPicTitleSuccess'); ?>");
        fadeOutAction(jQueryIcon, function(event) {
            jQueryIcon.attr("src", "/images/actions/edit-16.png");
            jQueryIcon.attr("title", "<?= tr('editCache_editPicTitle'); ?>");
            jQueryIcon.attr("onclick", "editPicTitleAction(this, '" + jQueryIcon.attr("uuid") + "')");
            jQueryIcon.addClass("pointer");
        });
    }
  });
}

// removing picture from the cache
function removePicAction(icon, uuid){
    var jQueryIcon = $(icon);

    var rpDialog = $(
        '<div style="font-size: 1.5em; text-align: center; margin: 5%">'
        + 'Do you really want to remove this picture?'
        + '</div>'
    ).dialog({
        position: { my: "center", at: "middle", of: $('#picTable') },
        autoOpen: false,
        modal: true,
        hide: "explode",
        show: "fade",
        title: "<?= tr('editCache_removePicDialogTitle'); ?>",
        buttons: {
            YesButton: {
                class: 'rpDialogYesButton',
                text: '<?= tr('editCache_removePicDialogYes'); ?>',
                'click': function() {
                    $(this).dialog("close");
                    jQueryIcon.attr("src", "/images/loader/spinning-circles.svg");
                    jQueryIcon.attr("title", "<?= tr('editCache_actionInProgress'); ?>");

                    $.ajax({
                        type:  "get",
                        cache: false,
                        url:   "/picture/removePicAjax/"+uuid,
                        error: function (xhr) {
                            console.debug("removePicAction: " + xhr.responseText);

                            jQueryIcon.attr("src", "/images/redcross.gif");
                            jQueryIcon.attr("title", "<?= tr('editCache_removePicError'); ?>");
                            // Deleting attempt can be repeated, so it is still enabled
                        },
                        success: function (data, status) {
                            jQueryIcon.attr("src", "/images/ok.gif");
                            jQueryIcon.attr("title", "<?= tr('editCache_removePicSuccess'); ?>");
                            jQueryIcon.removeClass("pointer");
                            jQueryIcon.attr("onclick", "");
                            var row = jQueryIcon.closest('tr');
                            if (row.length > 0) {
                                row.children("input").attr("disabled","disabled");
                                row.children("img").removeClass("pointer");
                                row.children("img").attr("onclick","");
                                row.addClass("picRowDeleted");
                                fadeOutAction(row, function(event) {
                                    row.addClass("hidden");
                                    row.remove();
                                    if ($("#picsTbody > tr").length == 0) {
                                        $("#picTable").addClass("picTableEmpty");
                                    }
                                });
                            }
                        }
                    });
                }
            },
            NoButton: {
                class: 'rpDialogNoButton',
                text: '<?= tr('editCache_removePicDialogNo'); ?>',
                autofocus: true,
                'click': function() {
                    $(this).dialog("close");
                }
            }
        }
    });

    rpDialog.dialog('open');
    $(".ui-dialog-titlebar-close").hide();
}

function picSpolerAction (chkbox, uuid) {

  var checkbox = $(chkbox);

  if (checkbox.prop('checked')) {
    var url = "/picture/addSpoilerAttrAjax/"+uuid;
  }else {
    var url = "/picture/rmSpoilerAttrAjax/"+uuid;
  }

  // add icon after checkbox
  var iconContainer = checkbox.next(); // find span after the checkbox
  iconContainer.empty(); // remove all current elements
  var jQueryIcon = $('<img src="/images/loader/spinning-circles.svg" />');
  jQueryIcon.attr("title", "<?= tr('editCache_actionInProgress'); ?>");
  jQueryIcon.addClass("icon16");

  iconContainer.prepend(jQueryIcon);

  $.ajax({
    type:  "get",
    cache: false,
    url:   url,
    error: function (xhr) {

        console.debug("picSpolerAction: " + xhr.responseText);

        jQueryIcon.attr("src", "/images/redcross.gif");
        jQueryIcon.attr("title", "<?= tr('editCache_spoilerChangeErr'); ?>");
    },
    success: function (data, status) {
      console.debug(data);

      jQueryIcon.attr("src", "/images/ok.gif");
      jQueryIcon.attr("title", "<?= tr('editCache_spoilerChangeSuccess'); ?>");
    }
  });
}

function picHideAction (chkbox, uuid) {

  var checkbox = $(chkbox);

  if (checkbox.prop('checked') ) {
    var url = "/picture/addHiddenAttrAjax/"+uuid;
  }else {
    var url = "/picture/rmHiddenAttrAjax/"+uuid;
  }

  // add icon after checkbox
  var iconContainer = checkbox.next(); // find span after the checkbox
  iconContainer.empty(); // remove all current elements

  var jQueryIcon = $('<img src="/images/loader/spinning-circles.svg" />');
  jQueryIcon.attr("title", "<?= tr('editCache_actionInProgress'); ?>");
  jQueryIcon.addClass("icon16");

  iconContainer.prepend(jQueryIcon);

  $.ajax({
    type:  "get",
    cache: false,
    url:   url,
    error: function (xhr) {

        console.debug("picHideAction: " + xhr.responseText);

        jQueryIcon.attr("src", "/images/redcross.gif");
        jQueryIcon.attr("title", "<?= tr('editCache_hiddenChangeErr'); ?>");
    },
    success: function (data, status) {
      console.debug(data);

      jQueryIcon.attr("src", "/images/ok.gif");
      jQueryIcon.attr("title", "<?= tr('editCache_hiddenChangeSuccess'); ?>");
    }
  });
}
</script>
