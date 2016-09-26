<?php
namespace Utils\View;

class View {

    const CHUNK_DIR = __DIR__.'/../../tpl/stdstyle/chunks/';

    //NOTE: local View vars should be prefixed by "_"
    private $_googleAnalyticsKey = '';              // GA key loaded from config

    public function __construct(){

        // load google analytics key from the config
        global $googleAnalytics_key;
        $this->_googleAnalyticsKey = isset($googleAnalytics_key) ? $googleAnalytics_key : '';
        $this->loadChunk('googleAnalytics'); // load GA chunk for all pages
    }

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

        $func = require(self::CHUNK_DIR.$chunkName.'.tpl.php');
        $funcName = $chunkName.'Chunk';
        $this->$funcName = $func;
    }

    public function getGoogleAnalyticsKey(){
        return $this->_googleAnalyticsKey;
    }

    public function callChunk($chunkName, ...$arg) {
        $this->loadChunk($chunkName);
        $funcName = $chunkName.'Chunk';
        $this->$funcName(...$arg);
    }

    public function loadJQuery(){
        $this->_loadJQuery = true;
    }

    /**
     * Returns true if GA key is set in config (what means that GA is enabled)
     */
    public function isGoogleAnalyticsEnabled(){
        return $this->_googleAnalyticsKey != '';
    }

    private function error($message){
        error_log($message);
    }

}
