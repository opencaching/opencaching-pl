<?php

use lib\Objects\OcConfig\OcConfig;
use Utils\Email\EmailSender\EmailSender;

class OcPdo extends PDO
{

    protected $debug;










    /**
     *
     * @param string $debug
     */
    function __construct($debug = false)
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
            PDO::ATTR_EMULATE_PREPARES => false
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

        }
    }

    function __destruct(){
        $this->debugOut('destructing object of OcPdo class <br/><br/>');
    }


    protected function errorMessage($line, $e, $query, $params)
    {
        $message = 'db.php, line: ' . $line . ', <p class="errormsg"> PDO error: ' . $e . '</p><br />
                    Database Query: ' . $query . '<br>
                    Parametres array: <pre>' .
                        print_r($params, true) .
                        '</pre><br><br>';

                        return $message;
    }


    protected function error($sender, $message){

        $emailSender = new EmailSender();
        $emailSender->adminErrorMessage($sender, $message);

        trigger_error("OcPDO ERROR: ", E_USER_ERROR);

        exit;
    }

    protected static function debugOut($text, $onlyHtmlString = false)
    {
        if(!$this->debug){
            return;
        }

        trigger_error("OcPDO ERROR: ", E_USER_ERROR); //TODO

        // TODO: make it configurable
        // useful when debugging stripts generating content other than HTML
        //if ($onlyHtmlString !== true){
        //  error_log($text);
        //}
        print $text;
    }

    /**
     * Returns instance of itself
     *
     * @return OcPdo object
     */
    public static function instance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone() {}

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup() {}


    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

}
