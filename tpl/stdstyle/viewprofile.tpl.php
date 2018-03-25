<?php

use Utils\Uri\SimpleRouter;

global $user_id;
?>

<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/Badge.css" />
<link rel="stylesheet" href="tpl/stdstyle/js/PieProgress/dist/css/asPieProgress.css">
<script src="tpl/stdstyle/js/PieProgress/js/jquery.js"></script>
<script src="tpl/stdstyle/js/PieProgress/dist/jquery-asPieProgress.js"></script>

<script>
jQuery(function($) {
  $('.Badge-pie-progress-small').asPieProgress({
    namespace: 'pie_progress'
  });

  $('.pie_progress').asPieProgress('start');

});

</script>

<script src="lib/js/wz_tooltip.js"></script>
<script>

    function ajaxGetFTF() {
        $('#showFtfBtn').hide();
        $('#commentsLoader').show();
        $('#ftfDiv').fadeOut(1000);
        request = $.ajax({
            url: "ajaxGetFTF.php",
            type: "post",
            data: {id: $('#userId').val()},
        });

        request.done(function (response, textStatus, jqXHR) {
            var ftfList = jQuery.parseJSON(response);
            html = '<table><tr><th>{{viewprofileDate}}</th><th>{{viewprofileTime}}</th><th>{{viewprofileCache}}</th></tr>';
            bgColor = '#eeeeff';
            var i = 0;
            ftfList.forEach(function (entry) {
                if (bgColor == '#eeeeff')
                    bgColor = '#ffffff';
                else
                    bgColor = '#eeeeff';
                var date = entry.date.split(" ");
                html += '<tr bgcolor="' + bgColor + '"><td style="width: 60px;" align="center">' + date[0] + '</td><td style="width: 60px;">' + date[1] + '</td><td><a href=viewcache.php?cacheid=' + entry.cache_id + '>' + entry.name + '</a></td></tr>';
                i++;
            });
            html += '</table><br>{{viewprofileTotFtf}}: ' + i;
            $('#ftfDiv').html(html);
            $('#ftfDiv').fadeIn(1000);
        });

        request.always(function () {
            $('#commentsLoader').hide();
        });
    }
</script>
<style>

</style>
<!--    CONTENT -->
<div class="content2-container">
  <?php $view->callChunk('infoBar', '', $view->infoMsg, $view->errorMsg ); ?>
    <div class="content2-pagetitle">{{user_profile}} {username} </div>
    <div class="content-title-noshade">
        <table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px;font-size: 115%;" width="97%">
            <tr>
                <td rowspan="3" width="64"><img src="tpl/stdstyle/images/blue/{profile_img}.png"  alt="" title="{profile_info}" align="middle"/></td>
                <td><span class="txt-blue08" >{{registered_since_label}}:</span> <span class="txt-black"> {registered}</span><br></td>
                <td rowspan="3" width="30%">
                    <img src="tpl/stdstyle/images/blue/email.png" class="icon32" alt="Email" title="Email" align="middle">&nbsp;<a href="<?=SimpleRouter::getLink('UserProfile', 'mailTo', $view->userid)?>" class="links">{{email_user}}</a><br />
                    <img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="Mapa" title="Map" align="middle">&nbsp;<a href="cachemap3.php?userid={userid}" class="links">{{show_user_map}}</a>
                </td>
            </tr>
            <tr>
                <td><span class="txt-blue08" >{{descriptions}}:</span><span class="txt-black" style="line-height: 0.5cm;"> {description_start}{description}{description_end}</span></td>
            </tr>
            <tr>
                <td><span class="txt-blue08" >{{lastlogins}}:</span><span class="txt-black" style="line-height: 0.5cm;"> {lastlogin}</span></td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
        </table>
    </div>
    {guide_info}
    <div class="nav4">
        <ul id="statmenu">
          <li class="group">
            <a style="background-image: url(images/actions/stat-18.png);background-repeat:no-repeat;"
               href="/ustatsg2.php?userid=<?=$user_id?>">
              <?=tr('graph_find')?>
            </a>
          </li>
          <li class="group">
            <a style="background-image: url(images/actions/stat-18.png);background-repeat:no-repeat;"
               href="/ustatsg1.php?userid=<?=$user_id?>">
              <?=tr('graph_created')?>
            </a>
          </li>
        </ul>
    </div>

    {content}
</div>
