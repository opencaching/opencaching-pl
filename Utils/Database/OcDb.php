<?php

namespace Utils\Database;
use PDOException;
use PDOStatement;

class OcDb extends OcPdo
{
    // -- THIS CODE WILL BE REMOVED SOON --
    protected $stmt; //internal PDOStatement
    // -- THIS CODE WILL BE REMOVED SOON --

    const BIND_CHAR = ':'; //

    /**
     * @return one row from result, or FALSE if there are no more rows available
     * The data is returned as an array indexed by column name, as returned in your
     * SQL SELECT
     */
    public function dbResultFetch( PDOStatement $stmt = null )
    {
        if(!is_null($stmt)){
            return $stmt->fetch();
        }

        // -- THIS CODE WILL BE REMOVED SOON --
            if(!is_object($this->stmt)){
                $this->error("PDO Fetch on non-object!", new PDOEXception("PDO Fetch on non-object!"));
            }
            return $this->stmt->fetch();
        // -- THIS CODE WILL BE REMOVED SOON --
    }

    /**
     * for queries witch LIMIT 1 return only one row
     * and reset database class preparing it for next job.
     */
    public function dbResultFetchOneRowOnly( PDOStatement $stmt = null )
    {
        if(!is_null($stmt)){
            $result = $stmt->fetch();
            $stmt->closeCursor();
            return $result;
        }

        // -- THIS CODE WILL BE REMOVED SOON --
            $result = $this->stmt->fetch();
            $this->reset();
            return $result;
        // -- THIS CODE WILL BE REMOVED SOON --
    }

    /**
     * The returned array contains all of the remaining rows
     * (if you have previously called dbResultFetch(), or all returned rows if not)
     * in the result set. The array represents each row as an array indexed by column name,
     * as returned in your SQL SELECT. An empty array is returned
     * if there are zero results to fetch, or FALSE on failure.
     *
     * @return all rows from result as complex array.
     */
    public function dbResultFetchAll( PDOStatement $stmt = null )
    {
        if(!is_null($stmt)){
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            return $result;
        }

        // -- THIS CODE WILL BE REMOVED SOON --
            $result = $this->stmt->fetchAll();
            $this->closeCursor();
            return $result;
        // -- THIS CODE WILL BE REMOVED SOON --
    }

    /**
     * This method returns the value from first column of first row in statement
     *
     * @param PDOStatement $stmt -
     * @param unknown $default - default value to return if there is no results
     */
    protected function dbResultFetchValue( PDOStatement $stmt, $default){

        $row = $this->dbResultFetch($stmt);
        $stmt->closeCursor();

        if ($row) {
            $value = reset($row);
            if ($value == null){
                return $default;
            } else {
                return $value;
            }
        } else {
            return $default;
        }
    }


    /**
     * @return number of row in results (i.e. number of rows returned by SQL SELECT)
     * or the number of rows affected by the last DELETE, INSERT, or UPDATE statement
     */
    public function rowCount( PDOStatement $stmt = null )
    {
        if(!is_null($stmt)){
            return $stmt->rowCount();
        }

        // -- THIS CODE WILL BE REMOVED SOON --
            return $this->stmt->rowCount();
        // -- THIS CODE WILL BE REMOVED SOON --
    }

