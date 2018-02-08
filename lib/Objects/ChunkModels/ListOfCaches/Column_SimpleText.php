<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

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
     * @see \lib\Objects\ChunkModels\ListOfCaches\AbstractColumn::getChunkName()
     */
    protected function getChunkName()
    {
        return "listOfCaches/simpleTextColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}


