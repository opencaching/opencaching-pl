<?php
/**
 * This is general class to sending emails
 * It shouldn't be used outside of EmailSender class;
 *
 * If you want to send some email from the OC code implement
 * proper method in EmailSender class.
 *
 */
namespace src\Utils\Email;

use Exception;
use src\Utils\Text\Validator;
use src\Models\OcConfig\OcConfig;

class Email
{
    private $toAddr = [];
    private $ccAddr = [];
    private $bccAddr = [];

    private $fromAddr;
    private $senderName;
    private $replyToAddr = '';

    private $subjectPrefix = '';
    private $subject = '';

    private $htmlBody = '';

    public function __construct()
    {
        // Set default sender name
        $this->senderName = OcConfig::getSiteName();
    }

    /**
     * Send the email based on current settings
     * Returns true on success
     */
    public function send()
    {
        // Check mandantory addresses
        if (empty($this->toAddr)) {
            throw new \RuntimeException("Missing email recipient.");
        }
        if (empty($this->fromAddr)) {
            throw new \RuntimeException("Missing email sender.");
        }
        if (!self::isValidEmailAddr($this->toAddr) ||
            !self::isValidEmailAddr($this->fromAddr)
        ) {
            // We cannot decide here how to handle that. Caller must evaluate
            // the return values of setFromAddr() and addToAddr() if handling
            // is needed.
            $to = implode(',', $this->toAddr);
            throw new \RuntimeException("Invalid recipient/sender address! $to/{$this->fromAddr}");
        }
        $headers = [];

        // Check subject. It is technically allowed to send a email without
        // subject, but we don't want to do that.
        if ($this->subject == '') {
            throw new \RuntimeException("Email subject missing");
        }

        if (empty($this->senderName)) {
            $headers[] = 'From: ' . $this->fromAddr;
        } else {
            $headers[] = 'From: "' . $this->senderName . '" <' . $this->fromAddr . '>';
        }

        // optional addresses
        if (!empty($this->ccAddr)) {
            $headers[] = 'Cc: ' . implode(',', $this->ccAddr);
        }
        if (!empty($this->bccAddr)) {
            $headers[] = 'Bcc: ' . implode(',', $this->bccAddr);
        }
        if (!empty($this->replyToAddr)) {
            $headers[] = 'Reply-To: ' . $this->replyToAddr;
        }

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $to = implode(', ', $this->toAddr);
        $subject = $this->subjectPrefix . " " . $this->subject;
        $message = $this->htmlBody;

        return mb_send_mail($to, $subject, $message, implode("\r\n", $headers));
    }

    public function setFromAddr($addr)
    {
        $this->fromAddr = $addr;
        return self::isValidEmailAddr($addr);
    }

    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

    public function addToAddr($addr)
    {
        $this->toAddr[] = $addr;
        return self::isValidEmailAddr($addr);
    }

    public function addCcAddr($addr)
    {
        if (self::isValidEmailAddr($addr)) {
            $this->ccAddr[] = $addr;
            return true;
        } else {
            return false;
        }
    }

    public function addBccAddr($addr)
    {
        if (self::isValidEmailAddr($addr)) {
            $this->bccAddr[] = $addr;
            return true;
        } else {
            return false;
        }
    }

    public function setReplyToAddr($addr)
    {
        if (self::isValidEmailAddr($addr)) {
            $this->replyToAddr = $addr;
            return true;
        } else {
            return false;
        }
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return ($subject != "");
    }

    public function addSubjectPrefix($newPrefix)
    {
        if (!empty($newPrefix)) { // because somebody may want to turn off the global prefix in config

            $this->subjectPrefix = $this->subjectPrefix . "[" . $newPrefix . "]";
        }
    }

    public function setHtmlBody($htmlText)
    {
        $this->htmlBody = $htmlText;
    }

    public function setPlainTextBody($plainText)
    {
        $body = htmlentities($plainText, ENT_QUOTES, "UTF-8");
        $body = nl2br($body);
        $this->htmlBody = '<pre>' . $body . '</pre>';
    }

    /**
     * @param $emailAddress string|array
     * @return bool - TRUE is the given email address(es) is/are empty have proper format
     */
    public static function isValidEmailAddr($emailAddress)
    {
        if (is_array($emailAddress)) {
            foreach ($emailAddress as $addr) {
                if (!self::isValidEmailAddr($addr)) {
                    return false;
                }
            }
            return true;
        }

        // Workaround for develsite problem -- following
        if (preg_match('/.*@localhost$/', $emailAddress)) {
            return true;
        }

        // TODO(mzylowski): Remove this if, when email refactoring will be finished:
        if ($emailAddress == "user@ocpl-devel") {
            return true;
        } // debugging purposes

        return Validator::isValidEmail($emailAddress);
    }
}
