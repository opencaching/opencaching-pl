<?php
/**
 * Class for safe database operations
 *
 * This class use newest php database library PDO, recomended for use for database operations.
 * using pdo is not so easy as classic mysql_* functions, but provide safety and easy to use
 * result. (array)
 *
 * most important methode in this class is paramQuery(). instructions and example of use included.
 *
 * @author Andrzej Łza Woźniak
 *
 */

class dataBase
{
	/**
	 * set this value to true to print all variables to screen.
	 * set to false to hide (switch off) all printed debug.
	 * 
	 * JG 2013-10-20
	 */
	private $debug;

	/**
	 * database link setup
	 * @var string
	 */
	private $server	= null;
	private $name = null;
	private $username = null;
	private $password = null;
	
	/**
	 * data obtained from database
	 * @var object
	 */
	private $dbData = null;
	private $lastInsertId;
	private $errorEmail;
	private $replyToEmail;

	function __construct($debug = false) {
	 	include __DIR__.'/settings.inc.php';
	 	$this->server   = $opt['db']['server'];
	 	$this->name     = $opt['db']['name'];
	 	$this->username = $opt['db']['username'];
	 	$this->password = $opt['db']['password'];
	 	
	 	$this->debug = $debug_page;
		$this->errorEmail[] = $mail_rt;
		$this->replyToEmail = $mail_rt;
	 		 	
	 	// turn on debug to screen
	 	if ($debug === true) {
	 		$this->debug = true;
	 	}
	}
	
	function __destruct() {
		if ($this->debug){
			self::debugOut('destructing object dataBase class <br ><br >');
		}
		// free up the memory
		$debug = null;
		$server	= null;
		$name = null;
		$username = null;
		$password = null;
		$dbData = null;
		$dbNumRows = null;
	}

	//JG 2013-12-14
	public function switchDebug( $debug ) {
		$this->debug = $debug;
	}
	
	
	/**
	 * @return one row from result, or FALSE if there are no more rows available
	 * The data is returned as an array indexed by column name, as returned in your 
	 * SQL SELECT
	 */
	public function dbResultFetch() {
		return $this->dbData->fetch();
	}
	
	/**
	 * @return number of row in results (i.e. number of rows returned by SQL SELECT)
	 * or the number of rows affected by the last DELETE, INSERT, or UPDATE statement
	 */
	public function rowCount() {
		return $this->dbData->rowCount();
	}
	
	/**
	 * @return all rows from result as complex array.
	 * The returned array contains all of the remaining rows (if you have previously called
	 * dbResultFetch(), or all returned rows if not) in the result set. The array represents 
	 * each row as an array indexed by column name, as returned in your SQL SELECT.
	 * An empty array is returned if there are zero results to fetch, or FALSE on failure.
	 */
	public function dbResultFetchAll() {
		return $this->dbData->fetchAll();
	}
	
	/**
	 * @return id of last inserted row
	 */
	public function lastInsertId() {
		// return $this->dbData->lastInsertId();
		return $this->lastInsertId;
	}
	
