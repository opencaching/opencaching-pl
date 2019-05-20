<?php
namespace src\Models\ChunkModels\ListOfCaches;

use src\Utils\Debug\Debug;
use src\Utils\View\View;
use src\Utils\Generators\TextGen;


/**
 * This is an abstract column for listOfCaches chunk.
 * Every implementation of this class should:
 *  - define getChunkName method which should return the chunk name in
 *    /srv/Views/chunks/listOfCaches/
 *  - has proper dataExtractor
 *
 * If 'dataExtractor' is not define every "row" is passing to chunk as is.
 * See the ListOfCachesModel for details of usage.
 */

abstract class AbstractColumn {
    const COLUMN_CHUNK_DIR = __DIR__.'/../../../../srv/Views/chunks/listOfCaches/';
    private $dataExtractor = null;
    private $header = '';
    private $additionalClass = '';
    private $chunkFunction = null;

    public final function __construct($header, callable $dataFromRowExtractor=null, $additionalClass=null){
        if(!is_null($dataFromRowExtractor)){
            $this->dataExtractor = $dataFromRowExtractor;
        }

        if(!empty($additionalClass)){
            $this->additionalClass = ' '.$additionalClass;
        }

        $this->header = $header;
    }

    protected abstract function getChunkName();

    public function __call($method, $args) {
        if (property_exists($this, $method) && is_callable($this->$method)) {
            return call_user_func_array($this->$method, $args);
        }else{
            Debug::errorLog(__METHOD__."Trying to call non-existed method: $method");
        }
    }

    public final function callColumnChunk($row){

        if(!$this->chunkFunction){
            $this->chunkFunction = View::getChunkFunc($this->getChunkName());
        }

        if(!is_null($this->dataExtractor)){
            $this->chunkFunction($this->dataExtractor($row), $this);
        }else{
            $this->chunkFunction($row, $this);
        }
    }

    public function getHeader(){
        return $this->header;
    }

    public function getCssClass(){
        return "center";
    }

    public function getAdditionalClass()
    {
        return $this->additionalClass;
    }
}
