<?php
namespace Utils\Debug;

/**
 * This class is former /lib/cbanch.php
 *
 */

class TimeBenchmark {

    var $start;
    var $stop;

    public function __construct()
    {
        $this->start = 0;
        $this->stop = 0;
    }

    private function getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    public function start()
    {
        $this->start = $this->getmicrotime();
    }

    public function stop()
    {
        $this->stop = $this->getmicrotime();
    }

    public function diff()
    {
        $result = $this->stop - $this->start;
        return $result;
    }

    public function runTime()
    {
        $result = $this->getmicrotime() - $this->start;
        return $result;
    }

}
