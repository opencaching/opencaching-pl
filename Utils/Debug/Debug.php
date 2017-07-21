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
        $backtrace = debug_backtrace();
        foreach($backtrace as $trace){
            $traceStr.= ' | '.$trace['file'].':'.$trace['line'];
        }
        return $traceStr;
    }


}