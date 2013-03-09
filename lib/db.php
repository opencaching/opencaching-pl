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
	 */
	private $debug = false;

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


	function __construct($debug = false) {
	 	include 'lib/settings.inc.php';
	 	$this->server   = $opt['db']['server'];
	 	$this->name     = $opt['db']['name'];
	 	$this->username = $opt['db']['username'];
	 	$this->password = $opt['db']['password'];
	 	
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

	
	public function dbResultFetch() {
		return $this->dbData->fetch();
	}
	
	public function rowCount() {
		return $this->dbData->rowCount();
	}
	
	public function dbResultFetchAll() {
		return $this->dbData->fetch();
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
		$dbh = new PDO("mysql:host=".$this->server.";dbname=".$this->name,$this->username,$this->password);
		$dbh -> query ('SET NAMES utf8');
		$dbh -> query ('SET CHARACTER_SET utf8_unicode_ci');

		$this->dbData  = $dbh -> prepare($query);
		$this->dbData  -> setFetchMode(PDO::FETCH_ASSOC);
		$this->dbData  -> execute();

		// $result = $STH -> fetch();
		
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
	 * $query: 'SELECT * FROM tabele WHERE field1=:variable1 AND field2:variable2'
	 * $params[variable1][value] = 1;
	 * $params[variable1][data_type] = 'integer';
	 * $params[variable2][value] = 'cat is very lovelly animal';
	 * $params[variable2][data_type] = 'string';
	 * ----------------------------------------------------------------------------------
	 * data type can be:
	 *
	 * - 'boolean'		Represents a boolean data type.
	 * - 'null' 		Represents the SQL NULL data type.
	 * - 'integer' 		Represents the SQL INTEGER data type.
	 * - 'string' 		Represents the SQL CHAR, VARCHAR, or other string data type.
	 * - 'large' 		Represents the SQL large object data type.
	 * - 'recordset' 	Represents a recordset type. Not currently supported by any drivers.
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
	public function paramQuery($query, $params, $fetchAll = false) {
		if (!is_array($params)) return false;

		$dbh = new PDO("mysql:host=".$this->server.";dbname=".$this->name,$this->username,$this->password);
		$dbh -> query ('SET NAMES utf8');
		$dbh -> query ('SET CHARACTER_SET utf8_unicode_ci');
		
		$this->dbData = $dbh->prepare($query);

		foreach ($params as $key => $val) {
			switch ($val['data_type']) {
				case 'integer':
					$this->dbData->bindParam($key, $val['value'], PDO::PARAM_INT);
					break;
				case 'boolean':
					$this->dbData->bindParam($key, $val['value'], PDO::PARAM_BOOL);
					break;
				case 'string':
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
	 * $query: 'SELECT something FROM tabele WHERE field1=:variable1 AND field2:variable2'
	 * $param1 = 1;
	 * $params2 'cat is very lovelly animal';
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
		
		$dbh = new PDO("mysql:host=".$this->server.";dbname=".$this->name,$this->username,$this->password);
		$dbh -> query ('SET NAMES utf8');
		$dbh -> query ('SET CHARACTER_SET utf8_unicode_ci');
		
		$this->dbData  = $dbh->prepare($query);
		
		$numargs = func_num_args();
		$arg_list = func_get_args();
		for ($i = 1; $i < $numargs; $i++) {
			if ($this->debug) echo 'db.php, # ' . __line__ .". Argument $i is: " . $arg_list[$i] . "<br />\n";
			
			$stmt->bindparam(':'.$i,$arg_list[$i]);
		}
		
		$this->dbData ->setFetchMode(PDO::FETCH_ASSOC);
		$this->dbData ->execute();

// 		$result['row_count'] = $stmt->rowCount();
// 		$result['result'] = $stmt -> fetchAll();
		
		if ($this->debug) {
			print 'db.php, # ' . __line__ .', Query on input: ' . $query .'<br />';
		}
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
