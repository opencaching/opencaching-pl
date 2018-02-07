<?php
namespace Utils\Debug;

/**
 * This class is a simple StopWatch for script perf. mesurement
 *
 * USAGE:
 *  - just make it run by StopWatch::click('MY START TIME FLAG');
 *  - (optionally) click for save subtimes
 *  - call StopWatch::getResults to get time from start and subtimes
 *  - be sure that request has set SWICH_VAR value (for example in GET)
 */

class StopWatch {

    const SWICH_VAR = 'StopWatch'; //this var needs to be set in request (in GET/POST etc)

    private $stages;

    /**
     * Save current time under $name
     *
     * @param string $nName
     */
    public static function click($name)
    {
        if($instance = self::instance()){
            $instance->stages[$name] = microtime();
        }
    }

    /**
     * Returns the array of saved points in time
     * @return void|string[]
     */
    public static function getResults()
    {
        if(! $instance = self::instance()){
            return;
        }

        $instance->stages['__now'] = microtime();

        $result = [];

        $last = null;
        $start = null;
        foreach ($instance->stages as $stageName => $stageTime){
            $ms = self::microTimeToMs($stageTime);

            if(is_null($start)){
                $start = $ms;
                $fromStart = 0;
            }else{
                $fromStart = number_format($ms-$start, 4);
            }

            if( is_null($last) ){
                $fromLast = '-';
            }else{
                $fromLast = number_format($ms - $last, 4);
            }

            $result[$stageName] = "$fromStart s. [$fromLast s.]";
            $last = $ms;

        }
        return $result;
    }

    /**
     * Reset saved measurements
     */
    public static function reset()
    {
        if( $instance = self::instance() ){
            $instance->stages = [];
        }
    }

    private static function instance()
    {
        static $instance = null;
        if ($instance === null) {
            if(isset($_REQUEST[self::SWICH_VAR])){
                $instance = new static();
                return $instance;
            }else{
                return null;
            }
        }
        return $instance;
    }

    private function __construct()
    {}

    private static function microTimeToMs($microtime)
    {
        list($usec, $sec) = explode(" ", $microtime);
        return ((float) $usec + (float) $sec);
    }



}
