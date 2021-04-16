<?php

namespace src\Utils\Database;

use src\Models\OcConfig\OcConfig;
use src\Utils\Email\EmailSender;
use PDO;
use PDOException;
use src\Utils\Email\OcSpamDomain;

class OcPdo extends PDO
{
    const NORMAL_ACCESS = 0;
    const ADMIN_ACCESS = 1;

    protected $debug; //bool, if set enabled debug messages
    protected $dbName;

    /**
     *
     * @param string $debug
     */
    public function __construct($adminAccess = false, $debug = false)
    {
        if ($debug === true) {
            $this->debug = true;
        }

        $this->dbName = OcConfig::getDbName();

        $dsnarr = array(
            'host' => OcConfig::getDbHost(),
            'dbname' => $this->dbName,
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
        try {
            parent::__construct(
                $dsn, OcConfig::getDbUser($adminAccess), OcConfig::getDbPass($adminAccess), $options
            );
        } catch (PDOException $e) {
            $this->error("OcPdo object creation failed!", $e);
        }
    }

    /**
     * Process error/exception occurence around DB operations
     *
     * @param string $message - description of the error
     * @param PDOException|null $e - exception object
     */
    protected function error(/*PHP7:string*/ $message, PDOException $e = null)
    {
        if ($e === null) {
            throw new PDOException($message);
        }
        if ($message != '') {
            throw new PDOException($message . "\n" . $e->getMessage());
        }
        throw $e;
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
     * This the ONLY way on which instance of this class should be accessed
     *
     * Returns instance of itself.
     *
     * @param $access - database access level of the returned instance
     * @return OcDb object
     */
    protected static function instance($access = self::NORMAL_ACCESS)
    {
        static $instance = [];
        if (!isset($instance[$access])) {
            $instance[$access] = new static($access == self::ADMIN_ACCESS, false);
        }
        return $instance[$access];
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
