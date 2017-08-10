<?php
$view->callChunk('tinyMCE', false);
?>

<script type="text/javascript">
    function chgStatus() {
        var newComment = tinyMCE.activeEditor.getContent();
        $('#ptStatSelectSpan').hide();
        $('#ajaxLoaderStatus').show();
        request = $.ajax({
            url: "powerTrail/ajaxUpdateStatus.php",
            type: "post",
            data: {
                projectId: $('#ptId').val(),
                newStatus: $('#ptStatusSelector').val(),
                commentTxt: newComment
            }
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR) {
            if (response != 'error') {
                $('#StatusOKimg').show();
                $('#ptStatus').html(response);
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown) {
            toggleStatusEdit();
        });

        request.always(function () {

            $('#ajaxLoaderStatus').hide();
            $('#stbtn1').show();
            $('#ptStatus').show();
        });

    }

    function rmCache(cacheId) {
        $("#rmCacheLoader" + cacheId).show();

        request = $.ajax({
            url: "powerTrail/ajaxAddCacheToPt.php",
            type: "post",
            data: {projectId: $('#ptId').val(), removeByCOG: 1, cacheId: cacheId},
        });

        request.done(function (response, textStatus, jqXHR) {
            if (response == 'removedByCOG') {
                $('#tr' + cacheId).remove();
            }

            console.log(response);
        });

        request.always(function () {
            $("#rmCacheLoader" + cacheId).hide();

        });
    }

</script>

    <!-- deleting entery comfirmation dialog  -->
    <div id="dialog-form" title="{{pt151}}" style="display: none">
        <form>
            <label for="delReason">{{pt152}} (max. 500 {{pt154}})</label><br><br>
            <input onkeypress="return event.keyCode != 13;" type="text" name="delReason" id="delReason" class="text ui-widget-content ui-corner-all" style="width: 280px;" maxlength="500">
        </form>
    </div>

    <div class="content2-pagetitle">
        <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="geocache" title="geocache">
        {{pt208}}
    </div>

    <div style="display: {selPtDiv}">
        <form action="powerTrailCOG.php" id="selPt" class="form-group-sm">
            {ptSelector} &nbsp;<a href="javascript:void(0);" onclick="$('#selPt').submit();" class="btn btn-default btn-sm">{{pt209}}</a>
        </form>
    </div>

  <div style="display: {PtDetailsDiv}">
        <input type="hidden" id="ptId" value="{ptId}">
        <table class="table">
            <tr>
                <td class="content-title-noshade">{{pt008}}</td>
                <td>{ptName}</td>
            </tr>
            <tr>
                <td class="content-title-noshade">{{pt023}}</td>
                <td>{ptType}</td>
            </tr>
            <tr>
                <td class="content-title-noshade">{{status_label}}</td>
                <td><span id="ptStatus">{ptStatus}</span>
                    <div id="ptStatSelectSpan" style="display: none;">
                        <span style="color:red;">{{pt221}}</span><br><hr>
                        {{pt238}}: {ptStatSelect}<br><br>
                        <textarea id="reason" class="desc tinymce">{{pt220}}</textarea>
                        <a id="stbtn2" href="javascript:void(0);" onclick="$('#stbtn1').show();
                                $('#ptStatus').show();
                                $('#ptStatSelectSpan').hide();" class="editPtDataButton">{{pt031}}</a>
                        <a id="stbtn3" href="javascript:void(0);" onclick="chgStatus();" class="editPtDataButton">{{pt044}}</a>
                    </div>
                    <a id="stbtn1" href="javascript:void(0);" onclick="$('#stbtn1').hide();
                            $('#ptStatus').hide();
                            $('#ptStatSelectSpan').show();" class="editPtDataButton">{{pt064}}</a>
                    <img src="tpl/stdstyle/images/misc/ptPreloader.gif" style="display: none" id="ajaxLoaderStatus" alt="">
                </td>
            </tr>
        </table>
    <div class="buffer"></div>
    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/basic2.png" class="icon32" alt=""/>{{pt020}}	</p>
    </div>
    <div class="content2-container">
      {ptCaches}
      <div class="buffer"></div>
      <div class="notice">{{pt211}}</div>
    </div>
  </div>
