<?php

namespace src\Utils\Database;

use PDOException;
use PDOStatement;
use src\Utils\Debug\Debug;

class OcDb extends OcPdo
{
    const BIND_CHAR = ':';

    protected $hasActiveTransaction = false;  // this var is used to detect nested transactions

    /**
     * This the ONLY way on which instance of this class
     * should be accessed
     *
     * Returns instance of itself.
     *
     * @param $access - database access level of the returned instance
     * @return OcDb object
     */
    public static function instance($access = self::NORMAL_ACCESS)
    {
        $instance = parent::instance($access);

        return $instance;
    }

    /**
     * Overloading of beginTransaction method to detect nested transations which can't work!
     * Only one transaction can be active
     *
     * {@inheritDoc}
     * @see OcPdo::beginTransaction()
     */
    function beginTransaction() {
        if ($this->hasActiveTransaction) {
            Debug::errorLog("DB transation already started - check the code!");
            return false;
        } else {
            $this->hasActiveTransaction = parent::beginTransaction ();
            return $this->hasActiveTransaction;
        }
    }

    /**
     * Overloading pdo-commit - see beginTransaction for details
     *
     * {@inheritDoc}
     * @see OcPdo::commit()
     */
    function commit () {
        parent::commit ();
        $this->hasActiveTransaction = false;
    }

    /**
     * Overloading pdo-rollback - see beginTransaction for details
     *
     * {@inheritDoc}
     * @see OcPdo::commit()
     */
    function rollback () {
        parent::rollback ();
        $this->hasActiveTransaction = false;
    }

    /**
     * @param PDOStatement|null $stmt
     * @param integer|null $fetchStyle
     *
     * @return array - one row from result, or FALSE if there are no more rows available
     * The data is returned as an array indexed by column name, as returned in your
     * SQL SELECT
     */
    public function dbResultFetch(PDOStatement $stmt = null, $fetchStyle = null)
    {
        if (!is_null($stmt)) {
            if (is_null($fetchStyle)) {
                return $stmt->fetch();
            } else {
                return $stmt->fetch($fetchStyle);
            }
        }

        $this->error('Call PDOstatement issue!');
    }

    /**
     * for queries witch LIMIT 1 return only one row
     * and reset database class preparing it for next job.
     * @param PDOStatement|null $stmt
     * @return mixed
     */
    public function dbResultFetchOneRowOnly(PDOStatement $stmt = null)
    {
        if (!is_null($stmt)) {
            $result = $stmt->fetch();
            $stmt->closeCursor();

            return $result;
        }
        $this->error('Call PDOstatement issue!');
    }

    /**
     * The returned array of $key-$vals pair returned by extractor based on DB rows.
     *
     * @param PDOStatement|null $stmt
     * @param callable|null $extractor - callable with argument $row (DB-ROW-ASSOC) which returns array [$key, $val]
     *
     * @return array dictionary with key => val returned by extractor
     *               dictionary first column value => second column value, if extractor === null and 2 columns
     *               dictionary first column value => row, if extractor === null and != 2 columns
     */
    public function dbResultFetchAllAsDict(PDOStatement $stmt = null, callable $extractor = null)
    {
        $result = [];
        while ($row = $this->dbResultFetch($stmt, OcDb::FETCH_ASSOC)) {
            if ($extractor !== null) {
                list($key, $val) = $extractor($row);
                $result[$key] = $val;
            } else {
                $key = reset($row);
                if (count($row) == 2) {
                    $result[$key] = next($row);
                } else {
                    $result[$key] = $row;
                }
            }
        }

        return $result;
    }

    /**
     * The returned array contains all of the remaining rows
     * (if you have previously called dbResultFetch(), or all returned rows if not)
     * in the result set. The array represents each row as an array indexed by column name,
     * as returned in your SQL SELECT. An empty array is returned
     * if there are zero results to fetch, or FALSE on failure.
     *
     * @param PDOStatement|null $stmt
     * @param $fetchStyle
     * @return array - all rows from result as complex array.
     */
    public function dbResultFetchAll(
        PDOStatement $stmt = null,
        $fetchStyle = null
    ) {
        if (!is_null($stmt)) {
            if (is_null($fetchStyle)) {
                $result = $stmt->fetchAll();
            } else {
                $result = $stmt->fetchAll($fetchStyle);
            }
            $stmt->closeCursor();

            return $result;
        }

        $this->error('Call PDOstatement issue!');
    }

