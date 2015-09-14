<?php

global $user_id;
?>
<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript">

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
    <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="{title_text}" title="{title_text}" />&nbsp;{{user_profile}}: {username} </div>
    <div class="content-title-noshade">
        <table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px;font-size: 115%;" width="97%">
            <tr>
                <td rowspan="3" width="64"><img src="tpl/stdstyle/images/blue/{profile_img}.png"  alt="" title="{profile_info}" align="middle"/></td>
                <td><span class="txt-blue08" >{{registered_since_label}}:</span> <span class="txt-black"> {registered}</span><br /><br/><span class="txt-blue08" >{{country_label}}:</span><span class="txt-black"> {country}</span></td>
                <td rowspan="3" width="30%">
                    <img src="tpl/stdstyle/images/blue/email.png" class="icon32" alt="Email" title="Email" align="middle"/>&nbsp;<a href="mailto.php?userid={userid}">{{email_user}}</a><br />
                    <img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="Mapa" title="Map" align="middle"/>&nbsp;<a href="cachemap3.php?userid={userid}">{{show_user_map}}</a>
                </td>
            </tr>
            <tr>
                <td><span class="txt-blue08" >{{descriptions}}:</span><span class="txt-black" style="line-height: 0.5cm;"> {description_start}{description}{description_end}</span></td>
            </tr>
            <tr>
                <td><span class="txt-blue08" >{{lastlogins}}:</span><span class="txt-black" style="line-height: 0.5cm;"> {lastlogin}</span></td>
            </tr>
            <tr>
                <td colspan="3"><hr></hr></td>
            </tr></table>
    </div>
    {guide_info}
    <div class="nav4">
        <?php
// statlisting
        $statidx = mnu_MainMenuIndexFromPageId($menu, "statlisting");
        if ($menu[$statidx]['title'] != '') {
            echo '<ul id="statmenu">';
            $menu[$statidx]['visible'] = false;
            echo '<li class="title" ';
            echo '>' . $menu[$statidx]["title"] . '</li>';
            mnu_EchoSubMenu($menu[$statidx]['submenu'], $tplname, 1, false);
            echo '</ul>';
        }
//end statlisting
        ?>
    </div>

    {content}
</div>
