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
            $traceStr.= ' | '.$trace['file'].':'.$trace['line'];
        }
        return $traceStr;
    }

    public static function errorLog($message, $addStackTrace=true){

        if($addStackTrace){
            $message .= " \n| STACKTRACE:".self::getTraceStr();
        }

        error_log($message);
    }
}