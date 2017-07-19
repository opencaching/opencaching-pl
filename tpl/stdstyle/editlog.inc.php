<?php

$submit = 'Wyslij';
$coord_empty_message = '<img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;&nbsp;<span class="errormsg">' . tr('empty_coordinatest') . '</span>';
$logtext_empty_message = '<img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;&nbsp;<span class="errormsg">' . tr('empty_log_text') . '</span>';

$error_wrong_node = "Ten wpis do logu został dostarczony przez inny Opencaching serwer i można go edytować tylko na tym serwerze.";

$date_message = '<span class="errormsg">Data ma niepoprawny format.Poprawny format: DD-MM-RRRR</span>';

$log_pw_field = '<tr><td colspan="2">' . tr('password_to_log') . ': <input class="input100" type="text" name="log_pw" maxlength="20" value="" /> (' . tr('only_for_found_it') . ')</td></tr>
                    <tr><td class="spacer" colspan="2"></td></tr>';
$log_pw_field_pw_not_ok = '<tr><td colspan="2">' . tr('password_to_log') . ': <input type="text" name="log_pw" maxlength="20" size="20" value=""/><span class="errormsg"> ' . tr('incorrect_password_to_log') . '!</span></td></tr><tr><td class="spacer" colspan="2"></td></tr>';