    /**
     * This method returns array of objects which are returned by $rowToObjectCallback function.
     *
     * @param PDOStatement $stmt
     * @param callable $rowToObjectCallback
     * @return array
     */
    public function dbFetchAllAsObjects(PDOStatement $stmt, callable $rowToObjectCallback)
    {
        $result = [];
        while ($row = $this->dbResultFetch($stmt, OcDb::FETCH_ASSOC)) {
            $result[] = $rowToObjectCallback($row);
        }

        return $result;
    }

    /**
     * Returns assoc. array generated by setting $keyCol value as array key
     * and $valCol as its value.
     *
     * @param PDOStatement $stmt
     * @param string $keyCol - column name to use for result key
     * @param string $valCol - column name to use for result value
     * @param bool $ignoreKeyCase - false => $keyCol case must match the case of table column
     * @return array
     */
    public function dbFetchAsKeyValArray(PDOStatement $stmt, $keyCol, $valCol, $caseSensitiveKey = true)
    {
        $result = [];
        if (!is_null($stmt)) {
            if (!$caseSensitiveKey) {
                $keyCol = strtolower($keyCol);
            }
            while ($row = $this->dbResultFetch($stmt, OcDb::FETCH_ASSOC)) {
                if (!$caseSensitiveKey) {
                    $row = array_change_key_case($row, CASE_LOWER);
                }
                $result[$row[$keyCol]] = $row[$valCol];
            }

            return $result;
        }

        $this->error('Call PDOstatement issue!');
    }

    /**
     * Returns array with values of $keyCol column.
     *
     * @param PDOStatement $stmt
     * @param string $keyCol - column name to use for result key
     * @return array
     */
    public function dbFetchOneColumnArray(PDOStatement $stmt, $keyCol, $caseSensitiveKey = true)
    {
        $result = [];
        if (!is_null($stmt)) {
            if (!$caseSensitiveKey) {
                $keyCol = strtolower($keyCol);
            }
            while ($row = $this->dbResultFetch($stmt, OcDb::FETCH_ASSOC)) {
                if (!$caseSensitiveKey) {
                    $row = array_change_key_case($row, CASE_LOWER);
                }
                $result[] = $row[$keyCol];
            }

            return $result;
        }

        $this->error('Call PDOstatement issue!');
    }


    /**
     * This method returns the value from first column of first row in statement
     *
     * @param PDOStatement $stmt -
     * @param mixed $default - default value to return if there is no results
     * @return mixed
     */
    protected function dbResultFetchValue(PDOStatement $stmt, $default)
    {

        $row = $this->dbResultFetch($stmt);
        $stmt->closeCursor();

        if ($row) {
            $value = reset($row);
            if (is_null($value)) {
                return $default;
            } else {
                return $value;
            }
        } else {
            return $default;
        }
    }


    /**
     * @param PDOStatement|null $stmt
     *
     * @return integer number of row in results (i.e. number of rows returned by SQL SELECT)
     * or the number of rows affected by the last DELETE, INSERT, or UPDATE statement
     */
    public function rowCount(PDOStatement $stmt = null)
    {
        if (!is_null($stmt)) {
            return $stmt->rowCount();
        }

        $this->error('Call PDOstatement issue!');
    }

    /**
     * simple query
     * Use only with static queries, Queries should contain no variables.
     * For queries with variables use paramQuery method
     *
     * @param string $query
     * @return PDOStatement|null obj, if the query succeeded; null otherwise
     */
    public function simpleQuery($query)
    {
        try {
            $stmt = $this->prepare($query);
            $stmt->setFetchMode(self::FETCH_ASSOC);
            $stmt->execute();

        } catch (PDOException $e) {

            $this->error('Query: '.$query, $e);
        }

        if ($this->debug) {
            self::debugOut(__METHOD__.":\n\nQuery: ".$query);
        }

        return $stmt;
    }

