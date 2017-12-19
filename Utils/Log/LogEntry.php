<?php

namespace Utils\Log;

/**
 * Class used only as a Log row representative for passing mulitple entries to Log.
 * Maps the logentry table columns one to one.
 */
class LogEntry
{
    public $module;
    public $eventid;
    public $userid;
    public $objectid1;
    public $objectid2;
    public $logtext;
    public $details;
    public $logtime;

    public function __construct($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details, $logtime) {
        $this->module = $module;
        $this->eventid = $eventid;
        $this->userid = $userid;
        $this->objectid1 = $objectid1;
        $this->objectid2 = $objectid2;
        $this->logtext = $logtext;
        $this->details = $details;
        $this->logtime = $logtime;
    }
}