	/**
	 * simple querry
	 * Use only with static queries, Queries should contain no variables.
	 * For queries with variables use paramQery method
	 *
	 * @param string $query
	 * @return true, if the query succeeded; false, if there was SQL error 
	 */
	public function simpleQuery($query) {
		try {
			$dbh = new PDO("mysql:host=".$this->server.";dbname=".$this->name,$this->username,$this->password);
			if ( $this->debug )
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //JG 2013-10-19
				
			// JG 2013-10-20
			$dbh -> query ("SET NAMES utf8");
			$dbh -> query ("SET CHARACTER SET utf8");
			$dbh -> query ("SET collation_connection = utf8_unicode_ci" );
				
			
			$this->dbData  = $dbh -> prepare($query);
			$this->dbData  -> setFetchMode(PDO::FETCH_ASSOC);
			$this->dbData  -> execute();
			$this->lastInsertId = $dbh->lastInsertId();
		} catch (PDOException $e) {
			$message = $this->errorMessage( __line__, $e, $query, array());
			if ($this->debug) {
				self::debugOut($message);
			} else {
				self::errorMail($message);
			}

			
			return false;
		}

		if ($this->debug) {
			self::debugOut('db.php, # ' . __line__ .', mysql query on input: ' . $query .'<br />');
		}
		
		return true;
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
	 * - 'boolean'					Represents a boolean data type.
	 * - 'null' 					Represents the SQL NULL data type.
	 * - 'integer' or 'int' or 'i' 	Represents the SQL INTEGER data type.
	 * - 'string' or 'str' or 's'	Represents the SQL CHAR, VARCHAR, or other string data type.
	 * - 'large' 					Represents the SQL large object data type.
	 * - 'recordset' 				Represents a recordset type. Not currently supported by any drivers.
	 *
	 * @return true, if the query succeeded; false, if there was SQL error 
	 */
	public function paramQuery($query, $params) {
		if (!is_array($params)) return false;

		try {
			$dbh = new PDO("mysql:host=".$this->server.";dbname=".$this->name,$this->username,$this->password);
			if ( $this->debug )
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //JG 2013-10-19
			
			// JG 2013-10-20
			$dbh -> query ("SET NAMES utf8");
			$dbh -> query ("SET CHARACTER SET utf8");
			$dbh -> query ("SET collation_connection = utf8_unicode_ci" );
			
			
			$this->dbData = $dbh->prepare($query);

			foreach ($params as $key => $val) {
				switch ($val['data_type']) {
					case 'integer':
					case 'int':	
					case 'i':
						$this->dbData->bindParam($key, $val['value'], PDO::PARAM_INT);
						break;
					case 'boolean':
						$this->dbData->bindParam($key, $val['value'], PDO::PARAM_BOOL);
						break;
					case 'string':
					case 'str':
					case 's':
						$this->dbData->bindParam($key, $val['value'], PDO::PARAM_STR);
						break;
					case 'null':
						$this->dbData->bindParam($key, $val['value'], PDO::PARAM_NULL);
						break;
					case 'large':
						$this->dbData->bindParam($key, $val['value'], PDO::PARAM_LOB);
						break;
					case 'recordset':
						$this->dbData->bindParam($key, $val['value'], PDO::PARAM_STMT);
						break;
					default:
						return false;
				}
			}

			$this->dbData->setFetchMode(PDO::FETCH_ASSOC);
			$this->dbData->execute();
			$this->lastInsertId = $dbh->lastInsertId();
		} catch (PDOException $e) {
			$message = $this->errorMessage( __line__, $e, $query, $params );
			if ($this->debug) {
				self::debugOut($message);
			} else {
				self::errorMail($message);
			}
				
			return false;
		}
		if ($this->debug) {
			self::debugOut('db.php, # ' . __line__ .', Query on input: ' . $query .'<br />');
			self::debugOC('db.php, # ' . __line__ .', input parametres for query', $params );
			// self::debugOC('db.php, # ' . __line__ .', database output', $result );
		}

		return true;
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
	public function multiVariableQuery($query) {
		$numargs = func_num_args();
		$arg_list = func_get_args();
		try {
			// We are instantinating new PDO object, and new connection for every query.
			// This is inefficient (compared to old sql() function, and especially on my Windows box)
			// Moreover, this could prevent TEMPORARY TABLES from working -> find out!
			$dbh = new PDO("mysql:host=".$this->server.";dbname=".$this->name,$this->username,$this->password);
			if ( $this->debug )
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //JG 2013-10-19
			
			// JG 2013-10-20
			$dbh -> query ("SET NAMES utf8");
			$dbh -> query ("SET CHARACTER SET utf8");
			$dbh -> query ("SET collation_connection = utf8_unicode_ci" );
			$this->dbData  = $dbh->prepare($query);

			for ($i = 1; $i < $numargs; $i++) {
				// if ($this->debug) echo 'db.php, # ' . __line__ .". Argument $i is: " . $arg_list[$i] . "<br />\n";
				$this->dbData->bindParam(':'.$i,$arg_list[$i]);
			}

			$this->dbData ->setFetchMode(PDO::FETCH_ASSOC);
			$this->dbData ->execute();
			$this->lastInsertId = $dbh->lastInsertId();
		} catch (PDOException $e) {
			$message = $this->errorMessage( __line__, $e, $query, $arg_list);
			
			if ($this->debug) {
				self::debugOut($message);
			} else {
				self::errorMail($message);
			}

			return false;
		}
		if ($this->debug) {
			self::debugOut('db.php, # ' . __line__ .', Query on input: ' . $query .'<br />');
			for ($i = 1; $i < $numargs; $i++)
				self::debugOut("Param :" . $i. " = " . $arg_list[$i]. "<br>");
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
	public function simpleQueryValue($query, $default) {
		$this->simpleQuery($query);
		$r = $this->dbResultFetch();
		if ($r){
			$value = reset($r);
			if ($value == null)
				return $default;
			else
				return $value;
		} else {
			return $default;
		}
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
	public function multiVariableQueryValue($query) {
		$arg_list = func_get_args();
		$default = null;
		if (count($arg_list)>=2){
			$default = $arg_list[1];
			unset($arg_list[1]);
		}
		// could be this done better?
		call_user_func_array(array($this, 'multiVariableQuery'), $arg_list);
		$r = $this->dbResultFetch();
		if ($r){
			$value = reset($r);
			if ($value == null)
				return $default;
			else
				return $value;
		} else {
			return $default;
		}
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
	public function paramQueryValue($query, $default, $params) {
		if (!is_array($params)) return false;
		$this->paramQuery($query, $params);
		$r = $this->dbResultFetch();
		if ($r){
			$value = reset($r);
			if ($value == null)
				return $default;
			else
				return $value;
		} else {
			return $default;
		}
	}

	private function errorMail($message, $topic=null) {
		$headers = 	'From: dataBase class' . "\r\n" .
					'Reply-To: '.$this->replyToEmail . "\r\n" .
					'X-Mailer: PHP/' . phpversion().
					'MIME-Version: 1.0' . "\r\n" .
					'Content-type: text/html; charset=utf-8' . "\r\n";
		
		if(!isset($topic)) $topic = 'Database error caught in db.php';
		foreach ($this->errorEmail as $email) {
			mail($email, $topic, $message, $headers);
		}
	}

	
	private function errorMessage( $line, $e, $query, $params ) {
		$message = 'db.php, line: ' . $line .', <p class="errormsg"> PDO error: ' . $e .'</p><br />
					Database Query: '.$query.'<br>
					Parametres array: <pre>'.
					print_r($params, true).
					'</pre><br><br>';
	
		return $message;
	}
	
	/**
	 * this methode can be used for display any array from anywhere
	 *
	 * @param string $position - put here what you want. just title(name) of array
	 * @param array $array - array to display
	 *
	 * @example dataBase::debugOC('some.php, # ' . __line__ .', my variable', $array_variable );
	 */
	public static function debugOC($position, $array) {
		dataBase::debugOut("<pre> --- $position --<br>");
		dataBase::debugOut(print_r ($array, true));
		dataBase::debugOut('----------------------<br /><br /></pre>', true);

	}
	
	private static function debugOut($text, $onlyHtmlString = false) {
		// TODO: make it configurable
		// useful when debugging stripts generating content other than HTML
		
		//if ($onlyHtmlString !== true){
		//	error_log($text);
		//}
		print $text; 
	}
}
