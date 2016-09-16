<?php
namespace Utils\View;

class View {

    //NOTE: local View vars should be prefixed by "_"
    private $_loadJQuery = false;
    private $_chunksDir = 'tpl/stdstyle/chunks/';

    /**
     * Set given variable as local View variable
     * (inside template only View variables are accessible)
     *
     *
     * @param String $varName
     * @param  $varValue
     */
    public function setVar($varName, $varValue){
        if(property_exists($this, $varName)){
            $this->error("Can't set View variable with name: ".$varName);
            return;
        }

        $this->$varName = $varValue;
    }

    public function __call($method, $args) {
        if (property_exists($this, $method) && is_callable($this->$method)) {
            return call_user_func_array($this->$method, $args);
        }else{
            $this->error("Trying to call non-existed method of View: $method");
        }
    }

    /**
     * Load chunk by given name.
     * Chunk should be an anonymous function
     * defined in file of the same name in tpl/stdstyle/chunks
     * It can be then called in template file by $view->$chunkName
     *
     * @param string $chunkName
     */
    public function loadChunk($chunkName){
        if(property_exists($this, $chunkName)){
            $this->error("Can't set View variable with name: $varName");
            return;
        }

        $func = require($this->_chunksDir.$chunkName.'.tpl.php');
        $funcName = $chunkName.'Chunk';
        $this->$funcName = $func;
    }


    public function loadJQuery(){
        $this->_loadJQuery = true;
    }

    public function shouldLoadJquery(){
        return $this->_loadJQuery;
    }

    private function error($message){
        error_log($message);
    }

}