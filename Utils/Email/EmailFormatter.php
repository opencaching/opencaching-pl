<?php
/**
 * Date: 22.04.16
 * Time: 21:42
 * Purpose of this class: easy applying mail templates
 * At these moment mail templates are stored in /tpl/stdstyle/email
 */

namespace Utils\Email;
use lib\Objects\OcConfig\OcConfig;


class EmailFormatter {
    private $emailContent;
    public function __construct($emailTemplateFile) {
        $this->emailContent = $emailContent = file_get_contents($emailTemplateFile);
    }

    //TODO: maybe prepare exactly the same mechanism like tpl_set_var... etc.
    public function setVariable($variable, $value) {
        $this->emailContent = mb_ereg_replace("{".$variable."}", $value, $this->emailContent);
    }

    public function getEmailContent()
    {
        return $this->emailContent;
    }

    /**
     * @param $username //because we always write to somebody with oc nick
     * @param bool $automatically //if you don't want to add 'mail_auto_generated' text into footer - set it to false
     */
    public function addFooterAndHeader($username, $automatically=true) {
        $footer = new EmailFormatter(__DIR__ . "/../../tpl/stdstyle/email/ocFooter.email.html");
        $header = new EmailFormatter(__DIR__ . "/../../tpl/stdstyle/email/ocHeader.email.html");

        $footer->setVariable("octeamEmailsSignature", OcConfig::getOcteamEmailsSignature());
        if($automatically) {
            $footer->setVariable("mail_auto_generated", tr("mail_auto_generated"));
        } else {
            $footer->setVariable("mail_auto_generated", "");
        }

        $header->setVariable("server", OcConfig::getAbsolute_server_URI());
        $header->setVariable("oc_logo", OcConfig::getHeaderLogo());
        $header->setVariable("sitename", OcConfig::getSiteName());
        $header->setVariable("short_sitename", OcConfig::getShortSiteName());
        $header->setVariable("welcome", tr("welcome"));
        $header->setVariable("user", $username);

        $this->emailContent = $header->getEmailContent() . $this->emailContent . $footer->getEmailContent();
    }
}
