<?php

/**
 * This class SHOULDN'T BE USED in any new implementations.
 * This is wrapper on OcDb class created for fast replacement mysql_* functions
 * without bigger integrations in the code.
 */

namespace Utils\Database;
use PDOStatement;
use PDOException;

class XDb extends OcDb {

    /**
     * This is replacement for mysql_query()
     * Oryginal mysql_query has params: sql-string, optional $link, optional params to sql-query
     * Oc code doesn't contains calls with params to sql query, so this is not implemented.
     * Link as argument is just ignored.
     *
     * @param string $sql
     * @return PDOStatement or false on error
     */
    public static function xQuery($sql)
    {
        $db = self::instance();
        return $db->query($sql);
    }

    /**
     * This is replacement for mysql_num_rows()
     * This function doesn't returns FALSE as mysql_num_rows() //TODO ?
     *
     *
     * @param PDOStatement $stmt
     * @return
     */
    public static function xNumRows(PDOStatement $stmt)
    {

        /*
         * WARNING: This "can" NOT WORK for SELECT... queries! Details:
         * http://php.net/manual/en/pdostatement.rowcount.php
         */
        return $stmt->rowCount();
    }

    /**
     * This is replacement for mysql_real_escape_string()
     *
     * ATTENTION: returned value is in ' ' (quotation marks) - SQL string needs to be refactored
     *
     * @param string $string
     * @return quoted string
     */
    public static function xQuote($string)
    {
        $db = self::instance();
        return $db->quote($string);
    }

    /**
     * This is replacement for sql_escape function from clicompatbase
     *
     * @param string $string
     */
    public static function xEscape($string)
    {
        $value = self::xQuote($string);
        $value = substr($value, 1, -1); //remove ' char from the begining and end of the string
        $value = mb_ereg_replace('&', '\&', $value);
        return $value;
    }

    /**
     * This is replacement for mysql_free_result()
     */
    public static function xFreeResults(PDOStatement $stmt)
    {
        return $stmt->closeCursor();
    }

    /**
     * This is replacement for mysql_fetch_array()
     * Second optional arg. of mysql_fetch_array is never used in OC code
     * so return value is in the same format as original
     *
     * @param PDOStatement $stmt
     * @return mixed row in style  MYSQL_BOTH/PDO::FETCH_BOTH
     */
    public static function xFetchArray(PDOStatement $stmt)
    {
        return $stmt->fetch(self::FETCH_BOTH); //PDO::FETCH_BOTH
    }

    /**
     * This is replacement for sql(...) function call from /lib/clicompatbase.php
     *
     * IMPORTANT: additional params needs to be converted from &<1-9> to ? in na proper way!
     * IMPORTANT: additional params can't be used as table/column name!
     *
     * @param unknown $sql
     * @param ... there can be optional list of params to bind with query
     */
    public static function xSql($query){
        $db = self::instance();
        try {
            $stmt = $db->prepare($query);

            //echo "Q: $query </br>"; //TMP_DEBUG!

            $argList = func_get_args();
            array_shift($argList); //remove first param. = sql query

            //echo "B: "; print_r($argList); echo "<br/>";  //TMP_DEBUG!

            $stmt->execute($argList);
        } catch (PDOException $e) {

            $db->error('Query: '.$query, $e);
            return null;
        }

        if ($db->debug) {
            self::debugOut(__METHOD__.":\n\nQuery: ".$query);
        }

        return $stmt;
    }

    /**
     * This is static way of access OcDb::SimpleQueryValue()
     *
     * @param unknown $query - sql query
     * @param unknown $default - default value to return
     */
    public static function xSimpleQueryValue($query, $default)
    {
        $db = self::instance();
        return $db->simpleQueryValue($query, $default);
    }

    /**
     * This is static way of access OcDb::SimpleQueryValue()
     *
     * @param unknown $query - sql query
     * @param unknown $default - default value to return
     */
    public static function xMultiVariableQueryValue($query, $default)
    {
        $params = array_slice(func_get_args(), 2); //skip query + default

        $db = self::instance();
        return $db->multiVariableQueryValue($query, $default, $params);
    }

    /**
     * Returns last inserted Id
     * Remember, if you use a transaction you should use lastInsertId
     * BEFORE you commit otherwise it will return 0
     */
    public static function xLastInsertId(){
        $db = self::instance();
        return $db->lastInsertId();
    }

    /**
     * This function checks if given table contains column of given name
     * @param unknown $tableName
     * @param unknown $columnName
     * @return true on success
     */
    public static function xContainsColumn($tableName, $columnName)
    {
        $tableName = self::xEscape($tableName);
        $columnName = self::xEscape($columnName);

        $stmt = XDb::xSql("SHOW COLUMNS FROM $tableName WHERE Field = '$columnName'");
        while( $column = XDb::xFetchArray($stmt)){
            return true; //any result
        }
        return false;
    }
}
