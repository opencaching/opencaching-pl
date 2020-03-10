<?php

namespace src\Utils\View;

use src\Controllers\PageLayout\MainLayoutController;
use src\Models\ApplicationContainer;
use src\Utils\DateTime\Year;
use src\Utils\Debug\Debug;
use src\Utils\I18n\CrowdinInContextMode;
use src\Utils\I18n\I18n;
use src\Utils\Uri\SimpleRouter;

class View
{

    const TPL_DIR = __DIR__ . '/../../../src/Views';
    const CHUNK_DIR = self::TPL_DIR . '/chunks/';

    //NOTE: local View vars should be prefixed by "_"

    private $_template = null;              // template used by current view
    private $_subtitle = '';                // Page subtitle

    private $_googleAnalyticsKey = '';      // GA key loaded from config

    private $_loadJQuery = false;
    private $_loadJQueryUI = false;
    private $_loadTimepicker = false;
    private $_loadFancyBox = false;

    private $_localCss = [];    // page-local css styles loaded from controller
    private $_localJs = [];     // page-local JS scripts loaded from controller

    private $_showGdprPage = false;
    private $_showVideoBannerState = false;

    private $_redirectToMainPageAfterLogin = false;

    private $_headerChunks = []; // chunks to load in <head> of the page

    public function __construct()
    {

        // load google analytics key from the config
        $this->_googleAnalyticsKey = isset($GLOBALS['googleAnalytics_key']) ?
            $GLOBALS['googleAnalytics_key'] : '';

        $this->handleCrowdinInContextMode();
    }

    /**
     * Set given variable as local View variable
     * (inside template only View variables are accessible)
     *
     * @param string $varName
     * @param  $varValue
     * @return View
     */
    public function setVar($varName, $varValue)
    {
        if (property_exists($this, $varName)) {
            $this->error("Can't set View variable with name: " . $varName);
            return $this;
        }

        $this->$varName = $varValue;

        return $this;
    }

    public function __call($method, $args)
    {
        if (property_exists($this, $method) && is_callable($this->$method)) {
            return call_user_func_array($this->$method, $args);
        } else {
            $this->error("Trying to call non-existed method of View: $method");
        }
    }

    public function getGoogleAnalyticsKey()
    {
        return $this->_googleAnalyticsKey;
    }

    public function setGoogleAnalyticsKey($value)
    {
        $this->_googleAnalyticsKey = $value;
    }

    public function callChunk($chunkName, ...$args)
    {

        $method = $chunkName . 'Chunk';

        if (!property_exists($this, $method)) {
            $func = self::getChunkFunc($chunkName);
            $this->$method = $func;
        }

        if (is_callable($this->$method)) {
            $this->$method(...$args);
        } else {
            $this->error("Can't call chunk: $chunkName");
        }
    }

    public function callChunkOnce($chunkName, ...$args)
    {
        self::callChunkInline($chunkName, ...$args);
    }

    public static function callChunkInline($chunkName, ...$args)
    {
        $func = self::getChunkFunc($chunkName);
        call_user_func_array($func, $args);
    }

    public static function getChunkFunc($chunkName)
    {
        return require(self::CHUNK_DIR . $chunkName . '.tpl.php');
    }

    /**
     * Call sub-template in some place of template.
     * Please note the meaning of context in subtemplate -
     * subtemplate used only $view var
     *
     * @param string $subTplPath - relative to: /src/Views
     * @return string
     */
    public function callSubTpl($subTplPath)
    {
        $subTplFile = self::TPL_DIR . $subTplPath . '.tpl.php';

        if (!is_file($subTplFile)) {
            $this->errorLog("Trying to call unknown sub-template: $subTplFile");
            return '';
        }

        ob_start();
        $view = $this; //context for sub-template
        include $subTplFile;
        return ob_get_clean();
    }

    public function handleCrowdinInContextMode()
    {
        if (!CrowdinInContextMode::enabled()) {
            // crowdin-in-context mode is not enabled
            return;
        }

        // crowdin-in-context mode is enabled - load proper chunk
        $this->addHeaderChunk('crowdinInContext');
    }

    /**
     * @return View
     */
    public function loadJQuery()
    {
        $this->_loadJQuery = true;

        return $this;
    }

    /**
     * @return View
     */
    public function loadJQueryUI()
    {
        $this->_loadJQueryUI = true;
        $this->_loadJQuery = true; // jQueryUI needs jQuery!

        return $this;
    }

    /**
     * @return View
     */
    public function loadTimepicker()
    {
        $this->_loadTimepicker = true;
        $this->_loadJQueryUI = true;
        $this->_loadJQuery = true;

        return $this;
    }

    /**
     * @return View
     */
    public function loadFancyBox()
    {
        $this->_loadFancyBox = true;
        $this->_loadJQuery = true; // fancyBox needs jQuery!

        return $this;
    }

    /**
     * Returns true if GA key is set in config (what means that GA is enabled)
     */
    public function isGoogleAnalyticsEnabled()
    {
        return $this->_googleAnalyticsKey != '';
    }

    public function isjQueryEnabled()
    {
        return $this->_loadJQuery;
    }

    public function isjQueryUIEnabled()
    {
        return $this->_loadJQueryUI;
    }

    public function isTimepickerEnabled()
    {
        return $this->_loadTimepicker;
    }

    /**
     * @return boolean
     */
    public function isFancyBoxEnabled()
    {
        return $this->_loadFancyBox;
    }