    /**
     * Execute multiple queries from one string, without parameters.
     * Recognizes the DELIMITER statement, which may be supplied ONCE
     * at the very beginning of the string.
     *
     * This very simple function will fail if the delimiter (default ';')
     * is included in a comment or string constant.
     *
     * @param $queries
     */
    public function simpleQueries($queries)
    {
        if (preg_match('/^DELIMITER\s+([^\s]+)\s/i', $queries, $matches)) {
            $delimiter = $matches[1];
        } else {
            $delimiter = ';';
        }
        foreach (explode($delimiter, $queries) as $query) {
            $query = trim($query);
            if (!empty($query) && strcasecmp(substr($query, 0, 9), 'DELIMITER') != 0) {
                $this->simpleQuery($query);
            }
        }
    }

    /**
     * Executes given query. If the query return no rows, or null value, default value is returned.
     * Otherwise, value of first column in a first row is returned.
     *
     * @param string $query Query to be executed
     * @param mixed $default Default value
     *
     * @return mixed
     */
    public function simpleQueryValue($query, $default)
    {
        $stmt = $this->simpleQuery($query);

        return $this->dbResultFetchValue($stmt, $default);
    }

    /**
     * @param $query - string, with params representation instead variables.
     * @param $params - array with variables.
     *
     * [keyname][value]
     * [keyname][data_type]
     *
     * example:
     * ----------------------------------------------------------------------------------
     * $query: 'SELECT * FROM tabele WHERE field1 = :variable1 AND field2 = :variable2'
     * $params['variable1']['value'] = 1;
     * $params['variable1']['data_type'] = 'integer';
     * $params['variable2']['value'] = 'cat is very lovelly animal';
     * $params['variable2']['data_type'] = 'string';
     * ----------------------------------------------------------------------------------
     * data type can be:
     *
     * - 'boolean'                  Represents a boolean data type.
     * - 'null'                     Represents the SQL NULL data type.
     * - 'integer' or 'int' or 'i'  Represents the SQL INTEGER data type.
     * - 'string' or 'str' or 's'   Represents the SQL CHAR, VARCHAR, or other string data type.
     * - 'large'                    Represents the SQL large object data type.
     * - 'recordset'                Represents a recordset type. Not currently supported by any drivers.
     *
     * @return PDOStatement obj, if the query succeeded; null otherwise
     */
    public function paramQuery(/*PHP7: string*/
        $query,
        array $params
    ) {
        try {
            $stmt = $this->prepare($query);

            foreach ($params as $key => $val) {
                switch ($val['data_type']) {
                    case 'integer':
                    case 'int':
                    case 'i':
                        $stmt->bindParam($key, $val['value'], \PDO::PARAM_INT);
                        break;
                    case 'boolean':
                        $stmt->bindParam($key, $val['value'], self::PARAM_BOOL);
                        break;
                    case 'string':
                    case 'str':
                    case 's':
                        $stmt->bindParam($key, $val['value'], self::PARAM_STR);
                        break;
                    case 'null':
                        $stmt->bindParam($key, $val['value'], self::PARAM_NULL);
                        break;
                    case 'large':
                        $stmt->bindParam($key, $val['value'], self::PARAM_LOB);
                        break;
                    case 'recordset':
                        $stmt->bindParam($key, $val['value'], self::PARAM_STMT);
                        break;
                    default:
                        return null;
                }
            }

            $stmt->setFetchMode(self::FETCH_ASSOC);
            $stmt->execute();

        } catch (PDOException $e) {

            $this->error("Query:\n$query\n\nParams:\n".implode(' | ', $params), $e);
        }
        if ($this->debug) {
            self::debugOut(__METHOD__.":\n\nQuery:\n$query\n\nParams:\n".implode(' | ', $params));
        }

        return $stmt;
    }

    /**
     * Executes given query, as described in method paramQuery().
     * If the query return no rows, or null value, default value is returned.
     * Otherwise, value from first column of the first row is returned.
     *
     * @param string $query Query to be executed
     * @param mixed $default Default value
     * @param array $params Query params
     *
     * @return mixed
     */
    public function paramQueryValue($query, $default, array $params)
    {
        $stmt = $this->paramQuery($query, $params);

        return $this->dbResultFetchValue($stmt, $default);
    }

