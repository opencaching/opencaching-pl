<?php
namespace Utils\View;

use Utils\DateTime\Year;
use lib\Objects\ApplicationContainer;
use Utils\Debug\Debug;
use Utils\I18n\I18n;
use Utils\Uri\Uri;

class View {

    const TPL_DIR = __DIR__ . '/../../tpl/stdstyle/';
    const CHUNK_DIR = self::TPL_DIR . 'chunks/';

    //NOTE: local View vars should be prefixed by "_"

    private $_template = null;              // template used by current view

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

    public function isTimepickerEnabled()
    {
        return $this->_loadTimepicker;
    }

    public function isLightBoxEnabled()
    {
        return $this->_loadLightBox;
    }

    public function isGMapApiEnabled()
    {
        return $this->_loadGMapApi;
    }


    private function error($message)
    {
        error_log($message);
    }


    public function errorLog($message)
    {
        Debug::errorLog("Template Error: $message", false);
    }

    /**
     * Redirect to given uri at local OC node
     * @param string $uri - uri should starts with "/"!
     */
    public function redirect($uri)
    {

        // if the first char of $uri is not a slash add slash
        if(substr($uri, 0, 1) !== '/'){
            $uri = '/'.$uri;
        }

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

    public function getLocalCss()
    {
        return $this->_localCss;
    }

    /**
     * Set template name (former tpl_set_tplname())
     * @param string $tplName
     */
    public function setTemplate($tplName)
    {
        //TODO: refactoring needed but this is still this way
        tpl_set_tplname($tplName);
        $this->_template = $tplName;
    }

    /**
     * TODO
     */
    public function buildView()
    {
        tpl_BuildTemplate();
    }

    /**
     * TODO
     * @param unknown $layoutTemplate
     */
    public function display($layoutTemplate=null)
    {

        if(is_null($layoutTemplate)){
            $layoutTemplate = 'common/mainLayout';
            $this->initMainLayout();
        }

        $this->_callTemplate($layoutTemplate);

        // nothing is called after this!
        exit;

    }

    /**
     * TODO
     * @param unknown $template
     */
    public function _callTemplate($template=null)
    {
        if(is_null($template)){
            $template = $this->_template;
        }

        // The only var accessed from within template code
        $view = $this;      // $view var for use inside template
        $tr = function($arg){
          // TODO: it will be refactored to proper call
          return tr($arg);
        };

        require_once(self::TPL_DIR . $template . '.tpl.php');

    }

    /**
     * This function inits vars used by main-layout
     */
    public function initMainLayout()
    {
        global $config; //TODO: refactor

        $this->setVar('_siteName', $config['siteName']);
        $this->setVar('_keywords', $config['header']['keywords']);
        $this->setVar('_favicon', '/images/'.$config['headerFavicon']);
        $this->setVar('_appleLogo', $config['header']['appleLogo']);

        $this->setVar('_title', "TODO-title"); //TODO!
        $this->setVar('_backgroundSeason', $this->getSeasonCssName());

        $this->addLocalCss(Uri::getLinkWithModificationTime(
            '/tpl/stdstyle/common/mainLayout.css'));

        if(Year::isPrimaAprilisToday()){
            $logo = $config['headerLogo1stApril'];
            $logoTitle = tr('oc_on_all_pages_top_1A');
            $logoSubtitle = tr('oc_subtitle_on_all_pages_1A');
        }else if(date('m') == 12 || date('m') == 1){
            $logo = $config['headerLogoWinter'];
            $logoTitle = tr('oc_on_all_pages_top_' . $config['ocNode']);
            $logoSubtitle = tr('oc_subtitle_on_all_pages_' . $config['ocNode']);
        }else{
            $logo = $config['headerLogo'];
            $logoTitle = tr('oc_on_all_pages_top_' . $config['ocNode']);
            $logoSubtitle = tr('oc_subtitle_on_all_pages_' . $config['ocNode']);
        }

        $this->setVar('_mainLogo', '/images/'.$logo);
        $this->setVar('_logoTitle', $logoTitle);
        $this->setVar('_logoSubtitle', $logoSubtitle);

        $this->setVar('_languageFlags',
            I18n::getLanguagesFlagsData($this->getLang()));


        $this->setVar('_qSearchByOwnerEnabled', $config['quick_search']['byowner']);
        $this->setVar('_qSearchByFinderEnabled', $config['quick_search']['byfinder']);
        $this->setVar('_qSearchByUserEnabled', $config['quick_search']['byuser']);

    }


}
