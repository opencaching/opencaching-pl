<?php
namespace Utils\View;

use Utils\DateTime\Year;
use lib\Objects\ApplicationContainer;

class View {

    const CHUNK_DIR = __DIR__.'/../../tpl/stdstyle/chunks/';

    //NOTE: local View vars should be prefixed by "_"

    private $_googleAnalyticsKey = '';      // GA key loaded from config

    private $_loadJQuery = false;
    private $_loadJQueryUI = false;
    private $_loadTimepicker = false;
    private $_loadGMapApi = false;
    private $_loadLightBox = false;

    private $_localCss = [];                // page-local css styles loaded from controller

    public function __construct(){

        // load google analytics key from the config
        $this->_googleAnalyticsKey = isset($GLOBALS['googleAnalytics_key']) ?
                $GLOBALS['googleAnalytics_key'] : '';
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

    public function getGoogleAnalyticsKey(){
        return $this->_googleAnalyticsKey;
    }

    public function callChunk($chunkName, ...$args) {

        $method = $chunkName.'Chunk';

        if(!property_exists($this, $method)){
            $func = self::getChunkFunc($chunkName);
            $this->$method = $func;
        }

        if(is_callable($this->$method)){
            $this->$method(...$args);
        }else{
            $this->error("Can't call chunk: $chunkName");
        }
    }

    public function callChunkOnce($chunkName, ...$args){
        self::callChunkInline($chunkName, ...$args);
    }

    public static function callChunkInline($chunkName, ...$args) {
        $func = self::getChunkFunc($chunkName);
        call_user_func_array($func, $args);
    }

    public static function getChunkFunc($chunkName){
        return require(self::CHUNK_DIR.$chunkName.'.tpl.php');
    }

    public function loadJQuery(){
        $this->_loadJQuery = true;
    }

    public function loadJQueryUI(){
        $this->_loadJQueryUI = true;
        $this->_loadJQuery = true; // jQueryUI needs jQuery!
    }

    public function loadTimepicker(){
        $this->_loadTimepicker = true;
        $this->_loadJQueryUI = true;
        $this->_loadJQuery = true;
    }

    public function loadLightBox(){
        $this->_loadLightBox = true;
        $this->_loadJQuery = true; // lightBox needs jQuery!
    }

    public function loadGMapApi($callback = null){
        $this->_loadGMapApi = true;
        $this->setVar('GMapApiCallback', $callback);
    }

    /**
     * Returns true if GA key is set in config (what means that GA is enabled)
     */
    public function isGoogleAnalyticsEnabled(){
        return $this->_googleAnalyticsKey != '';
    }

    public function isjQueryEnabled(){
        return $this->_loadJQuery;
    }

    public function isjQueryUIEnabled(){
        return $this->_loadJQueryUI;
    }

    public function isTimepickerEnabled(){
        return $this->_loadTimepicker;
    }

    public function isLightBoxEnabled(){
        return $this->_loadLightBox;
    }

    public function isGMapApiEnabled(){
        return $this->_loadGMapApi;
    }


    private function error($message){
        error_log($message);
    }

    public function redirect($uri)
    {
        header("Location: " . "//" . $_SERVER['HTTP_HOST'] . $uri);
    }

    public function getSeasonCssName()
    {

        $season = Year::GetSeasonName();
        switch($season){ //validate - for sure :)
            case 'spring':
            case 'winter':
            case 'autumn':
            case 'summer':
                return $season;
        }
    }

    public function getLang()
    {
        return ApplicationContainer::Instance()->getLang();
    }

    /**
     * Add css which will be loaded in page header
     * @param $url - url to css

     */
    public function addLocalCss($css_url){
        $this->_localCss[] = $css_url;
    }

    public function getLocalCss(){
        return $this->_localCss;
    }

    /**
     * Set template name (former tpl_set_tplname())
     * @param string $tplName
     */
    public function setTemplate($tplName){
        //TODO: refactoring needed but this is still this way
        tpl_set_tplname($tplName);
    }

    public function buildView(){
        //TODO: refactoring needed but this is still this way
        tpl_BuildTemplate();
    }


}
