<?php
/**
 * This is general class to sending emails
 * It shouldn't be used outside of EmailSender class;
 *
 * If you want to send some email from the OC code implement
 * proper method in EmailSender class.
 *
 */

namespace Utils\Email;

use lib\Objects\OcConfig\OcConfig;

class Email
{
    private $toAddr = array();
    private $ccAddr = array();
    private $bccAddr = array();

    private $fromAddr;
    private $senderName;
    private $replyToAddr;

    private $xMailer;

    private $subjectPrefix=''; //subject prefix set in all emails
    private $subject='';

    private $body='';

    private $isHtmlEmail;
    private $isHtmlBody;     // does body of the message needs html formatting

    public function __construct(){
        $this->xMailer = phpversion();
        $this->isHtmlEmail = true; //only HTML emails

        //TODO : should be set based on config
        $this->subjectPrefix = '';
        $this->body_header = '';
        $this->body_footer = '';

        $this->senderName = OcConfig::getSiteName();
    }

    /**
     * Send the email based on current settings
     * Returns true on success
     */
    public function send(){

        if(! $this->isEmailValid() ){
            return false;
        }
        // Each line of message should be separated with a CRLF (\r\n).
        // Lines should not be larger than 70 characters.
        //TODO:...
        //wordwrap($message, 70, "\r\n");
        $headers[] = 'From: '. $this->senderName .' <'.$this->fromAddr.'>';
        $headers[] = 'Reply-To: '. $this->replyToAddr;
        $headers[] = 'X-Mailer: PHP/' . $this->xMailer;

        if($this->isHtmlEmail){
            // To send HTML mail, the Content-type header must be set
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=utf-8';

            if(!$this->isHtmlBody){
                // format body
                $this->body = $this->formatToHtml($this->body);
            }
        }

        // Additional headers
        if(!empty($this->ccAddr))
            $headers[] = 'Cc: ' . implode(',',$this->ccAddr);

        if(!empty($this->bccAddr))
            $headers[] = 'Bcc: ' .implode(',',$this->bccAddr);

        if(!empty($this->toAddr))
            $to = implode(',', $this->toAddr);
        else
            $to=''; //TODO: is it work?

        $subject = $this->subjectPrefix . " " . $this->subject;
        $message = $this->body;

        return mb_send_mail($to, $subject, $message, implode("\r\n", $headers));
    }

    public function addToAddr($addr){
        if(self::isValidEmail($addr)){
            $this->toAddr[] = $addr;
            return true;
        }else{
            $this->error(__METHOD__.': improper email address: '.$addr);
            return false;
        }
    }

    public function addCcAddr($addr){
        if(self::isValidEmail($addr)){
            $this->ccAddr[] = $addr;
            return true;
        }else{
            $this->error(__METHOD__.': improper email address: '. $addr);
            return false;
        }
    }

    public function addBccAddr($addr){
        if(self::isValidEmail($addr)){
            $this->bccAddr[] = $addr;
            return true;
        }else{
            $this->error(__METHOD__.': improper email address: '. $addr);
            return false;
        }
    }

    public function setFromAddr($addr){
        if(self::isValidEmail($addr)){
            $this->fromAddr = $addr;
            return true;
        }else{
            $this->error(__METHOD__.': improper email address: '. $addr);
            return false;
        }
    }

    public function setReplyToAddr($addr){
        if(self::isValidEmail($addr)){
            $this->replyToAddr = $addr;
            return true;
        }else{
            $this->error(__METHOD__.': improper email address: '. $addr);
            return false;
        }
    }

    public function addSubjectPrefix($newPrefix) {
        if (empty($newPrefix) || $newPrefix == "") { //because somebody may want to turn off the global prefix in config
            return;
        } else {
            $this->subjectPrefix = $this->subjectPrefix ."[" . $newPrefix . "]";
        }
    }

    public function setSubject($subject){
        $this->subject = $subject;
    }

    public function setBody($body, $isHtml=false){
        $this->body = $body;
        $this->isHtmlBody = $isHtml;
    }

    /**
     * returns TRUE is given emailAddress has proper format
     * @param $emailAddress
     * @return bool
     */
    public static function isValidEmail($emailAddress)
    {
        if( false === filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    private function error($message, Exception $e=null){
        //TODO:
        trigger_error($message, E_USER_NOTICE);
    }

    private function isEmailValid(){

        //check recipients
        if( empty($this->toAddr) &&
            empty($this->ccAddr) &&
            empty($this->bccAddr) ){

                //no recipient of this email
                $this->error(__METHOD__.": Trying to send email with no recipients.");
                return false;
        }

        //check subject
        if($this->subject == ''){
            //empty subject email
            $this->error(__METHOD__.": Trying to send email without subject.");
            return false;
        }

        return true;
    }

    /**
     * prepare the string param to present as HTML message
     * @param string $string
     * @return string as HTML
     */
    private function formatToHtml($string){

        $string = htmlentities($string, ENT_QUOTES , "UTF-8");
        $string = '<pre>'.nl2br($string).'</pre>';

        return $string;
    }

    public function getSenderName()
    {
        return $this->senderName;
    }

    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }
}
