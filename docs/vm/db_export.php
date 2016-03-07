<?php

/*
 * Usage:
 *   php -f db_export.php | gzip -1 -c > result.sql.gz
 *
 */

/* --- Configuration: --- */
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = 'toor';
$dbName = 'ocpl';

// file with export pattern
$exportPatternFile = "./db_export_ocpl_pattern.json";

//if TRUE db structure will be attache at the begining of the result file
define('DB_STRUCT_DUMP', TRUE );

//if TRUE many debug messages are printed
define('DEBUG', TRUE );

/* ----- Configuration END ----- */



$db = null; //global mysqli object
$perfStatsArr = array(); //global tables used to store perfomance statistics

//posible actions from DB pattern
define('SKIP_DATA', 'skip-data');
define('GET_DATA', 'get-data');
define('SET_TO', 'set-to');
define('TRIM', 'trim');
define('SQL', 'sql');
$colActions = array( SKIP_DATA, GET_DATA, SQL, SET_TO, TRIM );

function error($msg){ echo ("\n--ERROR: $msg\n\n"); }
function info($msg) { echo ("-- -i-  $msg\n"); }
function debug($msg){ if(DEBUG){ echo ("-- -d-  $msg\n"); } }
function title($msg){ return "\n\n-- ----- $msg -----\n\n"; }

function perfStat($perfName, $getResult = FALSE){
    global $perfStatsArr;

    if( !array_key_exists( $perfName , $perfStatsArr) ) {
        //no such key - this is begin of measurement
        $perfStatsArr[$perfName] = time();
    }else{
        //begin is present - this is end of measurement
        if( !array_key_exists( $perfName.'_end',$perfStatsArr )){
            //no end - set and return the measurement result
            $perfStatsArr[$perfName.'_end'] = time();
            return $perfStatsArr[$perfName.'_end'] - $perfStatsArr[$perfName];
        } else {
            if( $getResult ){
                return $perfStatsArr[$perfName.'_end'] - $perfStatsArr[$perfName];
            } else {
                error('PerfStat: $perfName was already measured!');
                exit();
            }
        }
    }
}

/* This func check if given key exists in array and is also an array */
function isSubArr($child, array $mom){
    if( !array_key_exists( $child, $mom ) ){
        debug("Key: $child not found!");
        return FALSE;
    }

    if( !is_array($mom[$child]) ){
        debug("Key: $child is not array!");
        return FALSE;
    }

    return TRUE;
}

/* Open connection to DB */
function checkDb(){
    global $db, $dbHost, $dbUser, $dbPass, $dbName;

    $db = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    /* check connection */
    if ($db->connect_errno) {
        error("Connect failed: $db->connect_error");
        exit();
    } else {
        info("DB connection OK.");
    }
}

/*
    Create the array which describes current DB structure (tables/columns)
    in format used by this script
*/
function GetCurrentSchemaObj($dbName){
    global $db;
    $schema = NULL;

    $q = "SELECT table_name, column_name, column_comment
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_schema = '$dbName'
        ORDER BY table_name, ordinal_position";

    /* Select queries return a resultset */
    if ($res = $db->query($q)) {
        debug("Columns in db: $res->num_rows rows.");

        $schema = Array();
        $schema['tables'] = Array();

        /* fetch object array */
        while ($row = $res->fetch_array()) {
            $table = $row['table_name'];
            $column = $row['column_name'];

            if( !array_key_exists( $table, $schema['tables'] ) ){
                $schema['tables'][$table] = Array();
                $schema['tables'][$table]["__table_comment"] = "";
            }

            if( !array_key_exists( $column, $schema['tables'][$table] ) ){
                $schema['tables'][$table][$column] = Array();
                $schema['tables'][$table][$column]["__comment"] = "";
                $schema['tables'][$table][$column]["__action"] = "TODO";
            }

        }
        /* free result set */
        $res->close();
    }
    return $schema;
}

function DumpSchemaJson(Array $schema, $file){
    info("Schema Json saved to file: $file");;
    $fp = fopen($file, 'w');
    //PHP 5.4: fwrite($fp, json_encode($schema, JSON_PRETTY_PRINT));
    fwrite($fp, JsonFormatter(json_encode($schema))); //PHP < 5.4
    fclose($fp);
}

/**
JSON formatter "stollen" from http://stackoverflow.com/a/9776726
*/
function JsonFormatter($json) {
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "    ", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}

function ReadJsonSchema($file){
    $json = json_decode(file_get_contents($file), true);

    if(is_null($json)){
        //$jsonErr = json_last_error_msg();
        error("Can't decode JSON file: $file. Try to validate it with online JSON validator first.");
        exit();
    }
    return $json;
}

