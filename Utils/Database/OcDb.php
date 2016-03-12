<?php

class OcDb extends OcPdo
{

    protected $stmt; //internal PDOStatement TODO


    /**
     * @return one row from result, or FALSE if there are no more rows available
     * The data is returned as an array indexed by column name, as returned in your
     * SQL SELECT
     */
    public function dbResultFetch()
    {
        return $this->stmt->fetch();
    }

    /**
     * for queries witch LIMIT 1 return only one row and reset database class preparing it for next job.
     */
    public function dbResultFetchOneRowOnly()
    {
        $result = $this->stmt->fetch();
        $this->reset();
        return $result;
    }

    /**
     * @return all rows from result as complex array.
     * The returned array contains all of the remaining rows (if you have previously called
     * dbResultFetch(), or all returned rows if not) in the result set. The array represents
     * each row as an array indexed by column name, as returned in your SQL SELECT.
     * An empty array is returned if there are zero results to fetch, or FALSE on failure.
     */
    public function dbResultFetchAll()
    {
        $result = $this->stmt->fetchAll();
        $this->closeCursor();
        return $result;
    }

    /**
     * TODO
     * @param unknown $default
     */
    protected function dbResultFetchValue($default){
        $row = $this->dbResultFetch();
        $this->closeCursor();
        if ($row) {
            $value = reset($row);
            if ($value == null)
                return $default;
                else
                    return $value;
        } else {
            return $default;
        }
    }


    /**
     * @return number of row in results (i.e. number of rows returned by SQL SELECT)
     * or the number of rows affected by the last DELETE, INSERT, or UPDATE statement
     */
    public function rowCount()
    {
        return $this->stmt>rowCount();
    }










    /**
     * simple querry
     * Use only with static queries, Queries should contain no variables.
     * For queries with variables use paramQery method
     *
     * @param string $query
     * @return true, if the query succeeded; false, if there was SQL error
     */
    public function simpleQuery($query)
    {
        try {
            $this->stmt = $this->prepare($query);
            $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
            $this->stmt->execute();

        } catch (PDOException $e) {
            //TODO
            $message = $this->errorMessage(__line__, $e, $query, array());
            $this->error($message);

            return false;
        }

        if ($this->debug) { //TODO
            self::debugOut('db.php, # ' . __line__ . ', mysql query on input: ' . $query . '<br />');
        }

        return true;
    }

    /**
     * Executes given query. If the query return no rows, or null value, default value is returned.
     * Otherwise, value of first column in a first row is returned.
     *
     * @param $query Query to be executed
     * @param $default Default value
     *
     * @return
     */
    public function simpleQueryValue($query, $default)
    {
        $this->simpleQuery($query);
        return $this->dbResultFetchValue($default);
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
     * @return true, if the query succeeded; false, if there was SQL error
     */
    public function paramQuery($query, array $params)
    {
        try {
            $this->stmt = $this->prepare($query);

            foreach ($params as $key => $val) {
                switch ($val['data_type']) {
                    case 'integer':
                    case 'int':
                    case 'i':
                        $this->stmt->bindParam($key, $val['value'], PDO::PARAM_INT);
                        break;
                    case 'boolean':
                        $this->stmt->bindParam($key, $val['value'], PDO::PARAM_BOOL);
                        break;
                    case 'string':
                    case 'str':
                    case 's':
                        $this->stmt->bindParam($key, $val['value'], PDO::PARAM_STR);
                        break;
                    case 'null':
                        $this->stmt->bindParam($key, $val['value'], PDO::PARAM_NULL);
                        break;
                    case 'large':
                        $this->stmt->bindParam($key, $val['value'], PDO::PARAM_LOB);
                        break;
                    case 'recordset':
                        $this->stmt->bindParam($key, $val['value'], PDO::PARAM_STMT);
                        break;
                    default:
                        return false;
                }
            }

            $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
            $this->stmt->execute();

        } catch (PDOException $e) {
            $message = $this->errorMessage(__line__, $e, $query, $params);
            $this->error($message);
            return false;
        }
        if ($this->debug) {
            self::debugOut('db.php, # ' . __line__ . ', Query on input: ' . $query . '<br />');
            self::debugOC('db.php, # ' . __line__ . ', input parametres for query', $params);
       }

        return true;
    }



    /**
     * Executes given query, as described in method paramQuery().
     * If the query return no rows, or null value, default value is returned.
     * Otherwise, value of first column in a first row is returned.
     *
     * @param $query Query to be executed
     * @param $default Default value
     * @param $params Query params
     *
     * @return
     */
    public function paramQueryValue($query, $default, $params)
    {
        if (!is_array($params)) {
            return false;
        }
        $this->paramQuery($query, $params);
        return $this->dbResultFetchValue($default);
    }







    /**
     * @param $query - string, with params representation instead variables.
     * @param $param1, param2 .. paramN - variables.
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
     * @return true, if the query succeeded; false, if there was SQL error
     */
    public function multiVariableQuery($query)
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();

        if ($numargs === 2 && is_array($arg_list[1])) { // params were passed in array
            $arg_list = $arg_list[1];
            $numargs = count($arg_list) + 1;
        }

        try {
            $this->stmt = $this->prepare($query);
            for ($i = 1; $i < $numargs; $i++) {
                // if ($this->debug) echo 'db.php, # ' . __line__ .". Argument $i is: " . $arg_list[$i] . "<br />\n";
                $this->stmt->bindParam(self::bindChar . $i, $arg_list[$i]);
            }
            $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
            $this->stmt->execute();
            $this->lastInsertId = $this->dbh->lastInsertId();
        } catch (PDOException $e) {
            $message = $this->errorMessage(__line__, $e, $query, $arg_list);
            $this->error($message);
            return false;
        }

        if ($this->debug) {
            self::debugOut('db.php, # ' . __line__ . ', Query on input: ' . $query . '<br />');
            for ($i = 1; $i < $numargs; $i++)
                self::debugOut("Param :" . $i . " = " . $arg_list[$i] . "<br>");
        }
        return true;
    }


    /**
     * Executes given query, as described in method multiVariableQuery().
     * If the query return no rows, or null value, default value is returned.
     * Otherwise, value of first column in a first row is returned.
     *
     * @param $query Query to be executed, default value, query params
     *
     * @return
     */
    public function multiVariableQueryValue($query)
    {
        $arg_list = func_get_args();
        $default = null;
        if (count($arg_list) >= 2) {
            $default = $arg_list[1];
            unset($arg_list[1]);
        }
        // could be this done better?
        call_user_func_array(array($this, 'multiVariableQuery'), $arg_list);
        return $this->dbResultFetchValue($default);
    }






    /**
     * Closes current cursor. Some methods, which drain cursor (like dbResultFetchAll())
     * or expect only one row (like *QueryValue()) close cursor implicitly.
     */
    public function closeCursor()
    {
        try {
            if(is_object($this->stmt)){ /* hot fix TODO consider why cause fatals */
                $this->stmt->closeCursor();
            }
        } catch (Exception $e) {
            // ignore
        }
        $this->stmt = null;

    }

    /**
     * reset data from prevous results and make class ready for next query
     */
    public function reset()
    {
        $this->closeCursor();
    }

}