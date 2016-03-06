<?php
/* --- Configuration: --- */
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = 'toor';
$dbName = 'ocpl';

// file with export pattern
$exportPatternFile = "./db_export_ocpl_pattern.json";

// directory used for script dumps etc.
$tmpDir = NULL; //if NULL it is set to: './tmp_db_export_<date-time>'

$resultFile = NULL; //if NULL is set to: './oc_dev_export_<date-time>.sql'

//if TRUE db structure will be attache at the begining of the result file
define('DB_STRUCT_DUMP', TRUE );

//if TRUE many debug messages are printed
define('DEBUG', TRUE );

//if TRUE all temporary files created will be removed
define('CLEANUP', TRUE );

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

function error($msg){ echo ("\nERROR: $msg\n\n"); }
function info($msg) { echo ("-i-  $msg\n"); }
function debug($msg){ if(DEBUG){ echo ("-d-  $msg\n"); } }
function title($msg){ return "\n\n ----- $msg -----\n\n"; }

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

    global $dbUser, $dbPass, $dbHost, $dbName, $tmpDir;


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

        debug("Query for $tableName:\n \t\t$sqlQuery");

        //prefix for print query for debug purpose
        $qMsg = '';
        if(DEBUG){
           $qMsg = "echo '$sqlQuery';";
        }
        return "$qMsg mysql -u $dbUser --password=$dbPass -h $dbHost -X -e '$sqlQuery' $dbName 2>&1 1>$tmpDir/$tableName.xml";

    }
}

/*
    Function looking for xml file with name in format: $tabName.xml
    This xml file contains the table data exported to xml format by mysql
    <resultset>
        <row>
            <field name=...">row-value</field>
        </row>
    </resultset>

    All the data needs to be converted to sql-insert format and appended to $resultFile
*/
function ConvertXml2Sql($tabName, $resultFile){

    global $tmpDir;

    debug("\t-conversion date of table: $tabName");

    $resultFileHandler = fopen( $resultFile, "a+");
    $xmlFile = $tmpDir.'/'.$tabName.'.xml';

    $xml = new XMLReader;
    if(!$xml->open( $xmlFile, NULL, LIBXML_COMPACT | LIBXML_PARSEHUGE )){
        error("Can't open XML file: $xmlFile");
        exit();
    }

    $colArray = array();
    while ($xml->read()) {

        if ($xml->nodeType == XMLReader::ELEMENT) {
            //parse the xml tag
            switch($xml->name){
            case 'field':
                $colName = $xml->getAttribute('name');
                if(is_null($colName)){
                    error("Missing attr. 'name' ($tag in $xmlFile)");
                    exit();
                }
                $colVal = $xml->readString();
                $colArray['`'.$colName.'`'] = "'".html_entity_decode($colVal)."'";

                break;
            case 'row':
                if( empty($colArray) ){
                    //no data - continue
                    break;
                }
                //print previous row to result file
                $keys = implode(',',array_keys($colArray));
                $vals = implode(',',$colArray);
                $insertStr="INSERT INTO $tabName ( $keys ) VALUES ($vals);\n";
                if( FALSE === fwrite ( $resultFileHandler, $insertStr) ) {
                    error("Can't write to file: $resultFile");
                    exit();
                }
                //clear the array
                $colArray = array();

                break;
            case 'resultset':
                $tableHeader = "\n\n--- TABLE $tabName ---\n\n";
                if( FALSE === fwrite ( $resultFileHandler, $tableHeader) ) {
                    error("Can't write to file: $resultFile");
                    exit();
                }
                break;
            default:
                $tag = $xml->name;
                error("Unsupported tag: $tag in xml: $xmlFile");
                exit();
            }//switch
        }
    } //while

    //print the last row...
    if( !empty($colArray) ){
        //print previous row to result file
        $keys = implode(',',array_keys($colArray));
        $vals = implode(',',$colArray);
        $insertStr="INSERT INTO $tabName ( $keys ) VALUES ($vals);\n";
        //debug("\t$insertStr;\n");
        //if( !file_put_contents($resultFile, $insertStr, FILE_APPEND) ){
        if( FALSE === fwrite ( $resultFileHandler, $insertStr) ) {
            error("Can't write to file: $resultFile");
            exit();
        }
    }

    $xml->close();
    fclose ( $resultFileHandler );
}

