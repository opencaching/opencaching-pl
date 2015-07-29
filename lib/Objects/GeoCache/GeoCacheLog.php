<?php

namespace lib\Objects\GeoCache;

/**
 * Description of GeoCacheLog
 * 1    Found it    log/16x16-found.png
 * 2    Didn't find it  log/16x16-dnf.png
 * 3    Comment     log/16x16-note.png
 * 4    Moved   log/16x16-moved.png
 * 5    Potrzebny serwis    Needs maintenance   log/16x16-need-maintenance.png
 * 7    Attended    log/16x16-attend.png
 * 8    Zamierza uczestniczyć  Will attend     log/16x16-will_attend.png
 * 10   Gotowa do szukania  Ready to search     log/16x16-published.png
 * 11   Niedostępna czasowo    Temporarily unavailable     log/16x16-temporary.png
 * 12   Komentarz COG   OC Team comment     log/16x16-octeam.png
 * 9    Zarchiwizowana  Archived    log/16x16-trash.png
 * @author Łza
 */
class GeoCacheLog
{

    const LOGTYPE_FOUNDIT = 1;
    const LOGTYPE_DIDNOTFIND = 2;
    const LOGTYPE_COMMENT = 3;
    const LOGTYPE_MOVED = 4;
    const LOGTYPE_NEEDMAINTENANCE = 5;
    const LOGTYPE_ATTENDED = 7;

    private $logId;

    public function __construct()
    {

    }

}
