<?php
/**
 * Date: 22.04.16
 * Time: 21:42
 * Purpose of this class: easy applying mail templates
 * At these moment mail templates are stored in /tpl/stdstyle/email
 */

namespace Utils\Email;
use lib\Objects\ApplicationContainer;

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
     */
    public function addFooterAndHeader($username) {
        $footer = new EmailFormatter(__DIR__ . "/../../tpl/stdstyle/email/ocFooter.email.html");
        $header = new EmailFormatter(__DIR__ . "/../../tpl/stdstyle/email/ocHeader.email.html");

        $footer->setVariable("octeamEmailsSignature", ApplicationContainer::Instance()->getOcConfig()->getOcteamEmailsSignature());

        $header->setVariable("server", ApplicationContainer::Instance()->getOcConfig()->getAbsolute_server_URI());
        $header->setVariable("oc_logo", ApplicationContainer::Instance()->getOcConfig()->getHeaderLogo());
        $header->setVariable("sitename", ApplicationContainer::Instance()->getOcConfig()->getSiteName());
        $header->setVariable("short_sitename", ApplicationContainer::Instance()->getOcConfig()->getShortSiteName());
        $header->setVariable("welcome", tr("welcome"));
        $header->setVariable("user", $username);

        $this->emailContent = $header->getEmailContent() . $this->emailContent . $footer->getEmailContent();
    }
}
