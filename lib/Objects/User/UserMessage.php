<?php

namespace lib\Objects\User;

use lib\Objects\BaseObject;
use Utils\Database\XDb;
use Utils\Email\EmailSender;


class UserMessage extends BaseObject
{
    public static function SendUserMessage(User $from, User $to, $subject, $text, $attachSenderAddress)
    {

        // save email trace
        XDb::xSql(
            "INSERT INTO `email_user`
            SET `ipaddress`= ? , `date_generated`=NOW(),
                `from_user_id`= ? , `from_email`= ?,
                `to_user_id`= ?, `to_email`= ?,
                `mail_subject`= ?, `mail_text`= ?, `send_emailaddress`= ?",
            $_SERVER['REMOTE_ADDR'],
            $from->getUserId(), $from->getEmail(), $to->getUserId(), $to->getEmail(),
            $subject, $text, ($attachSenderAddress)?'1':'0');

        // send emails
        return EmailSender::sendUser2UserMessage($from, $to, $subject, $text, $attachSenderAddress);
    }
}

