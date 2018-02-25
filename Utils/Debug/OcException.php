<?php
namespace Utils\Debug;

use Exception;

/**
 * This is extended exception to user in OC code
 */
class OcException extends Exception
{
    private $displayToUser = true; // should exception details should be displayed to browser
    private $noticeInLog = false; // should exceptions be noticed in php error log

    /**
     * @param string $message
     * @param boolean $displayToUser - display details on error page
     * @param boolean $noticeInLog - notice exception in php error log
     */
    public function __construct($message, $displayToUser=true, $noticeInLog=false)
    {
        parent::__construct($message);
        $this->displayToUser = $displayToUser;
        $this->noticeInLog = $noticeInLog;
    }

    public function displayToUser()
    {
        return $this->displayToUser;
    }

    public function noticeInLog()
    {
        return $this->noticeInLog;
    }
}
