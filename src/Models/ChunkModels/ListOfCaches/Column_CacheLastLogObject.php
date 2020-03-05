<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column with last log-icon and log-text for given GeoCache object
 *
 * ADD THIS: $this->view->addLocalCss('/css/lightTooltip.css');
 */
class Column_CacheLastLogObject extends AbstractColumn
{

    /**
     * @return string
     */
    protected function getChunkName()
    {
        return "listOfCaches/cacheLastLogObjectColumn";
    }

    /**
     * @return string
     */
    public function getCssClass()
    {
        return 'left';
    }

}