    private function error($message)
    {
        Debug::errorLog($message);
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
        SimpleRouter::redirect($uri);
        exit;
    }

    public function redirectAndExit($uri)
    {
        $this->redirect($uri);
        exit;
    }

    public function getSeasonCssName()
    {
        return Year::GetSeasonName();
    }

    public function getLang()
    {
        return I18n::getCurrentLang();
    }

    /**
     * @return boolean
     */
    public function showGdprPage()
    {
        return $this->_showGdprPage;
    }

    /**
     * @param boolean $state
     */
    public function setShowGdprPage($state)
    {
        $state = boolval($state);
        $this->_showGdprPage = $state;
        if ($state) {
            $this->_googleAnalyticsKey = '';
            $this->_loadJQuery = false;
            $this->_loadJQueryUI = false;
            $this->_loadTimepicker = false;
            $this->_loadFancyBox = false;
        }
    }


    /**
     * Returns if should show video banner + slider on page
     *
     * @return boolean
     */
    public function showVideoBanner()
    {
        if (self::mobile()) {
            return false;
        }
        if (is_null(ApplicationContainer::Instance()->getLoggedUser())) {
            return true;
        }
        return $this->_showVideoBannerState;
    }

    /**
     * @return boolean - true if the view should be optimized for mobile devices
     */
    public static function mobile()
    {
        return isset($_REQUEST['mobileView']) && $_REQUEST['mobileView'] == 1;
    }

    /**
     * Set to show (or not) video banner + slider on page
     *
     * @param boolean $state
     */
    public function setVideoBanner($state)
    {
        $this->_showVideoBannerState = boolval($state);
    }

    /**
     * Set if user after login in top page form should be redirected into main page
     * or current (shown before "Login" click) page. Default is false.
     *
     * @param boolean $state
     * @return View
     */
    public function setRedirectToMainPageAfterLogin($state)
    {
        $this->_redirectToMainPageAfterLogin = boolval($state);
        return $this;
    }

    /**
     * Add css which will be loaded in page header
     * @param string $css_url - url to css
     * @return View
     */
    public function addLocalCss($css_url)
    {
        $this->_localCss[] = $css_url;
        $this->_localCss = array_unique($this->_localCss);

        return $this;
    }

    public function getLocalCss()
    {
        return $this->_localCss;
    }

    /**
     * Add JavaScript script which will be loaded in page header
     * @param string $jsUrl - url to js Script
     * @param boolean $async - load script asynchronous
     * @param boolean $defer - load script after the page has loaded
     * @return View
     */
    public function addLocalJs($jsUrl, $async = false, $defer = false)
    {
        $this->_localJs[] = [
            'url' => $jsUrl,
            'async' => $async,
            'defer' => $defer
        ];
        $this->_localJs = array_unique($this->_localJs, SORT_REGULAR);

        return $this;
    }

    public function getLocalJs()
    {
        return $this->_localJs;
    }

    /**
     * Add chunk which should be called in page header
     *
     * @param string $chunkName - chunk name
     * @param array $args - array of chunk arguments
     */
    public function addHeaderChunk($chunkName, array $args = null)
    {
        if (is_null($args)) {
            $this->_headerChunks[$chunkName] = [];
        } else {
            $this->_headerChunks[$chunkName] = $args;
        }
    }

    public function getHeaderChunks()
    {
        return $this->_headerChunks;
    }

    /**
     * Set template name (former tpl_set_tplname())
     * @param string $tplName
     * @return View
     */
    public function setTemplate($tplName)
    {
        //TODO: refactoring needed but this is still this way
        tpl_set_tplname($tplName);
        $this->_template = $tplName;

        return $this;
    }

    /**
     * Wrapper for obsolete template system.
     * Use display() instead!
     *
     */
    public function buildView()
    {
        tpl_BuildTemplate();
    }

    public function buildInMiniTpl()
    {
        tpl_BuildTemplate(true);
    }

    public function buildOnlySelectedTpl()
    {
        tpl_BuildTemplate(false, true);
    }

    /**
     * Build template and display page.
     * @param string $layoutTemplate - base template to use
     */
    public function display($layoutTemplate = null)
    {

        if (is_null($layoutTemplate)) {
            $layoutTemplate = MainLayoutController::MAIN_TEMPLATE;
            MainLayoutController::init(); // init vars for main-layout
        } else if ($layoutTemplate = MainLayoutController::MINI_TEMPLATE) {
            MainLayoutController::init();
        }

        $this->_callTemplate($layoutTemplate);

        // nothing is called after this!
        exit;

    }

    /**
     * TODO
     * @param string $template
     */
    public function _callTemplate($template = null)
    {
        if (is_null($template)) {
            $template = $this->_template;
        }

        // The only var accessed from within template code
        $view = $this;      // $view var for use inside template
        $tr = function ($arg) {
            // TODO: it will be refactored to proper call
            return tr($arg);
        };

        require_once(self::TPL_DIR . '/' . $template . '.tpl.php');

    }

    public static function showPlainText($text)
    {
        if (!headers_sent()) {
            header('Content-type: text/plain');
        }
        die($text);
    }

    /**
     * @return bool
     */
    public function isRedirectToMainPageAfterLogin()
    {
        return $this->_redirectToMainPageAfterLogin;
    }

    /**
     * Set subtitle of the page
     *
     * @param string $subtitle
     * @return View
     */
    public function setSubtitle($subtitle)
    {
        $this->_subtitle = $subtitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->_subtitle;
    }
}
