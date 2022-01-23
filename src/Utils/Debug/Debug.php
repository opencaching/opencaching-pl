<?php
namespace src\Utils\Debug;

use Throwable;

class Debug {

    /**
     * Returns backtrace in simple form:
     * file:line | file:line | ...
     *
     * @return string
     */
    public static function formTraceStr($backtrace)
    {
        $traceStr = "\n  STACKTRACE:";

        $i = 0;
        foreach ($backtrace as $trace) {
            $file = $trace['file'] ?? '?';
            $line = $trace['line'] ?? '?';
            $func = $trace['function'] ?? '?';
            $class = isset($trace['class']) ? $trace['class'] . '::' : '';

            $traceStr .= "\n  #$i: $file:$line [$class$func()]";
            $i ++;
        }
        return $traceStr . "\n";
    }

    public static function logException(Throwable $e)
    {
        $message = sprintf("%s: [%s:%d] %s ", get_class($e), $e->getFile(), $e->getLine(), $e->getMessage());
        if (! empty($e->getTrace())) {
            $message .= self::formTraceStr($e->getTrace());
        }
        error_log($message);
    }

    public static function errorLog($message, $addStackTrace = true)
    {
        if ($addStackTrace) {
            $message .= self::formTraceStr(debug_backtrace());
        }
        error_log($message);
    }

    public static function dumpToLog($var, $message = null)
    {
        if (! is_null($message)) {
            $result = $message . ': ';
        } else {
            $result = 'var-dump: ';
        }
        $result .= var_export($var, TRUE);
        error_log($result);
    }

    /**
     * Function to print memory usage to log.
     * Best usage: Debug::memUsage(__FILE__.':'.__LINE__)
     * Shows memory usage and diff from the last call.
     *
     * @param string $placeInCode - identifier to add to the string in log
     */
    public static function memUsage($placeInCode = "?"): void
    {
        static $lastMemUsage = 0;
        $currMemUsage = memory_get_usage();
        self::errorLog(
            $placeInCode.": ".round($currMemUsage/1024, 2)." / ".round(($currMemUsage-$lastMemUsage)/1024, 2), false);
        $lastMemUsage = $currMemUsage;
    }

}
