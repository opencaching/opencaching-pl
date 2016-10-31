<?php

?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="{{my_account}}" title="{{my_account}}" />&nbsp;{{my_account}}</div>
<div class="searchdiv">
    
    <a class="btn btn-primary" href="myprofile.php?action=change">{{change_data}}</a>
    <a class="btn btn-default" href="newemail.php">{{change_email}}</a>
    <a class="btn btn-default" href="newpw.php">{{change_password}}</a>
    <a class="btn btn-default" href="change_statpic.php">{{choose_statpic}}</a>
    <br/><br/>
    <div class="notice" style="height:44px;">
        {{myprofile01}}
    </div>
    <div class="notice">
        {{gray_field_is_hidden}}
    </div>
    
    <div class="buffer"></div>
    
    <p class="content-title-noshade-size2">{{data_in_profile}}:</p>
    <div class="buffer"></div>
    <table class="table">
        <tr>
            <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/user.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{username_label}}:</td>
            <td>{username}</td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/email.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{email_address}}:</td>
            <td class="txt-grey07">{email}</td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade"><img src="tpl/stdstyle/images/description/22x22-geokret.png" width="16" height="16" class="icon16" alt="" title="" align="middle" />&nbsp;{{GKApi01}}:</td>
            <td>{GeoKretyApiIntegration}</td>
        </tr>
        <tr>
            <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/world.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{country_label}}:</td>
            <td>{country}</td>
        </tr>
        {guides_start}
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/book_open.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{guide}}:</td>
            <td  valign="middle">
                {{myprofile02}}<a class="links" href="cacheguides.php">{{myprofile03}}</a></label>&nbsp;
            </td>
        </tr>
        {guides_end}
        <tr><td class="buffer" colspan="2"></td></tr>

        <tr>
            <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/map.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{coordinates}}:</td>
            <td>{coords}</td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/email_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{notification}}:</td>
            <td class="txt-grey07">{notify}</td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/page_copy.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{bulletin}}:</td>
            <td class="txt-grey07" valign="middle">{bulletin_label}</td>
        </tr>
        <tr>
            <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/script.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{my_description}}:</td>
            <td>{description}</td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/brick.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{other}}:</td>
            <td class="txt-grey07" valign="top">{user_options}</td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/plugin.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{ozi_path_label}}:</td>
            <td class="txt-grey07" valign="top">{ozi_path}</td>
        </tr>
        <tr>
            <td class="buffer" colspan="2">
                <div class="notice" style="height:44px;">{{ozi_path_info}}</div>
                <div class="notice" style="height:44px;">{{ozi_path_info2}}</div>
            </td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/calendar.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{registered_since_label}}:</td>
            <td><b>{registered_since}</b></td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade" style="vertical-align:top;"><img src="tpl/stdstyle/images/free_icons/chart_bar.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{statpic_label}}:</td>
            <td><img src="statpics/{userid}.jpg" align="middle" alt="" /></td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade" style="vertical-align:top;"><img src="tpl/stdstyle/images/free_icons/html.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{html_statpic_link}}:</td>
            <td class="txt-grey07">&lt;img src="{statlink}" alt="{site_name} {{statpic_html_link}} {username_html}" title="{site_name} {{statpic_html_link}} {username_html}" /></td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade" style="vertical-align:top;"><img src="tpl/stdstyle/images/free_icons/html.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{bbcode_statpic}}:</td>
            <td class="txt-grey07">[url={profileurl}][img]{statlink}[/img][/url]</td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
    </table>
</div>
