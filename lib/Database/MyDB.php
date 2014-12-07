<?php

class MyPDO extends PDO {
    // Extend and add debugging code here.
}

/**
 * This class was introdcued in response to issue #180 which claims the
 * existing `dataBase` class to be suboptimal.
 *
 * It simply instatiates a single PDO subclass with a single MySQL database
 * connection and allows developers to retrieve it with a public method.
 */
class MyDB {

    private static $dbh = null;

    /**
     * Get the database connection - a PDO class instance.
     *
     * This method will always return *the same* PDO instance (which will be
     * instantiated upon the first call). A single script should be able to run
     * with a single database connection.
     *
     * The PDO instance will be created with a set of predefined options,
     * among which these two might be the most important to you:
     *
     * PDO::ATTR_ERRMODE is PDO::ERRMODE_EXCEPTION
     * PDO::ATTR_DEFAULT_FETCH_MODE is PDO::FETCH_ASSOC
     *
     * @return MyPDO
     */
    public static function getPDO() {
        if (self::$dbh === null) {
            self::connect();
        }
        return self::$dbh;
    }

    private static function connect() {
        require(__DIR__ . '/../settings.inc.php');

        $dsnarr = array(
            'host' => $opt['db']['server'],
            'dbname' => $opt['db']['name'],
            'charset' => 'utf8'
        );

        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        );

        /* Older PHP versions do not support the 'charset' DSN option. */

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
        self::$dbh = new MyPDO(
                $dsn, $opt['db']['username'], $opt['db']['password'], $options
        );
    }

}
