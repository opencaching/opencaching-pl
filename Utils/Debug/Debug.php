<?php
namespace Utils\Debug;

use Exception;


/**
 * This is class used as error-log writer
 *
 */
class Debug {

    /**
     * Returns backtrace in simple form:
     * file:line | file:line | ...
     *
     * @return string
     */
    public static function getTraceStr()
    {
        $traceStr = '';
        $backtrace = debug_backtrace();

        array_shift($backtrace); //remove first element - call this method...

        foreach($backtrace as $trace){
            if( isset($trace['file']) && isset($trace['line']) ){
                $traceStr.= ' | '.$trace['file'].':'.$trace['line'];
            }else{
                $traceStr.= ' | ? : ?';
            }
        }
        return $traceStr;
    }

    /**
     * Prints log entry to error_log with stacktrace
     * @param string $message - message to log
     * @param boolean $addStackTrace - if true stackTrace will be added
     */
    public static function errorLog($message, $addStackTrace=true){

        if($addStackTrace){
            $message .= " \n| STACKTRACE:".self::getTraceStr();
        }

        error_log($message);
    }

    /**
     * Prints to log based on Throwable data
     * @param \Throwable $e
     */
    public static function logThrowable(/*PHP7: Throwable*/ $e)
    {
        error_log($e->getMessage()." | STACKTRACE: ".self::jTraceEx($e));
    }

    /**
     * Prints to log based on OcException data
     * @param OcException $e
     */
    public static function logOcException(OcException $e)
    {
        if( $e->noticeInLog() ){
            self::logThrowable($e);
        }
    }

    /**
     * Prints to log based on Exception data
     * @param Exception $e
     */
    public static function logException(Exception $e)
    {
        self::logThrowable($e);
    }

    /**
     * Dump variable value to log.
     *
     * @param mixed $var
     * @param string $message
     */
    public static function dumpToLog($var, $message=null){

        if(!is_null($message)){
            $result = $message.': ';
        }else{
            $result = 'var-dump: ';
        }
        $result .= var_export($var, TRUE);
        error_log($result);
    }

    /**
     * jTraceEx() - provide a Java style exception trace
     * @param $exception
     * @param $seen      - array passed to recursive calls to accumulate trace lines already seen
     *                     leave as NULL when calling this function
     * @return array of strings, one entry per trace line
     *
     * Code of this function based solution from:
     * http://php.net/manual/en/exception.gettraceasstring.php#114980
     * (Thanks to Ernest Vogelsinger!)
     */
    public static function jTraceEx($e, $seen=null) {
        $starter = $seen ? 'Caused by: ' : '';
        $result = array();
        if (!$seen){
            $seen = array();
        }
        $trace = $e->getTrace();
        $prev = $e->getPrevious();
        $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
        $file = $e->getFile();
        $line = $e->getLine();
        while (true) {
            $current = "$file:$line";
            if (is_array($seen) && in_array($current, $seen)) {
                $result[] = sprintf(' ... %d more', count($trace)+1);
                break;
            }
            $result[] = sprintf(' at %s%s%s(%s%s%s)',
                count($trace) && array_key_exists('class', $trace[0]) ?
                    str_replace('\\', '.', $trace[0]['class']) : '',
                count($trace) && array_key_exists('class', $trace[0]) &&
                    array_key_exists('function', $trace[0]) ? '.' : '',
                count($trace) && array_key_exists('function', $trace[0]) ?
                    str_replace('\\', '.', $trace[0]['function']) : '(main)',
                $line === null ? $file : basename($file),
                $line === null ? '' : ':',
                $line === null ? '' : $line);
            if (is_array($seen)){
                $seen[] = "$file:$line";
            }
            if (!count($trace)){
                break;
            }
            $file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
            $line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
            array_shift($trace);
        }
        $result = join("\n", $result);
        if ($prev){
            $result  .= "\n" . jTraceEx($prev, $seen);
        }

        return $result;
    }
}