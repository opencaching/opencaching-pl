<?php

use Utils\Uri\SimpleRouter;

?>
<div class="content2-pagetitle">{{management_users}} {username}</div>

<div class="content2-container">
    <p><span class="content-title-noshade txt-blue08" >{{user_ident}}:</span> <strong>{username}</strong> [<a href="viewprofile.php?userid={userid}" class="links">{{user_profile}}</a>]</p>
    <p><span class="content-title-noshade txt-blue08" >{{registered_since_label}}:</span> <strong>{registered}</strong></p>
    <p><span class="content-title-noshade txt-blue08" >{{email_address}}: </span>{email} [<strong><a href="<?=SimpleRouter::getLink('UserProfile', 'mailTo', $view->userid)?>">{{email_user}}</a></strong>]</p>
    <p><span class="content-title-noshade txt-blue08" >{{activation_code}}:</span> <strong>&nbsp;&nbsp;{activation_codes}</strong></p>
    <p><span class="content-title-noshade txt-blue08" >{{country_label}}:</span><strong> &nbsp;&nbsp;{country}</strong></p>
    <p><span class="content-title-noshade txt-blue08" >{{descriptions}}:</span> <strong>&nbsp;&nbsp;{description}</strong></p>
    <p><span class="content-title-noshade txt-blue08" >{{lastlogins}}:</span> <strong>&nbsp;&nbsp;{lastlogin}</strong></p>
    <hr></hr>
    <p><span class="content-title-noshade txt-blue08" >{is_active_flags}</span></p>
    <p><span class="content-title-noshade txt-blue08" >{stat_ban}</span></p>
    {hide_flag}
    {remove_all_logs}
    {ignoreFoundLimit}
    <hr></hr>
    <br>
    <p><span class="content-title-noshade txt-blue08" >{form_title}:</span></p>
    <form action="admin_users.php?userid={userid}" method="post" name="user_note">
        <table id="cache_note1" class="table">
            <tr valign="top">
                <td></td>
                <td>
                    <textarea name="note_content" rows="4" cols="85" style="font-size:13px;"></textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">
                    <button type="submit" name="save" value="save" style="width:100px">{submit_button}</button>
                </td>
            </tr>
        </table>
    </form>
</div>
