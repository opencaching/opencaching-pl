<?php
namespace Utils\Debug;


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

    public static function errorLog($message, $addStackTrace=true){

        if($addStackTrace){
            $message .= " \n| STACKTRACE:".self::getTraceStr();
        }

        error_log($message);
    }

    public static function dumpToLog($var, $message=null){

        if(!is_null($message)){
            $result = $message.': ';
        }else{
            $result = 'var-dump: ';
        }
        $result .= var_export($var, TRUE);
        error_log($result);
    }
}