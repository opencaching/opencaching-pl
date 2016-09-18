<?php

?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" alt="" title="{{register_new}}" class="icon32"/>&nbsp;{{register_new}}</div>

<form name="register" action="register.php" method="post" enctype="application/x-www-form-urlencoded" style="display: inline;" id="register">
    <input type="hidden" name="allcountries" value="{show_all_countries}" />

    <div class="notice">
        {{register_msg1}}
    </div>
    <div>
        <img src="tpl/stdstyle/images/free_icons/user_add.png" class="icon16" alt="" title="" align="middle" />&nbsp;<label for="username">{{username_label}}</label>
        <input type="text" name="username" id="username" maxlength="60" value="{username}" class="input200" placeholder="{{register00}}" required="required" /> <span style="font-size: 15px; color: red;">*</span> {username_message}
    </div>
    <div>
        <img src="tpl/stdstyle/images/free_icons/world_add.png" class="icon16" alt="" title="" align="middle" />&nbsp;<label for="country">{{country_label}}</label>

        <select name="country" id="country" class="input200" >
            {countries_list}
        </select>&nbsp;{all_countries_submit}
    </div>
    <div>
        <img src="tpl/stdstyle/images/free_icons/email_add.png" class="icon16" alt="" title="" align="middle" />&nbsp;<label for="email">{{email_address}}:</label>
        <input type="email" name="email" maxlength="80" id="email" value="{email}" class="input200" placeholder="{{register01}}" required="required"/> <span style="font-size: 15px; color: red;">*</span>&nbsp;{email_message}
    </div>
    <div><img src="tpl/stdstyle/images/free_icons/key_add.png" class="icon16" alt="" title="" align="middle" />&nbsp;<label for="password1">{{password}}:</label>
        <input type="password" name="password1" maxlength="80" id="password1" value="" class="input200" placeholder="{{register02}}" required="required"/> <span style="font-size: 15px; color: red;">*</span>&nbsp;{password_message}
    </div>
    <div><img src="tpl/stdstyle/images/free_icons/key_go.png" class="icon16" alt="" title="" align="middle" /> &nbsp;<label for="password2">{{password_confirm}}</label>
        <input type="password" name="password2" maxlength="80" id="password2" value="" class="input200" placeholder="{{register03}}" required="required"/> <span style="font-size: 15px; color: red;">*</span>
    </div>
    <div>
        {{register_msg2}}
    </div>
    <div class="notice">
        {{register_msg3}}
    </div>
    <div class="notice" style="height:44px;">
        {{register_msg7}}
    </div>

    <div>
        <img src="tpl/stdstyle/images/free_icons/script.png" class="icon16" alt="" title="" align="middle" />&nbsp;<input type="checkbox" name="TOS" value="ON" style="border:0;" />{{register_msg4}}
        {tos_message}
    </div>

    <div>
        <input type="reset" name="reset" value="{{register04}}" class="btn btn-default"/>&nbsp;&nbsp;
        <input type="submit" name="submit" value="{{registration}}" class="btn btn-primary"/>
    </div>
</form>
