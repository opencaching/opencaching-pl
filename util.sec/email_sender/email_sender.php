<?php

/* * *************************************************************************
  ./util/email_user/email_user.php
  -------------------
  begin                : Sat September 3 2005
  copyright            : (C) 2005 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************

  Unicode Reminder ăĄă˘

  Ggf. muss die Location des php-Binaries angepasst werden.

  Diese Script versendet die am Frontend eingetragenen und in der Table
  'email_user' zwischengespeicherten Emails an die entsprechenden Adressen.

 * ************************************************************************* */

$rootpath = '../../';
require_once($rootpath . 'lib/common.inc.php');

/* begin db connect */
db_connect();
if ($dblink === false) {
    echo 'Unable to connect to database';
    exit;
}
/* end db connect */

/* begin */
$result = sql('SELECT `id`, `to_email`, `send_emailaddress`, `from_email`, `mail_subject`, `mail_text` FROM `email_user` WHERE `date_sent`=0');

while ($row = sql_fetch_array($result)) {
    $headers = '';
    $to_email = ($debug == true) ? $debug_mailto : $row['to_email'];

    if ($row['send_emailaddress'] == '1') { // send emailaddress
        $headers = "Content-Type: text/plain; charset=utf-8\n";
        $headers .= 'From: "' . $mailfrom . '" <' . $emailaddr . ">\n";
        $headers .= 'Return-Path: ' . $row['from_email'] . "\n";
        $headers .= 'Reply-To: ' . $row['from_email'] . "\n";
    } else {
        $headers = "Content-Type: text/plain; charset=utf-8\n";
        $headers .= 'From: "' . $mailfrom . '" <' . $emailaddr . ">\n";
        $headers .= 'Return-Path: ' . $mailfrom . "\n";
        $headers .= 'Reply-To: ' . $mailfrom_noreply . "\n";
    }

    if (mb_send_mail($to_email, $row['mail_subject'], $row['mail_text'], $headers)) {
        // Kopie an Sender
        mb_send_mail($row['from_email'], $row['mail_subject'], tr('copy_sender') . ":\n" . $row['mail_text'], $headers);

        $upd_result = sql("UPDATE `email_user` SET `mail_text`='[Delivered]', `date_sent`=NOW() WHERE `id`='&1'", $row['id']);
    }
}
?>
