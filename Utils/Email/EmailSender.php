<?php

use lib\Objects\OcConfig\OcConfig;

class EmailSender
{
    private $conf;  //OcConfig instance

    function __construct()
    {
        $this->conf = OcConfig::instance();
        //TODO:

        $this->errorEmail[] = $mail_rt;
        $this->replyToEmail = $mail_rt;

    }

    function adminErrorMessage($source, $message)
    {
        //TODO:
        //mail($email, $topic, $message, $headers);
    }

}