function CompareSchemas(array $pattern, array $curr){

    global $colActions;

    if( !isSubArr('tables', $pattern ) ){
        error ("incorrect PATTERN json!");
        exit();
    }

    if( !isSubArr('tables', $curr ) ){
        error("Incorrect CURRENT json!");
        exit();
    }

    //check if every column in $curr has its equivalent in $pattern
    foreach( $curr['tables'] as $tabName => $columns ){
        debug("  $tabName");

        //check if pattern contains this table
        if( !isSubArr( $tabName, $pattern['tables']) ){
            error("Pattern doesn't contain table $tabName. Can't continue.");
            exit();
        }

        foreach( $columns as $colName => $colProps ){

            if( $colName == "__table_comment"){
                //fake columns used to store comments or userinfo
                continue;
            }

            //check if pattern contains this column
            if( !isSubArr( $colName, $pattern['tables'][$tabName] ) ){
                error("$tabName: Pattern doesn't contain column $colName. Can't continue.");
                exit();
            }

            //check if pattern contains action for this column
            if( !array_key_exists( '__action', $pattern['tables'][$tabName][$colName] ) ){
                error("$tabName -> $colName: Pattern doesn't contain action: $colName. Can't continue!");
                exit;
            }
            $action = $pattern['tables'][$tabName][$colName]['__action'];
            //check if pattern contains proper action for this column
            if( !in_array($action, $colActions ) ){
                error("$tabName -> $colName: Pattern contains improper action: $action. Can't continue!");
                exit;
            }

            //check additional action params...

            //TODO:!

            debug("    - $colName... OK");
        }
    }
}

/*
    for table returns string with proper mysql select which produce xml dump according to pattern description
    if table is marked as data-skip in pattern empty string is returned
*/
function GetDumpCommand($tableName, array $pattern){

    //find table pattern
    $table = $pattern['tables'][$tableName];

    $colSql = array();
    $whereSql = null;

    foreach ( $table as $colName => $colDesc ){

        //skip __table_comment
        if( $colName == '__table_comment' ) continue;

        //optional sql conditions to the sql query
        if( $colName == '__sql_where' ) {
            $whereSql = $colDesc;
        }

        //find action
        $action = $colDesc['__action'];
        switch( $action ){
            case SKIP_DATA:
                break;

            case GET_DATA:
                $colSql[] = "`$colName`";
                break;

            case TRIM:
                $trimTo = $colDesc['__trim_size'];
                $colSql[] = "SUBSTR(`$colName`, 1, $trimTo) AS `$colName`";
                break;

            case SQL:
                $rawSql = $colDesc['__sql'];
                $colSql[] = "$rawSql";
                break;

            case SET_TO:
                $setValue = $colDesc['__set_value'];
                $colSql[] = '"'.$setValue.'"'." AS `$colName`";
                break;

            default:
                error("Unupported action: $action");
                exit();
        }
    }

    //build sql query to dump data

    if( empty($colSql) ){
        //this table
        $sqlQuery = '';
        debug("Query for $tableName: -- skipped --");
        return "";
    }else {

        $sqlQuery = 'SELECT '.implode(',', $colSql)." FROM `$tableName`";

        if(!is_null($whereSql)){
            $sqlQuery .= ' WHERE '.$whereSql;
        }

        debug("Query for table `$tableName`:");
        debug("\t\t$sqlQuery");
        return $sqlQuery;
    }
}

function DumpDbStruct(){

    global $dbUser, $dbPass, $dbName;

    $command = "mysqldump --user=$dbUser --password=$dbPass --opt --no-data $dbName";

    //run dump script
    info(title("Dump DB struct"));

    $returnVar = NULL;
    echo system ( $command, $returnVar );
    echo "\n";
    if( $returnVar != 0 ){
        exit();
    }
    info(title("Dump DB struct completed"));
}

function DumpTableData($dumpCommand, $tabName){

    global $db;

    //run query
    $result = $db->query( $dumpCommand );
    if( $result == FALSE ){
        error("Can't execute command:\n\n".$dumpCommand);
        exit();
    }


    //get the list of fields
    $finfo = $result->fetch_fields();

    //take first row
    $row = $result->fetch_assoc();
    if(!is_null($row)){

        $cols = array();
        foreach ($row as $colName => $val){
            $cols[] = '`'.$colName.'`';
        }

        //open query
        echo "INSERT INTO `$tabName` ( " . implode(',', $cols) . ") VALUES \n\t";

        //dump data
        do {
            $vals = array();
            foreach( $row as $val ){
                $vals[] = "'".addslashes($val)."'";
            }

            echo '(' . implode(',', $vals) . ')';

            $row = $result->fetch_assoc();

            if( $row != NULL ) echo ",\n\t";
            else echo ";\n" ;

        } while ( $row );
    }
}

/////////////////////////////////////////

// check if this script is running from cli - exit otherwise
if (substr(php_sapi_name(), 0, 3) != 'cli') {
    echo "ERROR: This script should be called by CLI!";
    exit();
}

// to dump current schema uncomment this line
// DumpSchemaJson($currSchema, "./currentSchema.json");

//check DB connection
checkDb();

//read pattern file
$patternSchema = ReadJsonSchema($exportPatternFile);

//get DB schema from DB
$currSchema = GetCurrentSchemaObj($dbName);
if($currSchema===NULL){
    error("Can't read schema!");
    exit();
}

//check if the pattern is compatible with current schema
//if not pattern schema needs to be updated
debug(title("Compare schemas started"));
CompareSchemas($patternSchema, $currSchema);
debug(title("Compare schemas completed"));

//dump DB structure if needed
if ( DB_STRUCT_DUMP ){
    DumpDbStruct();
}

//prepare dump script
debug(title("Generating data dump:"));
$exportedTables = array();
foreach( array_keys($currSchema['tables']) as $tabName ){
    $dumpCommand = GetDumpCommand($tabName, $patternSchema);
    if( $dumpCommand != '' ){
        //execute dump command and create inserts...
        DumpTableData($dumpCommand, $tabName);
    }
}
info(title("Dump generation completed."));