    /**
     * simple querry
     * Use only with static queries, Queries should contain no variables.
     * For queries with variables use paramQery method
     *
     * @param string $query
     * @return PDOStatement obj, if the query succeeded; null otherwise
     */
    public function simpleQuery($query)
    {
        try {
            $stmt = $this->prepare($query);
            $stmt->setFetchMode(self::FETCH_ASSOC);
            $stmt->execute();

        } catch (PDOException $e) {

            $this->error('Query: '.$query, $e);
            return null;
        }

        if ($this->debug) {
            self::debugOut(__METHOD__.":\n\nQuery: ".$query);
        }

        // -- THIS CODE WILL BE REMOVED SOON --
            $this->stmt = $stmt;
        // -- THIS CODE WILL BE REMOVED SOON --

        return $stmt;
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
    public function paramQuery(/*PHP7: string*/ $query, array $params)
    {
        try {
            $stmt = $this->prepare($query);

            foreach ($params as $key => $val) {
                switch ($val['data_type']) {
                    case 'integer':
                    case 'int':
                    case 'i':
                        $stmt->bindParam($key, $val['value'], self::PARAM_INT);
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
            return null;
        }
        if ($this->debug) {
            self::debugOut(__METHOD__.":\n\nQuery:\n$query\n\nParams:\n".implode(' | ', $params));
        }

        // -- THIS CODE WILL BE REMOVED SOON --
            $this->stmt = $stmt;
        // -- THIS CODE WILL BE REMOVED SOON --

        return $stmt;
    }

    /**
     * Executes given query, as described in method paramQuery().
     * If the query return no rows, or null value, default value is returned.
     * Otherwise, value from first column of the first row is returned.
     *
     * @param $query Query to be executed
     * @param $default Default value
     * @param $params Query params
     *
     * @return
     */
    public function paramQueryValue($query, $default, array $params)
    {
        $stmt = $this->paramQuery($query, $params);
        return $this->dbResultFetchValue($stmt, $default);
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
     * @return PDOStatement obj, if the query succeeded; null otherwise
     */
    public function multiVariableQuery($query)
    {
        $argList = func_get_args(); //get list of params

        // check if params are passed as array
        if (2 === func_num_args() && is_array($argList[1])) {
            $argList = $argList[1];
        }else{
            unset($argList[0]); //remove query from arg. lists (rest are params)
        }

        try {
            $stmt = $this->prepare($query);

            $i = 1;
            foreach($argList as $param){
                //echo "Bind $i = $param <br/>"; //TMP_DEBUG!
                $stmt->bindValue(self::BIND_CHAR . $i++, $param);
            }
            $stmt->setFetchMode(self::FETCH_ASSOC);
            $stmt->execute();
        } catch (PDOException $e) {
            //d($e); //TMP_DEBUG!
            $message = 'Query|Params: '.$query.' | '.implode(' | ', $argList);
            $this->error($message, $e);
            return null;
        }

        if ($this->debug) {
            self::debugOut(__METHOD__.":\n\nQuery|Params: $query | ".implode(' | ', $argList));
        }

        // -- THIS CODE WILL BE REMOVED SOON --
        $this->stmt = $stmt;
        // -- THIS CODE WILL BE REMOVED SOON --

        return $stmt;
    }


    /**
     * Executes given query, as described in method multiVariableQuery().
     * If the query return no rows, or null value, default value is returned.
     * Otherwise, value of first column in a first row is returned.
     *
     * @param params - Query to be executed, default value, query params
     *
     * @return
     */
    public function multiVariableQueryValue($query, $default)
    {
        $argList = func_get_args();
        $numArgs = func_num_args();

        if ( $numArgs <= 2 ) {

            //only query + default value=> use simpleQuery
            $e = new PDOException('Improper using of '.__METHOD__.' . Too less arguments. Use simpleQueryValue() instead');
            $this->error('Improper using of '.__METHOD__, $e, false, false); //skip sending email

            return $this->simpleQueryValue($query, $default);
        }else {
            // check if params are passed as array
            if ($numArgs == 3  && is_array($argList[2])) {
                $argList = $argList[2];
            }else{
                $argList = array_slice($argList, 2);
            }
        }

        //more params - remove first two from argList and call...
        $stmt = $this->multiVariableQuery($query, $argList);

        return $this->dbResultFetchValue($stmt, $default);
    }



    // -- THIS CODE WILL BE REMOVED SOON --
        /**
         * Closes current cursor. Some methods, which drain cursor (like dbResultFetchAll())
         * or expect only one row (like *QueryValue()) close cursor implicitly.
         */
        public function closeCursor()
        {
            try{

                if(is_object($this->stmt)){
                    $this->stmt->closeCursor();
                }
                $this->stmt = null;
            }catch (PDOException $e) {
                $this->error('Unexpected error on cursor close?!', $e);
            }
        }
    // -- THIS CODE WILL BE REMOVED SOON --


    // -- THIS CODE WILL BE REMOVED SOON --
        /**
         * reset data from prevous results and make class ready for next query
         */
        public function reset()
        {
            $this->closeCursor();
        }
    // -- THIS CODE WILL BE REMOVED SOON --
}