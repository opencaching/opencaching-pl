<?php
namespace Utils\I18n;

use Utils\Uri\Uri;

class I18n
{

    /**
     * supported translations list is stored in config['supportedLanguages'] var in settings* files
     * @return array of supported tranlation in form: 'PL', 'EN', 'NL', 'RO'
     */
    public static function getSupportedTranslations(){

        if(isset($GLOBALS['config']['supportedLanguages']) && is_array($GLOBALS['config']['supportedLanguages'])){
            return $GLOBALS['config']['supportedLanguages'];
        } else {
            error_log(__METHOD__.': There is no $config[supportedLanguages] settings -'.
                        'please load settingsDefault.inc.php in your setting.inc.php!');
            return array('en', 'nl', 'pl', 'ro');
        }
    }

    public static function isTranslationSupported($lang){
        return in_array($lang, self::getSupportedTranslations());
    }

    public static function getLanguagesFlagsData($currentLang=null){

        $result = array();
        foreach(self::getSupportedTranslations() as $lang){
            if(!isset($currentLang) || $lang != $currentLang){
                $result[$lang]['name'] = $lang;
                $result[$lang]['img'] = '/images/flags/' . $lang . '.gif';
                $result[$lang]['link'] = Uri::setOrReplaceParamValue('lang',$lang);
            }
        }
        return $result;
    }
}

