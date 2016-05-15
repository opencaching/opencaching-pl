<?php

?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="{{change_account_data}}" title="{{change_account_data}}" align="middle" />&nbsp;{{change_account_data}}</div>
<div class="searchdiv">
    <div class="notice">
        {{gray_field_is_hidden}}
    </div>
    <div class="buffer"></div>
    <p class="content-title-noshade-size2">{{data_in_profile}}:</p>
    <div class="buffer"></div>
    <table class="table">
        <form name="change" action="myprofile.php?action=change" method="post" enctype="application/x-www-form-urlencoded"  style="display: inline;">
            <input type="hidden" name="show_all_countries" value="{show_all_countries}">
            <colgroup>
                <col width="150">
                <col>
            </colgroup>
            <tr>
                <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/user.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{username_label}}:</td>
                <td>
                    <input type="text" name="username" maxlength="60" value="{username}" class="input200"/>
                    {username_message}
                </td>
            </tr>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade txt-grey07"><img src="tpl/stdstyle/images/free_icons/email.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{email_address}}:</font></td>
                <td class="txt-grey07">{email}</td>
            </tr>
            <tr>
                <td class="content-title-noshade txt-grey07"><img src="tpl/stdstyle/images/description/22x22-geokret.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{GKApi02}}:</font></td>
                <td class="txt-grey07"><input type="text" name="GeoKretyApiSecid" maxlength="150" value="{GeoKretyApiSecid}" class="input200"/> <span style="color: red; font-weight:bold; font-size: 12;">{secid_message}</span></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <div class="notice" style="width:500px;height:44px;">{{GKApi03}} <a href="//geokrety.org/mypage.php" target="_blank">{{GKApi04}}</a></div>
                </td></tr>

            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/world.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{country_label}}:</td>
                <td>
                    <select name="country" class="input200">
                        {countrylist}
                    </select>
                    {allcountriesbutton}
                </td>
            </tr>
            {guide_start}
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/book_open.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{guide}}:</td>
                <td  valign="middle">
                    <input type="checkbox" name="guide" id="guide" value="1" {guide_sel} class="checkbox" />
                    <label for="guide">{{myprofile04}}.<br/> {{myprofile05}} <a class="links" href="cacheguides.php">{{myprofile03}}</a></label>
                </td>
            </tr>
            <tr><td>&nbsp;</td><td>
                    <div class="notice" style="width:500px;height:44px;">{{myprofile06}}</div>
                </td></tr>
            {guide_end}
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/map.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{coordinates}}:</td>
                <td class="content-title-noshade">
                    <fieldset style="border: 1px solid black; width: 50%; height: 32%; background-color: #FAFBDF;">
                        <legend>&nbsp; <strong>WGS-84</strong> &nbsp;</legend>&nbsp;&nbsp;&nbsp;
                        <select name="latNS" class="input40">
                            <option value="N"{latNsel}>N</option>
                            <option value="S"{latSsel}>S</option>
                        </select>
                        &nbsp;<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />
                        °&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input50" />&nbsp;'&nbsp;
                        {lat_message}
                        <br/>&nbsp;&nbsp;&nbsp;
                        <select name="lonEW" class="input40">
                            <option value="E" {lonEsel}>E</option>
                            <option value="W" {lonWsel}>W</option>
                        </select>
                        &nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />
                        °&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input50" />&nbsp;'&nbsp;
                        {lon_message}
                    </fieldset><br/>
                    <div class="notice" style="width:500px;height:44px;">{{myprofile07}}.</div>

                </td>
            </tr>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade txt-grey07" valign="top"><img src="tpl/stdstyle/images/free_icons/email_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{notification}}:</td>
                <td class="txt-grey07" valign="top">
                    {{notify_new_caches_radius}}&nbsp;
                    <input type="text" name="notify_radius" maxlength="3" value="{notify_radius}" class="input30" />
                    &nbsp;km {{from_home_coords}}.
                    &nbsp;
                    <div class="errormsg">{notify_message}</div>
                    <div class="notice" style="width:500px;height:44px;">{{radius_hint}}</div>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade txt-grey07" valign="top"><img src="tpl/stdstyle/images/free_icons/page_copy.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{bulletin}}:</td>
                <td class="txt-grey07" valign="middle">
                    <input type="checkbox" name="bulletin" id="bulletin" value="1" {is_checked} class="checkbox" />
                    <label for="bulletin">{{get_bulletin}}</label>&nbsp;
                    &nbsp;<br />
                </td>
            </tr>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade" valign="top"><img src="tpl/stdstyle/images/free_icons/script.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{my_description}}:</td>
                <td valign="top">
                    <textarea name="description" cols="50" rows="5">{description}</textarea>
                </td>
            </tr>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade txt-grey07" valign="top"><img src="tpl/stdstyle/images/free_icons/brick.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{other}}:</td>
                <td class="txt-grey07" valign="top">
                    <input type="checkbox" name="using_permanent_login" value="1" {permanent_login_sel} id="l_using_permanent_login" class="checkbox" />
                    <label for="l_using_permanent_login">{{no_auto_logout}}</label><br/>
                    <div class="notice" style="width:500px;height:44px;">{{no_auto_logout_warning}}</div>
                </td>
            </tr>

            <tr style="display: {displayGeoPathSection}">
                <td valign="top"></td>
                <td valign="top">
                    <span class="txt-grey07">
                        <input type="checkbox" name="geoPathsEmail" id="geoPathsEmail" value="1" {geoPathsEmailCheckboxChecked} class="checkbox" /> <label for="geoPathsEmail">{{pt235}}</label>
                    </span>
                </td>
            </tr>

            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade txt-grey07" valign="top"><img src="tpl/stdstyle/images/free_icons/plugin.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{ozi_path_label}}:</td>
                <td class="txt-grey07" valign="top"><input type="text" size="46" name="ozi_path" value="{ozi_path}"><br/>
                    <div class="notice" style="width:500px;height:44px;">{{ozi_path_info}}</div>
                    <div class="notice" style="width:500px;height:44px;">{{ozi_path_info2}}</div>
                </td>
            </tr>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade"><img src="tpl/stdstyle/images/free_icons/calendar.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{registered_since_label}}:</td>
                <td><b>{registered_since}</b></td>
            </tr>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit" value="{{change}}" style="font-size:14px;width:160px"><b>{{change}}</b></button>

                </td>
            </tr>
        </form>
    </table>
    <br/><br/>
</div>
