<?php

$message_user_not_found = tr('message_user_not_found');
$message_sent = tr('message_sent');

$errnosubject = '<span class="errormsg">' . tr('mailto_01') . '</span>';
$errnotext = '<span class="errormsg">' . tr('mailto_02') . '</span>';

$mailsubject = "[opencaching] " . tr('mailto_03') . " '{from_username}': {subject}";

$mailtext_email = tr('mailto_04') . " {to_username},\n\n";
$mailtext_email .= "'{from_username}' " . tr('mailto_05') . " {from_email} " . tr('mailto_06') . " $absolute_server_URI\n";
$mailtext_email .= tr('mailto_07') . $absolute_server_URI . "viewprofile.php?userid={from_userid}\n";
$mailtext_email .= tr('mailto_08') . "\n";
$mailtext_email .= "----------------------\n\n";
$mailtext_email .= "{{text}}\n";

$mailtext_anonymous = tr('mailto_04') . " {to_username},\n\n";
$mailtext_anonymous .= "'{from_username}' " . tr('mailto_06') . " $absolute_server_URI\n";
$mailtext_anonymous .= tr('mailto_07') . $absolute_server_URI . "viewprofile.php?userid={from_userid}\n";
$mailtext_anonymous .= tr('mailto_09') . "\n";
$mailtext_anonymous .= "----------------------\n";
$mailtext_anonymous .= "{{text}}\n";
?>
