<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column with clickable action buttons.
 * There can be many buttons definition.
 * Every button is a table like:
 *
 * DataRowExtractor should return array with columns:
 * [
 *   'btnClasses' =>  css classes to add to button
 *   'btnText' => text of the button
 *   'onClick' => onclick action - for example function name
 *   'title' => title value for title html param of the button
 * ],
 * [...], ...
 */

class Column_ActionButtons extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/actionButtonsColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}
