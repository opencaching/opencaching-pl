<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This class just display text returned by dataRowExtractor.
 * Example:
 *
 * $model->addColumn(
 *      new Column_SimpleText(
 *              tr('columnTitle'),
 *              function($row){
 *                 return Row['value'];
 *              }
 *      )
 * );
 *
 */
class Column_SimpleText extends AbstractColumn {

    /**
     * Returns the name of the chunk template
     * {@inheritDoc}
     * @see \src\Models\ChunkModels\ListOfCaches\AbstractColumn::getChunkName()
     */
    protected function getChunkName()
    {
        return "listOfCaches/simpleTextColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}
