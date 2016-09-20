<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<link rel="stylesheet" href="tpl/stdstyle/js/jquery_1.9.2_ocTheme/themes/cupertino/jquery.ui.all.css">
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/minified/jquery-ui.min.js"></script>
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.datepick-{language4js}.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
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

<style>

    .info {
        color: blue;
        font-size: 8px;
    }

    table, th, td
    {
        font-size: 12px;
    }

    table.statsTable td{
        padding-left: 5px;
        padding-right: 5px;
    }
    table.statsTable th{
        padding-left: 5px;
        padding-right: 5px;
        border-bottom: solid 2px;
        border-color: #000044;
        background-color:#0088CC;
        color: #FFFFCC;

    }
    table.statsTable th:not(:last-child), table.statsTable td:not(:last-child){
        border-right: solid 1px;
    }

    table.ptCacheTable th{
        padding-left: 5px;
        padding-right: 5px;
        border-bottom: solid 2px;
        background-color:#0088CC;
        color: #FFFFCC;
    }


    table.ptCacheTable th:first-child, table.statsTable th:first-child{
        -moz-border-top-left-radius: 5px;
        -webkit-border-top-left-radius: 5px;
        border-top-left-radius: 5px;
    }
    table.ptCacheTable th:last-child, table.statsTable th:last-child{
        -moz-border-top-right-radius: 5px;
        -webkit-border-top-right-radius: 5px;
        border-top-right-radius: 5px;
    }

    #powerTrailName{
        font-size: 36px;
        color:#000088;
        font-family: Shojumaru;
    }

    .CommentDate {
        font-size: 11px;
        padding-left: 2px;
        padding-right: 15px;
    }

    .commentContent{
        border-left: 1px solid #2F2727;
        padding-left: 15px;
        padding-right: 20px;
        padding-top: 5px;
        padding-bottom: 5px;
        max-width:550px;
        height:auto;
    }

    .commentHead{
        padding-top: 5px;
        font-family: verdana;
        font-size: 13px;
        padding-left: 10px;
        background-color: #FFFFFF; background-repeat: repeat-y;

        border-left: 1px solid #2F2727;
        border-top: 1px solid #2F2727;

        background: -webkit-gradient(linear, left top, right top, from(#DDDDDD), to(#FFFFFF));
        background: -webkit-linear-gradient(left, #DDDDDD  #FFFFFF);
        background: -moz-linear-gradient(left, #DDDDDD, #FFFFFF);
        background: -ms-linear-gradient(left, #DDDDDD, #FFFFFF);
        background: -o-linear-gradient(left, #DDDDDD, #FFFFFF);

        -moz-border-top-left-radius: 10px;
        -webkit-border-top-left-radius: 10px;
        border-top-left-radius: 10px;
    }

    #commentsTable{
        width: 95%;
    }

    .linearBg1 {
        height: 25px;
        color: #E7E5DC;
        font-family: verdana;
        font-size: 12px;
        font-weight: bold;
        padding-left:8px;
        background-color: #1a82f7; background-repeat: repeat-y;
        background: -webkit-gradient(linear, left top, right top, from(#1a82f7), to(#2F2727));
        background: -webkit-linear-gradient(left, #2F2727, #1a82f7);
        background: -moz-linear-gradient(left, #2F2727, #1a82f7);
        background: -ms-linear-gradient(left, #2F2727, #1a82f7);
        background: -o-linear-gradient(left, #2F2727, #1a82f7);
        -moz-border-top-right-radius: 8px;
        -webkit-border-top-right-radius: 8px;
        border-top-right-radius: 8px;
    }
    .linearBg2 {
        height: 1px;
        padding-left:8px;
        background-color: #1a82f7; background-repeat: repeat-y;
        background: -webkit-gradient(linear, left top, right top, from(#1a82f7), to(#2F2727));
        background: -webkit-linear-gradient(left, #2F2727, #1a82f7);
        background: -moz-linear-gradient(left, #2F2727, #1a82f7);
        background: -ms-linear-gradient(left, #2F2727, #1a82f7);
        background: -o-linear-gradient(left, #2F2727, #1a82f7);
    }
    .userActions {
        font-family: verdana;
        font-size: 9px;
    }
    .inlineTd{
        padding:15px;
    }
    .ptTd{
        font-family: verdana;
        font-size: 12px;
        text-align:center;
    }



    /* quite nice blue buttons */
    .editPtDataButton {
        -moz-box-shadow:inset 0px 1px 0px 0px #97c4fe;
        -webkit-box-shadow:inset 0px 1px 0px 0px #97c4fe;
        box-shadow:inset 0px 1px 0px 0px #97c4fe;
        background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #3d94f6), color-stop(1, #1e62d0) );
        background:-moz-linear-gradient( center top, #3d94f6 5%, #1e62d0 100% );
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#3d94f6', endColorstr='#1e62d0');
        background-color:#3d94f6;
        -moz-border-radius:6px;
        -webkit-border-radius:6px;
        border-radius:6px;
        border:1px solid #337fed;
        display:inline-block;
        color:#ffffff !important;
        font-family:arial;
        font-size:11px;
        font-weight:normal;
        padding:0px 16px;
        text-decoration:none !important;
        text-shadow:1px 1px 0px #1570cd;
    }.editPtDataButton:hover {
        background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #1e62d0), color-stop(1, #3d94f6) );
        background:-moz-linear-gradient( center top, #1e62d0 5%, #3d94f6 100% );
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e62d0', endColorstr='#3d94f6');
        background-color:#1e62d0;
    }.editPtDataButton:active {
        position:relative;
        top:1px;
    }
    /* This imageless css button was generated by CSSButtonGenerator.com */




    #powerTrailDescription img {
        max-width:550px;
        height:auto;
    }

    #oldIE{
        color: red;
        border: solid 1px;
        border-color: red;
        padding: 10px;
        width:90%;
    }

    .editDeleteComment {
        float:right
    }


    #messageDiv{
        display: none;
        border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;
        border:1px solid #337fed;
        width: 80%;
        padding: 5px;
    }
    #ptStatSelectSpan{
        margin-left: auto;
        margin-right: auto;
        overflow: hidden;
        display: none;
        border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;
        border:1px solid #337fed;
        width: 600px;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 20px;
        padding-right: 20px;
    }

</style>

<body>

    <br/><br/>
    <!-- deleting entery comfirmation dialog  -->
    <div id="dialog-form" title="{{pt151}}" style="display: none">
        <form>
            <label for="delReason">{{pt152}} (max. 500 {{pt154}})</label><br /><br />
            <input onkeypress="return event.keyCode != 13;" type="text" name="delReason" id="delReason" class="text ui-widget-content ui-corner-all" style="width: 280px;" maxlength="500" />
        </form>
    </div>

    <div id="oldIE" style="display: none">{{pt129}}</div>

    <!--[if IE 6 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]-->
    <!--[if IE 7 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]-->
    <!--[if IE 8 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]-->

    <div class="content2-pagetitle">
        <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="geocache" title="geocache" align="middle" />
        {{pt208}}
    </div>

    <div style="display: {selPtDiv}">
        <form action="powerTrailCOG.php" id="selPt" class="form-group-sm">
            {ptSelector} <a href="javascript:void(0);" onclick="$('#selPt').submit()"; class="btn btn-default btn-sm">{{pt209}}</a>
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
                        {{pt238}}: {ptStatSelect}<br /><br />
                        <textarea id="reason" size="200" maxlength="1000">{{pt220}}</textarea>
                       <!-- <input id="reason" size="200" type="text" maxlength="1000" placeholder="{{pt220}}"/><br> -->
                        <a id="stbtn2" href="javascript:void(0);" onclick="$('#stbtn1').show();
                                $('#ptStatus').show();
                                $('#ptStatSelectSpan').hide();" class="editPtDataButton">{{pt031}}</a>
                        <a id="stbtn3" href="javascript:void(0);" onclick="chgStatus();" class="editPtDataButton">{{pt044}}</a>
                    </div>
                    <a id="stbtn1" href="javascript:void(0);" onclick="$('#stbtn1').hide();
                            $('#ptStatus').hide();
                            $('#ptStatSelectSpan').show();" class="editPtDataButton">{{pt064}}</a>
                    <img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif"  style="display: none" id="ajaxLoaderStatus" />
                </td>
            </tr>
        </table>

        <br/><br/> {{pt020}}<br><hr>
        {{pt211}}<br/><br/>
        {ptCaches}
    </div>
