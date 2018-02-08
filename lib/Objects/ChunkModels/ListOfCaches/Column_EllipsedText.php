<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

/**
 * This class just display text returned by dataRowExtractor.
 * If text is longer it will be trimmed to $maxChars + popup will be generated on click
 *
 * Example:
 *
 * $model->addColumn(
 *      new Column_EllipsedText(
 *              tr('columnTitle'),
 *              function($row){
 *                 return [
 *                   'text' => '<text-to-display',
 *                   'maxChars' => '<max-chars-to-display>'
 *                 ];
 *              }
 *      )
 * );
 *
 */
class Column_EllipsedText extends AbstractColumn {

    /**
     * Returns the name of the chunk template
     * {@inheritDoc}
     * @see \lib\Objects\ChunkModels\ListOfCaches\AbstractColumn::getChunkName()
     */
    protected function getChunkName()
    {
        return "listOfCaches/ellipsedTextColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}


