<?php
use Utils\Uri\SimpleRouter;

?>
<div class="content2-pagetitle">{{my_account}}</div>
<div class="content2-container">

  <div class="align-right">
    <a class="btn btn-success btn-sm" href="/myprofile.php?action=change">{{change_data}}</a>
    <a class="btn btn-default btn-sm" href="/newemail.php">{{change_email}}</a>
    <a class="btn btn-default btn-sm" href="<?=SimpleRouter::getLink('UserAuthorization', 'newPassword')?>">{{change_password}}</a>
    <a class="btn btn-default btn-sm" href="/change_statpic.php">{{choose_statpic}}</a>
    <a class="btn btn-default btn-sm" href="<?=SimpleRouter::getLink('UserProfile', 'notifySettings')?>">{{settings_notifications}}</a>
  </div>

  <div class="buffer"></div>
  <div class="notice">{{myprofile01}}</div>
  <div class="notice">{{gray_field_is_hidden}}</div>

  <div class="buffer"></div>
  <p class="content-title-noshade-size2">{{data_in_profile}}</p>
  <div class="buffer"></div>
  <table class="table">
    <tr>
      <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/user.png" class="icon16" alt="" align="middle">&nbsp;{{username_label}}:</td>
      <td>{username}</td>
    </tr>
    <tr>
      <td class="buffer" colspan="2"></td></tr>
    <tr>
      <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/email.png" class="icon16" alt="" align="middle">&nbsp;{{email_address}}:</td>
      <td class="txt-grey07">{email}</td>
    </tr>
    <tr>
      <td class="buffer" colspan="2"></td>
    </tr>
    <tr>
      <td class="content-title-noshade"><img src="tpl/stdstyle/images/description/22x22-geokret.png" width="16" height="16" class="icon16" alt="" align="middle">&nbsp;{{GKApi01}}:</td>
      <td class="txt-grey07">{GeoKretyApiIntegration}</td>
    </tr>
    <tr>
      <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/world.png" class="icon16" alt="" align="middle">&nbsp;{{country_label}}:</td>
      <td>{country}</td>
    </tr>
    {guides_start}
    <tr>
      <td class="buffer" colspan="2"></td>
    </tr>
    <tr>
      <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/book_open.png" class="icon16" alt="" align="middle">&nbsp;{{guide}}:</td>
      <td  valign="middle">
        {{myprofile02}}<a class="links" href="/cacheguides.php">{{myprofile03}}</a></label>
      </td>
    </tr>
    {guides_end}
    <tr>
      <td class="buffer" colspan="2"></td>
    </tr>
    <tr>
      <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/script.png" class="icon16" alt="" align="middle">&nbsp;{{my_description}}:</td>
      <td>{description}</td>
    </tr>
    <tr>
      <td class="buffer" colspan="2"></td>
    </tr>
    <tr>
      <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/brick.png" class="icon16" alt="" align="middle">&nbsp;{{other}}:</td>
      <td class="txt-grey07" valign="top">{user_options}</td>
    </tr>
    <tr>
      <td class="buffer" colspan="2"></td>
    </tr>
    <tr>
      <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/plugin.png" class="icon16" alt="" align="middle">&nbsp;{{ozi_path_label}}:</td>
      <td class="txt-grey07" valign="top">{ozi_path}</td>
    </tr>
    <tr>
      <td colspan="2">
        <div class="notice">{{ozi_path_info}}</div>
        <div class="notice">{{ozi_path_info2}}</div>
      </td>
    </tr>
    <tr>
      <td class="buffer" colspan="2"></td>
    </tr>
    <tr>
      <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/calendar.png" class="icon16" alt="" align="middle">&nbsp;{{registered_since_label}}:</td>
      <td>{registered_since}</td>
    </tr>
    <tr>
      <td class="buffer" colspan="2"></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="vertical-align:top;"><img src="tpl/stdstyle/images/free_icons/chart_bar.png" class="icon16" alt="" align="middle">&nbsp;{{statpic_label}}:</td>
      <td><img src="statpics/{userid}.jpg" align="middle" alt=""></td>
    </tr>
    <tr>
      <td class="buffer" colspan="2"></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="vertical-align:top;"><img src="tpl/stdstyle/images/free_icons/html.png" class="icon16" alt="" align="middle">&nbsp;{{html_statpic_link}}:</td>
      <td class="txt-grey07">&lt;img src="{statlink}" alt="{site_name} {{statpic_html_link}} {username_html}" title="{site_name} {{statpic_html_link}} {username_html}"></td>
    </tr>
    <tr>
      <td class="buffer" colspan="2"></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="vertical-align:top;"><img src="tpl/stdstyle/images/free_icons/html.png" class="icon16" alt="" align="middle">&nbsp;{{bbcode_statpic}}:</td>
      <td class="txt-grey07">[url={profileurl}][img]{statlink}[/img][/url]</td>
    </tr>
  </table>
</div>