function DumpDbStruct(){
    global $resultFile;
    global $dbUser, $dbPass, $dbName;

    $command = "mysqldump --user=$dbUser --password=$dbPass --opt --no-data $dbName > $resultFile";

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

function RunExport(){

    global $dbName, $exportPatternFile;
    global $tmpDir;
    global $resultFile;

    perfStat('global');
    perfStat('prep');

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

    //prepare tmpDir:
    if( is_null( $tmpDir ) ){
        $tmpDir = './tmp_db_export_'.date("Y_m_d_H_i_s");
        mkdir($tmpDir);
        debug("Temp. dir created: $tmpDir");
    }

    //dump DB structure if needed
    if ( DB_STRUCT_DUMP ){
        DumpDbStruct();
    }

    //prepare dumpScript file
    $dumpScript = $tmpDir.'/dumpScript.sh';
    debug("DumpScript: $$dumpScript");
    if( !file_put_contents($dumpScript, "set -e\n", FILE_APPEND) ){
        error("Can't write to file: $dumpScript");
        exit();
    }

    //prepare dump script
    debug(title("Generating dump commands:"));
    $exportedTables = array();
    foreach( $currSchema['tables'] as $tabName => $columns ){
        $dumpCommand = GetDumpCommand($tabName, $patternSchema);
        if( $dumpCommand != '' ){
            if( !file_put_contents($dumpScript, $dumpCommand."\n", FILE_APPEND) ){
                error("Can't write to file: $dumpScript");
                exit();
            }
        $exportedTables[] = $tabName;
        }
    }
    info(title("Generating dump commands completed"));

    //set exec-bit @ dumpScript
    chmod ($dumpScript, 0755);

    perfStat('prep');

    //run dump script
    info(title("Dump from DB started"));
    perfStat('db');

    $returnVar = NULL;
    echo system ( $dumpScript, $returnVar );
    echo "\n";
    if( $returnVar != 0 ){
        exit();
    }
    perfStat('db');
    info(title("Dump from DB completed"));

    //convert from xmldump to sql inserts
    info(title("Starting conversions from xml dumps to sql insert file"));
    perfStat('conv');

    foreach( $exportedTables as $tabName ){
        ConvertXml2Sql($tabName, $resultFile);
    }
    perfStat('conv');
    info(title("Conversions from xml dumps to sql insert file ends"));


    //compress the result file
    info(title("Compress the result file..."));
    perfStat('bzip');
    $compressCmd = "bzip2 -9 $resultFile";
    echo system ( $compressCmd );
    perfStat('bzip');

    //remove tmp files dir after all
    if( CLEANUP ){
        $files = glob($tmpDir.'/*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
        rmdir($tmpDir);
    }

    $perfGlob = perfStat('global', TRUE);
    $perfDb = perfStat('db', TRUE);
    $perfConv = perfStat('conv', TRUE);
    $perfPrep = perfStat('prep', TRUE);
    $perfBzip = perfStat('bzip', TRUE);

    debug(title("Global perf stats:"));
    debug("\tGlobal: $perfGlob s.\n\tPreparation: $perfPrep s.\n\tDB operations: $perfDb s.\n\tResults conversions: $perfConv s.\n\tResults compression: $perfBzip s.");
}
/////////////////////////////////////////

// check if this script is running from cli - exit otherwise
if (substr(php_sapi_name(), 0, 3) != 'cli') {
    echo "ERROR: It should be calling over cli!";
    exit();
}

//prepare the result script
if( is_null( $resultFile )){
    $resultFile = './oc_dev_export_'.date("Y_m_d_H_i_s").'.sql';
}

// to dump current schema uncomment this line
// DumpSchemaJson($currSchema, "./currentSchema.json");

RunExport();