    /**
     * @param string $query - string, with params representation instead variables.
     * $param1 , param2 .. paramN - variables.
     *
     *
     * example:
     * ----------------------------------------------------------------------------------
     * $param1 = 1;
     * $param2 = 'cat is very lovelly animal';
     * // note that variable in query MUST be in format :1, :2, :3 (and so on).
     * $query = 'SELECT something FROM tabele WHERE field1=:1 AND field2=:2';
     *
     * multiVariableQuery($query, $param1, $param2 )
     * ----------------------------------------------------------------------------------
     *
     * @return PDOStatement|null obj, if the query succeeded; null otherwise
     */
    public function multiVariableQuery($query)
    {
        $argList = func_get_args(); //get list of params

        // check if params are passed as array
        if (2 === func_num_args() && is_array($argList[1])) {
            $argList = $argList[1];
        } else {
            unset($argList[0]); //remove query from arg. lists (rest are params)
        }

        try {
            $stmt = $this->prepare($query);

            $i = 1;
            foreach ($argList as $param) {
                $stmt->bindValue(self::BIND_CHAR.$i++, $param);
            }
            $stmt->setFetchMode(self::FETCH_ASSOC);
            $stmt->execute();
        } catch (PDOException $e) {
            $message = 'Query|Params: '.$query.' | '.implode(' | ', $argList);
            $this->error($message, $e);
        }

        if ($this->debug) {
            self::debugOut(__METHOD__.":\n\nQuery|Params: $query | ".implode(' | ', $argList));
        }

        return $stmt;
    }


    /**
     * Executes given query, as described in method multiVariableQuery().
     * If the query return no rows, or null value, default value is returned.
     * Otherwise, value of first column in a first row is returned.
     *
     * @param string $query Query to be executed
     * @param mixed $default Default value
     * $param1, $param2,.... query params
     *
     * @return mixed
     */
    public function multiVariableQueryValue($query, $default)
    {
        $argList = func_get_args();
        $numArgs = func_num_args();

        if ($numArgs <= 2) {

            //only query + default value=> use simpleQuery
            $this->error('Improper use of '.__METHOD__.': Too few arguments. Use simpleQueryValue() instead.');

        } else {
            // check if params are passed as array
            if ($numArgs == 3 && is_array($argList[2])) {
                $argList = $argList[2];
            } else {
                $argList = array_slice($argList, 2);
            }
        }

        //more params - remove first two from argList and call...
        $stmt = $this->multiVariableQuery($query, $argList);

        return $this->dbResultFetchValue($stmt, $default);
    }

    /**
     * This method enables SQL STRICT-MODE (STRICT_ALL_TABLES)
     * in current db session (instance)
     */
    public function enableStrictMode()
    {
        $sqlMode = $this->getSqlMode();
        $modes = explode(',', $sqlMode);

        if (($key = array_search('STRICT_ALL_TABLES', $modes)) !== false) {
            $trace = Debug::getTraceStr();
            Debug::errorLog("Sql Strict-mode already enabled! ($trace)");

            return;
        }

        $modes[] = 'STRICT_ALL_TABLES';
        $sqlMode = implode(',', $modes);

        $this->simpleQuery("SET sql_mode = '$sqlMode'");
    }

    /**
     * This method disables SQL STRICT-MODE (STRICT_ALL_TABLES)
     * in current db session (instance)
     */
    public function disableStrictMode()
    {
        $sqlMode = $this->getSqlMode();
        $modes = explode(',', $sqlMode);

        if (($key = array_search('STRICT_ALL_TABLES', $modes)) !== false) {
            unset($modes[$key]);
        } else {
            $trace = Debug::getTraceStr();
            Debug::errorLog("Sql Strict-mode already disabled! ($trace)");

            return;
        }

        $sqlMode = implode(',', $modes);
        $this->simpleQuery("SET sql_mode = '$sqlMode'");
    }

    /**
     * This method returns sql-mode from current DB session
     *
     * @return string
     */
    public function getSqlMode()
    {
        return $this->dbResultFetchOneRowOnly(
            $this->simpleQuery("SELECT @@sql_mode AS sql_mode"))['sql_mode'];
    }

    /**
     * Converts any limit/offset variables into integers which can be safely
     * inserted into a LIMIT clause, directly or as PDO param.
     *
     * @param int|float|string $limit
     * @param int|float|string $offset
     * @return array - [string $limit, string $offset]
     */
    public static function quoteLimitOffset($limit, $offset)
    {
        return [
            self::quoteLimit($limit),
            self::quoteOffset($offset)
        ];
    }

    /**
     * Converts any limit variable into integers which can be safely inserted
     * into a LIMIT clause, directly or as PDO param.
     *
     * @param int|float|string $limit
     * @return string $limit
     */
    public static function quoteLimit($limit)
    {
        if(is_null($limit)){
            // nulled limit means that there is no limit
            $limit = 'max';
        }
        return self::quoteLimitNumber($limit);
    }

