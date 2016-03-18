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

class Email
{
    private $toAddr = array();
    private $ccAddr = array();
    private $bccAddr = array();

    private $fromAddr;
    private $replyToAddr;

    private $xMailer;

    private $subjectPrefix=''; //subject prefix set in all emails
    private $subject='';

    private $body='';
    private $body_header=''; //header of message set in all emails
    private $body_footer=''; //footer of email set in all emails

    private $isHtmlEmail;

    public function __construct(){
        $this->xMailer = phpversion();
        $this->isHtmlEmail = true; //only HTML emails

        //TODO : should be set based on config
        $this->subjectPrefix = '';
        $this->body_header = '';
        $this->body_footer = '';
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

        $headers[] = 'From: '. $this->fromAddr;
        $headers[] = 'Reply-To: '. $this->replyToAddr;
        $headers[] = 'X-Mailer: PHP/' . $this->xMailer;

        if($this->isHtmlEmail){
            // To send HTML mail, the Content-type header must be set
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        }

        // Additional headers
        if(!empty($this->ccAddr))
            $headers[] = 'Cc: ' . implode(',',$this->ccAddr);

        if(!empty($this->bccAddr))
            $headers[] = 'Bcc: ' .implode(',',$this->bccAddr);;

        if(!empty($this->toAddr))
            $to = implode(',', $this->toAddr);
        else
            $to=''; //TODO: is it work?

        $subject = $this->subjectPrefix . $this->subject;
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

    public function setSubject($subject){
        $this->subject = $subject;
    }

    public function setBody($body){
        $this->body = $body;
    }

    /**
     * returns TRUE is given emailAddress has proper format
     */
    public static function isValidEmail($emailAddress)
    {
        //if( false === filter_var($emailAddress, FILTER_VALIDATE_EMAIL) ){
        //    return false;
        //}
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
}