<?php

/**
 * This class SHOULDN'T BE USED in any new implementations.
 * This is wrapper on OcDb class created for fast replacement mysql_* functions
 * without bigger integrations in the code.
 */

namespace Utils\Database;
use PDOStatement;

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
     * This is replacement for mysql_fetch_row()
     *
     * @param PDOStatement $stmt
     * @return array row in style PDO::FETCH_NUM
     */
    public static function xFetchRow(PDOStatement $stmt)
    {
        return $stmt->fetch(self::FETCH_NUM); //PDO::FETCH_NUM
    }

    /**
     * This is replacement for mysql_result()
     *
     * @param PDOStatement $stmt
     * @param int $colNum - index of the column in result
     * @return string value from the column at index $colNum
     */
    public static function xResult(PDOStatement $stmt, $colNum)
    {
        return $stmt->fetchColumn($colNum);
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

            $argList = func_get_args();
            array_shift($argList); //remove first param. = sql query

            $stmt->execute($argList);
        } catch (PDOException $e) {

            $db->error('Query: '.$query, $e);
            return false;
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
     * This is a static way of access OcDb::multiVariableQuery()
     * @param unknown $query
     * @param ... list of the arguments to query
     */
    public static function xMultiVariableQuery($query)
    {

        $argList = func_get_args(); //get list of params

        // check if params are passed as array
        if (2 === func_num_args() && is_array($argList[1])) {
            $argList = $argList[1];
        }else{
            //remove query from arg. lists (the rest are params)
            unset($argList[0]);
        }

        $db = self::instance();
        return $db->multiVariableQuery($query, $argList);
    }
}