    public static function quoteOffset($offset)
    {
        if (is_null($offset)) {
            // nulled offset means that there is no offset
            $offset = 0;
        }
        return self::quoteLimitNumber($offset);
    }

    /**
     * Do the validation and conversion
     */
    private static function quoteLimitNumber($number)
    {
        if ($number === 'max') {
            // We don't expect to ever reach > 1 billion rows in a table.
            // Note that is somewhat less than PHP_INT_MAX/2.
            return 1000000000;
        }
        if ($number <= 0) {
            // This includes all non-numeric values. Previous implementation
            // returned 1000000000 for non_numeric limit, but that prevents
            // detection of some programming errors.
            return 0;
        }

        // get rid of whitespace, non-integer components and other rubbish
        return (int) $number;
    }

    /**
     * Quote string before use in DB query
     * (needs for IN strings etc.)
     *
     * @param string $str
     * @return string
     */
    public function quoteString($str)
    {
        $value = $this->quote($str, self::PARAM_STR);
        $value = substr($value, 1, -1); //remove ' char from the begining and end of the string
        $value = mb_ereg_replace('&', '\&', $value); //escape '&' char

        return $value;
    }

    // Methods for querying database structure

    // We will avoid to use the information_schema if possible, because there
    // have been backward-incompatible information_schema changes in the past.

    // We will validate all passed entity names, so that calling functions
    // can rely that validation is done here.

    public function tableExists($table)
    {
        self::validateEntityName($this->dbName);

        return $this->multiVariableQueryValue(
            "SHOW TABLES FROM `".$this->dbName."` LIKE :1",
            null,
            $table
        ) !== null;
    }

    public function columnExists($table, $column)
    {
        self::validateEntityName($table);
        self::validateEntityName($column);

        return $this->multiVariableQueryValue(
            "SHOW COLUMNS FROM `".$table."` LIKE :1",
            null,
            $column
        ) !== null;
    }

    /**
     * Get column type including NULL and DEFAULT attributes, e.g.
     * "varchar(36) DEFAULT NULL" or "int(11) zerofill NOT NULL".
     */
    public function getFullColumnType($table, $column)
    {
        self::validateEntityName($table);
        self::validateEntityName($column);

        $row = $this->dbResultFetchOneRowOnly($this->multiVariableQuery(
            "SHOW COLUMNS FROM `".$table."` LIKE :1",
            $column
        ));
        if (!$row) {
            $this->error("Column not found: '".$table.".".$column."'");
        }
        $row = array_change_key_case($row, CASE_LOWER);

        $type = strtolower($row['type']);
        $isNullable = (strtoupper($row['null']) == 'YES');
        if (!$isNullable) {
            $type .= " NOT NULL";
        }
        if ($row['default'] !== null) {
            // Some MySQL/MariaDB versions quote numeric default values, other don't.
            // We will always quote them for consistency (and because it's simpler).

            $type .= " DEFAULT '" . $row['default'] . "'";

        } elseif ($isNullable) {
            $type .= " DEFAULT NULL";
        }

        return $type;
    }

    public function getColumnComment($table, $column)
    {
        self::validateEntityName($table);
        self::validateEntityName($column);

        $row = $this->dbResultFetchOneRowOnly($this->multiVariableQuery(
            "SHOW FULL COLUMNS FROM `".$table."` LIKE :1",
            $column
        ));
        if (!$row) {
            return '';
        }
        $row = array_change_key_case($row, CASE_LOWER);

        return $row['comment'];
    }

    public function indexExists($table, $index)
    {
        self::validateEntityName($table);
        self::validateEntityName($index);

        return $this->multiVariableQueryValue(
            "SHOW INDEX FROM `".$table."` WHERE key_name = :1",
            null,
            $index
        ) !== null;
    }

    public function foreignKeyExists($table, $column, $refTable)
    {
        // For consistency, we require also for this method that the $table exists:
        if (!$this->tableExists($table)) {
            $this->error("Table not found: '".$table."'");
        }
        self::validateEntityName($column);
        self::validateEntityName($refTable);

        return $this->multiVariableQueryValue(
            "SELECT 1
             FROM information_schema.key_column_usage
             WHERE table_schema = :1 AND table_name = :2
                AND column_name = :3 AND referenced_table_name = :4",
            0,
            $this->dbName,
            $table,
            $column,
            $refTable
        ) == 1;
    }

