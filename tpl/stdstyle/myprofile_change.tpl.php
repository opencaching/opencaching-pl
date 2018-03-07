<div class="content2-pagetitle">{{change_account_data}}</div>
<div class="content2-container">
  <div class="notice">{{gray_field_is_hidden}}</div>

  <div class="buffer"></div>
  <p class="content-title-noshade-size2">{{data_in_profile}}</p>
  <div class="buffer"></div>
  <form name="change" action="/myprofile.php?action=change" method="post" enctype="application/x-www-form-urlencoded"  style="display: inline;">
    <input type="hidden" name="show_all_countries" value="{show_all_countries}">
    <table class="table">
      <colgroup>
        <col width="150">
        <col>
      </colgroup>
      <tr class="form-group-sm">
        <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/user.png" class="icon16" alt="" align="middle">&nbsp;{{username_label}}:</td>
        <td>
          <input type="text" name="username" maxlength="60" value="{username}" class="form-control input200">
          {username_message}
        </td>
      </tr>
      <tr>
        <td class="buffer" colspan="2"></td>
      </tr>
      <tr class="form-group-sm">
        <td class="content-title-noshade txt-grey07"><img src="tpl/stdstyle/images/description/22x22-geokret.png" class="icon16" alt="" align="middle">&nbsp;{{GKApi02}}</td>
        <td class="txt-grey07"><input type="text" name="GeoKretyApiSecid" maxlength="150" value="{GeoKretyApiSecid}" class="form-control input200"> <span style="color: red; font-weight:bold; font-size: 12px;">{secid_message}</span></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>
          <div class="notice" style="width:500px;height:44px;">{{GKApi03}} <a href="https://geokrety.org/mypage.php" target="_blank" rel="noopener">{{GKApi04}}</a></div>
        </td>
      </tr>
      <tr class="form-group-sm">
        <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/world.png" class="icon16" alt="" align="middle">&nbsp;{{country_label}}:</td>
        <td>
          <select name="country" class="form-control input200">
            {countrylist}
          </select>
          {allcountriesbutton}
        </td>
      </tr>
      {guide_start}
      <tr>
        <td class="buffer" colspan="2"></td></tr>
      <tr>
        <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/book_open.png" class="icon16" alt="" align="middle">&nbsp;{{guide}}:</td>
        <td  valign="middle">
          <input type="checkbox" name="guide" id="guide" value="1" {guide_sel} class="checkbox">
          <label for="guide">{{myprofile04}}.<br> {{myprofile05}} <a class="links" href="/cacheguides.php">{{myprofile03}}</a></label>
        </td>
      </tr>
      {guide_end}
      <tr>
        <td class="buffer" colspan="2"></td>
      </tr>
      <tr>
        <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/script.png" class="icon16" alt="" align="middle">&nbsp;{{my_description}}:</td>
        <td valign="top">
          <textarea name="description" cols="50" rows="5">{description}</textarea>
        </td>
      </tr>
      <tr>
        <td class="buffer" colspan="2"></td>
      </tr>
      <tr>
        <td class="content-title-noshade txt-grey07" valign="top"><img src="tpl/stdstyle/images/free_icons/brick.png" class="icon16" alt="" align="middle">&nbsp;{{other}}:</td>
        <td class="txt-grey07" valign="top">
          <input type="checkbox" name="using_permanent_login" value="1" {permanent_login_sel} id="l_using_permanent_login" class="checkbox">
          <label for="l_using_permanent_login">{{no_auto_logout}}</label><br>
          <div class="notice" style="width:500px;height:44px;">{{no_auto_logout_warning}}</div>
        </td>
      </tr>
      <tr style="display: {displayGeoPathSection}">
        <td valign="top"></td>
        <td valign="top">
          <span class="txt-grey07">
            <input type="checkbox" name="geoPathsEmail" id="geoPathsEmail" value="1" {geoPathsEmailCheckboxChecked} class="checkbox"> <label for="geoPathsEmail">{{pt235}}</label>
          </span>
        </td>
      </tr>
      <tr>
        <td class="buffer" colspan="2"></td>
      </tr>
      <tr class="form-group-sm">
        <td class="content-title-noshade txt-grey07" valign="top"><img src="tpl/stdstyle/images/free_icons/plugin.png" class="icon16" alt="" align="middle">&nbsp;{{ozi_path_label}}:</td>
        <td class="txt-grey07" valign="top"><input type="text" size="46" name="ozi_path" value="{ozi_path}" class="form-control input300"><br>
          <div class="notice" style="width:500px;height:44px;">{{ozi_path_info}}</div>
          <div class="notice" style="width:500px;height:44px;">{{ozi_path_info2}}</div>
        </td>
      </tr>
    </table>
    <div class="align-center">
      <button type="submit" name="submit" value="{{change}}" class="btn btn-md btn-primary">{{save_changes}}</button>
    </div>
  </form>
</div>