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
global $debug_page;

class dataBase
{
	/**
	 * set this value to true to print all variables to screen.
	 * set to false to hide (switch off) all printed debug.
	 */
	private $debug;  //JG 2013-10-20

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

	function __construct($debug = false) {
	 	include __DIR__.'/settings.inc.php';
	 	$this->server   = $opt['db']['server'];
	 	$this->name     = $opt['db']['name'];
	 	$this->username = $opt['db']['username'];
	 	$this->password = $opt['db']['password'];
	 	
		// print_r($opt); exit;
		
	 	//JG 2013-10-20
	 	$this->debug = $debug_page;
	 		 	
	 	// turn on debug to screen
	 	if ($debug === true) {
	 		$this->debug = true;
	 	}
	}
	
	function __destruct() {
		if ($this->debug){
			print 'destructing object dataBase class <br ><br >';
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
	 * @return one row from result
	 */
	public function dbResultFetch() {
		return $this->dbData->fetch();
	}
	
	/**
	 * @return number of row in results
	 */
	public function rowCount() {
		return $this->dbData->rowCount();
	}
	
	/**
	 * @return all rows from result as complex array
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
	 * For queries with variables use paramQery methode
	 *
	 * @param string $query
	 * @return array
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
		} catch (PDOException $e) {
			$message = $this->errorMessage( __line__, $e, $query, "");
			if ($this->debug) {
				print $message;
			} else {
				self::errorMail($message);
			}

			
			return false;
		}

		if ($this->debug) {
			print 'db.php, # ' . __line__ .', mysql query on input: ' . $query .'<br />';
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
	 * @return array or false
	 *  - return array structure:
	 *  Array
	 * (
	 *   [row_count] => 1
	 *   [result] => Array
	 *     (
	 *       [secid] => 12
	 *     )
	 * )
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
		} catch (PDOException $e) {
			$message = $this->errorMessage( __line__, $e, $query, $params );
			if ($this->debug) {
				print $message;
			} else {
				self::errorMail($message);
			}
				
			return false;
		}
		if ($this->debug) {
			print 'db.php, # ' . __line__ .', Query on input: ' . $query .'<br />';
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
	 * @return array or false
	 *  - return array structure:
	 *  Array
	 * (
	 *   [row_count] => 2
	 *   [result] => Array
	 *     (
	 *      [0] => Array (
     *           	[something] => 12
     *           )
     *      [1] => Array (
     *           	[something] => 20
     *           )    
	 *     )
	 * )
	 */
	public function multiVariableQuery($query) {
		try {
			$dbh = new PDO("mysql:host=".$this->server.";dbname=".$this->name,$this->username,$this->password);
			if ( $this->debug )
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //JG 2013-10-19
			
			// JG 2013-10-20
			$dbh -> query ("SET NAMES utf8");
			$dbh -> query ("SET CHARACTER SET utf8");
			$dbh -> query ("SET collation_connection = utf8_unicode_ci" );
			
			$this->dbData  = $dbh->prepare($query);

			$numargs = func_num_args();
			$arg_list = func_get_args();
			for ($i = 1; $i < $numargs; $i++) {
			//	if ($this->debug) echo 'db.php, # ' . __line__ .". Argument $i is: " . $arg_list[$i] . "<br />\n";
				
				$this->dbData->	bindParam(':'.$i,$arg_list[$i]);
				//$dbh->bindParam(':'.$i,$arg_list[$i]);
			}

			$this->dbData ->setFetchMode(PDO::FETCH_ASSOC);
			$this->dbData ->execute();
			$this->lastInsertId = $dbh->lastInsertId();
		} catch (PDOException $e) {
			$message = $this->errorMessage( __line__, $e, $query, print_r($arg_list, true) );
			
			if ($this->debug) {
				print $message;
			} else {
				self::errorMail($message);
			}

			return false;
		}
		if ($this->debug) {
			print 'db.php, # ' . __line__ .', Query on input: ' . $query .'<br />';
			for ($i = 1; $i < $numargs; $i++)
				print "Param :" . $i. " = " . $arg_list[$i]. "<br>";
		}
		return true;
	}
	
	private function errorMail($message) {
	
		$headers = 	'From: dataBase class' . "\r\n" .
					'Reply-To: rt@opencaching.pl' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		
		
		if(!isset($topic)) $topic = 'ErrorMail'; //JG - niezainicjowna zmienna 2013-10-19
		mail('rt@opencaching.pl', $topic, $message, $headers);
	}

	
	private function errorMessage( $line, $e, $query, $params )
	{
	$message = 'db.php, line: ' . $line .', <p class="errormsg"> PDO error: ' . $e .'</p><br />
					Database Query: '.$query.'<br>
							Parametres array: '.
								print_r($params, true).
								'<br><br>';
	
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
		print "<pre> --- $position --<br>";
		print_r ($array);
		print '----------------------<br /><br /></pre>';

	}

}
