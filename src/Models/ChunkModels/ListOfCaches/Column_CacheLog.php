<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column with log-icon and log-text.
 * It needs vars in $data:
 * - logId - id of the log
 * - logType - type of the log
 * - logText - text of the log
 * - logUserName - name of the author
 * - logDate - date of the log
 * - recommended - if not null means cache is recommended by user (shows only if log-type=FOUND)
 *
 *
 * ADD THIS: $this->view->addLocalCss('/css/lightTooltip.css');
 */
class Column_CacheLog extends AbstractColumn {

    private $isFullLogTextPresented = false;

    protected function getChunkName()
    {
        return "listOfCaches/cacheLogColumn";
    }

    public function getCssClass(){
        return 'left';
    }

    public function showFullLogText()
    {
        $this->isFullLogTextPresented = true;
    }

    public function isFullLogTextPresented()
    {
        return $this->isFullLogTextPresented;
    }
}
