<?php

namespace Utils\Database;

use lib\Objects\OcConfig\OcConfig;
use Utils\Email\EmailSender;
use PDO;
use PDOException;
use Utils\Email\OcSpamDomain;

class OcPdo extends PDO
{

    protected $debug; //bool, if set enabled debug messages

    /**
     *
     * @param string $debug
     */
    public function __construct($debug = false)
    {
        if ($debug === true) {
            $this->debug = true;
        }

        $conf = OcConfig::instance();

        $dsnarr = array(
            'host' => $conf->getDbHost(),
            'dbname' => $conf->getDbName(),
            'charset' => 'utf8'
        );

        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_LOCAL_INFILE => true,
            PDO::ATTR_EMULATE_PREPARES => true /* TODO: we should consider disabling the emulation!
            But this means that placeholders can't be reuse in one query (case: multiVariableQuery) */
        );

        /*
         * Older PHP versions do not support the 'charset' DSN option.
         * This should be removed in future
         */
        if ($dsnarr['charset'] and version_compare(PHP_VERSION, '5.3.6', '<')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $dsnarr['charset'];
        }

        $dsnpairs = array();
        foreach ($dsnarr as $k => $v) {
            if ($v === null) {
                continue;
            }
            $dsnpairs[] = $k . "=" . $v;
        }

        $dsn = 'mysql:' . implode(';', $dsnpairs);
        try{
            parent::__construct(
                $dsn, $conf->getDbUser(), $conf->getDbPass(), $options
            );
        }catch (PDOException $e){
            $message = "OcPdo object creation failed!";
            $this->error($message, $e, true); //fatal error!
            return null;
        }
    }

    /**
     * Handle error/exception occurence around DB operations
     *
     * @param string $message - description of the error
     * @param PDOException $e - exception object
     * @param bool $fatal     - should this error shutdown the script?
     * @param bool $sendEmail - should Email about error should be send?
     */
    protected function error(/*PHP7:string*/ $message, PDOException $e=null,
                             /*PHP7:bool*/ $fatal=false, /*PHP7:bool*/ $sendEmail=true){

        $email_text  = "+++ PDO Error +++ ";
        $email_text .= "\n+++ Debug: ".$message;
        if( !is_null($e) ){
            $email_text .= "\n+++ Ex_Code: ".$e->getCode();
            $email_text .= "\n+++ Ex_Msg: ".$e->getMessage();
        }else{
            //there is no Exception - generate one to get the trace
            $e = new PDOException();
        }
        $email_text .= "\n+++ Ex_Trace:\n".$e->getTraceAsString();

        //get short version of the trace
        $traceStr = '';
        foreach($e->getTrace() as $trace){
            $traceStr.= ' | '.$trace['file'].'::'.$trace['line'];
        }

        //send email to RT
        if($sendEmail)
            EmailSender::adminOnErrorMessage($email_text, OcSpamDomain::DB_ERRORS);

        if($this->debug){
            d($email_text);
        }

        if($fatal){
            // TODO: How to better handle error - print some nice error page
            // this is fatal error - stop the script
            trigger_error("OcPdo Error:\n $message. Trace: ".$traceStr, E_USER_ERROR);
            error_log("Db message:".$e->getMessage());
            exit;
        }else{
            // non-fatal error: only print warning
            trigger_error("OcPdo Error: $message. Trace: ".$traceStr, E_USER_WARNING);
            error_log("Db message:".$e->getMessage());
        }
    }

    /**
     * Print debug messages around DB operations
     *
     * @param string $text
     */
    protected function debugOut(/*PHP7:string*/ $text)
    {
        if( ! $this->debug ){
            return;
        }
        d($text);
    }

    /**
     * This the ONLY way on which instance of this class
     * should be accessed
     *
     * Returns instance of itself.
     *
     * @return OcDb object
     */
    protected static function instance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new static(false);
        }
        return $instance;
    }

    /**
     * Turn on debug messages around DB operations
     * @param unknown $debug
     */
    public function setDebug(/*PHP7:bool*/ $debug)
    {
        $this->debug = $debug;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone() {}

}
