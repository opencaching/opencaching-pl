<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

/**
 * This is column with clickable icon.
 *
 * DataRowExtractor should return array with columns:
 * - icon - src of the icon (if null cell will be empty)
 * - onClick - onclick action - for example function name
 * - title - title value for title html param of the icon
 */

class Column_OnClickActionIcon extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/onClickActionIconColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}