    public function triggerExists($name)
    {
        return $this->funcExists($name, "SHOW TRIGGERS WHERE `trigger` = :1");
    }

    public function procedureExists($name)
    {
        return $this->funcExists($name, "SHOW PROCEDURE STATUS WHERE `name` = :1");
    }

    public function functionExists($name)
    {
        return $this->funcExists($name, "SHOW FUNCTION STATUS WHERE `name` = :1");
    }

    private function funcExists($name, $sql)
    {
        self::validateEntityName($name);
        return $this->multiVariableQueryValue($sql, null, $name) !== null;
    }

    // Failsafe methods for modifying database structure

    public function createTableIfNotExists($table, $fieldDefs, array $params = [])
    {
        self::validateEntityName($table);
        // $fields and $params are not validated.

          $p = '';
          foreach ($params as $key => $value) {
              $p .= " " . $key . "=" . $value;
          }
          $this->multiVariableQuery(
              "CREATE TABLE IF NOT EXISTS`".$table."` (" .
              implode(", ", $fieldDefs) . ")" . $p
          );
    }

    public function addColumnIfNotExists($table, $column, $type, $comment='', $after='')
    {
        // $type is not validated.

        if (!$this->columnExists($table, $column)) {
            if ($after) {
                self::validateEntityName($after);
                $after = "AFTER `".$after."`";
            }
            $this->multiVariableQuery(
                "ALTER TABLE `".$table."` ADD COLUMN `".$column."` ".$type." COMMENT :1 ".$after,
                $comment
            );
        }
    }

    public function updateColumnType($table, $column, $newType)
    {
        // $newType is not validated.

        if (strcasecmp($this->getFullColumnType($table, $column), $newType) !== 0) {

            // The above comparison will not always work, as different DB servers
            // use slightly differrent syntaxes. But it's just an optimization.

            $this->multiVariableQuery(
                "ALTER TABLE `".$table."` MODIFY COLUMN `".$column."` ".$newType." COMMENT :1",
                $this->getColumnComment($table, $column)
            );
        }
    }

    public function updateColumnComment($table, $column, $newComment)
    {
        if ($this->getColumnComment($table, $column) !== $newComment) {
            $type = $this->getFullColumnType($table, $column);
            $this->multiVariableQuery(
                "ALTER TABLE `".$table."` MODIFY COLUMN `".$column."` ".$type." COMMENT :1",
                $newComment
            );
        }
    }

    public function dropColumnIfExists($table, $column)
    {
        if ($this->columnExists($table, $column)) {
            $this->simpleQuery(
                "ALTER TABLE `".$table."` DROP COLUMN `".$column."`"
            );
        }
    }

    public function addPrimaryKeyIfNotExists($table, $column)
    {
        if (!$this->indexExists($table, 'PRIMARY')) {
            $this->simpleQuery(
                "ALTER TABLE `".$table."` ADD PRIMARY KEY (`".$column."`)"
            );
        }
    }

    public function addUniqueIndexIfNotExists($table, $index, array $columns = [])
    {
        $this->addIndexOfTypeIfNotExists($table, $index, 'UNIQUE', $columns);
    }

    public function addIndexIfNotExists($table, $index, array $columns = [])
    {
        $this->addIndexOfTypeIfNotExists($table, $index, 'INDEX', $columns);
    }

    public function addFulltextIfNotExists($table, $index, array $columns = [])
    {
        throw new \Exception(
            'FULLTEXT is not available at OC RO (MySQL 5.0)'
        );
        $this->addIndexOfTypeIfNotExists($table, $index, 'FULLTEXT', $columns);
    }

    private function addIndexOfTypeIfNotExists($table, $index, $type, array $columns = [])
    {
        // $type is not validated

        if (!$this->indexExists($table, $index)) {
            if ($columns) {
                self::validateEntityName($columns);
            } else {
                $columns = [$index];
            }
            $this->simpleQuery(
                "ALTER TABLE `".$table."` ADD ".$type." `".$index."` (`".implode("`,`", $columns)."`)"
            );
        }
    }

