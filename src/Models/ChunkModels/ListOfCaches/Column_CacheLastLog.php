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
 */
class Column_CacheLastLog extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/cacheLastLogColumn";
    }

    public function getCssClass(){
        return 'left';
    }
}
