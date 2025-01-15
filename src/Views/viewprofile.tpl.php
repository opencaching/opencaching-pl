<?php

use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;

?>

<link rel="stylesheet" type="text/css" media="screen,projection" href="/css/Badge.css">
<link rel="stylesheet" href="/js/pieProgress/dist/css/asPieProgress.css">
<script src="/js/pieProgress/js/jquery.js"></script>
<script src="/js/pieProgress/dist/jquery-asPieProgress.js"></script>

<script>
jQuery(function($) {
  $('.Badge-pie-progress-small').asPieProgress({
    namespace: 'pie_progress'
  });
  $('.pie_progress').asPieProgress('start');
});
</script>

<script src="/js/wz_tooltip.js"></script>

<script>
    function ajaxGetFTF() {
        $('#showFtfBtn').hide();
        $('#commentsLoader').show();
        $('#ftfDiv').fadeOut(1000);
        request = $.ajax({
            url: "/UserProfile/getUserFtfsAjax/"+$('#userId').val(),
            type: "get",
        });

        request.done(function (response, textStatus, jqXHR) {
            console.log(response);
            var ftfList = response;
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

<!--    CONTENT -->
<div class="content2-container">
  <?php $view->callChunk('infoBar', '', $view->infoMsg, $view->errorMsg ); ?>
    <div class="content2-pagetitle">{{user_profile}} {username} </div>
    <div class="content-title-noshade">
        <div class="user-profile">
            <div class="col1">
                <img src="/images/blue/{profile_img}.png"  alt="" title="{profile_info}" align="middle"/>
            </div>
            <div class="col2">
                <span class="txt-blue08" >{{registered_since_label}}:</span> <span class="txt-black"> {registered}</span><br>
                <span class="txt-blue08" >{{descriptions}}:</span><span class="txt-black" style="line-height: 0.5cm;"> {description_start}{description}{description_end}</span><br>
                <span class="txt-blue08" >{{lastlogins}}:</span><span class="{lastloginClass}" style="line-height: 0.5cm;"> {lastlogin}</span><br>
            </div>
            <div class="col3">
                <img src="/images/blue/email.png" class="icon32" alt="Email" title="Email" align="middle">&nbsp;<a href="<?=SimpleRouter::getLink('UserProfile', 'mailTo', $view->userid)?>" class="links">{{email_user}}</a><br />
                <img src="/images/blue/world.png" class="icon32" alt="Mapa" title="Map" align="middle">&nbsp;
                <a href="<?=Uri::setOrReplaceParamValue('userid', $view->userid, SimpleRouter::getLink('MainMap', 'embeded'))?>"
                   class="links">{{show_user_map}}</a>
            </div>
            <hr>
        </div>
    </div>
    {guide_info}
    <div class="nav4">
        <ul id="statmenu">
          <li class="group">
            <a style="background-image: url(images/actions/stat-18.png);background-repeat:no-repeat;"
               href="/ustatsg2.php?userid=<?=$view->userid?>">
              <?=tr('graph_find')?>
            </a>
          </li>
          <li class="group">
            <a style="background-image: url(images/actions/stat-18.png);background-repeat:no-repeat;"
               href="/ustatsg1.php?userid=<?=$view->userid?>">
              <?=tr('graph_created')?>
            </a>
          </li>
        </ul>
    </div>

    {content}
</div>
