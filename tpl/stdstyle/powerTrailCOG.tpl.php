<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<link rel="stylesheet" href="tpl/stdstyle/js/jquery_1.9.2_ocTheme/themes/cupertino/jquery.ui.all.css">
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/minified/jquery-ui.min.js"></script>
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.datepick-{language4js}.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;key={googlemap_key}"></script>
<link rel="stylesheet" href="tpl/stdstyle/css/powerTrail.css" type="text/css">
<script type="text/javascript">
    tinymce.init({
        selector: "textarea",
        width: 600,
        height: 350,
        menubar: false,
        toolbar_items_size: 'small',
        language: "{language4js}",
        gecko_spellcheck: true,
        relative_urls: false,
        remove_script_host: false,
        entity_encoding: "raw",
        toolbar1: "newdocument | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image code | preview ",
        toolbar3: "bold italic underline strikethrough |  alignleft aligncenter alignright alignjustify | hr | subscript superscript | charmap emoticons | forecolor backcolor | nonbreaking ",
        plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table directionality emoticons template textcolor paste textcolor"
        ],
    });
</script>
<script type="text/javascript">
    $(function () {
        $.datepicker.setDefaults($.datepicker.regional['pl']);
        $('#powerTrailDateCreatedInput').datepicker({
            dateFormat: 'yy-mm-dd',
            regional: '{language4js}'
        }).val();
        $('#commentDateTime').datepicker({
            dateFormat: 'yy-mm-dd',
            regional: '{language4js}'
        }).val();

    });

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

    <br><br>
    <!-- deleting entery comfirmation dialog  -->
    <div id="dialog-form" title="{{pt151}}" style="display: none">
        <form>
            <label for="delReason">{{pt152}} (max. 500 {{pt154}})</label><br><br>
            <input onkeypress="return event.keyCode != 13;" type="text" name="delReason" id="delReason" class="text ui-widget-content ui-corner-all" style="width: 280px;" maxlength="500">
        </form>
    </div>

    <div id="oldIE" style="display: none">{{pt129}}</div>

    <!--[if IE 6 ]> <div id="oldIE">{{pt129}}</div><br><br> <![endif]-->
    <!--[if IE 7 ]> <div id="oldIE">{{pt129}}</div><br><br> <![endif]-->
    <!--[if IE 8 ]> <div id="oldIE">{{pt129}}</div><br><br> <![endif]-->

    <div class="content2-pagetitle">
        <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="geocache" title="geocache">
        {{pt208}}
    </div>

    <div style="display: {selPtDiv}">
        <form action="powerTrailCOG.php" id="selPt" class="form-group-sm">
            {ptSelector} <a href="javascript:void(0);" onclick="$('#selPt').submit();" class="btn btn-default btn-sm">{{pt209}}</a>
        </form>
    </div>



    <div style="display: {PtDetailsDiv}">
        <input type="hidden" id="ptId" value="{ptId}">
        <table>
            <tr>
                <td>{{pt008}}</td>
                <td>{ptName}</td>
            </tr>
            <tr>
                <td>{{pt023}}</td>
                <td>{ptType}</td>
            </tr>
            <tr>
                <td>{{pt040}}</td>
                <td><span id="ptStatus">{ptStatus}</span>
                    <div id="ptStatSelectSpan" style="display: none;">
                        <span style="color:red;">{{pt221}}</span><br><hr>
                        {{pt238}}: {ptStatSelect}<br><br>
                        <textarea id="reason" maxlength="1000">{{pt220}}</textarea>
                       <!-- <input id="reason" size="200" type="text" maxlength="1000" placeholder="{{pt220}}"><br> -->
                        <a id="stbtn2" href="javascript:void(0);" onclick="$('#stbtn1').show();
                                $('#ptStatus').show();
                                $('#ptStatSelectSpan').hide();" class="editPtDataButton">{{pt031}}</a>
                        <a id="stbtn3" href="javascript:void(0);" onclick="chgStatus();" class="editPtDataButton">{{pt044}}</a>
                    </div>
                    <a id="stbtn1" href="javascript:void(0);" onclick="$('#stbtn1').hide();
                            $('#ptStatus').hide();
                            $('#ptStatSelectSpan').show();" class="editPtDataButton">{{pt064}}</a>
                    <img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" style="display: none" id="ajaxLoaderStatus" alt="">
                </td>
            </tr>
        </table>

        <br><br> {{pt020}}<br><hr>
        {{pt211}}<br><br>
        {ptCaches}
    </div>