    public function dropIndexIfExists($table, $index)
    {
        if ($this->indexExists($table, $index)) {
            $this->simpleQuery(
                "ALTER TABLE `".$table."` DROP INDEX `".$index."`"
            );
        }
    }

    public function addForeignKeyIfNotExists(
        $table, $column, $refTable, $refColumn, $refOptions = ''
    ) {
        if (!$this->foreignKeyExists($table, $column, $refTable)) {

            self::validateEntityName($refColumn);
            self::validateSqlKeywords($refOptions);

            $this->simpleQuery(
                "ALTER TABLE `".$table."` ADD FOREIGN KEY (`".$column."`) " .
                "REFERENCES `".$refTable."` (`".$refColumn."`) ".$refOptions
            );
        }
    }

    public function dropForeignKeyIfExists($table, $column, $refTable)
    {
        if ($this->foreignKeyExists($table, $column, $refTable)) {

            $constraint = $this->multiVariableQueryValue(
                "SELECT constraint_name FROM information_schema.key_column_usage
                WHERE table_name = :1 AND column_name = :2 AND referenced_table_name = :3",
                '[internal error]',
                $table,
                $column,
                $refTable
            );
            $this->simpleQuery(
                "ALTER TABLE `".$table."` DROP FOREIGN KEY `".$constraint."`"
            );
        }
    }

    public function dropTableIfExists($table)
    {
        self::validateEntityName($table);
        $this->simpleQuery("DROP TABLE IF EXISTS `".$table."`");
    }

    public function createOrReplaceTrigger($trigger, $definition)
    {
        self::validateEntityName($trigger);
        // $definition is not validated

        $this->dropTriggerIfExists($trigger);
        $this->simpleQuery(
            "CREATE TRIGGER `".$trigger."` ".$definition
        );
    }

    public function createOrReplaceProcedure($proc, array $params, $body)
    {
        self::validateEntityName($proc);
        // $params and $body are not validated

        $this->dropProcedureIfExists($proc);
        $this->simpleQuery(
            "CREATE PROCEDURE `".$proc."` (" . implode(", ", $params) . ")\n" .$body
        );
    }

    public function createOrReplaceFunction($func, array $params, $returns, $type, $body)
    {
        self::validateEntityName($func);
        // $params and $body are not validated

        $type = strtoupper($type);
        if ($type != 'DETERMINISTIC' && $type != 'READS SQL DATA') {

            # Other types will not work if binary logging is enabled (e.g. at OC NL); see
            # https://stackoverflow.com/questions/26015160/deterministic-no-sql-or-reads-sql-data-in-its-declaration-and-binary-logging-i

            $this->error('invalid function type: ' . $type);
        }

        $this->dropFunctionIfExists($func);
        $this->simpleQuery(
            "CREATE FUNCTION `".$func."` (" . implode(", ", $params) . ")\n" .
            "RETURNS " . $returns . "\n" .
            $type . "\n" .
            $body
        );
    }

    public function dropTriggerIfExists($trigger)
    {
        self::validateEntityName($trigger);
        $this->simpleQuery("DROP TRIGGER IF EXISTS `".$trigger."`");
    }

    public function dropProcedureIfExists($proc)
    {
        self::validateEntityName($proc);
        $this->simpleQuery("DROP PROCEDURE IF EXISTS `".$proc."`");
    }

    public function dropFunctionIfExists($func)
    {
        self::validateEntityName($func);
        $this->simpleQuery("DROP FUNCTION IF EXISTS `".$func."`");
    }

    /**
     * Sanity check for table, column, index etc. names, which are to be inserted
     * into an SQL statement name
     */
    public function validateEntityName($name)
    {
        if (is_array($name)) {
            foreach ($name as $entity) {
                self::validateEntityName($entity);
            }
        } elseif (!preg_match('/^[A-Za-z_][A-Za-z_0-9]*$/', $name)) {
            $this->error("Invalid entity name: '".$name."'");
        }
    }

    /**
     * Sanity check for one or more SQL keywords, which are to be inserted
     * into an SQL statement.
     */
    public function validateSqlKeywords($keywords)
    {
        if (!preg_match('/^[A-Za-z ]*$/', $keywords)) {
            $this->error("Invalid SQL keyword(s): '".$keywords."'");
        }
    }

    public function getServerVersion()
    {
        return $this->simpleQueryValue('SELECT version()', '?');
    }
}
